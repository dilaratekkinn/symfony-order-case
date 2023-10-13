<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property-read OrderRepository $repository
 */
class OrderService extends BaseService
{
    public function index(): array
    {
        return $this->repository->listByUser($this->getUser());

    }

    public function createOrder(): bool
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCartByOwnerUser();
        if (count($cart->getCartItems()) == 0) {
            throw new NotFoundHttpException('Sepette ürün yok,order oluşamaz');
        }
        $discountService = $this->container->get(DiscountService::class);
        $discount = $discountService->showDiscount($cart->getCartItems(), $cartService->getTotal($cart));
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setDiscountPrice($discount['discount'] ?? 0);
        $order->setDiscount($discount['discountCampaign'] ?? null);
        $order->setStatus('wait');
        $order->setTotal($cartService->getTotal($cart));
        $this->repository->add($order);

        $orderItemService = $this->container->get(OrderItemService::class);
        foreach ($cart->getCartItems() as $cartItem) {
            $orderItemService->addOrderItemToOrder($cartItem, $order);
        }
        $cartService->removeCart();
        $this->repository->flush();
        return true;
    }


    public function showOrder(int $id, bool $throw = false): array
    {
        $order = $this->repository->findOneBy(['user' => $this->getUser(), 'id' => $id]);
        if ($throw) {
            throw new NotFoundHttpException('There Is No Order With This ID!');
        }
        $orderTotalAmount = $order->getTotal() - $order->getDiscountPrice();

        return [
            'order' => $order,
            'amount' => $orderTotalAmount
        ];
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => OrderRepository::class,
            CartService::class => CartService::class,
            DiscountService::class => DiscountService::class,
            OrderItemService::class => OrderItemService::class
        ]);
    }
}
