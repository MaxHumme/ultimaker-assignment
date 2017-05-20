<?php
namespace AppBundle\Domain\Service;

use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Interfaces\Factory\UserFactoryInterface;
use AppBundle\Domain\Interfaces\Repository\UserRepositoryInterface;
use AppBundle\Domain\Interfaces\Service\UserServiceInterface;
use RuntimeException;

/**
 * Interface UserService
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class UserService implements UserServiceInterface
{
    /**
     * @var \AppBundle\Domain\Entity\User
     */
    private $user;

    /**
     * @var \AppBundle\Domain\Interfaces\Factory\UserFactoryInterface
     */
    private $userFactory;

    /**
     * @var \AppBundle\Domain\Interfaces\Repository\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserService constructor.
     *
     * @param \AppBundle\Domain\Interfaces\Factory\UserFactoryInterface $userFactory
     * @param \AppBundle\Domain\Interfaces\Repository\UserRepositoryInterface $userRepository
     */
    public function __construct(UserFactoryInterface $userFactory, UserRepositoryInterface $userRepository)
    {
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /** @inheritdoc */
    public function authenticate($apiToken): ?User
    {
        $this->user = $this->userRepository->userWithApiToken($apiToken);

        return $this->user;
    }

    /** @inheritdoc */
    public function authenticatedUser(): ?User {
        return $this->user;
    }

    /** @inheritdoc */
    public function createUser($username, $apiToken): User
    {
        $user = $this->userFactory->create($username, $apiToken);
        $user = $this->userRepository->add($user);

        return $user;
    }

    /** @inheritdoc */
    public function getUserWithUsername($username): ?User
    {
        return $this->userRepository->userWithUsername($username);
    }
}
