<?php
namespace AppBundle\Domain\Interfaces\Entity;

/**
 * Interface HasIdInterface
 *
 * To be used on all entities, so we can check if they are equal.
 *
 * @author Max Humme <max@humme.nl>
 */
interface HasIdInterface
{
    /**
     * Returns the id of the entity.
     *
     * We don't give the return type here, because Doctrine does not play nice with it.
     *
     * @return int
     */
    public function getId();
}
