<?php
namespace AppBundle\Domain\Interfaces\Factory;

use AppBundle\Domain\Entity\User;

/**
 * Interface UserFactoryInterface
 *
 * Responsible for creating Users.
 *
 * @author Max Humme <max@humme.nl>
 */
interface UserFactoryInterface
{
    /**
     * Creates a User.
     *
     * @param string $username
     * @param string $apiToken
     * @return \AppBundle\Domain\Entity\User
     */
    public function create($username, $apiToken): User;
}
