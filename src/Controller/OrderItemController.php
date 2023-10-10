<?php

namespace App\Controller;

use App\Service\OrderItemService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property-read OrderItemService $service
 * @Route("api/orderItem", name="orderItem")
 */
class OrderItemController extends BaseController
{

    public function create()
    {

    }


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
