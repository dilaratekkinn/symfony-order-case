<?php

namespace App\Service;

use App\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderItemService extends BaseService
{

    public function addOrderItemToOrder($cartItem, $order): OrderItem
    {
        if ($cartItem->getQuantity() > $cartItem->getProduct()->getStock()) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        $orderItem = new OrderItem();
        $orderItem->setBelongsToOrder($order);
        $orderItem->setProduct($cartItem->getProduct());
        $orderItem->setQuantity($cartItem->getQuantity());
        $this->getEntityManager()->getRepository(OrderItem::class)->add($orderItem);

        $cartItem->getProduct()->setStock($cartItem->getProduct()->getStock() - $cartItem->getQuantity());

        return $orderItem;
    }

}
