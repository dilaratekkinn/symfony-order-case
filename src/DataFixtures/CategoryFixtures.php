<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $cat1 = new Category();
        $cat1->setName("category_1");

        $cat2 = new Category();
        $cat2->setName("category_2");

        $manager->persist($cat1);
        $manager->persist($cat2);
        $this->addReference('category_1', $cat1);
        $this->addReference('category_2', $cat2);

        $manager->flush();

    }
}