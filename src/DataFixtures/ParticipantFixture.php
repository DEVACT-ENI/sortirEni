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

class ParticipantFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly CampusRepository $campusRepository)
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

        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setUsername($faker->userName());
            $participant->setNom($faker->lastName());
            $participant->setPrenom($faker->firstName());
            $participant->setPassword($this->passwordHasher->hashPassword($participant, 'password'));
            $participant->setMail($faker->email());
            $participant->setTelephone($faker->phoneNumber());
            $participant->setActif($faker->boolean(80));
            $participant->setCampus($faker->randomElement($tabCampus));
            $manager->persist($participant);
        }
        $manager->flush();

    }

    public static function getGroups(): array
    {
        return ['participant', "all"];
    }

    public function getOrder(): int
    {
        return 2;
    }
}
