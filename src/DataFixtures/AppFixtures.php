<?php

namespace App\DataFixtures;

use App\Entity\Equipe;
use App\Entity\Hackathon;
use App\Entity\Inscription;
use App\Entity\MembreJury;
use App\Entity\Noter;
use App\Entity\Organisateur;
use App\Entity\Participant;
use App\Entity\Projet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $org1 = new Organisateur();
        $org1->setNom("Hackat'Innov France")
            ->setStatut("Association")
            ->setEmail("contact@hackatinnov.fr")
            ->setSiteWeb("https://hackatinnov.fr");
        $manager->persist($org1);

        $hackathon = new Hackathon();
        $hackathon->setTheme("L'IA au service de la Santé")
            ->setLieu("Campus Numérique")
            ->setVille("Nantes")
            ->setDateHeureDebut(new \DateTime('2026-11-15 09:00:00'))
            ->setDateHeureFin(new \DateTime('2026-11-17 18:00:00'))
            ->setAffiche("affiche-ia-sante.png")
            ->setObjectifs("Développer des prototypes innovants pour assister le diagnostic médical.");

        if (method_exists($hackathon, 'addOrganisateur')) {
            $hackathon->addOrganisateur($org1);
        }
        $manager->persist($hackathon);

        $projet1 = new Projet();
        $projet1->setDescription("Application mobile de détection précoce des mélanomes par vision par ordinateur.")
            ->setRetenu(true)
            ->setHackathon($hackathon);
        $manager->persist($projet1);

        $jury1 = new MembreJury();
        $jury1->setNom("Dupont")
            ->setPrenom("Valérie")
            ->setEmail("valerie.dupont@sante-ia.org")
            ->setTelephone("0611223344");
        $manager->persist($jury1);

        $part1 = new Participant();
        $part1->setNom("Martin")
            ->setPrenom("Lucas")
            ->setEmail("lucas.martin@email.com")
            ->setTelephone("0699887766")
            ->setDateNaissance(new \DateTime('2002-04-12'))
            ->setLienPortefolio("https://github.com/l-martin");
        $manager->persist($part1);

        $part2 = new Participant();
        $part2->setNom("Durand")
            ->setPrenom("Emma")
            ->setEmail("emma.durand@email.com")
            ->setTelephone("0655443322")
            ->setDateNaissance(new \DateTime('2001-09-20'))
            ->setLienPortefolio("https://github.com/emma-d");
        $manager->persist($part2);

        $equipe1 = new Equipe();
        $equipe1->setNom("DeepHealth")
            ->setLienPrototype("https://github.com/hackat-innov/deephealth-proto")
            ->setProjet($projet1)
            ->setResponsable($part1);
        $manager->persist($equipe1);

        $inscr1 = new Inscription();
        $inscr1->setDate(new \DateTime('2026-10-01 14:30:00'))
            ->setCompetence("Développement Symfony / Python")
            ->setHackathon($hackathon)
            ->setParticipant($part1)
            ->setEquipe($equipe1);
        $manager->persist($inscr1);

        $inscr2 = new Inscription();
        $inscr2->setDate(new \DateTime('2026-10-02 10:15:00'))
            ->setCompetence("Data Science & Computer Vision")
            ->setHackathon($hackathon)
            ->setParticipant($part2)
            ->setEquipe($equipe1);
        $manager->persist($inscr2);

        $note = new Noter();
        $note->setMembreJury($jury1)
            ->setEquipe($equipe1)
            ->setNote(17.5);
        $manager->persist($note);

        $manager->flush();
    }
}
