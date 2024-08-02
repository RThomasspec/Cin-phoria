<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Salle;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

  
    public function findSeanceByFilm(int $filmId): ?array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT seance
             FROM App\Entity\Seance seance
             WHERE seance.film = :filmId'
        )
        ->setParameter('filmId', $filmId);

        return $query->getResult();
    }


    public function findSeanceByHoraire(int $horaireId):array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
           'SELECT seance
         FROM App\Entity\Seance seance
         JOIN seance.horaire horaire
         WHERE horaire.id = :horaireId'
        )
        ->setParameter('horaireId', $horaireId);

        return $query->getResult();
    }
   


    public function findSeanceByUtilisateur(int $userId):array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT s
            FROM App\Entity\Seance s
            INNER JOIN s.reservations r
            WHERE r.utilisateur = :userId'
        )->setParameter('userId', $userId);

        return $query->getResult();
    }
    
    //    /**
    //     * @return Seance[] Returns an array of Seance objects
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

    //    public function findOneBySomeField($value): ?Seance
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
