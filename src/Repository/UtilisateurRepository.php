<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }


    public function isMailUsed(string $email):int
    {
        $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT COUNT(u.id) 
        FROM App\Entity\Utilisateur u 
        WHERE u.mail = :email'
    )->setParameter('email', $email);

    return (int) $query->getSingleScalarResult();
    }


    public function findUserByEmail(string $email): ?Utilisateur
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\Utilisateur u
            WHERE u.mail = :email'
        )->setParameter('email', $email);

        // Retourne un utilisateur correspondant ou null s'il n'existe pas
        return $query->getOneOrNullResult();
    
        }



        
    //    /**
    //     * @return Utilisateur[] Returns an array of Utilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Utilisateur
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
