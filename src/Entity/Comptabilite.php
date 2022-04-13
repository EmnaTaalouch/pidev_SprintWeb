<?php

namespace App\Entity;

use App\Repository\ComptabiliteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ComptabiliteRepository::class)
 */
class Comptabilite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 3,
     *      max = 10,
     * minMessage = "Le libelle doit composer au mois {{ limit }} caractères",
     * maxMessage = "Le libelle doit composer au plus {{ limit }} caractères"
     * )
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 5,
     *      max = 50,
     * minMessage = "La description doit composer au mois {{ limit }} caractères",
     * maxMessage = "La description doit composer au plus {{ limit }} caractères"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=TypeComptabilite::class, inversedBy="comptabilites")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_type", referencedColumnName="id")
     * })
     */
    private $id_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdType(): ?TypeComptabilite
    {
        return $this->id_type;
    }

    public function setIdType(?TypeComptabilite $id_type): self
    {
        $this->id_type = $id_type;

        return $this;
    }
}
