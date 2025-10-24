<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findAll() :array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.startingDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Renvoie touts les sorties d'un campus sauf les historisés
     * @param Campus $campus
     * @return array
     */
    public function findByCampus(Campus $campus) :array
    {

        $query = $this->createQueryBuilder('s')
            ->addSelect('etat')
            ->join('s.state', 'etat')
            ->andWhere('s.campus = :campus')
            ->andWhere('etat.nb < 6')
            ->setParameter('campus', $campus)
            ->orderBy('s.startingDate', 'DESC')
            ->getQuery();
        dump($query);
        return $query->getResult();
    }
    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
