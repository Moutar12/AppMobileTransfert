<?php

namespace App\Repository;

use App\Entity\ResumeTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResumeTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResumeTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResumeTransaction[]    findAll()
 * @method ResumeTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResumeTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResumeTransaction::class);
    }

    // /**
    //  * @return ResumeTransaction[] Returns an array of ResumeTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResumeTransaction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
