<?php

namespace App\services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;

class MiseAJourEtatService
{
    public function __construct(private readonly SortieRepository $sortieRepository, private readonly EtatRepository $etatRepository){}

    public function miseAJourEtatSortie(?array $sortie): void
    {
        $sorties = $this->sortieRepository->findAll();

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

            if ($sortie->getEtat()->getLibelle() != 'Créée') {
                if ($dateDebut < $dateActuelleLessOneMonth) {
                    $etat = $this->etatRepository->findOneBy(['libelle' => 'Historisée']);
                } else if ($dateDebutDuree < $dateActuelle) {
                    $etat = $this->etatRepository->findOneBy(['libelle' => 'Passée']);
                } else if ($dateDebut < $dateActuelle) {
                    $etat = $this->etatRepository->findOneBy(['libelle' => 'Activité en cours']);
                } else if ($dateLimite < $dateActuelle || $nbInscriptions >= $maxInscriptions) {
                    $etat = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
                } else {
                    $etat = $this->etatRepository->findOneBy(['libelle' => 'Ouverte']);
                }
            } else
                $etat = $this->etatRepository->findOneBy(['libelle' => 'Créée']);
            $sortie->setEtat($etat);
            $this->sortieRepository->save($sortie);
        }
    }
}