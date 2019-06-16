<?php

namespace App\Repository;

use App\Entity\MaitreDuJeu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MaitreDuJeu|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaitreDuJeu|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaitreDuJeu[]    findAll()
 * @method MaitreDuJeu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaitreDuJeuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MaitreDuJeu::class);
    }

    // /**
    //  * @return MaitreDuJeu[] Returns an array of MaitreDuJeu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MaitreDuJeu
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
