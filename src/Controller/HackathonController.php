<?php

namespace App\Controller;

use App\Entity\Hackathon;
use App\Repository\HackathonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

// set to "/hackathons" later
#[Route('/')]
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
    public function indexJson(HackathonRepository $hackathonRepository, SerializerInterface $serializer): JsonResponse
    {
        $hackathons = $hackathonRepository->findAll();
        $jsonContent = $serializer->serialize($hackathons, 'json', ['groups' => 'hackathon:read']);

        return JsonResponse::fromJsonString($jsonContent);
    }

    #[Route('/api/{id}', name: 'app_hackathon_show_json', methods: ['GET'])]
    public function showJson(HackathonRepository $hackathonRepository, int $id, SerializerInterface $serializer): JsonResponse
    {
        $hackathon = $hackathonRepository->find($id);
        if (!$hackathon) {
            throw $this->createNotFoundException('Aucun hackathon trouvé pour l\'id ' . $id);
        }

        $jsonContent = $serializer->serialize($hackathon, 'json', ['groups' => 'hackathon:read']);

        return JsonResponse::fromJsonString($jsonContent);
    }

    #[Route('/api', name: 'app_hackathon_create_json', methods: ['POST'])]
    public function createJson(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $hackathon = $serializer->deserialize($jsonData, Hackathon::class, 'json');

        $entityManager->persist($hackathon);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($hackathon, 'json', ['groups' => 'hackathon:read']);

        return JsonResponse::fromJsonString($jsonContent, Response::HTTP_CREATED);
    }
}
