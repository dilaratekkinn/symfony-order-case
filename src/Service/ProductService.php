<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ProductService extends BaseService
{
    public function checkProductStock(Product $product, float $quantity): ?Product
    {
        if ($product->getStock() < $quantity) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        return $product;
    }

    public function getProductByID(int $id): Product
    {
        $product = $this->getEntityManager()->getRepository(Product::class)->find($id);
        if (is_null($product)) {
            throw new NotFoundHttpException('There Is No Product With This ID!');
        }
        return $product;
    }


}