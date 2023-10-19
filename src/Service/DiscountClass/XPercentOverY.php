<?php

namespace App\Service\DiscountClass;


use Doctrine\Common\Collections\Collection;

class XPercentOverY implements DiscountInterface
{
    public function calculate(array $settings, Collection $cartItem, float $total): ?float
    {
        if ($total>= $settings['min']) {
            return ($total/ 100) * $settings['discount'];
        }

        return null;
    }


}
