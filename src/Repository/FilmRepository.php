<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    public function findFilmsByCinema(int $cinemaId) :array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT f
            FROM App\Entity\Film f
            JOIN f.diffusions d
            WHERE d.cinemas = :cinema'
        )->setParameter('cinema', $cinemaId);

        return $query->getResult();
    } 

    public function findFilmsAvis() :array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT f 
            FROM App\Entity\Film f 
            INNER JOIN f.avis a 
            WHERE a.Valide = :Valide'
       )->setParameter('Valide', 0);

        return $query->getResult();
    } 

public function findByFilters(?int $cinemaId = null, ?string $genre= null, ?string $jour = null)
{
    $entityManager = $this->getEntityManager();

    // Création de la requête de base
    $dql = $this->createQueryBuilder('f')
    ->innerJoin('App\Entity\Diffusion', 'd', 'WITH', 'f.id = d.films')
    ->innerJoin('App\Entity\Cinema', 'c', 'WITH', 'd.cinemas = c.id')
    ->innerJoin('App\Entity\Seance', 's', 'WITH', 'f.id = s.film')
    ->innerJoin('App\Entity\Horaire', 'h', 'WITH', 's.horaire = h.id');


    $criteria = [];

    // Ajout de conditions basées sur les paramètres
    if ($cinemaId !== null) {
        $criteria[] = 'c.id = :cinemaId';
    }
    if ($genre !== null) {
        $criteria[] = 'f.genre = :genre';
    }
    if ($jour !== null) {
        $criteria[] = 'h.jour = :jour';
    }

    // Ajout des conditions à la requête si elles existent
    if (count($criteria) > 0) {
        $dql .= ' WHERE ' . implode(' AND ', $criteria);
    }

    // Création de la requête
    $query = $entityManager->createQuery($dql);

    // Définition des paramètres
    if ($cinemaId !== null) {
        $query->setParameter('cinemaId', $cinemaId);
    }
    if ($genre !== null) {
        $query->setParameter('genre', $genre);
    }
    if ($jour !== null) {
        $query->setParameter('jour', $jour);
    }


    return $query->getResult();
}



    //    /**
    //     * @return Film[] Returns an array of Film objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Film
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
