<?php

namespace App\Entity;

use App\Repository\TypeComptabiliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TypeComptabiliteRepository::class)
 */
class TypeComptabilite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("typecomptabilite")
     * @Groups("posts:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 3,
     *      max = 10,
     * minMessage = "Le type doit composer au mois {{ limit }} caractères",
     * maxMessage = "Le type doit composer au plus {{ limit }} caractères"
     * )
     * @Groups("typecomptabilite")
     * @Groups("posts:read")
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotNull
     * @Assert\Positive
     * @Groups("typecomptabilite")
     * @Groups("posts:read")
     */
    private $montant;

    /**
     * @ORM\OneToMany(targetEntity=Comptabilite::class, mappedBy="id_type")
     */
    private $comptabilites;

    public function __construct()
    {
        $this->comptabilites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }



    public function addComptabilite(Comptabilite $comptabilite): self
    {
        if (!$this->comptabilites->contains($comptabilite)) {
            $this->comptabilites[] = $comptabilite;
            $comptabilite->setIdType($this);
        }

        return $this;
    }

    public function removeComptabilite(Comptabilite $comptabilite): self
    {
        if ($this->comptabilites->removeElement($comptabilite)) {
            // set the owning side to null (unless already changed)
            if ($comptabilite->getIdType() === $this) {
                $comptabilite->setIdType(null);
            }
        }

        return $this;
    }
}
