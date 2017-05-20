<?php
namespace AppBundle\Domain\Service;

use AppBundle\Domain\Entity\Image;
use AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface;
use AppBundle\Domain\Interfaces\Repository\ImageRepositoryInterface;
use AppBundle\Domain\Interfaces\Service\ImageServiceInterface;

/**
 * Class ImageService
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class ImageService implements ImageServiceInterface
{
    /**
     * @var \AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface
     */
    private $imageFactory;

    /**
     * @var \AppBundle\Domain\Interfaces\Repository\ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @var string
     */
    private $imagesDirectory;

    /**
     * ImageService constructor.
     *
     * @param \AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface $imageFactory
     * @param \AppBundle\Domain\Interfaces\Repository\ImageRepositoryInterface $imageRepository
     * @param string $imagesDirectory
     */
    public function __construct(
        ImageFactoryInterface $imageFactory,
        ImageRepositoryInterface $imageRepository,
        $imagesDirectory
    ) {
        $this->imageFactory = $imageFactory;
        $this->imageRepository = $imageRepository;
        $this->imagesDirectory = $imagesDirectory;
    }

    /** @inheritdoc */
    public function createImage($fileOriginalName, $fileByteSize, $fileMimeType): Image
    {
        $image = $this->imageFactory->create($fileOriginalName, $fileByteSize, $fileMimeType);
        $this->imageRepository->add($image);

        return $image;
    }

    /** @inheritdoc */
    public function deleteImage(Image $image)
    {
        // delete physical image file
        unlink($this->imagesDirectory.'/'.$image->getFileName());

        // delete image from database
        $this->imageRepository->delete($image);
    }
}
