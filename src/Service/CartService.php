<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\DiscountRepository;
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


    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        ProductRepository      $productRepository,
        Security               $security,
        CartItemRepository     $cartItemRepository,
        DiscountService   $discountService
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->cartItemRepository = $cartItemRepository;
        $this->discountService =$discountService;
    }

    /**
     * @throws \Exception
     */


    public function add(array $parameters)
    {
        $user = $this->security->getUser();

        $product = $this->checkProductWithQuantity($parameters['item'], $parameters['quantity']);
        $cart = $this->getCart($user);

        $checkCartItem = $this->cartItemRepository->findOneBy(['cart' => $cart, 'product' => $product]);
        if (!$checkCartItem) {
            $checkCartItem = new CartItem();
            $checkCartItem->setCart($cart);
            $checkCartItem->setQuantity(0);
            $checkCartItem->setProduct($product);
        }
        $checkCartItem->setQuantity($checkCartItem->getQuantity() + $parameters['quantity']);
        $this->em->persist($checkCartItem);
        $this->em->flush();

        return $checkCartItem;
    }

    private function checkProductWithQuantity($id, $quantity)
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        // symfony Entity anotaion olarak eklenebilir
        if (!$product) {
            throw new \Exception('bu ürün yok');
        }
        if ($product->getStock() < $quantity) {
            throw new \Exception('bu ürünün stoğu yok');
        }
        return $product;
    }


    private function getCart($user)
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

    public function show($user): array
    {
        $cart = $this->getCart($user);
        $total=$this->getTotal($cart);
        $discounts=$this->discountService->showDiscount($cart->getCartItems(),$total);
        if($discounts !== null){
            $discounts = [
                'discount_reason' => $discounts['discountCampaign']->getDiscountReason(),
                'discount_campaign' => $discounts['discountCampaign']->getContent(),
                'discount' => $discounts['discount']
            ];
        }
        return [$cart,$total, $discounts];
    }

    public function remove($id): bool
    {
        $cartItem = $this->checkCartItem($id);
        $this->em->remove($cartItem);
        $this->em->flush();
        return true;
    }

    public function update(array $parameters, $id)
    {
        $cartItem = $this->checkCartItem($id);
        if (!$cartItem) {
            throw new \Exception('Cart Item yok ki');
        }
        if ($parameters['quantity'] > $cartItem->getProduct()->getStock()) {
            throw new \Exception('İçeride bu kadar stock bulunmamakta');
        }


        $cartItem->setQuantity($parameters['quantity']);
        $this->em->persist($cartItem);
        $this->em->flush();
        return $cartItem;
    }

    private function checkCartItem($id)
    {
        $user = $this->security->getUser();
        $cartItem = $this->cartItemRepository->findOneBy([
            'cart' => $user->getCart(),
            'product' => $this->productRepository->find($id)
        ]);

        return $cartItem;
    }


    public static function getTotal($cart)
    {
        $return = 0;
        foreach ($cart->getCartItems() as $item) {
            $return += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $return;
    }


}