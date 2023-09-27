<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
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


    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        ProductRepository      $productRepository,
        Security               $security,
        CartItemRepository     $cartItemRepository
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->cartItemRepository = $cartItemRepository;
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
        $checkCartItem = $this->cartItemRepository->findOneBy(['cart' => $cart, 'product', $product]);
        dd($checkCartItem);
        if (!$checkCartItem) {
            $checkCartItem = new CartItem();
            $checkCartItem->setCart($cart);
            $checkCartItem->setQuantity(0);
            $checkCartItem->setProduct($product);


            //tablodan kaldırılacak alanlar
            $checkCartItem->setUnitPrice(0);
            $checkCartItem->setTotal(0);
        }
        $checkCartItem->setQuantity($checkCartItem->getQuantity() + $parameters['quantity']);
        $this->em->persist($checkCartItem);
        $this->em->flush();

        return true;
    }
}