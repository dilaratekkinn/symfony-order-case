<?php

namespace App\Service\DiscountClass;

use App\Service\CartService;
use App\Service\DiscountService;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class MoreXPercentY implements DiscountInterface, ServiceSubscriberInterface
{
    private $cartService;
    private $discountService;

    public function __construct(ContainerInterface $container)
    {
        $this->cartService = $container->get(CartService::class);
        $this->discountService = $container->get(DiscountService::class);
    }

    /**
     * @return array|null
     */
    public function calculate(): ?array
    {
        $count = 0;
        $found = 0;
        $cartItems = $this->cartService->getCart()->getCartItems();
        $discounts = $this->discountService->repository->findBy(
            ['discountReason' => 'MORE_X_PERCENT_Y', 'status' => 1],
            ['priority' => 'ASC']
        );

        foreach ($discounts as $discount) {
            $settings = $discount->getSettings();
            foreach ($cartItems as $item) {
                foreach ($item->getProduct()->getCategory() as $category) {
                    if (in_array($category->getId(), $settings['categories'])) {
                        $count++;
                        if ($found == 0) {
                            $found = $item->getProduct()->getPrice();
                            continue;
                        }
                        if ($item->getProduct()->getPrice() < $found) {
                            $found = $item->getProduct()->getPrice();
                        }
                    }
                }
            }

            if ($count >= $settings['product_count']) {
                return [
                    'content' => $discount->getContent(),
                    'discount' => ($found / 100) * $settings['discount']
                ];
            }
        }

        return null;
    }

    public static function getSubscribedServices(): array
    {
        return [
            CartService::class => CartService::class,
            DiscountService::class => DiscountService::class
        ];
    }

}
