<?php

namespace Enigma972\UserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Enigma972\UserBundle\Entity\ResetPasswordCode;

/**
 * @method ResetPasswordCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPasswordCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetPasswordCode[]    findAll()
 * @method ResetPasswordCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetPasswordCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordCode::class);
    }

    // /**
    //  * @return ResetPasswordCode[] Returns an array of ResetPasswordCode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResetPasswordCode
    {
        return $this->createQueryBuilder('r')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
