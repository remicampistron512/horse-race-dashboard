<?php

namespace App\Controller;

use App\Repository\RaceResultRepository;
use App\Service\FilterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RaceResultController extends AbstractController
{
    #[Route('/api/race-results', methods: ['GET'])]
    public function list(Request $request, FilterFactory $filterFactory, RaceResultRepository $repository): JsonResponse
    {
        $results = $repository->findFiltered($filterFactory->fromRequest($request));
        return $this->json(array_map(fn($r) => [
            'id' => $r->getId(),
            'date' => $r->getRace()->getDate()->format('Y-m-d'),
            'racecourse' => $r->getRace()->getRacecourse()->getName(),
            'race' => $r->getRace()->getName(),
            'horse' => $r->getHorse()->getName(),
            'jockeyOrDriver' => $r->getJockeyOrDriver()->getFullName(),
            'trainer' => $r->getTrainer()->getFullName(),
            'distance' => $r->getRace()->getDistance(),
            'groundCondition' => $r->getRace()->getGroundCondition(),
            'odds' => $r->getOdds(),
            'finishPosition' => $r->getFinishPosition(),
            'earnings' => $r->getEarnings(),
            'roiSimulated' => $r->getFinishPosition() === 1 ? round(($r->getOdds() * 10 - 10) / 10, 2) : -1,
        ], $results));
    }
}
