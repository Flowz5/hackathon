<?php

namespace App\Repository;

use App\Entity\Equipe;
use App\Entity\Hackathon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    /**
     * Équipes d'un hackathon (via leur projet), triées par nom.
     *
     * @return Equipe[]
     */
    public function findByHackathon(Hackathon $hackathon): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.projet', 'p')
            ->where('p.hackathon = :hackathon')
            ->setParameter('hackathon', $hackathon)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
