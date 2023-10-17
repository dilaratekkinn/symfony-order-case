<?php

namespace App\Service;

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

        $cartItem = $this->getCartItem($product);
        if (is_null($cartItem)) {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setQuantity(0);
            $cartItem->setProduct($product);
            $this->repository->add($cartItem);
        }
        $cartItem->setQuantity($cartItem->getQuantity() + $parameters['quantity']);
        $this->repository->flush();

        return $cartItem;
    }

    /**
     * @param int $id
     * @return void
     */
    public function removeItem(int $id): void
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCart();
        $cartItem = $this->repository->getCartItemByIdAndCart($id, $cart);
        $this->repository->remove($cartItem, true);
    }

    /**
     * @param array $parameters
     * @param int $id
     * @return CartItem
     */
    public function updateCartItemQuantity(array $parameters, int $id): CartItem
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCart();
        $cartItem = $this->repository->getCartItemByIdAndCart($id, $cart);

        if ($parameters['quantity'] > $cartItem->getProduct()->getStock()) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        $cartItem->setQuantity($parameters['quantity']);
        $this->repository->flush();
        return $cartItem;
    }

    /**
     * @param Product $product
     * @return CartItem|null
     */
    private function getCartItem(Product $product): ?CartItem
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCart();
        return $this->repository->findOneBy([
            'cart' => $cart,
            'product' => $product
        ]);
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => CartItemRepository::class,
            ProductService::class => ProductService::class,
            CartService::class => CartService::class,
        ]);
    }
}