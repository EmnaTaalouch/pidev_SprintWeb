<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findEventByNameDQL($nom)
    {
        $Query=$this->getEntityManager()
            ->createQuery("select a from App\Entity\Event a where a.nom_event LIKE :nom or a.event_theme LIKE :nom or a.event_status LIKE :nom  ")
            ->setParameter('nom','%'.$nom.'%');
        return $Query->getResult();
    }

    public function findEventByNameAcceptedDQL($nom)
    {
        $Query=$this->getEntityManager()
            ->createQuery("select a from App\Entity\Event a where ( a.nom_event LIKE :nom or a.event_theme LIKE :nom or a.event_status LIKE :nom ) and a.demande_status = 'DemandeAccepted'  ")
            ->setParameter('nom','%'.$nom.'%');
        return $Query->getResult();
    }

    public function sortEventbyDateASCDQL()
    {
        $Query=$this->getEntityManager()
            ->createQuery("select a from App\Entity\Event a where a.demande_status = 'DemandeAccepted'  and a.event_status = 'publique' order by a.date_debut ASC ");
        return $Query->getResult();
    }
    public function sortEventbyDateDESCDQL()
    {
        $Query=$this->getEntityManager()
            ->createQuery("select a from App\Entity\Event a where a.demande_status = 'DemandeAccepted'  and a.event_status = 'publique' order by a.date_debut DESC ");
        return $Query->getResult();
    }
    

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Event $entity, bool $flush = true): void
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
    public function remove(Event $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
