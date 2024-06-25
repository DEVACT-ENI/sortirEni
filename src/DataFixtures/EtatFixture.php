<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Etat;
class EtatFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->loadEtats($manager);
    }

    private function loadEtats(ObjectManager $manager): void
    {
        $tabEtat = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        foreach ($tabEtat as $libelle) {
            $etat = new Etat();
            $etat->setLibelle($libelle);
            $manager->persist($etat);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['etat', "all"];
    }

    public function getOrder(): int
    {
        return 1;
    }
}
