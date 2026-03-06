<?php

namespace App\DataFixtures;

use App\Entity\Horse;
use App\Entity\JockeyOrDriver;
use App\Entity\Race;
use App\Entity\RaceResult;
use App\Entity\Racecourse;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $racecourses = [];
        foreach ([['Vincennes', 'Île-de-France', 'Sable'], ['Longchamp', 'Île-de-France', 'Herbe'], ['Deauville', 'Normandie', 'PSF'], ['Lyon-Parilly', 'Auvergne-Rhône-Alpes', 'Herbe']] as [$name, $region, $surface]) {
            $rc = (new Racecourse())->setName($name)->setRegion($region)->setSurface($surface);
            $manager->persist($rc); $racecourses[] = $rc;
        }

        $trainers = [];
        foreach ([['Jean', 'Morel'], ['Claire', 'Dubois'], ['Marc', 'Lefevre'], ['Sophie', 'Renaud']] as [$f, $l]) {
            $t = (new Trainer())->setFirstName($f)->setLastName($l); $manager->persist($t); $trainers[] = $t;
        }

        $jockeys = [];
        foreach ([['Lucas', 'Martin', 'Trot'], ['Emma', 'Petit', 'Plat'], ['Nina', 'Bernard', 'Obstacle'], ['Hugo', 'Lambert', 'Trot']] as [$f, $l, $d]) {
            $j = (new JockeyOrDriver())->setFirstName($f)->setLastName($l)->setDiscipline($d); $manager->persist($j); $jockeys[] = $j;
        }

        $horses = [];
        for ($i = 1; $i <= 24; $i++) {
            $h = (new Horse())
                ->setName('Cheval ' . $i)
                ->setAge(random_int(3, 9))
                ->setSex(['M', 'F', 'H'][array_rand(['M', 'F', 'H'])])
                ->setTrainer($trainers[array_rand($trainers)])
                ->setHabitualJockeyOrDriver($jockeys[array_rand($jockeys)])
                ->setRecentForm((string) random_int(10000, 99999))
                ->setTotalEarnings((float) random_int(15000, 280000));
            $manager->persist($h); $horses[] = $h;
        }

        for ($r = 1; $r <= 40; $r++) {
            $race = (new Race())
                ->setName('Réunion ' . random_int(1, 10) . ' - Course ' . random_int(1, 8))
                ->setDate(new \DateTimeImmutable('-' . random_int(1, 220) . ' days'))
                ->setRacecourse($racecourses[array_rand($racecourses)])
                ->setRaceType(['Handicap', 'Groupe', 'Réclamer'][array_rand(['Handicap', 'Groupe', 'Réclamer'])])
                ->setDiscipline(['Plat', 'Trot', 'Obstacle'][array_rand(['Plat', 'Trot', 'Obstacle'])])
                ->setDistance([1400, 1600, 2100, 2400, 2700][array_rand([1400,1600,2100,2400,2700])])
                ->setGroundCondition(['Souple', 'Bon', 'Lourd', 'Collant'][array_rand(['Souple', 'Bon', 'Lourd', 'Collant'])])
                ->setPrizeMoney((float) random_int(18000, 120000))
                ->setRunnerCount(random_int(8, 16));
            $manager->persist($race);

            $runners = $horses;
            shuffle($runners);
            $runners = array_slice($runners, 0, $race->getRunnerCount());
            foreach ($runners as $idx => $horse) {
                $trainer = $horse->getTrainer();
                $jockey = $horse->getHabitualJockeyOrDriver();
                $position = $idx + 1;
                $result = (new RaceResult())
                    ->setRace($race)
                    ->setHorse($horse)
                    ->setJockeyOrDriver($jockey)
                    ->setTrainer($trainer)
                    ->setOdds(round(mt_rand(120, 2500) / 100, 2))
                    ->setFinishPosition($position)
                    ->setEarnings($position <= 5 ? round($race->getPrizeMoney() * [0.45, 0.22, 0.14, 0.1, 0.09][$position - 1], 2) : 0)
                    ->setRopeNumber(random_int(1, $race->getRunnerCount()))
                    ->setWeightCarried(round(mt_rand(530, 620) / 10, 1))
                    ->setTimeRecorded('' . random_int(1, 2) . ':' . random_int(10, 59) . '.' . random_int(1, 9));
                $manager->persist($result);
            }
        }

        $manager->flush();
    }
}
