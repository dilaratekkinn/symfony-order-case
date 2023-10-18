<?php

namespace App\Controller;

use App\Helper\ApiResponse;
use App\Service\CartService;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property-read CartService $service
 * @Route("api/cart", name="cart_")
 */
class CartController extends BaseController
{
    /**
     * @Route(name="app_cart_show",methods={"GET"})
     */
    public function show(): JsonResponse
    {
        $data = $this->container->get(CartService::class)->showCart();
        $serialized = $this->serializer->serialize($data, 'json',
            SerializationContext::create()->setGroups(['cart'])
        );
        return ApiResponse::success(json_decode($serialized, true));
    }

    /**
     * @Route("/delete", name="app_cart_delete", methods={"DELETE"})
     */
    public function delete(): JsonResponse
    {
        $this->container->get(CartService::class)->removeCart();
        return ApiResponse::remove('Cart Deleted');
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CartService::class => CartService::class
        ]);
    }
}
