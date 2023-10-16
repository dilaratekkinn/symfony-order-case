<?php

namespace App\Service\DiscountClass;


use Doctrine\Common\Collections\Collection;

class SecondCategoryDiscount implements DiscountInterface
{
    private $products;
    private $currentTotal;
    private $settings;

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products): void
    {
        $this->products = $products;
    }

    /**
     * @return mixed
     */
    public function getCurrentTotal()
    {
        return $this->currentTotal;
    }

    /**
     * @param mixed $currentTotal
     */
    public function setCurrentTotal($currentTotal): void
    {
        $this->currentTotal = $currentTotal;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }


    public function __construct(array $settings, Collection $products, float $currentTotal)
    {
        $this->products = $products;
        $this->currentTotal = $currentTotal;
        $this->settings = $settings;
    }

    public function calculate()
    {
        $return = 0;
        foreach ($this->getProducts() as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if ($category->getId() == $this->getSettings()['category_id'] && $item->getQuantity() >= $this->getSettings()['product_count']) {
                    $return += $item->getProduct()->getPrice() * $this->getSettings()['free_product_count'];
                    break;
                }
            }
        }
        return $return;
    }

}
