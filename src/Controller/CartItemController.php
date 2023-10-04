<?php

namespace App\Controller;

use App\Helper\ApiResponse;
use App\Service\CartItemService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("api/cartItem", name="cartItem")
 */
class CartItemController extends AbstractController
{
    private $cartItemService;

    public function __construct(CartItemService $cartItemService)
    {
        $this->cartItemService = $cartItemService;
    }

    /**
     * @Route("/delete/{id}", name="app_cartItem_delete", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $this->cartItemService->removeItem($id);
        return ApiResponse::message(true, 'Item Removed From Cart with Product\'s Id ' . $id);
    }

    /**
     * @Route("/update/{id}", name="app_cartItem_update", methods={"POST"})
     */

    public function update(Request $request, $id): JsonResponse
    {
        $updatedCartItem = $this->cartItemService->updateCartItemQuantity($request->toArray(), $id);
        return ApiResponse::message(true, 'Cart Item Stock Quantity', $updatedCartItem);

    }

}
