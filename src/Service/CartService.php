<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\DiscountRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CartService
{
    private $em;
    private $cartRepository;
    private $productRepository;
    private $security;
    private $cartItemRepository;
    private $discountService;
    private $orderItemRepository;


    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        ProductRepository      $productRepository,
        Security               $security,
        CartItemRepository     $cartItemRepository,
        DiscountService        $discountService,
        OrderItemRepository    $orderItemRepository
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->cartItemRepository = $cartItemRepository;
        $this->discountService = $discountService;
    }


    public function addCartItemToCart(array $parameters)
    {
        $cart = $this->getCart($this->security->getUser());
        $product = $this->checkProductWithQuantity($parameters['item'], $parameters['quantity']);
        $getCartItem = $this->getCartItemByProductId($product);
        if (!$getCartItem) {
            $getCartItem = new CartItem();
            $getCartItem->setCart($cart);
            $getCartItem->setQuantity(0);
            $getCartItem->setProduct($product);
        }

        $getCartItem->setQuantity($getCartItem->getQuantity() + $parameters['quantity']);
        $this->em->persist($getCartItem);
        $this->em->flush();

        return $getCartItem;
    }

    public function showCart($user): array
    {
        $cart = $this->getCart($user);
        $total = $this->getTotal($cart);
        $changed = false;

        foreach($cart->getCartItems() as $cartItem){
            if($cartItem->getProduct()->getStock() < $cartItem->getQuantity()){
                $cartItem->setQuantity($cartItem->getProduct()->getStock());
                $changed = true;
            }
        }
        if($changed){
            $this->em->persist($cart);
            $this->em->flush();
        }

        $discounts = $this->discountService->showDiscount($cart->getCartItems(), $total);
        if ($discounts !== null) {
            $discounts = [
                'discount_reason' => $discounts['discountCampaign']->getDiscountReason(),
                'discount_campaign' => $discounts['discountCampaign']->getContent(),
                'discount' => $discounts['discount']
            ];
        }
        return [$cart,$total,$discounts,$changed];
    }

    public
    function removeItem($id): bool
    {
        $cartItem = $this->getCartItemByProductId($id);
        $this->em->remove($cartItem);
        $this->em->flush();
        return true;
    }

    public
    function updateCartItemQuantity(array $parameters, $id)
    {
        $cartItem = $this->checkCartItemWithQuantity($id, $parameters['quantity']);
        $cartItem->setQuantity($parameters['quantity']);
        $this->em->persist($cartItem);
        $this->em->flush();
        return $cartItem;
    }

    public
    function removeCart($user): bool
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            throw new \Exception('Userın böyle bi sepeti yok');

        }
        $this->em->remove($cart);
        $this->em->flush();
        return true;

    }

    private
    function getCartItemByProductId($id)
    {
        return $this->cartItemRepository->findOneBy([
            'cart' => $this->security->getUser()->getCart(),
            'product' => $this->productRepository->find($id)
        ]);
    }

    private
    function checkCartItemWithQuantity($id, $quantity)
    {
        $cartItem = $this->getCartItemByProductId($id);
        if (!$cartItem) {
            throw new \Exception('Cart Item yok ki');
        }
        if ($quantity > $cartItem->getProduct()->getStock()) {
            throw new \Exception('İçeride bu kadar stock bulunmamakta');
        }
        return $cartItem;
    }

    private
    function checkProductWithQuantity($id, $quantity)
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        if (!$product) {
            throw new \Exception('bu ürün yok');
        }
        if ($product->getStock() < $quantity) {
            throw new \Exception('bu ürünün stoğu yok');
        }
        return $product;
    }

    private
    function getCart($user)
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->em->persist($cart);
            $this->em->flush();
        }
        return $cart;
    }

    public
    static function getTotal($cart)
    {
        $return = 0;
        foreach ($cart->getCartItems() as $item) {
            $return += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $return;
    }


}