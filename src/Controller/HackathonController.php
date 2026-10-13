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
    #[Route('', name: 'app_hackathon_index_html', methods: ['GET'])]
    public function indexHtml(HackathonRepository $hackathonRepository): Response
    {
        $hackathons = $hackathonRepository->findAll();

        return $this->render('hackathon/index.html.twig', [
            'hackathons' => $hackathons,
        ]);
    }

    #[Route('/api', name: 'app_hackathon_index_json', methods: ['GET'])]
    public function indexJson(HackathonRepository $hackathonRepository): JsonResponse
    {
        $hackathons = $hackathonRepository->findAll();

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

        return $this->json($data, Response::HTTP_OK);
    }
}
