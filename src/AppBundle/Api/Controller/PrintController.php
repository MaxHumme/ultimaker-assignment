<?php
namespace AppBundle\Api\Controller;

use AppBundle\Api\Traits\ImageUploadTrait;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Class PrintController
 *
 * @author Max Humme <max@humme.nl>
 */
final class PrintController extends AbstractApiController
{
    use ImageUploadTrait;

    /**
     * @var int
     */
    private $maxByteSizeUploadedImage;

    /**
     * Returns the list of prints of User with $username.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(Request $request, $username)
    {
        $this->guardUserExists($username);
        $this->guardIndexActionInputIsValid($request, $username);

        $printService = $this->getPrintService();
        $limit = $this->getInput($request, 'limit');
        $afterPublicId = $this->getInput($request, 'after_id');
        $prints = $printService->getPrintsOfUser($username, $limit, $afterPublicId);
        $formattedPrints = $this->getFormatterFactory()->createPrintsFormatter($prints)->render();

        return $this->jsonResponse(['prints' => $formattedPrints]);
    }

    /**
     * Creates a new Print.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request, $username)
    {
        $this->guardUserOwnsResource($username);
        $this->guardCreateActionInputIsValid($request);

        $printService = $this->getPrintService();
        $title = $this->getInput($request,'title');
        $description = $this->getInput($request,'description');
        $uploadedImage = $this->getUploadedImage($request, 'image');
        $fileOriginalName = $this->getUploadedImageOriginalFileName($uploadedImage);
        $fileByteSize = $this->getUploadedImageByteSize($uploadedImage);
        $fileMimeType = $this->getUploadedImageMimeType($uploadedImage);

        $print = $printService->createPrint(
            $username,
            $title,
            $description,
            $fileOriginalName,
            $fileByteSize,
            $fileMimeType
        );

        // Now we have the $print, we also have the file name to store the uploaded image as.
        $storeFileName = $print->getImage()->getFileName();
        $this->storeUploadedImageFile($uploadedImage, $storeFileName);

        $formattedPrint = $this->getFormatterFactory()->createPrintFormatter($print)->render();

        return $this->jsonResponse(['print' => $formattedPrint], Response::HTTP_CREATED);
    }

    /**
     * Returns the print.
     *
     * @param string $username
     * @param string $printPublicId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function showAction($username, $printPublicId)
    {
        $this->guardPrintOfUserExists($username, $printPublicId);

        $printService = $this->getPrintService();
        $print = $printService->getPrint($printPublicId);
        $formattedPrint = $this->getFormatterFactory()->createPrintFormatter($print)->render();

        return $this->jsonResponse(['print' => $formattedPrint]);
    }

    /**
     * Updates the print.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $username
     * @param string $printPublicId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request, $username, $printPublicId)
    {
        $this->guardUserOwnsResource($username);
        $this->guardPrintOfUserExists($username, $printPublicId);
        $this->guardUpdateActionInputIsValid($request);

        $printService = $this->getPrintService();
        $print = $printService->getPrint($printPublicId);
        $title = $this->getInput($request,'title');
        $description = $this->getInput($request,'description');
        $uploadedImage = $this->getUploadedImage($request, 'image');
        $fileOriginalName = $this->getUploadedImageOriginalFileName($uploadedImage);
        $fileByteSize = $this->getUploadedImageByteSize($uploadedImage);
        $fileMimeType = $this->getUploadedImageMimeType($uploadedImage);

        $updatedPrint = $printService->updatePrint(
            $print,
            $title,
            $description,
            $fileOriginalName,
            $fileByteSize,
            $fileMimeType
        );

        // Now we have the $updatedPrint, we also have the file name to store the uploaded image as.
        $storeFileName = $print->getImage()->getFileName();
        $this->storeUploadedImageFile($uploadedImage, $storeFileName);

        $formattedPrint = $this->getFormatterFactory()->createPrintFormatter($updatedPrint)->render();

        return $this->jsonResponse(['print' => $formattedPrint]);
    }

    /**
     * Deletes the print.
     *
     * @param string $username
     * @param string $printPublicId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction($username, $printPublicId)
    {
        $this->guardUserOwnsResource($username);
        $this->guardPrintOfUserExists($username, $printPublicId);

        $this->getPrintService()->deletePrint($printPublicId);

        return $this->jsonResponse();
    }

    /**
     * Returns the FormatterFactory.
     *
     * @return \AppBundle\Api\Interfaces\Factory\FormatterFactoryInterface
     */
    private function getFormatterFactory()
    {
        return $this->container->get('app.formatter_factory');
    }

    /**
     * Returns the PrintService.
     *
     * @return \AppBundle\Domain\Interfaces\Service\PrintServiceInterface
     */
    private function getPrintService()
    {
        return $this->container->get('app.print_service');
    }

    /**
     * Returns the UserService.
     *
     * @return \AppBundle\Domain\Interfaces\Service\UserServiceInterface
     */
    private function getUserService()
    {
        return $this->container->get('app.user_service');
    }

