<?php

namespace App\Controller;

use App\Helper\ApiResponse;
use App\Service\CartItemService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/cartItem", name="cartItem")
 */
class CartItemController extends BaseController
{
    /**
     * @Route("/create", name="app_cartItem_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $this->container->get(CartItemService::class)->addCartItemToCart($request->toArray());
        return ApiResponse::create('Sepete Atıldı');
    }

    /**
     * @Route("/delete/{id}", name="app_cartItem_delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $this->container->get(CartItemService::class)->removeItem($id);
        return ApiResponse::remove('Item Removed From Cart');
    }

    /**
     * @Route("/update/{id}", name="app_cartItem_update", methods={"POST"})
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->container->get(CartItemService::class)->updateCartItemQuantity($request->toArray(), $id);
        return ApiResponse::update('Cart Item Updated Quantity');
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CartItemService::class => CartItemService::class
        ]);
    }
}
