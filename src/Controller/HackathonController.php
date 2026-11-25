<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Hackathon;
use App\Entity\Inscription;
use App\Repository\EquipeRepository;
use App\Repository\HackathonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotNull;

#[Route('/hackathons')]
class HackathonController extends AbstractController
{
    #[Route('', name: 'app_hackathon_index_html', methods: ['GET'])]
    public function indexHtml(EntityManagerInterface $entityManager): Response
    {
        $hackathons = $entityManager->getRepository(Hackathon::class)->findBy([], ['dateHeureDebut' => 'ASC']);

        return $this->render('hackathon/index.html.twig', [
            'hackathons' => $hackathons,
        ]);
    }

    #[Route('/{id}', name: 'app_hackathon_show', requirements: ['id' => '\d+'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $hackathon = $entityManager->getRepository(Hackathon::class)->findWithInscriptions($id);
        if (!$hackathon) {
            throw $this->createNotFoundException('Aucun hackathon trouvé pour l\'id ' . $id);
        }

        return $this->render('hackathon/show.html.twig', ['hackathon' => $hackathon]);
    }

    #[Route('/{id}/equipes', name: 'app_hackathon_equipes', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function equipes(int $id, HackathonRepository $hackathonRepository, EquipeRepository $equipeRepository): Response
    {
        $hackathon = $hackathonRepository->findWithInscriptions($id);
        if (!$hackathon) {
            throw $this->createNotFoundException('Aucun hackathon trouvé pour l\'id ' . $id);
        }

        return $this->render('hackathon/equipes.html.twig', [
            'hackathon' => $hackathon,
            'equipes' => $equipeRepository->findByHackathon($hackathon),
            'nbMaxMembres' => Equipe::NB_MAX_MEMBRES,
        ]);
    }

    #[Route('/{id}/affectation', name: 'app_hackathon_affectation', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function affecter(
        int $id,
        Request $request,
        HackathonRepository $hackathonRepository,
        EquipeRepository $equipeRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        // Hackathon + inscriptions + participants + équipes en 1 requête (anti N+1)
        $hackathon = $hackathonRepository->findWithInscriptions($id);
        if (!$hackathon) {
            throw $this->createNotFoundException('Aucun hackathon trouvé pour l\'id ' . $id);
        }

        // Seulement les équipes de CE hackathon (Equipe -> Projet -> Hackathon) qui ne sont pas complètes
        $listeEquipes = $equipeRepository->findDisponiblesByHackathon($hackathon, Equipe::NB_MAX_MEMBRES);

        $form = $this->createFormBuilder()
            ->add('inscription', ChoiceType::class, [
                'label' => 'Inscription',
                'choices' => $hackathon->getInscriptions()->toArray(),
                // valeur de l'<option> : num (unique dans un hackathon)
                'choice_value' => fn (?Inscription $inscription) => $inscription?->getNum(),
                // Participant n'a pas de __toString() : on construit le libellé nous-mêmes
                'choice_label' => fn (Inscription $inscription) => sprintf(
                    '%s %s (%s)',
                    $inscription->getParticipant()->getPrenom(),
                    $inscription->getParticipant()->getNom(),
                    $inscription->getEquipe()?->getNom() ?? 'sans équipe'
                ),
                'placeholder' => 'Choisir une inscription',
                'constraints' => [new NotNull()],
            ])
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'label' => 'Équipe',
                'choices' => $listeEquipes,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une équipe',
                'constraints' => [new NotNull()],
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer l’affectation',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var Inscription $inscription */
            $inscription = $data['inscription'];
            /** @var Equipe $equipe */
            $equipe = $data['equipe'];

            $inscription->setEquipe($equipe);
            $entityManager->flush();

            $this->addFlash('success', 'L’affectation a été enregistrée avec succès pour l’inscription : '
                . $inscription->getParticipant()->getPrenom() . ' ' . $inscription->getParticipant()->getNom()
                . ' et l’équipe : ' . $equipe->getNom());

            // Pattern PRG (Post/Redirect/Get) : évite la re-soumission avec F5
            return $this->redirectToRoute('app_hackathon_affectation', ['id' => $id]);
        }

        return $this->render('hackathon/affectation.html.twig', [
            'hackathon' => $hackathon,
            'form' => $form,
            'equipesDisponibles' => $listeEquipes,
        ]);
    }
}
