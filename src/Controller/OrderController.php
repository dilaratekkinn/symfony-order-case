<?php

namespace App\Controller;

use App\Helper\ApiResponse;
use App\Service\OrderService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/order", name="order_")
 */
class OrderController extends AbstractController
{
    private $orderService;
    private $serializer;

    public function __construct(
        OrderService        $orderService,
        SerializerInterface $serializer
    )
    {
        $this->orderService = $orderService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="app_order")
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->index($request->toArray());
        return ApiResponse::data([json_decode($this->serializer->serialize($orders, 'json', SerializationContext::create()->setGroups(['order'])))]);
    }


    /**
     * @Route("/create", name="app_order_create",methods={"POST"})
     */

    public function create(): JsonResponse
    {
        $order = $this->orderService->createOrder();
        return ApiResponse::message(true, 'Order Created', $order);
    }

    /**
     * @Route("/show/{id}", name="app_order_show",methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        list($order, $amount) = $this->orderService->showOrder($id);
        return ApiResponse::data([
            'order' => json_decode($this->serializer->serialize($order, 'json', SerializationContext::create()->setGroups(['order']))),
            'amount' => $amount,

        ]);

    }

}
