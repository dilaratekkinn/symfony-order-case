<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property-read ProductRepository $repository
 */
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
        $product = $this->repository->find($id);
        if (is_null($product)) {
            throw new NotFoundHttpException('There Is No Product With This ID!');
        }
        return $product;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => ProductRepository::class,
        ]);
    }


}