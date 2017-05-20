<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Domain\Entity\User;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadUserData
 *
 * @author Max Humme <max@humme.nl>
 */
final class LoadUserData implements FixtureInterface
{
    /**
     * Loads a user into the database.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('ultimaker');
        $user->setApiToken('my-api-token');

        $now = new DateTime();
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);

        $manager->persist($user);
        $manager->flush();
    }
}
