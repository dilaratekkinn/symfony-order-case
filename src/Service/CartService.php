<?php

namespace App\Service;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;

class CartService
{
    private $em;
    private $cartRepository;
    private $security;
    private $discountService;
    private $cartItemService;


    public function __construct(

        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        Security               $security,
        DiscountService        $discountService,
        CartItemService        $cartItemService

    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->security = $security;
        $this->discountService = $discountService;
        $this->cartItemService = $cartItemService;
    }

    public function createCart(array $parameters)
    {
        $cart = $this->getCartByFilter(['user' => $this->security->getUser()]);
        $this->cartItemService->addCartItemToCart($cart,$parameters['item'], $parameters['quantity']);
        return $cart;

    }

    public function removeCart($user): bool
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            throw new UserNotFoundException('User does not have a cart with this id');
        }
        $this->em->remove($cart);
        $this->em->flush();
        return true;

    }

    public function showCart($user): array
    {
        $cart = $this->getCartByFilter(['user' => $user]);
        if ($cart->getCartItems()->isEmpty()) {
            throw new NotFoundHttpException('Your Cart Is Empty,Shop Now!');
        }

        $total = $this->getTotal($cart);
        $changed = false;

        foreach ($cart->getCartItems() as $cartItem) {
            if ($cartItem->getProduct()->getStock() < $cartItem->getQuantity()) {
                $cartItem->setQuantity($cartItem->getProduct()->getStock());
                $changed = true;
            }
        }
        if ($changed) {
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
        return [$cart, $total, $discounts, $changed];
    }

    private function getCartByFilter($query)
    {
        $cart = $this->cartRepository->findOneBy($query);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($this->security->getUser());
            $this->em->persist($cart);
            $this->em->flush();
        }
        return $cart;

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