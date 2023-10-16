<?php

namespace App\Service\DiscountClass;


use Doctrine\Common\Collections\Collection;

class TotalDiscount implements DiscountInterface
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
        if ($this->currentTotal >= $this->getSettings()['min']) {
            return ($this->currentTotal / 100) * $this->getSettings()['discount'];
        }
        return 0;
    }

}
