<?php

namespace App\Controller;

use App\Repository\HorseRepository;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/horses')]
class HorseController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(HorseRepository $horseRepository): JsonResponse
    {
        $horses = array_map(fn($h) => ['id' => $h->getId(), 'name' => $h->getName(), 'trainer' => $h->getTrainer()->getFullName(), 'totalEarnings' => $h->getTotalEarnings()], $horseRepository->findAll());
        return $this->json($horses);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(int $id, HorseRepository $horseRepository, StatisticsService $statisticsService): JsonResponse
    {
        $horse = $horseRepository->find($id);
        if (!$horse) {
            return $this->json(['error' => 'Horse not found'], 404);
        }
        return $this->json($statisticsService->horseDetail($horse));
    }
}
