<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VilleFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->loadVilles($manager);
    }

    private function loadVilles(ObjectManager $manager): void
    {
        $i = 0;
        $tabVille = ["44300" => "NANTES", "44400" => "REZE", "44500" => "LA BAULE", "44600" => "SAINT-NAZAIRE", "44700" => "ORVAULT", "44800" => "SAINT-HERBLAIN", "44980" => "SAINTE-LUCE-SUR-LOIRE", "49000" => "ANGERS", "49100" => "ANGERS", "49240" => "AVRILLE", "49300" => "CHOLET", "49480" => "SAINT-SYLVAIN-D'ANJOU", "49500" => "SEGRE", "49610" => "MOZE-SUR-LOUET", "49770" => "LA MEIGNANNE", "49800" => "TRELAZE", "53000" => "LAVAL", "53100" => "MAYENNE", "53200" => "CHATEAU-GONTIER", "53300" => "AMBRIERES-LES-VALLEES", "53400" => "CRAON", "53500" => "ERNEE", "53600" => "EVRON", "53700" => "VILLAINES-LA-JUHEL", "53800" => "RENAZE", "53940" => "SAINT-BERTHEVIN", "54000" => "NANCY", "54100" => "NANCY", "54200" => "TOUL", "54300" => "LUNEVILLE", "54400" => "LONGWY", "54500" => "VANDOEUVRE-LES-NANCY", "54600" => "VILLERS-LES-NANCY", "54700" => "PONT-A-MOUSSON", "54800" => "JARNY", "54910" => "VALLEROY", "55000" => "BAR-LE-DUC", "55100" => "VERDUN", "55200" => "COMMERCY", "55300" => "SAINT-MIHIEL"];
        foreach ($tabVille as $codePostal => $nomVille) {
            $ville = new Ville();
            $ville->setNom($nomVille);
            $ville->setCodePostal($codePostal);
            $manager->persist($ville);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['ville', "all"];
    }

    public function getOrder(): int
    {
       return 1;
    }
}
