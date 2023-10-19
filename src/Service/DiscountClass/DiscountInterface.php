<?php

namespace App\Service\DiscountClass;

use Doctrine\Common\Collections\Collection;

interface DiscountInterface
{
    public function calculate(array $settings, Collection $cartItem, float $total): ?float;
}
