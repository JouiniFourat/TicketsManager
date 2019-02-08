<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user2 = new User();
        $user2->setPassword(
            $this->encoder->encodePassword($user2, 'azerty')
        );
        $user->setPassword(
            $this->encoder->encodePassword($user, '0000')
    );
        $user->setUsername('admin');
        $user->setEmail('no-reply@localhost');
        $user2->setUsername('fourat');
        $user2->setEmail('no-reply-fourat@localhost');
        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}