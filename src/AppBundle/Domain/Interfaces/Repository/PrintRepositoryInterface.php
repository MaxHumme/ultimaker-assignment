<?php
namespace AppBundle\Domain\Interfaces\Repository;

use AppBundle\Domain\Entity\ThreeDPrint;
use AppBundle\Domain\Entity\User;

/**
 * Interface PrintRepositoryInterface
 *
 * Responsible for retrieving, adding and deleting ThreeDPrints.
 *
 * @author Max Humme <max@humme.nl>
 */
interface PrintRepositoryInterface
{
    /**
     * Adds a ThreeDPrint to the repository.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint $print
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function add(ThreeDPrint $print): ThreeDPrint;

    /**
     * Deletes a ThreeDPrint from the repository.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint $print
     */
    public function delete(ThreeDPrint $print);

    /**
     * Returns the ThreeDPrint with $publicId.
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
     * @param \AppBundle\Domain\Entity\User $user
     * @param int $limit
     * @param string|null $afterPrintPublicId
     * @return \AppBundle\Domain\Entity\ThreeDPrint[]
     */
    public function getPrintsOfUser(User $user, $limit = 20, $afterPrintPublicId = null): array;
}
