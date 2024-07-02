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
    private ?string $choiceValue = null;
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

    public function getChoiceValue(): ?string
    {
        return $this->choiceValue;
    }

    public function setChoiceValue(?string $choiceValue): void
    {
        $this->choiceValue = $choiceValue;
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