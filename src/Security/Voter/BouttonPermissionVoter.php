<?php

namespace App\Security\Voter;

use App\Entity\Sortie;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BouttonPermissionVoter extends Voter
{
    public const BOUTTON_CONNECTION_ALL = 'PERM_BOUTTON_USER_CONNECTED';
    public const BOUTTON_INSCRIPTION = 'PERM_BOUTTON_INSCRIPTION';
    public const BOUTTON_DESINSCRIPTION = 'PERM_BOUTTON_DESINSCRIPTION';
    public const BOUTTON_PUBLIER = 'PERM_BOUTTON_PUBLIER';
    public const BOUTTON_ANNULER = 'PERM_BOUTTON_ANNULER';
    public const BOUTTON_MODIFIER = 'PERM_BOUTTON_MODIFIER';
    public const BOUTTON_AFFICHER = 'PERM_BOUTTON_AFFICHER';
    public const BOUTTON_SUPPRIMER = 'PERM_BOUTTON_SUPPRIMER';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::BOUTTON_INSCRIPTION,
                self::BOUTTON_DESINSCRIPTION,
                self::BOUTTON_PUBLIER,
                self::BOUTTON_ANNULER,
                self::BOUTTON_MODIFIER,
                self::BOUTTON_AFFICHER,
                self::BOUTTON_SUPPRIMER,
                self::BOUTTON_CONNECTION_ALL
            ])
            && ($subject instanceof Sortie || $subject == null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::BOUTTON_AFFICHER, self::BOUTTON_CONNECTION_ALL => true,
            self::BOUTTON_INSCRIPTION => $this->permInscription($user, $subject),
            self::BOUTTON_DESINSCRIPTION => $this->permDesinscription($user, $subject),
            self::BOUTTON_PUBLIER => $this->permPublier($user, $subject),
            self::BOUTTON_ANNULER => $this->permAnnuler($user, $subject),
            self::BOUTTON_MODIFIER => $this->permModifier($user, $subject),
            self::BOUTTON_SUPPRIMER => $this->permSupprimer($user, $subject),
            default => false,
        };
    }

    private function permInscription(UserInterface $user, mixed $subject) : bool
    {
        $result = true;

        if ($subject->getListInscrit()->contains($user))
            $result = false;
        if ($subject->getEtat()->getCode() != "OPN")
            $result = false;
        if ($subject->getDateLimiteInscription() < new \DateTime())
            $result = false;
        if ($subject->getOrganisateur() == $user)
            $result = false;
        if ($subject->getNbInscriptionMax() <= $subject->getListInscrit()->count())
            $result = false;

        return $result;
    }

    private function permDesinscription(UserInterface $user, mixed $subject) : bool
    {
        $result = true;

        if (!$subject->getListInscrit()->contains($user))
            $result = false;
        if ($subject->getEtat()->getCode() != "OPN")
            $result = false;
        if ($subject->getDateLimiteInscription() < new \DateTime())
            $result = false;
        if ($subject->getOrganisateur() == $user)
            $result = false;

        return $result;
    }

    private function permPublier(UserInterface $user, mixed $subject) : bool
    {
        $result = true;

        if ($subject->getOrganisateur() != $user)
            $result = false;
        if ($subject->getEtat()->getCode() != "CRT")
            $result = false;

        return $result;
    }

    private function permAnnuler(UserInterface $user, mixed $subject) : bool
    {
        $result = true;

        if ($subject->getOrganisateur() != $user)
            $result = false;
        if ($subject->getEtat()->getCode() != "OPN")
            $result = false;

        return $result;
    }

    private function permModifier(UserInterface $user, mixed $subject) : bool
    {
        $result = true;

        if ($subject->getOrganisateur() != $user)
            $result = false;
        if ($subject->getEtat()->getCode() != "CRT")
            $result = false;

        return $result;
    }

    private function permSupprimer(UserInterface $user, mixed $subject) : bool
    {
        $result = true;

        if ($subject->getOrganisateur() != $user)
            $result = false;
        if ($subject->getEtat()->getCode() != "CRT")
            $result = false;

        return $result;
    }
}
