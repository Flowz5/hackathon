<?php

namespace App\Entity;

use App\Repository\NoterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoterRepository::class)]
class Noter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $note = null;

    #[ORM\ManyToOne(inversedBy: 'noters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MembreJury $membreJury = null;

    #[ORM\ManyToOne(inversedBy: 'noters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $Equipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getMembreJury(): ?MembreJury
    {
        return $this->membreJury;
    }

    public function setMembreJury(?MembreJury $membreJury): static
    {
        $this->membreJury = $membreJury;

        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->Equipe;
    }

    public function setEquipe(?Equipe $Equipe): static
    {
        $this->Equipe = $Equipe;

        return $this;
    }
}
