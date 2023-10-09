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
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->index($request->toArray());
        $serialized = $this->serializer->serialize($data, 'json', SerializationContext::create()->setGroups(['order']));
        return ApiResponse::success(json_decode($serialized, true));
    }

    /**
     * @Route("/create", name="app_order_create",methods={"POST"})
     */
    public function create(): JsonResponse
    {
        $this->service->createOrder();
        return ApiResponse::create('Order Created');
    }

    /**
     * @Route("/show/{id}", name="app_order_show",methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        $data = $this->service->showOrder($id);
        $serialized = $this->serializer->serialize($data, 'json', SerializationContext::create()->setGroups(['order']));
        return ApiResponse::success(json_decode($serialized, true));
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'service' => OrderService::class
        ]);
    }
}
