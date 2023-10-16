<?php

namespace App\Service;

use App\Factory\DiscountFactory;
use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;

/**
 * @property-read DiscountRepository $repository
 */
class DiscountService extends BaseService
{
    /**
     * @throws ClassNotFoundException
     */
    public function showDiscount(Collection $products, float $currentTotal): ?array
    {
        $discountCampaigns = $this->repository->getActiveDiscounts();
        foreach ($discountCampaigns as $discountCampaign) {
            $discountClass = DiscountFactory::create($discountCampaign->getClassName(), $discountCampaign->getSettings(), $products, $currentTotal);
            $discount = $discountClass->calculate();
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