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
     * Équipes d'un hackathon (via leur projet) avec leurs membres et leur responsable,
     * en une seule requête (évite le N+1).
     *
     * @return Equipe[]
     */
    public function findByHackathon(Hackathon $hackathon): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.projet', 'p')
            ->leftJoin('e.inscriptions', 'i')->addSelect('i')
            ->leftJoin('i.participant', 'pa')->addSelect('pa')
            ->leftJoin('e.responsable', 'r')->addSelect('r')
            ->where('p.hackathon = :hackathon')
            ->setParameter('hackathon', $hackathon)
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('i.num', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Équipes du hackathon qui ont encore de la place (moins de $nbMax membres).
     *
     * @return Equipe[]
     */
    public function findDisponiblesByHackathon(Hackathon $hackathon, int $nbMax): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.projet', 'p')
            ->leftJoin('e.inscriptions', 'i')
            ->where('p.hackathon = :hackathon')
            ->groupBy('e.id')
            // COUNT(i.num) : Inscription a une clé composée ; les NULL du leftJoin ne sont pas comptés
            ->having('COUNT(i.num) < :nbMax')
            ->setParameter('hackathon', $hackathon)
            ->setParameter('nbMax', $nbMax)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
