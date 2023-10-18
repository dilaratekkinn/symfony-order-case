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
        $product = $productService->getProductByID($parameters['item']);
        $productService->checkProductStock($product, $parameters['quantity']);

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
        $cartItem = $this->getCartItem($id);
        $this->getEntityManager()->getRepository(CartItem::class)->find($id)->remove($cartItem, true);

    }

    /**
     * @param array $parameters
     * @param int $id
     * @return CartItem
     */
    public function updateCartItemQuantity(array $parameters, int $id): CartItem
    {
        $cartItem = $this->getCartItem($id);
        if ($parameters['quantity'] > $cartItem->getProduct()->getStock()) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        $cartItem->setQuantity($parameters['quantity']);
        $this->getEntityManager()->getRepository(CartItem::class)->flush();

        return $cartItem;
    }

    /**
     * @param $id
     * @return CartItem|null
     */
    public function getCartItem($id): ?CartItem
    {
        $cartItem = $this->getEntityManager()->getRepository(CartItem::class)->find($id);
        if (is_null($cartItem)) {
            throw new NotFoundHttpException('There Is No CartItem SWıth This ID!');
        }
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