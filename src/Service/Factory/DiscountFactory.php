<?php

namespace App\Service\Factory;

use App\Service\DiscountClass\DiscountInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DiscountFactory
{

    /**
     * @param string $className
     * @return DiscountInterface
     */
    public function create(string $className): DiscountInterface
    {
        if (!class_exists($className)) {
            throw new NotFoundHttpException('There Is No Class Type Like This');
        }

        $class = new $className();
        if (!$class instanceof DiscountInterface) {
            throw new NotFoundHttpException('This ClassType is Not Okay With DiscountInterface');
        }

        return new $className;
    }

}