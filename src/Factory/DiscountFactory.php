<?php

namespace App\Factory;

use App\Service\DiscountClass\DiscountInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;

class DiscountFactory
{
    /**
     * @throws ClassNotFoundException
     */
    public static function create(string $className, array $settings, Collection $products, float $currentTotal): DiscountInterface
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException($className);
        }
        return new $className($settings,$products,$currentTotal);
    }
}