<?php

namespace App\Service;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property-read CartRepository $repository
 */
class CartService extends BaseService
{
    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        $user = $this->getUser();
        $cart = $this->getCartByOwnerUser();
        if (is_null($cart)) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->getEntityManager()->getRepository(Cart::class)->add($cart, true);
        }

        return $cart;
    }

    /**
     * @return void
     */
    public function removeCart(): void
    {
        //id ile işlem yap.
        $cart = $this->getCartByOwnerUser();
        $this->getEntityManager()->getRepository(Cart::class)->remove($cart,true);
    }

    /**
     * @return array
     */
    public function showCart(): array
    {
        $cart = $this->getCart();
        if ($cart->getCartItems()->isEmpty()) {
            throw new NotFoundHttpException('Your Cart Is Empty,Shop Now!');
        }
        $total = $this->getTotal($cart);
        $isChangeStock = $this->checkCartByProductStock($cart);
        $discountService = $this->container->get(DiscountService::class);
        $discounts = $discountService->showDiscount();
        return [
            'cart' => $cart,
            'total' => $total,
            'discount' => $discounts,
            'isChangeStock' => $isChangeStock
        ];
    }

    /**
     * @param Cart $cart
     * @return float
     */
    public function getTotal(Cart $cart): float
    {
        $total = 0;
        foreach ($cart->getCartItems() as $item) {
            $total += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        return $total;
    }

    /**
     * @param Cart $cart
     * @return void
     */
    public function checkCartByProductStock(Cart $cart): bool
    {
        $isChangeStock = false;
        foreach ($cart->getCartItems() as $cartItem) {
            if ($cartItem->getProduct()->getStock() < $cartItem->getQuantity()) {
                $cartItem->setQuantity($cartItem->getProduct()->getStock());
                $isChangeStock = true;
            }
        }

        if ($isChangeStock) {
            $this->getEntityManager()->getRepository(Cart::class)->flush();
        }
        return $isChangeStock;
    }

    /**
     * @param bool $throw
     * @return Cart|null
     */
    public function getCartByOwnerUser(bool $throw = true): ?Cart
    {
        return $this->getEntityManager()->getRepository(Cart::class)->findOneBy(['user' => $this->getUser()]);
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            DiscountService::class => DiscountService::class,
            CartItemService::class => CartItemService::class
        ]);
    }

}