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
        // 1. Créer quelques Organisateurs
        $organisateurs = [];
        for ($i = 0; $i < 3; $i++) {
            $org = new Organisateur();
            $org->setNom("Orga numéro " . $i)
                ->setStatut("Association")
                ->setEmail("contact$i@orga.fr")
                ->setSiteWeb("https://orga$i.fr");
            $manager->persist($org);
            $organisateurs[] = $org;
        }

        // 2. Créer 5 Hackathons
        $hackathons = [];
        $villes = ['Paris', 'Lyon', 'Nantes', 'Lille', 'Bordeaux'];

        for ($i = 0; $i < 5; $i++) {
            $hack = new Hackathon();
            $hack->setDateHeureDebut(new \DateTime());
            $hack->setDateHeureFin((new \DateTime())->modify('+2 days'));
            $hack->setLieu('Lycée ' . ($i + 1));
            $hack->setVille($villes[$i]);
            $hack->setTheme('Thème ' . ($i + 1));
            $hack->setAffiche("affiche-$i.png");
            $hack->setObjectifs("Objectifs du hackathon numéro $i");

            // On ajoute un organisateur au pif pour tester le ManyToMany
            if (method_exists($hack, 'addOrganisateur')) {
                $hack->addOrganisateur($organisateurs[array_rand($organisateurs)]);
            }

            $manager->persist($hack);
            $hackathons[] = $hack;
        }

        // 3. Créer des Projets liés aux Hackathons
        $projets = [];
        for ($i = 0; $i < 10; $i++) {
            $projet = new Projet();
            $projet->setDescription("Description du super projet $i par vision par ordinateur.")
                ->setRetenu((bool)rand(0, 1))
                ->setHackathon($hackathons[array_rand($hackathons)]);
            $manager->persist($projet);
            $projets[] = $projet;
        }

        // 4. Créer des Jurys
        $jurys = [];
        for ($i = 0; $i < 4; $i++) {
            $jury = new MembreJury();
            $jury->setNom("JuryNom$i")
                ->setPrenom("Prenom$i")
                ->setEmail("jury$i@sante-ia.org")
                ->setTelephone("061122334$i");
            $manager->persist($jury);
            $jurys[] = $jury;
        }

        // 5. Créer des Participants
        $participants = [];
        for ($i = 0; $i < 15; $i++) {
            $part = new Participant();
            $part->setNom("DevNom$i")
                ->setPrenom("Prenom$i")
                ->setEmail("dev$i@email.com")
                ->setTelephone("069988776$i")
                // Correction ici avec les parenthèses pour PHP < 8.4
                ->setDateNaissance((new \DateTime('2000-01-01'))->modify("+$i years"))
                ->setLienPortefolio("https://github.com/dev$i");
            $manager->persist($part);
            $participants[] = $part;
        }

        // 6. Créer des Equipes
        $equipes = [];
        for ($i = 0; $i < 8; $i++) {
            $equipe = new Equipe();
            $equipe->setNom("Team $i")
                ->setLienPrototype("https://github.com/hackat-innov/team$i-proto")
                ->setProjet($projets[array_rand($projets)])
                ->setResponsable($participants[array_rand($participants)]);
            $manager->persist($equipe);
            $equipes[] = $equipe;
        }

        // 7. Inscriptions et Notes
        for ($i = 0; $i < 15; $i++) {
            // Inscription
            $inscr = new Inscription();
            // Correction ici aussi avec les parenthèses
            $inscr->setDate((new \DateTime('2026-10-01'))->modify("+$i days"))
                ->setCompetence("Compétence $i")
                ->setHackathon($hackathons[array_rand($hackathons)])
                ->setParticipant($participants[$i])
                ->setEquipe($equipes[array_rand($equipes)]);
            $manager->persist($inscr);

            // Note au pif pour quelques équipes
            if ($i % 2 === 0) {
                $note = new Noter();
                $note->setMembreJury($jurys[array_rand($jurys)])
                    ->setEquipe($equipes[array_rand($equipes)])
                    ->setNote(rand(10, 20));
                $manager->persist($note);
            }
        }

        $manager->flush();
    }
}
