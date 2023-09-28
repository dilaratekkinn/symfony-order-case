<?php

namespace App\DiscountClasses;

interface DiscountInterface
{
    public function __construct($products,$currentTotal);
    public function calculate();
}
