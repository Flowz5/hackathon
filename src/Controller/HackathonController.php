<?php

namespace App\Controller;

use App\Repository\HackathonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/hackathons')]
class HackathonController extends AbstractController
{
    /**
     * TP 2 - Version 1 : Format HTML
     * Route accessible sur : http://localhost:8000/hackathons
     */
    #[Route('', name: 'app_hackathon_index_html', methods: ['GET'])]
    public function indexHtml(HackathonRepository $hackathonRepository): Response
    {
        // Récupération de tous les hackathons via le Repository
        $hackathons = $hackathonRepository->findAll();

        // Rendu du template Twig avec les données
        return $this->render('hackathon/index.html.twig', [
            'hackathons' => $hackathons,
        ]);
    }

    /**
     * TP 2 - Version 2 : Format JSON
     * Route accessible sur : http://localhost:8000/hackathons/api
     */
    #[Route('/api', name: 'app_hackathon_index_json', methods: ['GET'])]
    public function indexJson(HackathonRepository $hackathonRepository): JsonResponse
    {
        // Récupération de tous les hackathons
        $hackathons = $hackathonRepository->findAll();

        // Transformation en tableau associatif pour un format JSON propre
        $data = array_map(function ($h) {
            return [
                'id' => $h->getId(),
                'theme' => $h->getTheme(),
                'lieu' => $h->getLieu(),
                'ville' => $h->getVille(),
                'dateDebut' => $h->getDateHeureDebut()?->format('Y-m-d H:i:s'),
                'dateFin' => $h->getDateHeureFin()?->format('Y-m-d H:i:s'),
                'affiche' => $h->getAffiche(),
                'objectifs' => $h->getObjectifs(),
                'nbProjets' => count($h->getProjets()),
            ];
        }, $hackathons);

        // Retourne la réponse en JSON avec le code HTTP 200 OK
        return $this->json($data, Response::HTTP_OK);
    }
}
