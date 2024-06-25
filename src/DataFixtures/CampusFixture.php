<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->loadCampus($manager);
    }

    private function loadCampus(ObjectManager $manager): void
    {
        $tabCampus = ["CHARTRE-DE-BRETAGNE", "SAINT-HERBLAIN", "LA ROCHE SUR YON"];
        foreach ($tabCampus as $nomCampus) {
            $campus = new Campus();
            $campus->setNom($nomCampus);
            $manager->persist($campus);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['campus', "all"];
    }


    public function getOrder(): int
    {
        return 1;
    }
}
