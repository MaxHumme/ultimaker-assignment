<?php

use AppBundle\App\JsonArrayComparator;
use AppBundle\Domain\Interfaces\Service\PrintServiceInterface;
use AppBundle\Domain\Interfaces\Service\UserServiceInterface;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    use KernelDictionary;

    /**
     * @var \Behat\Mink\Driver\DriverInterface
     */
    private $driver;

    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var \AppBundle\App\JsonArrayComparator
     */
    private $jsonArrayComparator;

    /**
     * @var \AppBundle\Domain\Entity\ThreeDPrint[]
     */
    private $loadedPrints;

    /**
     * @var \Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser
     */
    private $mimeTypeGuesser;

    /**
     * @var string[]
     */
    private $printInputValues;

    /**
     * @var \AppBundle\Domain\Interfaces\Service\PrintServiceInterface
     */
    private $printService;

    /**
     * @var \AppBundle\Domain\Entity\User
     */
    private $user;

    /**
     * @var \AppBundle\Domain\Interfaces\Service\UserServiceInterface
     */
    private $userService;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param \AppBundle\Domain\Interfaces\Service\PrintServiceInterface $printService
     * @param \AppBundle\Domain\Interfaces\Service\UserServiceInterface $userService
     * @param \AppBundle\App\JsonArrayComparator $jsonArrayComparator
     */
    public function __construct(
        PrintServiceInterface $printService,
        UserServiceInterface $userService,
        JsonArrayComparator $jsonArrayComparator
    ) {
        $this->printService = $printService;
        $this->userService = $userService;
        $this->mimeTypeGuesser = MimeTypeGuesser::getInstance();
        $this->jsonArrayComparator = $jsonArrayComparator;
    }

    /** @BeforeScenario */
    public function before()
    {
        $this->driver = $this->getSession()->getDriver();

        // empty the database
        $purger = new ORMPurger($this->getDoctrineManager());
        $purger->purge();

        // remove all created images
        array_map('unlink', glob($this->getContainer()->getParameter('images_directory').'/*.*'));
    }

    /**
     * @Given there are users:
     */
    public function thereAreUsers(TableNode $table)
    {
        foreach ($table as $row) {
            $this->userService->createUser($row['username'], $row['apiToken']);
        }
    }

    /**
     * @Given I am authenticated as user :arg1
     */
    public function iAmAuthenticatedAsUser($username)
    {
        $this->user = $this->userService->getUserWithUsername($username);
        $this->headers['HTTP_Authorization'] = $this->user->getApiToken();
    }

    /**
     * @When I create a print with title :arg1, description :arg2 and image :arg3
     */
    public function iCreateAPrintWithTitleDescriptionAndImage($title, $description, $image)
    {
        // store print input values, so we can refer to them in this scenario
        $this->printInputValues = ['title' => $title, 'description' => $description];

        $files = ['image' => $this->createUploadedFile($image)];
        $this->driver->getClient()->request('POST', '/api/v1/'.$this->user->getUsername().'/prints/new', $this->printInputValues, $files, $this->headers);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

    /**
     * @Then I should get the new print returned
     */
    public function iShouldGetTheNewPrintReturned()
    {
        $content = $this->getJsonResponseContentArray();

        $username = $this->user->getUsername();
        $title = $this->printInputValues['title'];
        $description = $this->printInputValues['description'];
        $expectedContent = [];
        $expectedContent['data'] = $this->buildShowPrintExpectedContent(null, $username, $title, $description);
        $expectedContent['status'] = $this->buildExpectedStatusContent(Response::HTTP_CREATED);

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @Then the print should be returned when I view my prints
     */
    public function thePrintShouldBeReturnedWhenIViewMyPrints()
    {
        $this->driver->getClient()->request('GET', '/api/v1/'.$this->user->getUsername().'/prints', [], [], $this->headers);
        $this->assertResponseStatus(Response::HTTP_OK);

        $content = $this->getJsonResponseContentArray();

        $username = $this->user->getUsername();
        $title = $this->printInputValues['title'];
        $expectedContent = $this->buildShowPrintsExpectedPrintContent(null, $username, $title);

        $this->assertJsonContentHasExpectedContent($content, $expectedContent);
    }

    /**
     * @Given there are prints:
     */
    public function thereArePrints(TableNode $table)
    {
        foreach ($table as $row) {
            $path = $this->getTestImagesPath($row['image']);
            $size = $this->getFileSize($path);
            $mimeType = $this->getMimeType($path);

            $print = $this->printService->createPrint(
                $row['user'],
                $row['title'],
                $row['description'],
                $row['image'],
                $size,
                $mimeType
            );

            $print->setPublicId($row['publicId']);

            // create physical image
            copy($this->getTestImagesPath($row['image']), $this->getImagesPath($print->getImage()->getFileName()));

            $this->loadedPrints[] = $print;
        }

        $this->getDoctrineManager()->flush();
    }

    /**
     * @When I delete print :arg1
     */
    public function iDeletePrint($printPublicId)
    {
        $requestedPrint = $this->getRequestedPrint($printPublicId);
        $this->driver->getClient()->request('DELETE', '/api/v1/'.$requestedPrint->getUser()->getUsername().'/prints/'.$printPublicId, [], [], $this->headers);
    }

    /**
     * @Then the print should be deleted
     */
    public function thePrintShouldBeDeleted()
    {
        $expectedResponseStatusCode = Response::HTTP_OK;
        $this->assertResponseStatus($expectedResponseStatusCode);

        $content = $this->getJsonResponseContentArray();
        $expectedContent = [];
        $expectedContent['status'] = $this->buildExpectedStatusContent($expectedResponseStatusCode);

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @Then the print should not be deleted
     */
    public function thePrintShouldNotBeDeleted()
    {
        $expectedResponseStatusCode = Response::HTTP_FORBIDDEN;
        $this->assertResponseStatus($expectedResponseStatusCode);

        $content = $this->getJsonResponseContentArray();
        $expectedContent = [];
        $expectedContent['status'] = $this->buildExpectedStatusContent($expectedResponseStatusCode);
        $expectedContent['error'] = null;

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @Then the print should not be returned when I view my prints
     */
    public function thePrintShouldNotBeReturnedWhenIViewMyPrints()
    {
        $this->driver->getClient()->request('GET', '/api/v1/'.$this->user->getUsername().'/prints', [], [], $this->headers);
        $this->assertResponseStatus(Response::HTTP_OK);

        $content = $this->getJsonResponseContentArray();

        $printUnderTest = $this->loadedPrints[0];
        $excludedContent = $this->buildShowPrintsExpectedPrintContent(
            $printUnderTest->getPublicId(),
            $this->user->getUsername(),
            $printUnderTest->getTitle(),
            null,
            $printUnderTest->getUpdatedAt());

        $this->assertJsonContentExcludesContent($content, $excludedContent);
    }

    /**
     * @When I view the print with publicId :arg1
     */
    public function iViewThePrintWithPublicId($printPublicId)
    {
        $this->driver->getClient()->request('GET', '/api/v1/'.$this->user->getUsername().'/prints/'.$printPublicId, [], [], $this->headers);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    /**
     * @Then I should get the print returned
     */
    public function iShouldGetThePrintReturned()
    {
        $content = $this->getJsonResponseContentArray();

        $expectedContent = [];
        $printUnderTest = $this->loadedPrints[0];
        $username = $this->user->getUsername();
        $expectedContent['data'] = $this->buildShowPrintExpectedContent(
            $printUnderTest->getPublicId(),
            $username,
            $printUnderTest->getTitle(),
            $printUnderTest->getDescription(),
            $imageUrl = null,
            $printUnderTest->getUpdatedAt()->format('Y-m-d H:i:s')
        );

        $expectedContent['status'] = $this->buildExpectedStatusContent(Response::HTTP_OK);

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @When I view my prints
     */
    public function iViewMyPrints()
    {
        $this->driver->getClient()->request('GET', '/api/v1/'.$this->user->getUsername().'/prints', [], [], $this->headers);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    /**
     * @Then I should get the prints returned
     */
    public function iShouldGetThePrintsReturned()
    {
        $content = $this->getJsonResponseContentArray();

        $printsUnderTest = $this->loadedPrints;
        $username = $this->user->getUsername();
        $expectedContent['data'] = $this->buildShowPrintsExpectedContent($printsUnderTest, $username);
        $expectedContent['status'] = $this->buildExpectedStatusContent(Response::HTTP_OK);

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @When I update print :arg1 and set the title to :arg2, the description to :arg3 and the image to :arg4
     */
    public function iUpdatePrintAndSetTheTitleToTheDescriptionToAndTheImageTo($printPublicId, $title, $description, $image)
    {
        // store print input values, so we can refer to them in this scenario
        $this->printInputValues = ['publicId' => $printPublicId, 'title' => $title, 'description' => $description];

        $parameters = ['title' => $title, 'description' => $description];
        $files = ['image' => $this->createUploadedFile($image)];

        // This is a put request
        $parameters['_method'] = 'put';

        $requestedPrint = $this->getRequestedPrint($printPublicId);
        $this->driver->getClient()->request('POST', '/api/v1/'.$requestedPrint->getUser()->getUsername().'/prints/'.$printPublicId, $parameters, $files, $this->headers);
    }

    /**
     * @Then the print should be updated
     */
    public function thePrintShouldBeUpdated()
    {
        $expectedResponseStatus = Response::HTTP_OK;
        $this->assertResponseStatus($expectedResponseStatus);

        $content = $this->getJsonResponseContentArray();

        $expectedContent = [];
        $username = $this->user->getUsername();
        $expectedContent['data'] = $this->buildShowPrintExpectedContent(
            $this->printInputValues['publicId'],
            $username,
            $this->printInputValues['title'],
            $this->printInputValues['description']
        );

        $expectedContent['status'] = $this->buildExpectedStatusContent($expectedResponseStatus);

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @Then the print should not be updated
     */
    public function thePrintShouldNotBeUpdated()
    {
        $expectedResponseStatus = Response::HTTP_FORBIDDEN;
        $this->assertResponseStatus($expectedResponseStatus);

        $content = $this->getJsonResponseContentArray();

        $expectedContent = [];
        $expectedContent['status'] = $this->buildExpectedStatusContent($expectedResponseStatus);
        $expectedContent['error'] = null;

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * @Then I should get the updated print returned when I view print :arg1
     */
    public function iShouldGetTheUpdatedPrintReturnedWhenIViewPrint($printPublicId)
    {
        $this->driver->getClient()->request('GET', '/api/v1/'.$this->user->getUsername().'/prints/'.$printPublicId, [], [], $this->headers);
        $this->assertResponseStatus(Response::HTTP_OK);

        $content = $this->getJsonResponseContentArray();

        $expectedContent = [];
        $username = $this->user->getUsername();
        $expectedContent['data'] = $this->buildShowPrintExpectedContent(
            $this->printInputValues['publicId'],
            $username,
            $this->printInputValues['title'],
            $this->printInputValues['description']
        );

        $expectedContent['status'] = $this->buildExpectedStatusContent(Response::HTTP_OK);

        $this->assertJsonContentEqualsExpectedContent($content, $expectedContent);
    }

    /**
     * Asserts that $content equals $expectedContent.
     *
     * @param mixed[] $content
     * @param mixed[] $expectedContent
     * @throws \Behat\Mink\Exception\ExpectationException when a key differs.
     * @throws \Behat\Mink\Exception\ExpectationException when a key is missing.
     * @throws \Behat\Mink\Exception\ExpectationException when we have an extra key.
     */
    private function assertJsonContentEqualsExpectedContent(array $content, array $expectedContent)
    {
        $diff = $this->jsonArrayComparator->compareJsonArrays($content, $expectedContent);

        if (!empty($diff)) {
            foreach ($diff as $key => $value) {
                if ($key == 'differingKeys') {
                    foreach ($diff[$key] as $diffKey => $diffValue) {
                        throw new ExpectationException("Key '$diffKey' is '".$diffValue['is']."', but was expected to be '".$diffValue['expected']."'", $this->driver);
                    }
                } elseif ($key == 'missingKeys') {
                    foreach ($diff[$key] as $missingKeyValue) {
                        throw new ExpectationException("Key '$missingKeyValue' was expected, but not present.", $this->driver);
                    }
                } elseif ($key == 'extraKeys') {
                    foreach ($diff[$key] as $extraKey => $extraValue) {
                        throw new ExpectationException("Key '$extraKey' was not expected, but present.", $this->driver);
                        throw new ExpectationException("Key '$extraKey' was not expected, but present.", $this->driver);
                    }
                }
            }
        }
    }

    /**
     * Asserts that $content does not contain $expectedContent.
     *
     * @param mixed[] $content
     * @param mixed[] $excludedContent
     * @throws \Behat\Mink\Exception\ExpectationException when $excludedContent is present in $content.
     */
    private function assertJsonContentExcludesContent(array $content, array $excludedContent)
    {
        if ($this->jsonArrayComparator->jsonArrayContainsJsonArray($content, $excludedContent)) {
            throw new ExpectationException("Content that should not be included is present.", $this->driver);
        }
    }

    /**
     * Asserts that $content contains $expectedContent.
     *
     * @param mixed[] $content
     * @param mixed[] $expectedContent
     * @throws \Behat\Mink\Exception\ExpectationException when $excludedContent is not present in $content.
     */
    private function assertJsonContentHasExpectedContent(array $content, array $expectedContent)
    {
        if (!$this->jsonArrayComparator->jsonArrayContainsJsonArray($content, $expectedContent)) {
            throw new ExpectationException("Expected content is not present.", $this->driver);
        }
    }

    /**
     * Creates the expected content in associative array form for the status part of the json response.
     *
     * @param int $statusCode
     * @return mixed[]
     */
    private function buildExpectedStatusContent($statusCode)
    {
        return [
            'code' => $statusCode,
            'message' => Response::$statusTexts[$statusCode]
        ];
    }

    /**
     * Creates the expected content in associative array form for the print part of the json response.
     *
     * @param string|null $id
     * @param string|null $username
     * @param string|null $title
     * @param string|null $description
     * @param string|null $imageUrl
     * @param string|null $dateTime
     * @return mixed[]
     */
    private function buildShowPrintExpectedContent(
        $id = null,
        $username = null,
        $title = null,
        $description = null,
        $imageUrl = null,
        $dateTime = null
    ) {
        return [
            'print' => [
                'id' => $id,
                'user' => [
                    'username' => $username
                ],
                'title' => $title,
                'description' => $description,
                'imageUrl' => $imageUrl,
                'dateTime' => $dateTime
            ]
        ];
    }

    /**
     * Creates the expected content in associative array form for the prints part of the json response.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint[] $printsUnderTest
     * @param string $username
     * @return mixed[]
     */
    private function buildShowPrintsExpectedContent(array $printsUnderTest, $username)
    {
        $printsUnderTest = array_reverse($printsUnderTest);
        $expectedContent = ['prints' => []];

        foreach ($printsUnderTest as $print) {
            $expectedContent['prints'][] = $this->buildShowPrintsExpectedPrintContent(
                $print->getPublicId(),
                $username,
                $print->getTitle(),
                null,
                $print->getUpdatedAt()->format('Y-m-d H:i:s'));
        }

        return $expectedContent;
    }

    /**
     * Creates the expected content in associative array form for the single
     * print part of the prints part of the json response.
     *
     * @param string|null $id
     * @param string|null $username
     * @param string|null $title
     * @param string|null $imageUrl
     * @param string|null $dateTime
     * @return mixed[]
     */
    private function buildShowPrintsExpectedPrintContent(
        $id = null,
        $username = null,
        $title = null,
        $imageUrl = null,
        $dateTime = null
    ) {
        return [
            'id' => $id,
            'user' => [
                'username' => $username
            ],
            'title' => $title,
            'imageUrl' => $imageUrl,
            'dateTime' => $dateTime
        ];
    }

    /**
     * Creates an UploadedFile from $image.
     *
     * Creates the physical file in the process.
     *
     * @param string $image
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private function createUploadedFile($image)
    {
        $originalPath = $this->getTestImagesPath($image);
        $tempPath = $this->getTestImagesPath('temp_test_image');
        copy($originalPath, $tempPath);
        $mimeType = $this->getMimeType($originalPath);
        $size = $this->getFileSize($originalPath);

        return new UploadedFile($tempPath, $image, $mimeType, $size, UPLOAD_ERR_OK, true);
    }

    /**
     * Returns Doctrine's object manager.
     *
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getDoctrineManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Returns the byte size of $file.
     *
     * @param string $file
     * @return int
     */
    private function getFileSize($file)
    {
        return filesize($file);
    }

    /**
     * Returns the path of image with name $imageName.
     *
     * @param string $imageName
     * @return string
     */
    private function getImagesPath($imageName)
    {
        return $this->getContainer()->getParameter('images_directory').'/'.$imageName;
    }

    /**
     * Returns the json response of the request in associative array form.
     *
     * @return mixed[]
     */
    private function getJsonResponseContentArray()
    {
        return json_decode($this->driver->getContent(), true);
    }

    /**
     * Returns the MIME type of the $file.
     *
     * @param string $file
     * @return string
     */
    private function getMimeType($file)
    {
        return $this->mimeTypeGuesser->guess($file);
    }

    /**
     * Returns the requested ThreeDPrint.
     *
     * @param string $printPublicId
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    private function getRequestedPrint($printPublicId)
    {
        $requestedPrint = null;
        foreach ($this->loadedPrints as $loadedPrint) {
            if ($loadedPrint->getPublicId() == $printPublicId) {
                $requestedPrint = $loadedPrint;
            }
        }

        return $requestedPrint;
    }

    /**
     * Returns the path of the test image with name $imageName.
     *
     * @param string $imageName
     * @return string
     */
    private function getTestImagesPath($imageName)
    {
        return $this->getKernel()->getRootDir().'/../features/test_images/'.$imageName;
    }
}
