<?php

namespace App\Service\DiscountClass;

use Doctrine\Common\Collections\Collection;

class MoreXPercentY implements DiscountInterface
{
    private $settings;
    private $cartItem;

    public function __construct(array $settings, Collection $cartItem, float $total)
    {
        $this->settings = $settings;
        $this->cartItem = $cartItem;
    }

    /**
     * @return array|null
     */
    public function calculate(): ?float
    {
        $count = 0;
        $found = 0;

        foreach ($this->cartItem as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if (in_array($category->getId(), $this->settings['categories'])) {
                    $count++;
                    if ($found == 0) {
                        $found = $item->getProduct()->getPrice();
                        continue;
                    }
                    if ($item->getProduct()->getPrice() < $found) {
                        $found = $item->getProduct()->getPrice();
                    }
                }
            }
        }
        if ($count >= $this->settings['product_count']) {
            return ($found / 100) * $this->settings['discount'];
        }

        return null;
    }

}
