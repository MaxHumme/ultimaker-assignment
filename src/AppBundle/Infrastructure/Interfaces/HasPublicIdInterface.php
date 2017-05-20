<?php
namespace AppBundle\Infrastructure\Interfaces;

/**
 * Interface HasPublicIdInterface
 *
 * Use it on the repositories that have entities which have a publicId attribute.
 *
 * @author Max Humme <max@humme.nl>
 */
interface HasPublicIdInterface
{
    /**
     * Returns a new and unique public id.
     *
     * @return string
     */
    public function newPublicId(): string;
}
