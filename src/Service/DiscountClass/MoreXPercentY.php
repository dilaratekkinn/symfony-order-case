<?php

namespace App\Service\DiscountClass;

use Doctrine\Common\Collections\Collection;

class MoreXPercentY implements DiscountInterface
{

    /**
     * @return array|null
     */
    public function calculate(array $settings, Collection $cartItem, float $total): ?float
    {
        $count = 0;
        $found = 0;

        foreach ($cartItem as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if (in_array($category->getId(), $settings['categories'])) {
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
        if ($count >= $settings['product_count']) {
            return ($found / 100) * $settings['discount'];
        }

        return null;
    }

}
