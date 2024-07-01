<?php

namespace App\Form\Modeles;

use App\Entity\Campus;
use Symfony\Component\Validator\Constraints as Assert;

class FormFilterMainModele
{
    private ?Campus $campus = null;
    private ?string $keyword = null;
    private ?\DateTime $dateDebut = null;
    #[Assert\GreaterThan(propertyPath: 'dateDebut', message: 'La date de fin doit être supérieure à la date de début')]
    private ?\DateTime $dateFin = null;
    private ?bool $organisateur = null;
    private ?bool $inscrit = null;
    private ?bool $nonInscrit = null;
    private ?bool $sortiesPassees = null;

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTime $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    public function getNonInscrit(): ?bool
    {
        return $this->nonInscrit;
    }

    public function setNonInscrit(?bool $nonInscrit): void
    {
        $this->nonInscrit = $nonInscrit;
    }

    public function getSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }

    public function setSortiesPassees(?bool $sortiesPassees): void
    {
        $this->sortiesPassees = $sortiesPassees;
    }
}