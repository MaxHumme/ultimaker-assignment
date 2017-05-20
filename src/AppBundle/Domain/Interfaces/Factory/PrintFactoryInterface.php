<?php
namespace AppBundle\Domain\Interfaces\Factory;

use AppBundle\Domain\Entity\ThreeDPrint;
use AppBundle\Domain\Entity\User;

/**
 * Interface PrintFactoryInterface
 *
 * Responsible for creating ThreeDPrints.
 *
 * @author Max Humme <max@humme.nl>
 */
interface PrintFactoryInterface
{
    /**
     * Creates a ThreeDPrint.
     *
     * @param \AppBundle\Domain\Entity\User $user
     * @param string $title
     * @param string $description
     * @param string $fileOriginalName
     * @param integer $fileByteSize
     * @param string $fileMimeType
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function create(
        User $user,
        $title,
        $description,
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): ThreeDPrint;
}
