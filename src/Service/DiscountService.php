<?php

namespace App\Service;

use App\Entity\Discount;
use App\Repository\DiscountRepository;
use App\Service\DiscountClass\DiscountInterface;

/**
 * @property-read DiscountRepository $repository
 */
class DiscountService extends BaseService
{
    /**
     * @return array|null
     */
    public function showDiscount(): ?array
    {
        /**
         * Keyler kampanya adını temsil eder,unique olmalıdır.Apiden kullanıcya id yerine keyler döner key=id denilebilir.
         * className ise hangi sınıfın çalıştırılacağını temsil eder.Kampayanlar birbirinden farklı olabilir ama aynı sınıfı çalıştırabilirler.
         * Örneğin 1 idli katgoriden 4 alana 1 bedava 2idli kategoriden 6 alana 1 bedava.Burada kampanyalar farklı olsa bile çalışack olan aynı classtır.
         * Key bu ikisini birbirinden ayırır (get_4_free_1_in_1,get_6_free_1_in_2)
         * class name bu kampanyaların hangi class üzerinden çalışacağını gösterir.
         */

        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCart();
        $items = $cart->getCartItems();
        $total = $cartService->getTotal($cart);
        /**
         *
         * Burada Factory kullanamama sebebi bu döngü başlayana kadar DiscountClass içerisinde hangi sınıflar olduğunu ve hangisinin çalışması gerektiği bilinmez.
         * DiscountClass içerisinde 259 kampanya bulunabilir ama sistemde 2 kampanya aktif o yüzden sadece aktif olanları tek sorguda getirmeli
         */
        $discountClasses = $this->getEntityManager()->getRepository(Discount::class)->getActiveDiscounts();
        foreach ($discountClasses as $discountClass) {

            if (!class_exists($discountClass->getClassName())) {
                continue;
            }
            $class = $discountClass->getClassName();
            /**
             * buradan classın içeride hangi parametreleri kullanacğı bilinmez,ona kullanması için bu değişkenler verilir içeride ne yaptığı class'a aittir.Dönen cevaptan sadece
             * yapılacaksa indirim yapılmayacaksa null beklenir.
             * Parametre sayıları dinamikte ayarlanabilir ( new $class(...$parametersx)) parametreler bir array olur ... operatoru içerisidneki keyleri açar
             * her classınn içerinde statik olarak istediği dataları gösteren bir schemaya ihtiyaç duyulur burada oluşturulacak her yeni kampanyanın ihtiyaç duyabileceği değişkenler asbitlenmiştir
             */

            $class = new $class($discountClass->getSettings(), $items, $total);
            if (!$class instanceof DiscountInterface) {
                /**
                 * Classı Interface kurallarına bağlamak için ekstra kontrol
                 */
                continue;
            }
            $discount = $class->calculate();
            if (!is_null($discount)) {
                return [
                    'discountContent' => $discountClass->getContent(),
                    'discountReason' => $discountClass->getDiscountReason(),
                    'discountTotal' => $discount
                ];
            }
        }
        return null;
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CartService::class => CartService::class
        ]);
    }
}