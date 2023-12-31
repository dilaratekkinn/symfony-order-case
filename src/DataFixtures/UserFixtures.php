<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setEmail('dilaratekinn@gmail.com');
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword('123');
        $manager->persist($user);
        $manager->flush();

        $this->addReference('user-token',$user);
    }
}