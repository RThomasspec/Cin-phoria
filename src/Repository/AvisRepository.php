<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }


    public function findAvisByFilm(int $filmId):array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
          'SELECT a
         FROM App\Entity\Avis a
         WHERE a.film = :film'
        )
        ->setParameter('film', $filmId);

        return $query->getResult();
    }

    public function findAvisValidebyFilm(int $filmId) :array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
             FROM App\Entity\Avis a
             WHERE a.film = :filmId
               AND a.Valide = :Valide'
        )
        ->setParameter('filmId', $filmId)
        ->setParameter('Valide', 1);

        $avis = $query->getResult();
       

        return $query->getResult();
    } 


    public function FilmGetAvis(int $filmId, int $utilisateurId): int
    {
        $entityManager = $this->getEntityManager();
    
        $query = $entityManager->createQuery(
            'SELECT COUNT(a.id) 
             FROM App\Entity\Avis a 
             WHERE a.film = :filmId 
             AND a.utilisateur = :utilisateurId'
        )
        ->setParameter('filmId', $filmId)
        ->setParameter('utilisateurId', $utilisateurId);
    
        return $query->getSingleScalarResult();
    }
    //    /**
    //     * @return Avis[] Returns an array of Avis objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Avis
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
