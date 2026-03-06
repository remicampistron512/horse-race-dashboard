<?php

namespace App\Repository;

use App\Entity\JockeyOrDriver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JockeyOrDriverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JockeyOrDriver::class);
    }
}
