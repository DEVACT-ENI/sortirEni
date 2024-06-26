<?php

namespace App\Security\Voter;

use App\Entity\Sortie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BouttonPermissionVoter extends Voter
{
    public const BOUTTON_INSCRIPTION = 'PERM_BOUTTON_INSCRIPTION';
    public const BOUTTON_DESINSCRIPTION = 'PERM_BOUTTON_DESINSCRIPTION';
    public const BOUTTON_PUBLIER = 'PERM_BOUTTON_PUBLIER';
    public const BOUTTON_ANNULER = 'PERM_BOUTTON_ANNULER';
    public const BOUTTON_MODIFIER = 'PERM_BOUTTON_MODIFIER';
    public const BOUTTON_AFFICHER = 'PERM_BOUTTON_AFFICHER';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::BOUTTON_INSCRIPTION,
                self::BOUTTON_DESINSCRIPTION,
                self::BOUTTON_PUBLIER,
                self::BOUTTON_ANNULER,
                self::BOUTTON_MODIFIER,
                self::BOUTTON_AFFICHER
            ])
            && $subject instanceof Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::BOUTTON_AFFICHER => true,
            self::BOUTTON_INSCRIPTION => $this->permInscription($user, $subject),
            self::BOUTTON_DESINSCRIPTION => $this->permDesinscription($user, $subject),
            self::BOUTTON_PUBLIER => $this->permPublier($user, $subject),
            self::BOUTTON_ANNULER => $this->permAnnuler($user, $subject),
            self::BOUTTON_MODIFIER => $this->permModifier($user, $subject),
            default => false,
        };
    }

    private function permInscription(UserInterface $user, mixed $subject) : bool
    {
        if ($subject->getListInscrit()->contains($user)) {
            return false;
        }
        if ($subject->getEtat()->getLibelle() != "Ouverte") {
            return false;
        }
        if ($subject->getDateLimiteInscription() < new \DateTime()) {
            return false;
        }
        if ($subject->getOrganisateur() == $user) {
            return false;
        }
        if ($subject->getNbInscriptionMax() <= $subject->getListInscrit()->count()) {
            return false;
        }
        return true;
    }

    private function permDesinscription(UserInterface $user, mixed $subject) : bool
    {
        if (!$subject->getListInscrit()->contains($user)) {
            return false;
        }
        if ($subject->getEtat()->getLibelle() != "Ouverte") {
            return false;
        }
        if ($subject->getDateLimiteInscription() < new \DateTime()) {
            return false;
        }
        if ($subject->getOrganisateur() == $user) {
            return false;
        }
        return true;
    }

    private function permPublier(UserInterface $user, mixed $subject) : bool
    {
        if ($subject->getOrganisateur() != $user) {
            return false;
        }
        if ($subject->getEtat()->getLibelle() != "Créée") {
            return false;
        }
        return true;
    }

    private function permAnnuler(UserInterface $user, mixed $subject) : bool
    {
        if ($subject->getOrganisateur() != $user) {
            return false;
        }
        if ($subject->getEtat()->getLibelle() != "Ouverte") {
            return false;
        }
        return true;
    }

    private function permModifier(UserInterface $user, mixed $subject) : bool
    {
        if ($subject->getOrganisateur() != $user) {
            return false;
        }
        if ($subject->getEtat()->getLibelle() != "Créée") {
            return false;
        }
        return true;
    }
}
