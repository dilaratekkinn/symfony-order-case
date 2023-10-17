<?php

namespace App\Service\DiscountClass;


use App\Service\CartService;
use App\Service\DiscountService;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class XPercentOverY implements DiscountInterface, ServiceSubscriberInterface
{
    private $cartService;
    private $discountService;

    public function __construct(ContainerInterface $container)
    {
        $this->cartService = $container->get(CartService::class);
        $this->discountService = $container->get(DiscountService::class);
    }

    public function calculate(): ?array
    {

        $cart = $this->cartService->getCart();
        $discounts = $this->discountService->repository->findBy(
            ['discountReason' => 'X_PERCENT_OVER_Y', 'status' => 1],
            ['priority' => 'ASC']
        );

        foreach ($discounts as $discount) {
            $settings = $discount->getSettings();
            $total = $this->cartService->getTotal($cart);
            if ($total >= $settings['min']) {
                return [
                    'content' => $discount->getContent(),
                    'discount' => ($total / 100) * $settings['discount']
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
