<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use App\Exception\NotFoundException;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function save(Cart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getProducts(User $user): Collection
    {
        $cart = $this->findOneBy(["user" => $user]);
        if(!$cart){
            throw new NotFoundException("Le panier est vide");
        }
        return $cart->getProducts();
    }

    public function addProduct(User $user, Product $product): void
    {
        $cart = $this->findOneBy(["user" => $user]);
        if(!$cart){
            $cart = new Cart();
            $cart->setUser($user);
        }
        $cart->addProduct($product);
        $this->save($cart,true);
    }

    public function removeProduct(User $user, Product $product): void
    {
        $cart = $this->findOneBy(["user" => $user]);
        if(!$cart){
            throw new NotFoundException("Le panier est vide");
        }
        $cart->removeProduct($product);
        $this->save($cart,true);
    }

    public function clearCart(User $user): void
    {
        $cart = $this->findOneBy(["user" => $user]);
        if(!$cart){
            throw new NotFoundException("Le panier est déjà vide");
        }
        while($cart->getProducts()->count() > 0){
            $cart->removeProduct($cart->getProducts()->first());
        }
    }

//    /**
//     * @return Cart[] Returns an array of Cart objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cart
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
