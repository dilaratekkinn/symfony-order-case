<?php

namespace App\Service;

use App\Repository\DiscountRepository;
use App\Service\DiscountClass\BuyXGetY;
use App\Service\DiscountClass\MoreXPercentY;
use App\Service\DiscountClass\XPercentOverY;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;

/**
 * @property-read DiscountRepository $repository
 */
class DiscountService extends BaseService
{
    /**
     * @throws ClassNotFoundException
     */
    public function showDiscount(): ?array
    {
        $discountCampaignTypes = [
            XPercentOverY::class,
            BuyXGetY::class,
            MoreXPercentY::class,
        ];

        $discountTotal = 0;
        $discountReasons = [];
        foreach ($discountCampaignTypes as $discountType) {
            $discountClass = $this->container->get($discountType);
            $discount = $discountClass->calculate();
            if (!is_null($discount)) {
                $discountReasons[] = $discount;
                $discountTotal += $discount['discount'];
            }
        }

        return [
            'discountReasons' => $discountReasons,
            'discountTotal' => $discountTotal
        ];
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => DiscountRepository::class,
            XPercentOverY::class => XPercentOverY::class,
            BuyXGetY::class => BuyXGetY::class,
            MoreXPercentY::class => MoreXPercentY::class,
        ]);
    }
}