    /**
     * Guards that createAction has the input it needs.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException when input is missing.
     */
    private function guardCreateActionHasNecessaryInput(Request $request)
    {
        if (!$this->hasInput($request, 'title')
            || !$this->hasInput($request, 'description')
            || !$this->requestHasUploadedImage($request, 'image')
        ) {
            throw new BadRequestHttpException("Input 'title', 'description' and 'image' are required.");
        }
    }

    /**
     * Guards that the input for createAction is valid.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException when 'title' is empty.
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException when 'description' is missing.
     * @throws \RuntimeException when 'image' is not uploaded successfully.
     * @throws \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException when 'image' is of an invalid type.
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException when 'image' size is too large.
     */
    private function guardCreateActionInputIsValid(Request $request)
    {
        $this->guardCreateActionHasNecessaryInput($request);

        $title = $this->getInput($request, 'title');
        if ($title == "") {
            throw new BadRequestHttpException("Input 'title' is required.");
        }

        $description = $this->getInput($request, 'description');
        if ($description == "") {
            throw new BadRequestHttpException("Input 'description' is required.");
        }

        $uploadedImage = $this->getUploadedImage($request, 'image');
        if (!$uploadedImage->isValid()) {
            throw new RuntimeException('Image not uploaded successfully.');
        }

        if (!$this->uploadedImageIsOfAValidType($uploadedImage)) {
            throw new UnsupportedMediaTypeHttpException('Uploading a file of type '.$uploadedImage->getMimeType().' is not allowed.');
        }

        if ($this->uploadedImageIsTooLarge($uploadedImage)) {
            throw new BadRequestHttpException("The uploaded image is too large. The maximum size of an image is ".($this->maxByteSizeUploadedImage() / (1024 * 1024)." MB"));
        }
    }

    /**
     * Guards that the input for indexAction is valid.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $username
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException when 'limit' is not numeric.
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException when 'limit' is not greater than 0.
     */
    private function guardIndexActionInputIsValid(Request $request, $username)
    {
        if ($this->hasInput($request, 'limit')) {
            $limit = $this->getInput($request, 'limit');
            if (!is_numeric($limit)) {
                throw new BadRequestHttpException("'limit' should have a numeric value.");
            } elseif ($limit <= 0) {
                throw new BadRequestHttpException("'limit' should be greater then 0.");
            }
        }

        if ($this->hasInput($request, 'after_id')) {
            $printPublicId = $this->getInput($request, 'after_id');
            $this->guardPrintOfUserExists($username, $printPublicId);
        }
    }

    /**
     * Guards that user with $username has a print with $publicId.
     *
     * @param string $username
     * @param string $printPublicId
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *          when there is no user with $username which has a print with $publicId.
     */
    private function guardPrintOfUserExists($username, $printPublicId)
    {
        $this->guardUserExists($username);

        $print = $this->getPrintService()->getPrint($printPublicId);
        if (is_null($print) || $print->getUser()->getUsername() != $username) {
            throw new NotFoundHttpException("Print of user with '$username' with id '$printPublicId' does not exist.");
        }
    }

    /**
     * Guards that the input for updateAction is valid.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function guardUpdateActionInputIsValid(Request $request)
    {
        $this->guardCreateActionInputIsValid($request);
    }

    /**
     * Guards that a user with $username exists.
     *
     * @param string $username
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException when the a user with $username does not exist.
     */
    private function guardUserExists($username)
    {
        $user = $this->getUserService()->getUserWithUsername($username);
        if (is_null($user)) {
            throw new NotFoundHttpException("User with username '$username' does not exist.");
        }
    }

    /**
     * Guards that the authenticated user is the user with $username.
     *
     * @param string $username
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException when the user does not own the resource.
     */
    private function guardUserOwnsResource($username)
    {
        $this->guardUserExists($username);

        if ($this->authenticatedUser()->getUsername() != $username) {
            throw new AccessDeniedHttpException("You don't own this resource.");
        }
    }

    /**
     * Returns the max byte size for an uploaded image.
     *
     * Loads it from the parameters of the app.
     *
     * @return int
     */
    private function maxByteSizeUploadedImage()
    {
        if (!isset($this->maxByteSizeUploadedImage)) {
            $this->maxByteSizeUploadedImage = $this->getParameter('uploaded_image_max_byte_size');
        }
        return $this->maxByteSizeUploadedImage;
    }

    /**
     * Checks if $uploadedImage is of a valid type.
     *
     * Valid types are 'image/jpeg', 'image/png' and 'image/gif'.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @return bool
     */
    private function uploadedImageIsOfAValidType(UploadedFile $uploadedImage)
    {
        $mimeType = $this->getUploadedImageMimeType($uploadedImage);

        return $mimeType == 'image/jpeg' || $mimeType == 'image/png' || $mimeType == 'image/gif';
    }

    /**
     * Checks if $uploadedImage is too large.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @return bool
     */
    private function uploadedImageIsTooLarge(UploadedFile $uploadedImage)
    {
        $maxByteSizeUploadedImage = $this->maxByteSizeUploadedImage();
        $uploadedImageByteSize = $this->getUploadedImageByteSize($uploadedImage);

        return $maxByteSizeUploadedImage < $uploadedImageByteSize;
    }
}
