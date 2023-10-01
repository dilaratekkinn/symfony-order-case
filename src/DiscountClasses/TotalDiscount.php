<?php

namespace App\DiscountClasses;


class TotalDiscount implements DiscountInterface
{

    private $products;
    private $currentTotal;
    private $settings;

    public static $settingSchema = [
        'min'=>[
            'name'=>'indirim uygulanacak minumum tutarı girin',
            'type'=>'number',
            'min'=>1,
            'default'=>1000,
            'required'=>true
        ],
        'discount'=>[
            'name'=>'indirim oranı',
            'type'=>'number',
            'min'=>1,
            'default'=>10,
            'max'=>100,
            'required'=>true
        ],
    ];

    public function __construct(array $settings, $products, $currentTotal)
    {
        $this->products = $products;
        $this->currentTotal = $currentTotal;
        $this->settings = $settings;


        return $this;
    }

    public function calculate()
    {

        if ($this->currentTotal >= $this->settings['min'])
            return ($this->currentTotal / 100) * $this->settings['discount'];


        return 0;
    }

}
