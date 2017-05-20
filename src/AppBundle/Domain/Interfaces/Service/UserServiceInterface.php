<?php
namespace AppBundle\Domain\Interfaces\Service;

use AppBundle\Domain\Entity\User;

/**
 * Interface UserServiceInterface
 *
 * Responsible for working with Users in the Domain.
 *
 * @author Max Humme <max@humme.nl>
 */
interface UserServiceInterface
{
    /**
     * Authenticates the user which has $apiToken.
     *
     * @param string $apiToken
     * @return \AppBundle\Domain\Entity\User|null
     */
    public function authenticate($apiToken): ?User;

    /**
     * Returns the authenticated User.
     *
     * @return \AppBundle\Domain\Entity\User
     */
    public function authenticatedUser(): ?User;

    /**
     * Creates a User.
     *
     * @param string $username
     * @param string $apiToken
     * @return \AppBundle\Domain\Entity\User
     */
    public function createUser($username, $apiToken): User;

    /**
     * Returns the User with $username.
     *
     * @param string $username
     * @return \AppBundle\Domain\Entity\User|null
     */
    public function getUserWithUsername($username): ?User;
}
