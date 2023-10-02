<?php

namespace App\Controller;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Service\CartService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("api/cart", name="cart_")
 */
class CartController extends AbstractController
{
    private $cartService;
    private $successResponse;
    private $failResponse;
    private $serializer;
    private $security;


    public function __construct(
        CartService         $cartService,
        FailResponse        $failResponse,
        SuccessResponse     $successResponse,
        SerializerInterface $serializer,
        Security            $security
    )
    {
        $this->cartService = $cartService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;
        $this->serializer = $serializer;
        $this->security = $security;
    }


    /**
     * @Route("/create", name="app_cart",methods={"POST"})
     */
    public function createCart(Request $request): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->cartService->addCartItemToCart($request->toArray())
            )->setMessages(['Cart Created Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/", name="app_cart_show",methods={"GET"})
     */
    public function showCart(): JsonResponse
    {
        try {
            list($cart, $total, $discount) = $this->cartService->showCart($this->security->getUser());
            return $this->successResponse->setData([
                    'cart' => json_decode($this->serializer->serialize($cart, 'json', SerializationContext::create()->setGroups(['cart']))),
                    'total' => $total,
                    'discount' => $discount
                ]
            )->setMessages(['Your Cart Showed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/remove_item/{id}", name="app_cartItem_delete", methods={"DELETE"})
     */
    public function removeCartItem($id): JsonResponse
    {
        try {
            $this->cartService->removeItem($id);
            return $this->successResponse->setMessages(['Item Removed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }
    /**
     * @Route("/delete/{id}", name="app_cart_delete", methods={"DELETE"})
     */
    public function removeCart($id): JsonResponse
    {
        try {
            $this->cartService->removeCart($id);
            return $this->successResponse->setMessages(['removeCart Removed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


    /**
     * @Route("/update/{id}", name="app_cartItem_update", methods={"POST"})
     */

    public function updateCartItem(Request $request, $id): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->cartService->updateCartItemQuantity($request->toArray(), $id)
            )->setMessages(['Item Stock Updated'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


}
