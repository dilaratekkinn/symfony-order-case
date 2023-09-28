<?php

namespace App\DiscountClasses;


class FirstCategoryDiscount implements DiscountInterface
{

    private $products;
    private $currentTotal;


    public function __construct($products, $currentTotal)
    {
        $this->products = $products;
        $this->currentTotal = $currentTotal;

        return $this;
    }

    public function calculate()
    {
        $count = 0;
        $found = 0;

        foreach ($this->products as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if ($category->getId() == 1) {
                    $count++;
                    if ($found == 0) {
                        $found = $item->getProduct()->getPrice();
                        continue;
                    }
                    if ($item->getProduct()->getPrice() < $found) {
                        $found = $item->getProduct()->getPrice();
                    }
                }
            }
        }
        if ($count >= 2) {
            return ($found / 100) * 20;
        }
        return 0;

    }

}
