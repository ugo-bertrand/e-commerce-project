<?php

namespace App\Repository;

use App\Entity\User;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserByLogin(string $login): User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.login = :login')
            ->setParameter('login',$login)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateUserById(int $id, User $newUser): void
    {
        $user = $this->find($id);

        if(!$user){
            throw new NotFoundException("L'utilisateur n'existe pas");
        }

        $user->setLogin($newUser->getLogin());
        $user->setPassword($newUser->getPassword());
        $user->setEmail($newUser->getEmail());
        $user->setFirstname($newUser->getFirstname());
        $user->setLastname($newUser->getLastname());

        $this->getEntityManager()->flush();
    }

    public function getUserById(int $id): User
    {
        $user = $this->find($id);
        if(!$user){
            throw new NotFoundException("L'utilisateur n'existe pas");
        }
        return $user;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    
}
