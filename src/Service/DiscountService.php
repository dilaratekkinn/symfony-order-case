<?php

namespace App\Service;

use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @property-read DiscountRepository $repository
 */
class DiscountService extends BaseService
{
    private $discountRepository;
    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    public function showDiscount($products, $currentTotal): ?array
    {
        $discountCampaigns = $this->discountRepository->getActiveDiscounts();
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