<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $category_1 = $this->getReference('category_1');
        $category_2 = $this->getReference('category_2');

        $products = [
            [
                "name" => "Black&Decker A7062 40 Parça Cırcırlı Tornavida Seti",
                "category" => $category_1,
                "price" => "120.75",
                "stock" => 10,
            ],
            [
                "name" => "Reko Mini Tamir Hassas Tornavida Seti 32'li",
                "category" => $category_1,
                "price" => "49.50",
                "stock" => 10
            ],
            [
                "name" => "Viko Karre Anahtar - Beyaz",
                "category" => $category_2,
                "price" => "11.28",
                "stock" => 10
            ],
            [
                "name" => "Legrand Salbei Anahtar, Alüminyum",
                "category" => $category_2,
                "price" => "22.80",
                "stock" => 10
            ],
            [
                "name" => "Schneider Asfora Beyaz Komütatör",
                "category" => $category_2,
                "price" => "12.95",
                "stock" => 10
            ]
        ];

        foreach ($products as $product) {

            $productData = new Product();
            $productData->setName($product['name']);
            $productData->setStock($product['stock']);
            $productData->setPrice($product['price']);
            $productData->addCategory($product['category']);
            $manager->persist($productData);
        }
        $manager->flush();
    }
}
