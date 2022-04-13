<?php

namespace App\Repository;

use App\Entity\TypeComptabilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeComptabilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeComptabilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeComptabilite[]    findAll()
 * @method TypeComptabilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeComptabiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeComptabilite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TypeComptabilite $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(TypeComptabilite $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TypeComptabilite[] Returns an array of TypeComptabilite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeComptabilite
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
