<?php

namespace App\Repository;

use App\Entity\PersonnageJoueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PersonnageJoueur|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonnageJoueur|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonnageJoueur[]    findAll()
 * @method PersonnageJoueur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnageJoueurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PersonnageJoueur::class);
    }

    // /**
    //  * @return PersonnageJoueur[] Returns an array of PersonnageJoueur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PersonnageJoueur
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
