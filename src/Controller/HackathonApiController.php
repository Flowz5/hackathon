<?php

namespace App\Controller;

use App\Entity\Hackathon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/hackathons/api')]
class HackathonApiController extends AbstractController
{
    #[Route('', name: 'app_hackathon_index_json', methods: ['GET'])]
    public function indexJson(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $hackathons = $entityManager->getRepository(Hackathon::class)->findAll();
        $jsonContent = $serializer->serialize($hackathons, 'json', ['groups' => 'hackathon:read']);

        return JsonResponse::fromJsonString($jsonContent);
    }

    #[Route('/{id}', name: 'app_hackathon_show_json', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showJson(EntityManagerInterface $entityManager, int $id, SerializerInterface $serializer): JsonResponse
    {
        $hackathon = $entityManager->getRepository(Hackathon::class)->find($id);
        if (!$hackathon) {
            throw $this->createNotFoundException('Aucun hackathon trouvé pour l\'id ' . $id);
        }

        $jsonContent = $serializer->serialize($hackathon, 'json', ['groups' => 'hackathon:read']);

        return JsonResponse::fromJsonString($jsonContent);
    }

    #[Route('', name: 'app_hackathon_create_json', methods: ['POST'])]
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
