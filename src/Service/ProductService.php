<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ProductService extends BaseService
{
    /**
     * @param int $id
     * @param float $quantity
     * @return Product|null
     */
    public function checkProduct(int $id, float $quantity): ?Product
    {
        $product = $this->getEntityManager()->getRepository(Product::class)->getProductByID($id);
        if ($product->getStock() < $quantity) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        return $product;
    }


}