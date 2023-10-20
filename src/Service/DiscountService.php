<?php

namespace App\Service;

use App\Entity\Discount;
use App\Repository\DiscountRepository;
use App\Service\Factory\DiscountFactory;

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
        $discount = $this->getDiscount();
        if (!is_null($discount)) {
            return [
                'discountContent' => $discount['discountClass']->getContent(),
                'discountReason' => $discount['discountClass']->getDiscountReason(),
                'discountTotal' => $discount['discountTotal']
            ];
        }
        return null;
    }

    public function getDiscount(): ?array
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
         * DiscountClass içerisinde 259 kampanya bulunabilir ama sistemde 2 kampanya aktif o yüzden sadece aktif olanları tek sorguda getirmeli
         */
        $discountClasses = $this->getEntityManager()->getRepository(Discount::class)->getActiveDiscounts();
        foreach ($discountClasses as $discountClass) {
            $class = $this->container->get(DiscountFactory::class)->create($discountClass->getClassName());
            $discount = $class->calculate($discountClass->getSettings(), $items, $total);
            // Entity ve indirim fiyatı dönüldü
            if (!is_null($discount)) {
                return [
                    'discountClass' => $discountClass,
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
            CartService::class => CartService::class,
            DiscountFactory::class => DiscountFactory::class
        ]);
    }
}