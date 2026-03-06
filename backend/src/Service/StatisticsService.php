<?php

namespace App\Service;

use App\Dto\FilterParams;
use App\Entity\Horse;
use App\Entity\RaceResult;
use App\Repository\RaceResultRepository;

class StatisticsService
{
    public function __construct(private readonly RaceResultRepository $raceResultRepository) {}

    public function kpis(FilterParams $filters): array
    {
        $results = $this->raceResultRepository->findFiltered($filters);
        $participations = count($results);
        $wins = count(array_filter($results, fn(RaceResult $r) => $r->getFinishPosition() === 1));
        $places = count(array_filter($results, fn(RaceResult $r) => $r->getFinishPosition() <= $filters->topPlaceThreshold));
        $totalEarnings = array_sum(array_map(fn(RaceResult $r) => $r->getEarnings(), $results));
        $avgOdds = $participations > 0 ? array_sum(array_map(fn(RaceResult $r) => $r->getOdds(), $results)) / $participations : 0;

        $stake = $participations * 10;
        $winnerReturn = array_sum(array_map(fn(RaceResult $r) => $r->getFinishPosition() === 1 ? 10 * $r->getOdds() : 0, $results));
        $placedReturn = array_sum(array_map(fn(RaceResult $r) => $r->getFinishPosition() <= $filters->topPlaceThreshold ? 10 * ($r->getOdds() * 0.35) : 0, $results));

        return [
            'totalRaces' => count(array_unique(array_map(fn(RaceResult $r) => $r->getRace()->getId(), $results))),
            'totalHorses' => count(array_unique(array_map(fn(RaceResult $r) => $r->getHorse()->getId(), $results))),
            'winRate' => $participations ? round($wins / $participations, 4) : 0,
            'placeRate' => $participations ? round($places / $participations, 4) : 0,
            'averageEarnings' => $participations ? round($totalEarnings / $participations, 2) : 0,
            'averageOdds' => round($avgOdds, 2),
            'roiWinner' => $stake ? round(($winnerReturn - $stake) / $stake, 4) : 0,
            'roiPlaced' => $stake ? round(($placedReturn - $stake) / $stake, 4) : 0,
        ];
    }

    public function performanceOverTime(FilterParams $filters): array
    {
        $results = $this->raceResultRepository->findFiltered($filters);
        $bucket = [];
        foreach ($results as $result) {
            $day = $result->getRace()->getDate()->format('Y-m-d');
            $bucket[$day] ??= ['date' => $day, 'wins' => 0, 'places' => 0, 'runs' => 0];
            $bucket[$day]['runs']++;
            if ($result->getFinishPosition() === 1) $bucket[$day]['wins']++;
            if ($result->getFinishPosition() <= $filters->topPlaceThreshold) $bucket[$day]['places']++;
        }
        ksort($bucket);
        return array_values($bucket);
    }

    public function groupByRacecourse(FilterParams $filters): array { return $this->groupBy($filters, fn($r)=>$r->getRace()->getRacecourse()->getName()); }
    public function groupByDistance(FilterParams $filters): array { return $this->groupBy($filters, fn($r)=> (string)$r->getRace()->getDistance()); }
    public function heatmap(FilterParams $filters): array {
        $results = $this->raceResultRepository->findFiltered($filters);
        $data=[];
        foreach($results as $r){$key=$r->getRace()->getRacecourse()->getName().'|'.$r->getRace()->getDistance();$data[$key]=($data[$key]??0)+($r->getFinishPosition()===1?1:0);} 
        $out=[]; foreach($data as $k=>$v){[$racecourse,$distance]=explode('|',$k);$out[]=['racecourse'=>$racecourse,'distance'=>(int)$distance,'wins'=>$v];}
        return $out;
    }
    public function oddsVsResults(FilterParams $filters): array {
        return array_map(fn(RaceResult $r)=>['odds'=>$r->getOdds(),'finishPosition'=>$r->getFinishPosition(),'horse'=>$r->getHorse()->getName()],$this->raceResultRepository->findFiltered($filters));
    }

