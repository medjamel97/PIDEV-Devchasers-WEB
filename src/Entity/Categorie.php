<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $nomCategorie;

    /**
     * @ORM\OneToMany(targetEntity=OffreDeTravail::class, mappedBy="categorie")
     */
    public $offreDeTravail;

    public function __construct()
    {
        $this->offreDeTravail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    /**
     * @return Collection|OffreDeTravail[]
     */
    public function getOffreDeTravail(): Collection
    {
        return $this->offreDeTravail;
    }

    public function addOffreDeTravail(OffreDeTravail $offreDeTravail): self
    {
        if (!$this->offreDeTravail->contains($offreDeTravail)) {
            $this->offreDeTravail[] = $offreDeTravail;
            $offreDeTravail->setCategorie($this);
        }

        return $this;
    }

    public function removeOffreDeTravail(OffreDeTravail $offreDeTravail): self
    {
        if ($this->offreDeTravail->removeElement($offreDeTravail)) {
            // set the owning side to null (unless already changed)
            if ($offreDeTravail->getCategorie() === $this) {
                $offreDeTravail->setCategorie(null);
            }
        }

        return $this;
    }
    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
}
