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
            $dateLimiteInscription = $faker->dateTimeBetween('-15 days', '+15 days');
            $sortie->setDateLimiteInscription($dateLimiteInscription);
            $dateHeureDebut = clone $dateLimiteInscription;
            $dateHeureDebut->modify('+' . $faker->numberBetween(3, 15) . ' days');
            $sortie->setDateHeureDebut($dateHeureDebut);
            $sortie->setDuree($faker->numberBetween(60, 180));
            $sortie->setNbInscriptionMax($faker->numberBetween(5, 20));
            $sortie->setInfoSortie($faker->text(200));
            $sortie->setCampus($faker->randomElement($tabCampus));
            $organisateur = $faker->randomElement($tabParticipants);
            $sortie->setOrganisateur($organisateur);
            $sortie->addListInscrit($organisateur);
            $sortie->setLieu($faker->randomElement($tabLieux));
            $now = new \DateTime();
            if ($dateHeureDebut > $now) {
                if ($dateLimiteInscription > $now) {
                    $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Ouverte']));
                } else {
                    $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Clôturée']));
                }
            } else {
                if ($dateHeureDebut->modify('+' . $sortie->getDuree() . ' minutes') > $now) {
                    $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Activité en cours']));
                } else {
                    $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Passée']));
                }
            }
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
