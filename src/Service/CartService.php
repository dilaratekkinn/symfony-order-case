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
    private $discountRepository;


    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        ProductRepository      $productRepository,
        Security               $security,
        CartItemRepository     $cartItemRepository,
        DiscountRepository     $discountRepository
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->cartItemRepository = $cartItemRepository;
        $this->discountRepository = $discountRepository;
    }

    /**
     * @throws \Exception
     */
    public function add(array $parameters)
    {
        $user = $this->security->getUser();

        $product = $this->productRepository->findOneBy(['id' => $parameters['item']]);
        if (!$product) {
            throw new \Exception('bu ürün yok');
        }
        if ($product->getStock() < $parameters['quantity']) {
            throw new \Exception('bu ürünün stoğu yok');
        }
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->em->persist($cart);
            $this->em->flush();
        }
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

    public function show($user)
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        $discountCampaigns = $this->discountRepository->getActiveDiscounts();
        foreach ($discountCampaigns as $discountCampaign) {
            $class = '\\App\\DiscountClasses\\' . $discountCampaign->getClassName();
            $class = new $class($cart->getCartItems(), $this->getTotal($cart));

            $discount = $class->calculate();
            $availableDiscount = 0;
            if ($discount <= 0){
                continue;
            }

            $availableDiscount = [
                'discountCampaign' => $discountCampaign->getContent(),
                'discountKey'=>$discountCampaign->getDiscountReason(),
                'discount' => 'Kazancınız '.$discount . 'TL '
            ];
            break;
        }
        return [$cart, $availableDiscount];

    }

    private function getTotal($cart)
    {
        $return = 0;
        foreach ($cart->getCartItems() as $item) {
            $return += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $return;
    }
}