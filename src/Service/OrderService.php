<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\DiscountRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class OrderService
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

    public function add(array $parameters)
    {
        $user = $this->security->getUser();
        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            throw new \Exception('Sepetiniz Bulunmamaktadır,Order oluşturamazsınız');
        }
        $discountCampaigns = $this->discountRepository->getActiveDiscounts();

        foreach ($discountCampaigns as $discountCampaign) {
            $class = '\\App\\DiscountClasses\\' . $discountCampaign->getClassName();
            $class = new $class($cart->getCartItems(), $this->getTotal($cart));
            $discount = $class->calculate();

            $availableDiscount = 0;
            $availableDiscountCampaign = null;

            if ($discount <= 0) {
                continue;
            }
            $availableDiscount = $discount;
            break;
        }

        $order = new Order();
        $order->setUser($cart->getUser());
        $order->setDiscountPrice($availableDiscount);
        $order->setDiscount($availableDiscountCampaign);
        $order->setTotal($this->getTotal($cart));
        $this->em->persist($order);
        $this->em->flush();

        if ($cart->getCartItems() !== null) {

            foreach ($cart->getCartItems() as $cartItem) {
                $product = $this->productRepository->findOneBy(['id' => $cartItem->getProduct()->getId()]);
                $orderItem = new OrderItem();
                $orderItem->setBelongsToOrder($order);
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());

                $this->em->persist($orderItem);
                $this->em->flush();

                $product->setStock($product->getStock() - $cartItem->getQuantity());
                $this->em->persist($product);
                $this->em->flush();
            }
            return $order;
        }
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
