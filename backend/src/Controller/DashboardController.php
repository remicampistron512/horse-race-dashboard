<?php

namespace App\Controller;

use App\Service\FilterFactory;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/dashboard')]
class DashboardController extends AbstractController
{
    public function __construct(private readonly FilterFactory $filterFactory, private readonly StatisticsService $statisticsService) {}

    #[Route('/kpis', methods: ['GET'])]
    public function kpis(Request $request): JsonResponse { return $this->json($this->statisticsService->kpis($this->filterFactory->fromRequest($request))); }

    #[Route('/performance-over-time', methods: ['GET'])]
    public function performanceOverTime(Request $request): JsonResponse { return $this->json($this->statisticsService->performanceOverTime($this->filterFactory->fromRequest($request))); }

    #[Route('/by-racecourse', methods: ['GET'])]
    public function byRacecourse(Request $request): JsonResponse { return $this->json($this->statisticsService->groupByRacecourse($this->filterFactory->fromRequest($request))); }

    #[Route('/by-distance', methods: ['GET'])]
    public function byDistance(Request $request): JsonResponse { return $this->json($this->statisticsService->groupByDistance($this->filterFactory->fromRequest($request))); }

    #[Route('/heatmap', methods: ['GET'])]
    public function heatmap(Request $request): JsonResponse { return $this->json($this->statisticsService->heatmap($this->filterFactory->fromRequest($request))); }

    #[Route('/odds-vs-results', methods: ['GET'])]
    public function oddsVsResults(Request $request): JsonResponse { return $this->json($this->statisticsService->oddsVsResults($this->filterFactory->fromRequest($request))); }
}
