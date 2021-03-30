<?php

namespace App\Entity;

use App\Repository\OffreDeTravailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreDeTravailRepository::class)
 */
class OffreDeTravail
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
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=Categorie::class, mappedBy="offreDeTravail", cascade={"persist", "remove"})
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureOffre::class, mappedBy="offreDeTravail")
     */
    private $candidatureOffres;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="offreDeTravail")
     */
    private $societe;

    public function __construct()
    {
        $this->candidatureOffres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        // unset the owning side of the relation if necessary
        if ($categorie === null && $this->categorie !== null) {
            $this->categorie->setOffreDeTravail(null);
        }

        // set the owning side of the relation if necessary
        if ($categorie !== null && $categorie->getOffreDeTravail() !== $this) {
            $categorie->setOffreDeTravail($this);
        }

        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|CandidatureOffre[]
     */
    public function getCandidatureOffres(): Collection
    {
        return $this->candidatureOffres;
    }

    public function addCandidatureOffre(CandidatureOffre $candidatureOffre): self
    {
        if (!$this->candidatureOffres->contains($candidatureOffre)) {
            $this->candidatureOffres[] = $candidatureOffre;
            $candidatureOffre->setOffreDeTravail($this);
        }

        return $this;
    }

    public function removeCandidatureOffre(CandidatureOffre $candidatureOffre): self
    {
        if ($this->candidatureOffres->removeElement($candidatureOffre)) {
            // set the owning side to null (unless already changed)
            if ($candidatureOffre->getOffreDeTravail() === $this) {
                $candidatureOffre->setOffreDeTravail(null);
            }
        }

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }
}
