<?php

namespace App\DiscountClasses;


class SecondCategoryDiscount implements DiscountInterface
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
        $return = 0;
        foreach ($this->products as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if ($category->getId() == 2 && $item->getQuantity() >= 6) {
                    $return += $item->getProduct()->getPrice();
                    break;
                }
            }
        }
        return $return;
    }

}
