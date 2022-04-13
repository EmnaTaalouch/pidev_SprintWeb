<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("login")
 */
class User implements UserInterface
{   
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="le nom doit etre non vide")
     * @Assert\Length(
     *      min = 4,
     *      max = 20,
     *      minMessage = "Entrer au minimum 4 caracteres ",
     *      maxMessage = "Entrer au maximum 20 caracteres" )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="le nom ne doit pas contenir un entier"
     * )
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="le prenom doit etre non vide")
     * @Assert\Length(
     *      min = 4,
     *      max = 20,
     *      minMessage = "Entrer au minimum 4 caracteres ",
     *      maxMessage = "Entrer au maximum 20 caracteres" )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="le prenom ne doit pas contenir un entier"
     * )
     * @ORM\Column(type="string", length=20)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message="le login doit etre non vide")
     * @Assert\Email(
     *     message = "cet email '{{ value }}' est invalide."
     * )
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $login;

    /**
     * @Assert\NotBlank(message="le mot de passe doit etre non vide")
     * @Assert\Regex(
     *    pattern = "/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%])(?!.*(.)\1{2}).*[a-z]/m",
     *    match=true,
     *    message="Votre mot de passe doit comporter au moins huit caractères, dont des lettres 
     *          majuscules et minuscules, un chiffre et un symbole.")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\Choice({"admin", "client"})
     * @ORM\Column(type="string", length=20)
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="users")
     */
    private $events;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        $this->events->removeElement($event);
        return $this;
    }
    public function getUsername()
    {
        return $this->nom;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }


    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
