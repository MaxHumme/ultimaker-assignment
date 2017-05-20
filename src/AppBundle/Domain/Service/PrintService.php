<?php
namespace AppBundle\Domain\Service;

use AppBundle\Domain\Entity\ThreeDPrint;
use AppBundle\Domain\Interfaces\Factory\PrintFactoryInterface;
use AppBundle\Domain\Interfaces\Repository\PrintRepositoryInterface;
use AppBundle\Domain\Interfaces\Service\ImageServiceInterface;
use AppBundle\Domain\Interfaces\Service\PrintServiceInterface;
use AppBundle\Domain\Interfaces\Service\UserServiceInterface;

/**
 * Class PrintService
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class PrintService implements PrintServiceInterface
{
    /**
     * @var \AppBundle\Domain\Interfaces\Service\ImageServiceInterface
     */
    private $imageService;

    /**
     * @var \AppBundle\Domain\Interfaces\Factory\PrintFactoryInterface
     */
    private $printFactory;

    /**
     * @var \AppBundle\Domain\Interfaces\Repository\PrintRepositoryInterface
     */
    private $printRepository;

    /**
     * @var \AppBundle\Domain\Interfaces\Service\UserServiceInterface
     */
    private $userService;

    /**
     * PrintService constructor.
     *
     * @param \AppBundle\Domain\Interfaces\Service\ImageServiceInterface $imageService
     * @param \AppBundle\Domain\Interfaces\Factory\PrintFactoryInterface $printFactory
     * @param \AppBundle\Domain\Interfaces\Repository\PrintRepositoryInterface $printRepository
     * @param \AppBundle\Domain\Interfaces\Service\UserServiceInterface $userService
     */
    public function __construct(
        ImageServiceInterface $imageService,
        PrintFactoryInterface $printFactory,
        PrintRepositoryInterface $printRepository,
        UserServiceInterface $userService
    ) {
        $this->imageService = $imageService;
        $this->printFactory = $printFactory;
        $this->printRepository = $printRepository;
        $this->userService = $userService;
    }

    /** @inheritdoc */
    public function createPrint(
        $username,
        $title,
        $description,
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): ThreeDPrint
    {
        $user = $this->userService->getUserWithUsername($username);

        $print = $this->printFactory->create(
            $user,
            $title,
            $description,
            $fileOriginalName,
            $fileByteSize,
            $fileMimeType
        );

        $print = $this->printRepository->add($print);

        return $print;
    }

    /** @inheritdoc */
    public function deletePrint($publicId)
    {
        $print = $this->getPrint($publicId);
        $image = $print->getImage();

        // We delete the print before the image, so we don't set the NOT NULLABLE field 'image_id' to NULL.
        $this->printRepository->delete($print);

        // We remove the image here ourselves instead of cascading, because next to the database image,
        // we also need to delete the image file.
        $this->imageService->deleteImage($image);
    }

    /** @inheritdoc */
    public function getPrint($publicId): ?ThreeDPrint
    {
        return $this->printRepository->getPrint($publicId);
    }

    /** @inheritdoc */
    public function getPrintsOfUser($username, $limit = 20, $afterPrintPublicId = null): array
    {
        $user = $this->userService->getUserWithUsername($username);

        return $this->printRepository->getPrintsOfUser($user, $limit, $afterPrintPublicId);
    }

    /** @inheritdoc */
    public function updatePrint(
        ThreeDPrint $print,
        $title,
        $description,
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): ThreeDPrint
    {
        $print->setTitle($title);
        $print->setDescription($description);

        // We create and add a new image before we delete the old one, so we don't set the NOT NULLABLE field
        // 'image_id' to NULL.
        $newImage = $this->imageService->createImage($fileOriginalName, $fileByteSize, $fileMimeType);
        $oldImage = $print->getImage();
        $print->setImage($newImage);
        $this->imageService->deleteImage($oldImage);

        $this->printRepository->update();

        return $print;
    }
}
