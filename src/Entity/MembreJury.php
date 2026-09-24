<?php

namespace App\Entity;

use App\Repository\MembreJuryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreJuryRepository::class)]
class MembreJury extends Membre
{
    /**
     * @var Collection<int, Hackathon>
     */
    #[ORM\ManyToMany(targetEntity: Hackathon::class, mappedBy: 'membresJury')]
    private Collection $hackathons;

    /**
     * @var Collection<int, Noter>
     */
    #[ORM\OneToMany(targetEntity: Noter::class, mappedBy: 'membreJury')]
    private Collection $noters;

    public function __construct()
    {
        $this->hackathons = new ArrayCollection();
        $this->noters = new ArrayCollection();
    }

    /**
     * @return Collection<int, Hackathon>
     */
    public function getHackathons(): Collection
    {
        return $this->hackathons;
    }

    public function addHackathon(Hackathon $hackathon): static
    {
        if (!$this->hackathons->contains($hackathon)) {
            $this->hackathons->add($hackathon);
            $hackathon->addMembresJury($this);
        }

        return $this;
    }

    public function removeHackathon(Hackathon $hackathon): static
    {
        if ($this->hackathons->removeElement($hackathon)) {
            $hackathon->removeMembresJury($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Noter>
     */
    public function getNoters(): Collection
    {
        return $this->noters;
    }

    public function addNoter(Noter $noter): static
    {
        if (!$this->noters->contains($noter)) {
            $this->noters->add($noter);
            $noter->setMembreJury($this);
        }

        return $this;
    }

    public function removeNoter(Noter $noter): static
    {
        if ($this->noters->removeElement($noter)) {
            // set the owning side to null (unless already changed)
            if ($noter->getMembreJury() === $this) {
                $noter->setMembreJury(null);
            }
        }

        return $this;
    }
}
