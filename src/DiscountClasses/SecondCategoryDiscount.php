<?php

namespace App\DiscountClasses;


class SecondCategoryDiscount implements DiscountInterface
{

    private $products;
    private $currentTotal;
    private $settings;


    //Setting bölümünün admin tarafından istenilen biçimi bu schema ayarlarına göre yapılır
    public static $settingSchema = [
        'category_id'=>[
            'name'=>'indirim uygulanacak kategoriyi seçiniz',
            'type'=>'number',//category'e özel ayarlanabilir
            'required'=>true
        ],
        'product_count'=>[
            'name'=>'Ürünün toplam adetini girin',
            'type'=>'number',
            'min'=>1,
            'default'=>6,
            'required'=>true
        ],
        'free_product_count'=>[
            'name'=>'İndirim yapılacak Ürün Miktarı',
            'type'=>'number',
            'min'=>1,
            'default'=>1,
            'required'=>true
        ],
    ];

    public function __construct(array $settings,$products, $currentTotal)
    {
        $this->products = $products;
        $this->currentTotal = $currentTotal;
        $this->settings = $settings;

        return $this;
    }

    public function calculate()
    {
        $return = 0;
        foreach ($this->products as $item) {
            foreach ($item->getProduct()->getCategory() as $category) {
                if ($category->getId() == $this->settings['category_id'] && $item->getQuantity() >= $this->settings['product_count'] ) {
                    $return += $item->getProduct()->getPrice()*$this->settings['free_product_count'] ;
                    break;
                }
            }
        }
        return $return;
    }

}
