<?php
namespace AppBundle\Domain\Entity;

use AppBundle\Domain\Interfaces\Entity\HasIdInterface;

/**
 * Class AbstractEntity
 *
 * To be used as a base class for every entity.
 *
 * @author Max Humme <max@humme.nl>
 */
abstract class AbstractEntity
{
    /**
     * Checks if $entity equals this entity.
     *
     * @param \AppBundle\Domain\Interfaces\Entity\HasIdInterface $entity
     * @return bool
     */
    abstract public function equals(HasIdInterface $entity): bool;
}
