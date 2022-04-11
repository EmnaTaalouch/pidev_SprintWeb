<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_event;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $event_description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $event_theme;

    /**
     * @ORM\Column(name="date_debut", type="date")
     */
    private $date_debut;

    /**
     * @ORM\Column(name="date_fin", type="date")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $event_status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $demande_status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     */
    private $id_client;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     */
    private $id_responsable;

    /**
     * @ORM\ManyToOne(targetEntity=EventType::class, inversedBy="events")
     */
    private $id_type;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="events")
     */
    private $users;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbr_participants;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_event;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEvent(): ?string
    {
        return $this->nom_event;
    }

    public function setNomEvent(string $nom_event): self
    {
        $this->nom_event = $nom_event;

        return $this;
    }

    public function getEventDescription(): ?string
    {
        return $this->event_description;
    }

    public function setEventDescription(string $event_description): self
    {
        $this->event_description = $event_description;

        return $this;
    }

    public function getEventTheme(): ?string
    {
        return $this->event_theme;
    }

    public function setEventTheme(string $event_theme): self
    {
        $this->event_theme = $event_theme;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getEventStatus(): ?string
    {
        return $this->event_status;
    }

    public function setEventStatus(string $event_status): self
    {
        $this->event_status = $event_status;

        return $this;
    }

    public function getDemandeStatus(): ?string
    {
        return $this->demande_status;
    }

    public function setDemandeStatus(string $demande_status): self
    {
        $this->demande_status = $demande_status;

        return $this;
    }

    public function getIdClient(): ?User
    {
        return $this->id_client;
    }

    public function setIdClient(?User $id_client): self
    {
        $this->id_client = $id_client;

        return $this;
    }

    public function getIdResponsable(): ?User
    {
        return $this->id_responsable;
    }

    public function setIdResponsable(?User $id_responsable): self
    {
        $this->id_responsable = $id_responsable;

        return $this;
    }

    public function getIdType(): ?EventType
    {
        return $this->id_type;
    }

    public function setIdType(?EventType $id_type): self
    {
        $this->id_type = $id_type;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getNbrParticipants(): ?int
    {
        return $this->nbr_participants;
    }

    public function setNbrParticipants(int $nbr_participants): self
    {
        $this->nbr_participants = $nbr_participants;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getImageEvent(): ?string
    {
        return $this->image_event;
    }

    public function setImageEvent(string $image_event): self
    {
        $this->image_event = $image_event;

        return $this;
    }
}
