<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

/**
 * @property-read OrderItemRepository $repository
 */
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
        $this->repository->add($orderItem);

        $cartItem->getProduct()->setStock($cartItem->getProduct()->getStock() - $cartItem->getQuantity());

        return $orderItem;
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => OrderItemRepository::class,
        ]);
    }
}
