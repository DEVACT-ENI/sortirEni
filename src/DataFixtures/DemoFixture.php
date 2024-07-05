<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DemoFixture extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CampusRepository            $campusRepository,
        private readonly ParticipantRepository       $participantRepository,
        private readonly EtatRepository              $etatRepository,
        private readonly LieuRepository              $lieuRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadParticipants($manager);
        $this->loadSortie($manager);
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

    private function loadSortie(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $tabTitle = [
            "Sortie au bar du coin, grosse fiesta",
            "Paintball, le carnage assuré",
            "Bowling entre amis, strike en vue",
            "Randonnée en montagne, vue époustouflante",
            "Soirée karaoké, ambiance garantie",
            "Visite de musée, culture et découvertes",
            "Dégustation de vin, soirée épicurienne",
            "Escape game, mystère à résoudre",
            "Cinéma en plein air, film sous les étoiles",
            "Pique-nique au parc, détente et nature",
            "Cours de cuisine, recettes gourmandes",
            "Concert live, musique et vibrations",
            "Soirée jeux de société, fun assuré",
            "Tournoi de mini-golf, compétition amicale",
            "Balade à vélo, parcours aventure",
            "Journée à la plage, soleil et baignade",
            "Sortie au théâtre, spectacle captivant",
            "Visite de zoo, rencontre avec les animaux",
            "Patinage sur glace, glissade en folie",
            "Session de karting, adrénaline garantie",
            "Atelier de poterie, créativité en céramique",
            "Soirée disco, danse jusqu'à l'aube",
            "Tournoi de billard, précision et stratégie",
            "Sortie en montgolfière, vue panoramique",
            "Chasse au trésor, aventure et mystère",
            "Balade en bateau, détente sur l'eau",
            "Atelier de peinture, artistes en herbe",
            "Sortie à la fête foraine, fun et frissons",
            "Soirée stand-up, éclats de rire",
            "Séance de yoga, relaxation totale",
            "Tournoi de ping-pong, défis et fun",
            "Excursion en kayak, exploration aquatique",
            "Soirée salsa, rythmes latinos",
            "Visite de château, voyage dans le temps",
            "Sortie à la bibliothèque, plongée littéraire",
            "Journée pêche, tranquillité au bord de l'eau",
            "Atelier photo, capturer l'instant",
            "Séance de méditation, esprit zen",
            "Sortie à l'aquarium, immersion marine",
            "Randonnée nocturne, aventure sous les étoiles",
            "Excursion en forêt, immersion naturelle",
            "Sortie au spa, détente et bien-être",
            "Tournoi de badminton, matchs intenses",
            "Soirée jazz, ambiance feutrée",
            "Sortie au parc d'attractions, sensations fortes",
            "Atelier d'écriture, plume inspirée",
            "Excursion en VTT, parcours sportif",
            "Tournoi de tennis, échanges et points",
            "Soirée pyjama, cocooning entre amis",
            "Marathon de series",
        ];

        $tabCampus = $this->campusRepository->findAll();
        $tabParticipants = $this->participantRepository->findAll();
        $tabLieux = $this->lieuRepository->findAll();

        for ($i = 0; $i < 50; $i++) {
            $sortie = new Sortie();
            $nbParticipants = rand(0, 4);

            $sortie->setNom($tabTitle[$i]);
            $sortie->setDateLimiteInscription($faker->dateTimeBetween('-15 days', '+15 days'));
            $dateDebut = clone $sortie->getDateLimiteInscription();
            $sortie->setDateHeureDebut($dateDebut->modify('+' . $faker->numberBetween(3, 15) . ' days'));
            $sortie->setDuree($faker->numberBetween(60, 180));
            $sortie->setNbInscriptionMax($faker->numberBetween(5, 20));
            $sortie->setInfoSortie($faker->text(200));
            $sortie->setCampus($faker->randomElement($tabCampus));
            if ($i == 49)
                $sortie->setOrganisateur($tabParticipants[4]);
            else
                $sortie->setOrganisateur($faker->randomElement($tabParticipants));
            $sortie->addListInscrit($sortie->getOrganisateur());
            for ($j = 0; $j < $nbParticipants; $j++) {
                if ($tabParticipants[$j] != $sortie->getOrganisateur())
                    $sortie->addListInscrit($tabParticipants[$j]);
            }
            $sortie->setLieu($faker->randomElement($tabLieux));
            $now = new \DateTime();
            if ($sortie->getDateHeureDebut() > $now) {
                if ($sortie->getDateLimiteInscription() > $now) {
                    $sortie->setEtat($this->etatRepository->findOneBy(['code' => 'OPN']));
                } else {
                    $sortie->setEtat($this->etatRepository->findOneBy(['code' => 'CLO']));
                }
            } else {
                if (clone $sortie->getDateHeureDebut()->modify('+' . $sortie->getDuree() . ' minutes') > $now) {
                    $sortie->setEtat($this->etatRepository->findOneBy(['code' => 'ANC']));
                } else {
                    $sortie->setEtat($this->etatRepository->findOneBy(['code' => 'PAS']));
                }
            }
            $manager->persist($sortie);
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
