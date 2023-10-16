<?php

namespace App\Service\DiscountClass;

use Doctrine\Common\Collections\Collection;

interface DiscountInterface
{
    public function __construct(array $settings, Collection $products, float $currentTotal);

    public function calculate();
}
