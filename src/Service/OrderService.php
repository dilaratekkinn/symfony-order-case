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

    private $em;
    private $orderRepository;
    private $discountService;
    private $orderItemService;

    public function __construct(
        EntityManagerInterface $em,
        OrderRepository        $orderRepository,
        OrderItemService       $orderItemService,
        DiscountService        $discountService
    )
    {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
        $this->orderItemService = $orderItemService;
        $this->discountService = $discountService;
    }


    public function index(array $parameters)
    {
        $defaults = [
            'pageNumber' => 1,
            'rowsPerPage' => '',
            'searchText' => '',
            'orderBy' => 'id',
            'order' => 'desc'
        ];
        $parameters = array_merge($defaults, $parameters);
        $repo = $this->em->getRepository(Order::class);
        $orders = $repo->createQueryBuilder('o');

        $orders->setMaxResults($parameters['rowsPerPage'])->setFirstResult($parameters['pageNumber'] - 1)->orderBy('o.id', 'desc');
        if (isset($parameters['user'])) {
            $orders
                ->join('o.user', 'u')
                ->andWhere('u.id = :user')
                ->setParameter('user', $this->getUser());
        }
        return $orders
            ->getQuery()
            ->getResult();
    }

    public function createOrder(): bool
    {
        $cartService = $this->container->get(CartService::class);
        $cart = $cartService->getCartByOwnerUser();

        $discounts = $this->discountService->showDiscount($cart->getCartItems(), $cartService->getTotal($cart));

        if (count($cart->getCartItems()) == 0) {
            throw new \Exception('Sepette ürün yok,order oluşamaz');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setDiscountPrice($discounts['discount'] ?? 0);
        $order->setDiscount($discounts['discountCampaign'] ?? null);
        $order->setStatus('wait');
        $order->setTotal($cartService->getTotal($cart));
        $this->repository->add($order);
        foreach ($cart->getCartItems() as $cartItem) {
            $this->orderItemService->addOrderItemToOrder($cartItem, $order);
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
            CartService::class => CartService::class
        ]);
    }
}
