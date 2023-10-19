<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property-read CartItemRepository $repository
 */
class CartItemService extends BaseService
{
    /**
     * @param array $parameters
     * @return CartItem
     */
    public function addCartItemToCart(array $parameters): CartItem
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCart();
        $productService = $this->container->get(ProductService::class);
        $product=$productService->checkProduct($parameters['item'], $parameters['quantity']);

        $cartItem = $this->getEntityManager()->getRepository(CartItem::class)->getCartItemByProductAndCart($cart, $product);

        if (is_null($cartItem)) {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setQuantity(0);
            $cartItem->setProduct($product);
            $this->getEntityManager()->getRepository(CartItem::class)->add($cartItem);
        }
        $cartItem->setQuantity($cartItem->getQuantity() + $parameters['quantity']);
        $this->getEntityManager()->getRepository(CartItem::class)->flush();

        return $cartItem;
    }


    /**
     * @param int $id
     * @return void
     */
    public function removeItem(int $id): void
    {
        $cartItem = $this->getEntityManager()->getRepository(CartItem::class)->getCartItem($id);
        $this->getEntityManager()->getRepository(CartItem::class)->remove($cartItem, true);

    }

    /**
     * @param array $parameters
     * @param int $id
     * @return CartItem
     */
    public function updateCartItemQuantity(array $parameters, int $id): CartItem
    {
        $cartItem = $this->getEntityManager()->getRepository(CartItem::class)->getCartItem($id);
        if ($parameters['quantity'] > $cartItem->getProduct()->getStock()) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        $cartItem->setQuantity($parameters['quantity']);
        $this->getEntityManager()->getRepository(CartItem::class)->flush();

        return $cartItem;
    }


    /**
     * @return array|string[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            ProductService::class => ProductService::class,
            CartService::class => CartService::class,
        ]);
    }
}