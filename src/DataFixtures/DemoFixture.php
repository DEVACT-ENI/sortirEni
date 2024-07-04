<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DemoFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher,private readonly CampusRepository $campusRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadParticipants($manager);
    }

    private function loadParticipants(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $tabCampus = $this->campusRepository->findAll();

        $tabUsername = ["TrailerSwift", "SaucisseVolante", "Bazinga", "LeRoiDuSel", "sly"];
        $tabName = ["Abel", "Adam", "Yves", "Terry", "Sylvain"];
        $tabFirstname = ["Auboisdorman", "Quelquesjours", "Vapabien", "Kiki", "Tropée"];

        for ($i = 0; $i < 5; $i++) {
            $participant = new Participant();
            $participant->setUsername($tabUsername[$i]);
            $participant->setNom($tabFirstname[$i]);
            $participant->setPrenom($tabName[$i]);
            $participant->setPassword($this->passwordHasher->hashPassword($participant, 'password'));
            $participant->setMail($faker->email());
            $participant->setTelephone($faker->phoneNumber());
            $participant->setActif(true);
            $participant->setCampus($faker->randomElement($tabCampus));
            $manager->persist($participant);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ["demo"];
    }

    public function getOrder()
    {
        return 3;
    }
}
