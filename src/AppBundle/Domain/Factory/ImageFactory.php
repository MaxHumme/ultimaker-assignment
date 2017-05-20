<?php
namespace AppBundle\Domain\Factory;

use AppBundle\Domain\Entity\Image;
use AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface;
use DateTime;

/**
 * Class ImageFactory
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class ImageFactory implements ImageFactoryInterface
{
    /** @inheritdoc */
    public function create(
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): Image
    {
        $image = new Image();
        $image->setOriginalFileName($fileOriginalName);
        $image->setFileByteSize($fileByteSize);
        $image->setMimeType($fileMimeType);

        $now = new DateTime();
        $image->setCreatedAt($now);
        $image->setUpdatedAt($now);

        return $image;
    }
}
