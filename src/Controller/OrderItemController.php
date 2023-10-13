<?php

namespace App\Controller;

use App\Helper\ApiResponse;
use App\Service\OrderItemService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property-read OrderItemService $service
 * @Route("api/orderItem", name="orderItem")
 */
class OrderItemController extends BaseController
{



    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'service' => OrderItemService::class
        ]);
    }
}
