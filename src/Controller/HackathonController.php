<?php

namespace App\Controller;

use App\Entity\Hackathon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
        // Requête unique avec JOIN (inscriptions + participants + équipes) pour éviter le N+1
        $hackathon = $entityManager->getRepository(Hackathon::class)->findWithInscriptions($id);
        if (!$hackathon) {
            throw $this->createNotFoundException('Aucun hackathon trouvé pour l\'id ' . $id);
        }

        return $this->render('hackathon/show.html.twig', ['hackathon' => $hackathon]);
    }
}
