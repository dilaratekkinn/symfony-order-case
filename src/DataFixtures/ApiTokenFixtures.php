<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApiTokenFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $token = new ApiToken();
        $token->setUser($this->getReference('user-token'));
        $token->setToken("dilara");
        $manager->persist($token);
        $manager->flush();

    }
}