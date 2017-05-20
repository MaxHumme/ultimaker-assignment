<?php
namespace AppBundle\Domain\Interfaces\Factory;

use AppBundle\Domain\Entity\Image;

/**
 * Interface ImageFactoryInterface
 *
 * Responsible for creating Image entities.
 *
 * @author Max Humme <max@humme.nl>
 */
interface ImageFactoryInterface
{
    /**
     * Creates an Image.
     *
     * @param string $fileOriginalName
     * @param integer $fileByteSize
     * @param string $fileMimeType
     * @return \AppBundle\Domain\Entity\Image
     */
    public function create($fileOriginalName, $fileByteSize, $fileMimeType ): Image;
}
