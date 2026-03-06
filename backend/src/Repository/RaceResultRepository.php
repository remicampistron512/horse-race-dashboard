<?php

namespace App\Repository;

use App\Dto\FilterParams;
use App\Entity\RaceResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RaceResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaceResult::class);
    }

    public function findFiltered(FilterParams $filters): array
    {
        $qb = $this->createQueryBuilder('rr')
            ->join('rr.race', 'r')->addSelect('r')
            ->join('rr.horse', 'h')->addSelect('h')
            ->join('rr.jockeyOrDriver', 'j')->addSelect('j')
            ->join('rr.trainer', 't')->addSelect('t')
            ->join('r.racecourse', 'rc')->addSelect('rc');

        if ($filters->startDate) $qb->andWhere('r.date >= :start')->setParameter('start', new \DateTimeImmutable($filters->startDate));
        if ($filters->endDate) $qb->andWhere('r.date <= :end')->setParameter('end', new \DateTimeImmutable($filters->endDate));
        if ($filters->racecourse) $qb->andWhere('rc.name = :racecourse')->setParameter('racecourse', $filters->racecourse);
        if ($filters->raceType) $qb->andWhere('r.raceType = :raceType')->setParameter('raceType', $filters->raceType);
        if ($filters->discipline) $qb->andWhere('r.discipline = :discipline')->setParameter('discipline', $filters->discipline);
        if ($filters->distanceMin) $qb->andWhere('r.distance >= :distanceMin')->setParameter('distanceMin', $filters->distanceMin);
        if ($filters->distanceMax) $qb->andWhere('r.distance <= :distanceMax')->setParameter('distanceMax', $filters->distanceMax);
        if ($filters->groundCondition) $qb->andWhere('r.groundCondition = :groundCondition')->setParameter('groundCondition', $filters->groundCondition);
        if ($filters->trainerId) $qb->andWhere('t.id = :trainerId')->setParameter('trainerId', $filters->trainerId);
        if ($filters->jockeyOrDriverId) $qb->andWhere('j.id = :jockeyId')->setParameter('jockeyId', $filters->jockeyOrDriverId);
        if ($filters->horseId) $qb->andWhere('h.id = :horseId')->setParameter('horseId', $filters->horseId);
        if ($filters->oddsMin) $qb->andWhere('rr.odds >= :oddsMin')->setParameter('oddsMin', $filters->oddsMin);
        if ($filters->oddsMax) $qb->andWhere('rr.odds <= :oddsMax')->setParameter('oddsMax', $filters->oddsMax);
        if ($filters->runnerCountMin) $qb->andWhere('r.runnerCount >= :runnerCountMin')->setParameter('runnerCountMin', $filters->runnerCountMin);
        if ($filters->runnerCountMax) $qb->andWhere('r.runnerCount <= :runnerCountMax')->setParameter('runnerCountMax', $filters->runnerCountMax);

        return $qb->orderBy('r.date', 'DESC')->getQuery()->getResult();
    }
}
