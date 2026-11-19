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
}
