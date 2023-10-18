<?php

namespace App\Service\DiscountClass;


use Doctrine\Common\Collections\Collection;

class BuyXGetY implements DiscountInterface
{

    private $cartItem;
    private $settings;

    public function __construct(array $settings, Collection $cartItem, float $total)
    {
        $this->settings = $settings;
        $this->cartItem = $cartItem;
    }

    public function calculate(): ?float
    {

        foreach ($this->cartItem as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if (in_array($category->getId(), $this->settings['categories']) && $item->getQuantity() >= $this->settings['product_count']) {
                    return $item->getProduct()->getPrice() * $this->settings['free_product_count'];

                }
            }
        }

        return null;
    }

}
