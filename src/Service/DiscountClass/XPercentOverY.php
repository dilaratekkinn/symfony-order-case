<?php

namespace App\Service\DiscountClass;


use Doctrine\Common\Collections\Collection;

class XPercentOverY implements DiscountInterface
{
    private $total;
    private $settings;

    public function __construct(array $settings, Collection $cartItem, float $total)
    {
        $this->settings = $settings;
        $this->total = $total;
    }

    public function calculate(): ?float
    {

        if ($this->total >= $this->settings['min']) {
            return ($this->total / 100) * $this->settings['min'];
        }

        return null;
    }


}
