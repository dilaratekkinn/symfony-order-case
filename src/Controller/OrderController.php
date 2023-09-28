<?php

namespace App\Controller;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Service\OrderService;
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
        OrderService         $orderService,
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
     * @Route("/order", name="app_order")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/OrderController.php',
        ]);
    }


    /**
     * @Route("/create", name="app_order_create",methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->orderService->add($request->toArray())
            )->setMessages(['Order Created Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }
}
