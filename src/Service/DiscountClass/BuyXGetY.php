<?php

namespace App\Service\DiscountClass;


use App\Service\CartService;
use App\Service\DiscountService;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class BuyXGetY implements DiscountInterface, ServiceSubscriberInterface
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
        $cartItems = $this->cartService->getCart()->getCartItems();
        $discounts = $this->discountService->repository->findBy(
            ['discountReason' => 'BUY_X_GET_Y', 'status' => 1],
            ['priority' => 'ASC']
        );

        foreach ($discounts as $discount) {
            $settings = $discount->getSettings();
            foreach ($cartItems as $item) {
                foreach ($item->getProduct()->getCategory() as $category) {
                    if (in_array($category->getId(), $settings['categories']) && $item->getQuantity() >= $settings['product_count']) {
                        return [
                            'content' => $discount->getContent(),
                            'discount' => $item->getProduct()->getPrice() * $settings['free_product_count']
                        ];
                    }
                }
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