    public function horseDetail(Horse $horse): array
    {
        $all = $this->raceResultRepository->findBy(['horse' => $horse], ['id' => 'DESC']);
        $lastTen = array_slice($all, 0, 10);
        return [
            'id' => $horse->getId(),
            'name' => $horse->getName(),
            'age' => $horse->getAge(),
            'sex' => $horse->getSex(),
            'trainer' => $horse->getTrainer()->getFullName(),
            'habitualJockeyOrDriver' => $horse->getHabitualJockeyOrDriver()->getFullName(),
            'recentForm' => $horse->getRecentForm(),
            'totalEarnings' => $horse->getTotalEarnings(),
            'formIndex' => $this->formIndex(array_slice($all, 0, 5)),
            'lastRaces' => array_map(fn(RaceResult $r) => [
                'date' => $r->getRace()->getDate()->format('Y-m-d'),
                'race' => $r->getRace()->getName(),
                'finishPosition' => $r->getFinishPosition(),
                'odds' => $r->getOdds(),
                'earnings' => $r->getEarnings(),
                'racecourse' => $r->getRace()->getRacecourse()->getName(),
                'distance' => $r->getRace()->getDistance(),
                'groundCondition' => $r->getRace()->getGroundCondition(),
            ], $lastTen),
        ];
    }

    public function roleStats(FilterParams $filters, string $role): array
    {
        $results = $this->raceResultRepository->findFiltered($filters);
        $bag = [];
        foreach ($results as $r) {
            $key = $role === 'trainer' ? $r->getTrainer()->getId() : $r->getJockeyOrDriver()->getId();
            $name = $role === 'trainer' ? $r->getTrainer()->getFullName() : $r->getJockeyOrDriver()->getFullName();
            $bag[$key] ??= ['id' => $key, 'name' => $name, 'runs' => 0, 'wins' => 0, 'places' => 0, 'earnings' => 0.0];
            $bag[$key]['runs']++; if ($r->getFinishPosition() === 1) $bag[$key]['wins']++; if ($r->getFinishPosition() <= $filters->topPlaceThreshold) $bag[$key]['places']++; $bag[$key]['earnings'] += $r->getEarnings();
        }
        return array_values(array_map(function (array $x) {
            $x['winRate'] = $x['runs'] ? round($x['wins'] / $x['runs'], 4) : 0;
            $x['placeRate'] = $x['runs'] ? round($x['places'] / $x['runs'], 4) : 0;
            $x['roi'] = $x['runs'] ? round(($x['wins'] * 12 - $x['runs'] * 10) / ($x['runs'] * 10), 4) : 0;
            return $x;
        }, $bag));
    }

    private function groupBy(FilterParams $filters, callable $grouper): array
    {
        $results = $this->raceResultRepository->findFiltered($filters);
        $groups = [];
        foreach ($results as $r) {
            $key = $grouper($r);
            $groups[$key] ??= ['label' => $key, 'runs' => 0, 'wins' => 0, 'earnings' => 0.0];
            $groups[$key]['runs']++;
            if ($r->getFinishPosition() === 1) $groups[$key]['wins']++;
            $groups[$key]['earnings'] += $r->getEarnings();
        }
        return array_values(array_map(function (array $item) {
            $item['winRate'] = $item['runs'] ? round($item['wins'] / $item['runs'], 4) : 0;
            $item['averageEarnings'] = $item['runs'] ? round($item['earnings'] / $item['runs'], 2) : 0;
            return $item;
        }, $groups));
    }

    private function formIndex(array $results): float
    {
        // Hypothèse métier: score dégressif sur les 5 dernières courses (1er=10 pts, 2e=8 ... 10e+=1)
        $points = 0;
        foreach ($results as $index => $result) {
            $base = max(1, 11 - min(10, $result->getFinishPosition()));
            $weight = 1 - ($index * 0.15);
            $points += $base * max(0.4, $weight);
        }
        return round($points, 2);
    }
}
