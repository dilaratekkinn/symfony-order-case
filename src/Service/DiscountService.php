<?php

namespace App\Service;

use App\Repository\DiscountRepository;

/**
 * @property-read DiscountRepository $repository
 */
class DiscountService extends BaseService
{
    public function showDiscount($products, $currentTotal): ?array
    {
        $discountCampaigns = $this->repository->getActiveDiscounts();
        foreach ($discountCampaigns as $discountCampaign) {
            $class = '\\App\\DiscountClasses\\' . $discountCampaign->getClassName();
            $class = new $class($discountCampaign->getSettings(), $products, $currentTotal);
            $discount = $class->calculate();
            if ($discount <= 0) {
                continue;
            }

            return [
                'discountCampaign' => $discountCampaign,
                'discount' => $discount
            ];
        }
        return null;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => DiscountRepository::class,
            DiscountService::class => DiscountService::class,
            CartItemService::class => CartItemService::class
        ]);
    }
}