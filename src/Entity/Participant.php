<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Le nom d\'utilisateur ne doit pas être vide.')]
    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom d\'utilisateur doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le prénom doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $prenom = null;

    /**
     * ^ : pour dire dès le début de la chaine $numero on veut "(\+33|0)"
     * (\+33|0) : pour dire +33 ou 0, la barre oblique est "ou", les parenthèses nous servent à englober notre condition
     * [67] : suivi de 6 ou 7, les crochets (une classe) nous permettent de demander 6 ou 7, on aurait par exemple pu mettre 679 pour accepte les 09
     * [0-9] : une plage de 0 à 9 (grâce au tiret), de 8 caractères grâce à {8} qui le suit
     * $ : pour dire la fin de la chaine doit s'arrêter là et ne pas accepter autre chose après le numéro
     */

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern : "#^(\+33|0)[67][0-9]{8}$#",
        message: "Le numéro de téléphone n'est pas valide. Il doit être au format 06 XX XX XX XX ou +33 7 XX XX XX XX."
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(
        message: "L'email '{{ value }}' n'est pas valide."
    )]
    private ?string $mail = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function copyFrom(Participant $participant): static
    {
        $this->setNom($participant->getNom());
        $this->setPrenom($participant->getPrenom());
        $this->setTelephone($participant->getTelephone());
        $this->setMail($participant->getMail());
        $this->setCampus($participant->getCampus());
        $this->setPassword($participant->getPassword());
        $this->setUsername($participant->getUsername());

        return $this;
    }

}
