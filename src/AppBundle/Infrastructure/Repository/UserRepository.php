<?php
namespace AppBundle\Infrastructure\Repository;

use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Interfaces\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class UserRepository
 *
 * Responsible for retrieving, adding and deleting Users.
 *
 * @author Max Humme <max@humme.nl>
 */
final class UserRepository extends AbstractEntityRepository implements UserRepositoryInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $doctrineRepository;

    /**
     * UserRepository constructor.
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        parent::__construct($doctrine->getManager());

        $this->doctrineRepository = $doctrine->getRepository('AppBundle:User');
    }

    /** @inheritdoc */
    public function add(User $user): User
    {
        $this->doctrineManager->persist($user);
        $this->doctrineManager->flush();

        // Load $user in memory, so we don't have to query the database if we need it again this request.
        $this->load($user);

        return $user;
    }

    /** @inheritdoc */
    public function userWithApiToken($apiToken): ?User
    {
        // Try to find the user in memory first, so we might not need to query the database
        $loadedUsers = $this->getLoadedEntities();
        foreach ($loadedUsers as $loadedUser) {
            if ($this->loadedUserMatchesUserWithApiToken($loadedUser, $apiToken)) {
                return $loadedUser;
            }
        }

        // User not found in memory, query the database.
        $user = $this->doctrineRepository->findOneByApiToken($apiToken);

        if (!is_null($user)) {
            $this->load($user);
        }

        return $user;
    }

    /** @inheritdoc */
    public function userWithUsername($username): ?User
    {
        // Try to find the user in memory first, so we might not need to query the database
        $loadedUsers = $this->getLoadedEntities();
        foreach ($loadedUsers as $loadedUser) {
            if ($this->loadedUserMatchesUserWithUsername($loadedUser, $username)) {
                return $loadedUser;
            }
        }

        // User not found in memory, query the database.
        $user = $this->doctrineRepository->findOneByUsername($username);

        if (!is_null($user)) {
            $this->load($user);
        }

        return $user;
    }

    /**
     * Checks if $loadedUser has $apiToken.
     *
     * @param \AppBundle\Domain\Entity\User $loadedUser
     * @param string $apiToken
     * @return bool
     */
    private function loadedUserMatchesUserWithApiToken(User $loadedUser, $apiToken)
    {
        return $loadedUser->getApiToken() == $apiToken;
    }

    /**
     * Checks if $loadedUser has $username.
     *
     * @param \AppBundle\Domain\Entity\User $loadedUser
     * @param string $username
     * @return bool
     */
    private function loadedUserMatchesUserWithUsername(User $loadedUser, $username)
    {
        return $loadedUser->getUsername() == $username;
    }
}
