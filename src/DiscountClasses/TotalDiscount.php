<?php

namespace App\DiscountClasses;


class TotalDiscount implements DiscountInterface
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

        if ($this->currentTotal >= 1000)
            return ($this->currentTotal / 100) * 10;


        return 0;
    }

}
