<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<CartItem>
 *
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    /**
     * @param Cart $cart
     * @param Product $product
     * @return CartItem|null
     */
    public function getCartItemByProductAndCart(Cart $cart, Product $product): ?CartItem
    {
        return $this ->findOneBy(['cart' => $cart, 'product' => $product]);
    }

    /**
     * @param CartItem $entity
     * @param bool $flush
     * @return void
     */
    public function add(CartItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param CartItem $entity
     * @param bool $flush
     * @return void
     */
    public function remove(CartItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $id
     * @return CartItem|null
     */
    public function getCartItem($id): ?CartItem
    {
        $cartItem = $this->find($id);
        if (is_null($cartItem)) {
            throw new NotFoundHttpException('There Is No CartItem SWıth This ID!');
        }
        return $cartItem;
    }
}
