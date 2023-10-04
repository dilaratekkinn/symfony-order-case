<?php

namespace App\Controller;

use App\Helper\ApiResponse;
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
    private $serializer;
    private $security;


    public function __construct(
        CartService         $cartService,
        SerializerInterface $serializer,
        Security            $security
    )
    {
        $this->cartService = $cartService;
        $this->serializer = $serializer;
        $this->security = $security;
    }

    /**
     * @Route("/create", name="app_cart",methods={"POST"})
     */

    public function create(Request $request): JsonResponse
    {
        $cart = $this->cartService->createCart($request->toArray());
        return ApiResponse::message(true, 'Card Created', $cart);
    }

    /**
     * @Route("/", name="app_cart_show",methods={"GET"})
     */
    public function show(): JsonResponse
    {
        list($cart, $total, $discount, $changed) = $this->cartService->showCart($this->security->getUser());
        return ApiResponse::data([
            'cart' => json_decode($this->serializer->serialize($cart, 'json', SerializationContext::create()->setGroups(['cart']))),
            'total' => $total,
            'discount' => $discount,
            'changed' => $changed
        ]);

    }

    /**
     * @Route("/delete/{id}", name="app_cart_delete", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $this->cartService->removeCart($id);
        return ApiResponse::message(true, 'Cart Deleted with Cart\'s Id ' . $id);

    }



}
