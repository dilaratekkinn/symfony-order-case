<?php

namespace App\DiscountClasses;

// 1 ID\'li kategoriden iki veya daha fazla ürün satın alındığında, en ucuz ürüne %20 indirim yapılır.
// buyFromXCategoryYQuantityOverProductZPersentDiscount
class FirstCategoryDiscount implements DiscountInterface
{

    private $products;
    private $currentTotal;
    private $settings;
    public static $settingSchema = [
        'category_id' => [
            'name' => 'indirim uygulanacak kategoriyi seçiniz',
            'type' => 'number',//category'e özel ayarlanabilir
            'required' => true
        ],
        'product_count'=>[
            'name'=>'Ürünün toplam adetini girin',
            'type'=>'number',
            'min'=>2,
            'default'=>6,
            'required'=>true
        ],
        'discount' => [
            'name' => 'indirim oranı',
            'type' => 'number',
            'min' => 1,
            'default' => 20,
            'max' => 100,
            'required' => true
        ],

    ];

    public function __construct(array $settings, $products, $currentTotal)
    {
        $this->products = $products;
        $this->currentTotal = $currentTotal;
        $this->settings = $settings;
    }

    public function calculate()
    {
        $count = 0;
        $found = 0;

        foreach ($this->products as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if ($category->getId() == $this->settings['category_id']) {
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
        return 0;

    }

}
