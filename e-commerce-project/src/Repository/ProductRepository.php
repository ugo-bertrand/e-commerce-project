<?php

namespace App\Repository;

use App\Entity\Product;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getProducts(): array
    {
        return $this->createQueryBuilder('product')
            ->orderBy('product.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function updateProductById(int $id, Product $newProduct): void
    {
        $product = $this->find($id);

        if(!$product){
            throw new NotFoundException("Le produit n'existe pas");
        }

        $product->setName($newProduct->getName());
        $product->setDescription($newProduct->getDescription());
        $product->setPhoto($newProduct->getPhoto());
        $product->setPrice($newProduct->getPrice());

        $this->save($product,true);
    }

    public function getProductById(int $id): Product
    {
        $product = $this->find($id);
        if(!$product){
            throw new NotFoundException("Le produit n'existe pas");
        }
        return $product;
    }

    public function deleteProductById(int $id): void
    {
        $product = $this->find($id);
        if(!$product){
            throw new NotFoundException("Le produit n'existe pas");
        }
        $this->remove($product,true);
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
