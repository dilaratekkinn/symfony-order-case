<?php

namespace App\Service\DiscountClass;

use Doctrine\Common\Collections\Collection;

interface DiscountInterface
{
    public function __construct(array $settings, Collection $cartItem, float $total);
    public function calculate(): ?float;
}
