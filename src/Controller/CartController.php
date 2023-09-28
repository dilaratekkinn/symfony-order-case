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


    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CartController.php',
        ]);
    }


    /**
     * @Route("/create", name="app_cart",methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->cartService->add($request->toArray())
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
    public function show(): JsonResponse
    {
        try {
            $cart = $this->cartService->show($this->security->getUser());
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($cart, 'json', SerializationContext::create()->setGroups(['cart']))),
                ]
            )->setMessages([ 'Your Cart Showed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }
}