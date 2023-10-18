<?php

namespace App\Controller;

use App\Helper\ApiResponse;
use App\Service\OrderService;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property-read OrderService $service
 * @Route("api/order", name="order_")
 */
class OrderController extends BaseController
{
    /**
     * @Route("/", name="app_order")
     */
    public function index(): JsonResponse
    {
        $data = $this->container->get(OrderService::class)->index();
        $serialized = $this->serializer->serialize($data, 'json', SerializationContext::create()->setGroups(['order']));
        return ApiResponse::success(json_decode($serialized, true));
    }

    /**
     * @Route("/create", name="app_order_create",methods={"POST"})
     * @throws \Exception
     */
    public function create(): JsonResponse
    {
        $this->container->get(OrderService::class)->createOrder();
        return ApiResponse::create('Order Created');
    }

    /**
     * @Route("/show/{id}", name="app_order_show",methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        $data = $this->container->get(OrderService::class)->showOrder($id);
        $serialized = $this->serializer->serialize($data, 'json', SerializationContext::create()->setGroups(['order']));
        return ApiResponse::success(json_decode($serialized, true));
    }

    public function remove(){
        //order remove ederken status delete edebilirsin
    }



    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            OrderService::class => OrderService::class
        ]);
    }
}
