<?php
namespace AppBundle\Domain\Interfaces\Service;

use AppBundle\Domain\Entity\ThreeDPrint;

/**
 * Interface PrintServiceInterface
 *
 * Responsible for working with ThreeDPrints in the Domain.
 *
 * @author Max Humme <max@humme.nl>
 */
interface PrintServiceInterface
{
    /**
     * Creates a ThreeDPrint.
     *
     * And adds it to its repository.
     *
     * @param string $username
     * @param string $title
     * @param string $description
     * @param string $fileOriginalName
     * @param int $fileByteSize
     * @param string $fileMimeType
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function createPrint(
        $username,
        $title,
        $description,
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): ThreeDPrint;

    /**
     * Deletes a ThreeDPrint with $publicId from the Domain.
     *
     * @param string $publicId
     */
    public function deletePrint($publicId);

    /**
     * Gets the ThreeDPrint with $publicId.
     *
     * @param string $publicId
     * @return \AppBundle\Domain\Entity\ThreeDPrint|null
     */
    public function getPrint($publicId): ?ThreeDPrint;

    /**
     * Returns the ThreeDPrints that are owned by $user.
     *
     * With a maximum number of 20, and starting after $afterPrintPublicId if given.
     * Sorted by newest ThreeDPrints.
     *
     * @param string $username
     * @param int $limit
     * @param string|null $afterPrintPublicId
     * @return \AppBundle\Domain\Entity\ThreeDPrint[]
     */
    public function getPrintsOfUser($username, $limit = 20, $afterPrintPublicId = null): array;

    /**
     * Updates $print with the give input.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint $print
     * @param string $title
     * @param string $description
     * @param string $fileOriginalName
     * @param int $fileByteSize
     * @param string $fileMimeType
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function updatePrint(
        ThreeDPrint $print,
        $title,
        $description,
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): ThreeDPrint;
}
