<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{

    public function __construct(private readonly CampusRepository $campusRepository, private readonly ParticipantRepository $participantRepository, private readonly EtatRepository $etatRepository, private readonly LieuRepository $lieuRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadSorties($manager);
    }

    private function loadSorties(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $tabCampus = $this->campusRepository->findAll();
        $tabParticipants = $this->participantRepository->findAll();
        $tabEtats = $this->etatRepository->findAll();
        $tabLieux = $this->lieuRepository->findAll();

        for ($i = 0; $i < 10; $i++) {
            $sortie = new Sortie();
            $sortie->setNom($faker->sentence(3));
            $sortie->setDateHeureDebut($faker->dateTimeBetween('-10 days', '+25 days'));
            $sortie->setDuree($faker->numberBetween(60, 360));
            $sortie->setDateLimiteInscription($faker->dateTimeBetween('-10 days', '+25 days'));
            $sortie->setNbInscriptionMax($faker->numberBetween(5, 20));
            $sortie->setInfoSortie($faker->text(200));
            $sortie->setCampus($faker->randomElement($tabCampus));
            $sortie->setOrganisateur($faker->randomElement($tabParticipants));
            $sortie->setEtat($faker->randomElement($tabEtats));
            $sortie->setLieu($faker->randomElement($tabLieux));
            $manager->persist($sortie);
        }
        $manager->flush();



    }

    public static function getGroups(): array
    {
        return ['sortie', "all"];
    }

    public function getOrder(): int
    {
        return 3;
    }


}