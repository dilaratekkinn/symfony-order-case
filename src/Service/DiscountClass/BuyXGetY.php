<?php

namespace App\Service\DiscountClass;


use Doctrine\Common\Collections\Collection;

class BuyXGetY implements DiscountInterface
{

    public function calculate(array $settings, Collection $cartItem, float $total): ?float
    {

        foreach ($cartItem as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if (in_array($category->getId(), $settings['categories']) && $item->getQuantity() >= $settings['product_count']) {
                    return $item->getProduct()->getPrice() * $settings['free_product_count'];

                }
            }
        }

        return null;
    }

}
