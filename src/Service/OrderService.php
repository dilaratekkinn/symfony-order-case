<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class OrderService
{

    private $em;
    private $cartRepository;
    private $security;
    private $orderRepository;
    private $discountService;


    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        Security               $security,
        OrderRepository        $orderRepository,
        DiscountService        $discountService
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->security = $security;
        $this->orderRepository = $orderRepository;
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
                ->setParameter('user', $this->security->getUser());
        }

        return $orders
            ->getQuery()
            ->getResult();
    }

    public function add(): bool
    {
        $user = $this->security->getUser();
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            throw new \Exception('Sepetiniz Bulunmamaktadır,Order oluşturamazsınız');
        }

        $discounts = $this->discountService->showDiscount($cart->getCartItems(), CartService::getTotal($cart));


        if ($cart->getCartItems() !== null) {

            $order = new Order();
            $order->setUser($cart->getUser());
            $order->setDiscountPrice($discounts['discount'] ?? 0);
            $order->setDiscount($discounts['discountCampaign'] ?? null);
            $order->setStatus('wait');
            $order->setTotal(CartService::getTotal($cart));
            $this->em->persist($order);

            foreach ($cart->getCartItems() as $cartItem) {
                if ($cartItem->getQuantity() > $cartItem->getProduct()->getStock()) {
                    throw new \Exception('İçeride bu kadar stock bulunmamakta');
                }
                $orderItem = new OrderItem();
                $orderItem->setBelongsToOrder($order);
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());
                $this->em->persist($orderItem);
                $this->em->flush();
                $cartItem->getProduct()->setStock($cartItem->getProduct()->getStock() - $cartItem->getQuantity());
                $this->em->persist($cartItem->getProduct());
            }
            $this->em->flush();
        }
        $this->em->remove($cart);
        $this->em->flush();
        return true;
    }

    public function show($id)
    {
        $order = $this->orderRepository->findOneBy(['user' => $this->security->getUser(), 'id' => $id]);
        if (!$order) {
            throw new \Exception('No order ID');
        }
        $amount=$order->getTotal() - $order->getDiscountPrice();

        return [$order,$amount];
    }

}
