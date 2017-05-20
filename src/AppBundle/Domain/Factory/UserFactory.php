<?php
namespace AppBundle\Domain\Factory;

use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Interfaces\Factory\UserFactoryInterface;
use DateTime;

/**
 * Class UserFactory
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class UserFactory implements UserFactoryInterface
{
    /** @inheritdoc */
    public function create($username, $apiToken): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setApiToken($apiToken);

        $now = new DateTime();
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);

        return $user;
    }
}
