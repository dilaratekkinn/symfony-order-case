<?php

namespace App\DataFixtures;

use App\Entity\Discount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiscountFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $discounts = [
            ['discount_reason' => '10_PERCENT_OVER_1000',
                'content' => 'Toplam 1000TL ve üzerinde alışveriş yapan bir müşteri, siparişin tamamından %10 indirim kazanır.',
                'class_name' => 'TotalDiscount',
                'priority' => 1,
                'status' => 1,
                'settings' => [
                    'min'=>1000,
                    'discount'=>10,
                ]
            ],
            ['discount_reason' => 'BUY_5_GET_1',
                'content' => '2 ID\'li kategoriye ait bir üründen 6 adet satın alındığında, bir tanesi ücretsiz olarak verilir.',
                'class_name' => 'SecondCategoryDiscount',
                'priority' => 2,
                'status' => 1,
                'settings' => [
                    'category_id'=>2,
                    'product_count'=>6,
                    'free_product_count'=>1
                ]
                ],
            ['discount_reason' => 'MORE_2_20_PERCENT_1',
                'content' => '1 ID\'li kategoriden iki veya daha fazla ürün satın alındığında, en ucuz ürüne %20 indirim yapılır.',
                'class_name' => 'FirstCategoryDiscount',
                'priority' => 3,
                'status' => 1,
                'settings' => [
                    'category_id'=>1,
                    'discount'=>20,
                    'product_count'=>2,
                ]
            ],

        ];

        foreach ($discounts as $campaign){

            $discount = new Discount();
            $discount->setDiscountReason($campaign['discount_reason']);
            $discount->setContent($campaign['content']);
            $discount->setClassName($campaign['class_name']);
            $discount->setPriority($campaign['priority']);
            $discount->setStatus($campaign['status']);
            $discount->setSettings($campaign['settings']);
            $manager->persist($discount);
        }
        $manager->flush();

    }
}