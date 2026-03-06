<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FilterParams
{
    #[Assert\Date]
    public ?string $startDate = null;
    #[Assert\Date]
    public ?string $endDate = null;
    public ?string $racecourse = null;
    public ?string $raceType = null;
    public ?string $discipline = null;
    public ?int $distanceMin = null;
    public ?int $distanceMax = null;
    public ?string $groundCondition = null;
    public ?int $trainerId = null;
    public ?int $jockeyOrDriverId = null;
    public ?int $horseId = null;
    public ?float $oddsMin = null;
    public ?float $oddsMax = null;
    public ?int $runnerCountMin = null;
    public ?int $runnerCountMax = null;
    public int $topPlaceThreshold = 3;
}
