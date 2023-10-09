<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

/**
 * @property-read CartItemRepository $repository
 */
class CartItemService extends BaseService
{
    private $em;
    private $productRepository;
    private $security;
    private $cartItemRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductRepository      $productRepository,
        Security               $security
    )
    {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->security = $security;
    }

    public function addCartItemToCart($cart, $product, $quantity)
    {
        $product = $this->checkProductStock($product, $quantity);
        $cartItem = $this->getCartItemByProductId($product);
        if (!$cartItem) {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setQuantity(0);
            $cartItem->setProduct($product);
        }
        $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        $this->em->persist($cartItem);
        $this->em->flush();

        return $cartItem;
    }

    public function removeItem($id): bool
    {
        $cartItem = $this->getCartItemByProductId($id);
        $this->em->remove($cartItem);
        $this->em->flush();
        return true;
    }

    public function updateCartItemQuantity(array $parameters, $id)
    {
        $cartItem = $this->getCartItemByProductId($id);
        if ($parameters['quantity'] > $cartItem->getProduct()->getStock()) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        $cartItem->setQuantity($parameters['quantity']);
        $this->em->persist($cartItem);
        $this->em->flush();
        return $cartItem;
    }

    private function getCartItemByProductId($id)
    {
        return $this->cartItemRepository->findOneBy([
            'cart' => $this->security->getUser()->getCart(),
            'product' => $this->productRepository->find($id)
        ]);
    }

    private function checkProductStock($id, $quantity)
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        if (!$product) {
            throw new NotFoundHttpException('There Is No Product With This ID!');
        }
        if ($product->getStock() < $quantity) {
            throw new NotFoundHttpException('There Is Not Enough Stock For This Product As You Wish!');
        }
        return $product;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'repository' => CartItemRepository::class,

        ]);
    }
}