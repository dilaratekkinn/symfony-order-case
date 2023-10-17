<?php

namespace App\Service\DiscountClass;

interface DiscountInterface
{
    public function calculate(): ?array;
}
