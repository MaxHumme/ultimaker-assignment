<?php
namespace AppBundle\Domain\Interfaces\Service;

use AppBundle\Domain\Entity\Image;

/**
 * Interface ImageServiceInterface
 *
 * Responsible for working with Images in the Domain.
 *
 * @author Max Humme <max@humme.nl>
 */
interface ImageServiceInterface
{
    /**
     * Creates an Image.
     *
     * And adds it to the Domain.
     *
     * @param string $fileOriginalName
     * @param int $fileByteSize
     * @param string $fileMimeType
     * @return \AppBundle\Domain\Entity\Image
     */
    public function createImage($fileOriginalName, $fileByteSize, $fileMimeType): Image;

    /**
     * Deletes an Image from the Domain.
     *
     * @param \AppBundle\Domain\Entity\Image $image
     */
    public function deleteImage(Image $image);
}
