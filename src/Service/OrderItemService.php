<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class OrderItemService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addOrderItemToOrder($cartItem, $order): OrderItem
    {
        if ($cartItem->getQuantity() > $cartItem->getProduct()->getStock()) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        $orderItem = new OrderItem();
        $orderItem->setBelongsToOrder($order);
        $orderItem->setProduct($cartItem->getProduct());
        $orderItem->setQuantity($cartItem->getQuantity());
        $this->em->persist($orderItem);
        $this->em->flush();
        $cartItem->getProduct()->setStock($cartItem->getProduct()->getStock() - $cartItem->getQuantity());
        $this->em->persist($cartItem->getProduct());

        return $orderItem;
    }


}
