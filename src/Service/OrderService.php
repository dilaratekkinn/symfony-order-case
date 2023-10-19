<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property-read OrderRepository $repository
 */
class OrderService extends BaseService
{
    /**
     * @return array
     */
    public function index(): array
    {
        return $this->getEntityManager()->getRepository(Order::class)->listByUser($this->getUser());

    }

    /**
     * @return bool
     */
    public function createOrder(): bool
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCartByOwnerUser();

        if (is_null($cart) || count($cart->getCartItems()) == 0) {
            throw new NotFoundHttpException('Sepette ürün yok,order oluşamaz');
        }
        $discountService = $this->container->get(DiscountService::class);
        $discount = $discountService->getDiscount();
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setDiscountPrice($discount['discountTotal'] ?? 0);
        $order->setDiscount( $discount['discountClass'] ?? null);
        $order->setStatus('wait');
        $order->setTotal($cartService->getTotal($cart));
        $this->getEntityManager()->getRepository(Order::class)->add($order);

        $orderItemService = $this->container->get(OrderItemService::class);
        foreach ($cart->getCartItems() as $cartItem) {
            $orderItemService->addOrderItemToOrder($cartItem, $order);
        }
        $this->getEntityManager()->getRepository(Order::class)->flush();

        $cartService->removeCart();

        return true;
    }

    /**
     * @param int $id
     * @return array
     */
    public function showOrder(int $id): array
    {
        $order = $this->checkOrder($id);
        $orderTotalAmount = $order->getTotal() - $order->getDiscountPrice();

        return [
            'order' => $order,
            'amount' => $orderTotalAmount
        ];
    }

    /**
     * @param int $id
     * @return void
     */
    public function removeOrder(int $id) : void
    {
        $order = $this->checkOrder($id);
        if($order->getStatus() != 'wait'){
            throw new NotFoundHttpException('You can cancel order while only waiting status!');
        }
        $order->setStatus('canceled');
        $this->getEntityManager()->getRepository(Order::class)->flush();
    }

    /**
     * @param int $id
     * @return Order|null
     */
    public function checkOrder(int $id): ?Order
    {
        $order = $this->getEntityManager()->getRepository(Order::class)->findOneBy(['user' => $this->getUser(), 'id' => $id]);
        if (is_null($order)) {
            throw new NotFoundHttpException('There Is No Order With This ID!');
        }
        return $order;
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CartService::class => CartService::class,
            DiscountService::class => DiscountService::class,
            OrderItemService::class => OrderItemService::class
        ]);
    }
}
