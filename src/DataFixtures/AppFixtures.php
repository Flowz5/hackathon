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
    private const PRENOMS = [
        'Emma', 'Lucas', 'Léa', 'Hugo', 'Chloé', 'Louis', 'Manon', 'Gabriel', 'Inès', 'Arthur',
        'Camille', 'Jules', 'Sarah', 'Adam', 'Jade', 'Nathan', 'Lina', 'Raphaël', 'Zoé', 'Tom',
        'Alice', 'Noah', 'Juliette', 'Ethan', 'Louise', 'Paul', 'Anna', 'Théo', 'Clara', 'Mathis',
        'Eva', 'Sacha', 'Rose', 'Maël', 'Lola', 'Enzo', 'Ambre', 'Nolan', 'Mila', 'Yanis',
        'Agathe', 'Timéo', 'Romane', 'Axel', 'Margaux',
    ];

    private const NOMS = [
        'Martin', 'Bernard', 'Thomas', 'Petit', 'Robert', 'Richard', 'Durand', 'Dubois', 'Moreau', 'Laurent',
        'Simon', 'Michel', 'Lefebvre', 'Leroy', 'Roux', 'David', 'Bertrand', 'Morel', 'Fournier', 'Girard',
        'Bonnet', 'Dupont', 'Lambert', 'Fontaine', 'Rousseau', 'Vincent', 'Muller', 'Lefèvre', 'Faure', 'André',
        'Mercier', 'Blanc', 'Guérin', 'Boyer', 'Garnier', 'Chevalier', 'François', 'Legrand', 'Gauthier', 'Garcia',
        'Perrin', 'Robin', 'Clément', 'Morin', 'Nicolas',
    ];

    private const COMPETENCES = [
        'Développement back-end', 'Développement front-end', 'Data science', 'UX/UI design',
        'DevOps', 'Gestion de projet', 'Cybersécurité', 'Développement mobile',
    ];

    private const NOMS_EQUIPES = [
        'Les Pixels', 'Code Breakers', 'Null Pointers', 'Byte Me', 'Stack Overflowers', 'Les Débuggeurs',
        'Green Coders', 'Data Wizards', 'Les Algorithmiciens', 'Cloud Nine', 'Open Source Squad', 'Les Hackeurs Fous',
    ];

    private const HACKATHONS = [
        ['theme' => 'IA et santé',             'ville' => 'Paris',     'lieu' => 'Lycée Jean Jaurès',        'debut' => '2026-09-18 09:00', 'equipes' => [4, 3, 5],    'sansEquipe' => 2],
        ['theme' => 'Ville durable',           'ville' => 'Lyon',      'lieu' => 'La Cuisine du Web',        'debut' => '2026-10-16 09:00', 'equipes' => [3, 4, 4],    'sansEquipe' => 1],
        ['theme' => 'Cybersécurité',           'ville' => 'Nantes',    'lieu' => 'Lycée Livet',              'debut' => '2026-12-04 09:00', 'equipes' => [5, 3],       'sansEquipe' => 0],
        ['theme' => 'Accessibilité numérique', 'ville' => 'Lille',     'lieu' => 'EuraTechnologies',         'debut' => '2027-01-22 09:00', 'equipes' => [4, 4, 3, 3], 'sansEquipe' => 3],
        ['theme' => 'Agriculture connectée',   'ville' => 'Bordeaux',  'lieu' => 'Lycée Gustave Eiffel',     'debut' => '2027-02-12 09:00', 'equipes' => [],           'sansEquipe' => 5],
        ['theme' => 'Éducation et numérique',  'ville' => 'Marseille', 'lieu' => 'Lycée Saint-Exupéry',      'debut' => '2027-03-19 09:00', 'equipes' => [],           'sansEquipe' => 0],
    ];

    public function load(ObjectManager $manager): void
    {
        $organisateurs = [];
        foreach (['Hackat\'Innov', 'Région Numérique', 'Code for Good'] as $i => $nom) {
            $org = new Organisateur();
            $org->setNom($nom)
                ->setStatut($i === 1 ? 'Collectivité' : 'Association')
                ->setEmail("contact$i@orga.fr")
                ->setSiteWeb("https://orga$i.fr");
            $manager->persist($org);
            $organisateurs[] = $org;
        }

        $jurys = [];
        for ($i = 0; $i < 6; $i++) {
            $jury = new MembreJury();
            $jury->setNom(self::NOMS[44 - $i])
                ->setPrenom(self::PRENOMS[44 - $i])
                ->setEmail("jury$i@hackat-innov.fr")
                ->setTelephone(sprintf('06112233%02d', $i));
            $manager->persist($jury);
            $jurys[] = $jury;
        }

        $participants = [];
        foreach (self::PRENOMS as $i => $prenom) {
            $nom = self::NOMS[$i];
            $part = new Participant();
            $part->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail(sprintf('%s.%s%d@email.com', $this->slug($prenom), $this->slug($nom), $i))
                ->setTelephone(sprintf('06998877%02d', $i))
                ->setDateNaissance(new \DateTime(sprintf('%d-%02d-%02d', 1998 + $i % 9, 1 + $i % 12, 1 + $i % 28)))
                ->setLienPortefolio($i % 4 === 3 ? null : 'https://github.com/' . $this->slug($prenom . $nom));
            $manager->persist($part);
            $participants[] = $part;
        }

        $hackathons = [];
        foreach (self::HACKATHONS as $i => $data) {
            $debut = new \DateTime($data['debut']);
            $hack = new Hackathon();
            $hack->setTheme($data['theme'])
                ->setVille($data['ville'])
                ->setLieu($data['lieu'])
                ->setDateHeureDebut($debut)
                ->setDateHeureFin((clone $debut)->modify('+2 days 8 hours'))
                ->setAffiche("affiche-$i.png")
                ->setObjectifs("Imaginer en 48 h des solutions innovantes autour du thème « {$data['theme']} ».")
                ->addOrganisateur($organisateurs[$i % 3]);
            if ($i % 2 === 0) {
                $hack->addOrganisateur($organisateurs[($i + 1) % 3]);
            }
            $jurys[$i % 6]->addHackathon($hack);
            $jurys[($i + 1) % 6]->addHackathon($hack);
            $manager->persist($hack);
            $hackathons[] = $hack;
        }
        $manager->flush();

        $curseurParticipant = 0;
        $numEquipe = 0;
        $numInscriptionGlobal = 0;

        foreach (self::HACKATHONS as $h => $data) {
            $hack = $hackathons[$h];
            $num = 0;

            $groupes = $data['equipes'];
            if ($data['sansEquipe'] > 0) {
                $groupes[] = null;
            }

            foreach ($groupes as $taille) {
                $equipe = null;
                if ($taille !== null) {
                    $projet = new Projet();
                    $projet->setDescription(sprintf('Projet de l\'équipe %s pour le hackathon « %s ».', self::NOMS_EQUIPES[$numEquipe % 12], $data['theme']))
                        ->setRetenu($numEquipe % 3 === 0)
                        ->setHackathon($hack);
                    $manager->persist($projet);

                    $equipe = new Equipe();
                    $equipe->setNom(self::NOMS_EQUIPES[$numEquipe % 12])
                        ->setLienPrototype(sprintf('https://github.com/hackat-innov/equipe-%d', $numEquipe + 1))
                        ->setProjet($projet);
                    $manager->persist($equipe);
                    $numEquipe++;
                }

                $nbMembres = $taille ?? $data['sansEquipe'];
                for ($m = 0; $m < $nbMembres; $m++) {
                    $participant = $participants[$curseurParticipant % count($participants)];
                    $curseurParticipant++;
                    $num++;

                    $inscr = new Inscription();
                    $inscr->setHackathon($hack)
                        ->setNum($num)
                        ->setDate((clone $hack->getDateHeureDebut())->modify(sprintf('-%d days', 40 - $num * 2)))

                        ->setCompetence($numInscriptionGlobal % 7 === 6 ? null : self::COMPETENCES[$numInscriptionGlobal % 8])
                        ->setParticipant($participant)
                        ->setEquipe($equipe);
                    $manager->persist($inscr);
                    $numInscriptionGlobal++;

                    if ($equipe !== null && $m === 0) {
                        $equipe->setResponsable($participant);
                    }
                }

                if ($equipe !== null) {
                    for ($j = 0; $j < 2; $j++) {
                        $note = new Noter();
                        $note->setMembreJury($jurys[($h + $j) % 6])
                            ->setEquipe($equipe)
                            ->setNote(10 + ($numEquipe * 3 + $j * 5) % 11);
                        $manager->persist($note);
                    }
                }
            }
        }

        $manager->flush();
    }

    private function slug(string $texte): string
    {
        $texte = strtr($texte, ['é' => 'e', 'è' => 'e', 'ë' => 'e', 'ç' => 'c', 'ï' => 'i', 'É' => 'e']);

        return strtolower(preg_replace('/[^A-Za-z]/', '', $texte));
    }
}
