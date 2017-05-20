<?php
namespace AppBundle\Domain\Interfaces\Repository;

use AppBundle\Domain\Entity\User;

/**
 * Interface PrintRepositoryInterface
 *
 * Responsible for retrieving, adding and deleting Users.
 *
 * @author Max Humme <max@humme.nl>
 */
interface UserRepositoryInterface
{
    /**
     * Adds a User to the repository.
     *
     * @param \AppBundle\Domain\Entity\User $user
     * @return \AppBundle\Domain\Entity\User
     */
    public function add(User $user): User;

    /**
     * Returns the User that has $apiToken.
     *
     * @param string $apiToken
     * @return \AppBundle\Domain\Entity\User|null
     */
    public function userWithApiToken($apiToken): ?User;

    /**
     * Returns the User that has $username.
     *
     * @param string $username
     * @return \AppBundle\Domain\Entity\User|null
     */
    public function userWithUsername($username): ?User;
}
