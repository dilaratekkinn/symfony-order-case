<?php

namespace App\Service\DiscountClass;

use Doctrine\Common\Collections\Collection;

class FirstCategoryDiscount implements DiscountInterface
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
        $count = 0;
        $found = 0;

        foreach ($this->getProducts() as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if ($category->getId() == $this->getSettings()['category_id']) {
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
        if ($count >= $this->getSettings()['product_count']) {
            return ($found / 100) * $this->getSettings()['discount'];
        }
        return 0;

    }

}
