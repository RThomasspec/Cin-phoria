<?php

namespace App\Repository;

use App\Entity\Horaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Horaire>
 */
class HoraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horaire::class);
    }

    public function findHoraireByFilm(int $filmId):array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
          'SELECT h,s
         FROM App\Entity\Horaire h
         JOIN h.seance s
         WHERE s.film = :film'
        )
        ->setParameter('film', $filmId);

        return $query->getResult();
    }

    public function findHoraireBySeance(int $seanceId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
          'SELECT h
        FROM App\Entity\Horaire h
        INNER JOIN App\Entity\Seance s WITH h.id = s.horaire
        WHERE s.id = :seanceId'
        )
        ->setParameter('seanceId', $seanceId);

        return $query->getResult();
    }

    //    /**
    //     * @return Horaire[] Returns an array of Horaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Horaire
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
