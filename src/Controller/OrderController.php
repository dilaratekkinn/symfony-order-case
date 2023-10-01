<?php

namespace App\Controller;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Service\OrderService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("api/order", name="order_")
 */
class OrderController extends AbstractController
{
    private $orderService;
    private $successResponse;
    private $failResponse;
    private $serializer;
    private $security;

    public function __construct(
        OrderService        $orderService,
        FailResponse        $failResponse,
        SuccessResponse     $successResponse,
        SerializerInterface $serializer,
        Security            $security
    )
    {
        $this->orderService = $orderService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;
        $this->serializer = $serializer;
        $this->security = $security;
    }

    /**
     * @Route("/", name="app_order")
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderService->index($request->toArray());
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($orders, 'json', SerializationContext::create()->setGroups('order'))),
                ]
            )->setMessages(['Orders Listed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


    /**
     * @Route("/create", name="app_order_create",methods={"POST"})
     */
    public function create(): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->orderService->add()
            )->setMessages(['Order Created Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/show/{id}", name="app_order_show",methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        try {
            $order = $this->orderService->show($id);
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($order, 'json', SerializationContext::create()->setGroups(['order']))),
                ]
            )->setMessages(['Order Showed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


}
