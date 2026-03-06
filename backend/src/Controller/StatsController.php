<?php

namespace App\Controller;

use App\Service\FilterFactory;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    public function __construct(private readonly FilterFactory $filterFactory, private readonly StatisticsService $statisticsService) {}

    #[Route('/api/jockeys-drivers/stats', methods: ['GET'])]
    public function jockeyStats(Request $request): JsonResponse
    {
        return $this->json($this->statisticsService->roleStats($this->filterFactory->fromRequest($request), 'jockey'));
    }

    #[Route('/api/trainers/stats', methods: ['GET'])]
    public function trainerStats(Request $request): JsonResponse
    {
        return $this->json($this->statisticsService->roleStats($this->filterFactory->fromRequest($request), 'trainer'));
    }
}
