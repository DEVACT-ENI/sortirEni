<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LieuFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function __construct(private readonly VilleRepository $villeRepository){}

    public function load(ObjectManager $manager): void
    {
        $this->createLieu($manager);
    }

    private function createLieu(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $tabVille = $this->villeRepository->findAll();

        for ($i = 0; $i < 10; $i++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->company());
            $lieu->setRue($faker->streetAddress());
            $lieu->setLatitude($faker->latitude());
            $lieu->setLongitude($faker->longitude());
            $lieu->setVille($faker->randomElement($tabVille));
            $manager->persist($lieu);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['lieu', "all"];
    }

    public function getOrder(): int
    {
        return 2;
    }
}
