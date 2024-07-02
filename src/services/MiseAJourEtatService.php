<?php

namespace App\services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;

class MiseAJourEtatService
{
    public function __construct(private readonly SortieRepository $sortieRepository, private readonly EtatRepository $etatRepository){}

    public function miseAJourEtatSortie(?array $sorties): void
    {
        foreach ($sorties as $sortie) {
            $dateActuelle = new \DateTime();
            $dateActuelleLessOneMonth = clone $dateActuelle;
            $dateActuelleLessOneMonth->modify('-1 month');
            $dateLimite = $sortie->getDateLimiteInscription();
            $dateDebut = $sortie->getDateHeureDebut();
            $dateDebutDuree = clone $dateDebut;
            $dateDebutDuree->modify('+' . $sortie->getDuree() . ' minutes');
            $nbInscriptions = count($sortie->getListInscrit());
            $maxInscriptions = $sortie->getNbInscriptionMax();

            if ($sortie->getEtat()->getCode() != 'CRT' && $sortie->getEtat()->getCode() != 'ANN') {
                if ($dateDebut < $dateActuelleLessOneMonth) {
                    $etat = $this->etatRepository->findOneBy(['code' => 'HIS']);
                } else if ($dateDebutDuree < $dateActuelle) {
                    $etat = $this->etatRepository->findOneBy(['code' => 'PAS']);
                } else if ($dateDebut < $dateActuelle) {
                    $etat = $this->etatRepository->findOneBy(['code' => 'ACN']);
                } else if ($dateLimite < $dateActuelle || $nbInscriptions >= $maxInscriptions) {
                    $etat = $this->etatRepository->findOneBy(['code' => 'CLO']);
                } else {
                    $etat = $this->etatRepository->findOneBy(['code' => 'OPN']);
                }
                $sortie->setEtat($etat);
            }
            $this->sortieRepository->save($sortie);
        }
    }
}