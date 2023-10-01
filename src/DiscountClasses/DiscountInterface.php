<?php

namespace App\DiscountClasses;

interface DiscountInterface
{
    public function __construct(array $settings,$products,$currentTotal);
    public function calculate();
}
