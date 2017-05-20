<?php
namespace AppBundle\Domain\Interfaces\Repository;

use AppBundle\Domain\Entity\Image;

/**
 * Interface ImageRepositoryInterface
 *
 * Responsible for retrieving, adding and deleting Images.
 *
 * @author Max Humme <max@humme.nl>
 */
interface ImageRepositoryInterface
{
    /**
     * Adds an $image to the repository.
     *
     * @param \AppBundle\Domain\Entity\Image $image
     * @return \AppBundle\Domain\Entity\Image
     */
    public function add(Image $image): Image;

    /**
     * Deletes an $image from the repository.
     *
     * @param \AppBundle\Domain\Entity\Image $image
     */
    public function delete(Image $image);

    /**
     * Returns a new unique image file name.
     *
     * @param string $mimeType
     * @return string
     */
    public function newImageFileName($mimeType): string;
}
