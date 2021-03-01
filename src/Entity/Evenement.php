<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
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
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="evenement")
     */
    private $societe;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureEvenement::class, mappedBy="evenement")
     */
    private $candidatureEvenement;

    public function __construct()
    {
        $this->candidatureEvenement = new ArrayCollection();
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

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * @return Collection|CandidatureEvenement[]
     */
    public function getCandidatureEvenement(): Collection
    {
        return $this->candidatureEvenement;
    }

    public function addCandidatureEvenement(CandidatureEvenement $candidatureEvenement): self
    {
        if (!$this->candidatureEvenement->contains($candidatureEvenement)) {
            $this->candidatureEvenement[] = $candidatureEvenement;
            $candidatureEvenement->setEvenement($this);
        }

        return $this;
    }

    public function removeCandidatureEvenement(CandidatureEvenement $candidatureEvenement): self
    {
        if ($this->candidatureEvenement->removeElement($candidatureEvenement)) {
            // set the owning side to null (unless already changed)
            if ($candidatureEvenement->getEvenement() === $this) {
                $candidatureEvenement->setEvenement(null);
            }
        }

        return $this;
    }
}
