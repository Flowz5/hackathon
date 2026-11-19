<?php

namespace App\Repository;

use App\Entity\Hackathon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hackathon>
 */
class HackathonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hackathon::class);
    }

    /**
     * Charge un hackathon avec ses inscriptions, participants et équipes
     * en une seule requête SQL (évite le problème du N+1).
     */
    public function findWithInscriptions(int $id): ?Hackathon
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.inscriptions', 'i')->addSelect('i')
            ->leftJoin('i.participant', 'p')->addSelect('p')
            ->leftJoin('i.equipe', 'e')->addSelect('e')
            ->where('h.id = :id')
            ->setParameter('id', $id)
            ->orderBy('i.num', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Hackathon[] Returns an array of Hackathon objects
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

//    public function findOneBySomeField($value): ?Hackathon
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
