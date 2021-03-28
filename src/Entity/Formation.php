<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormationRepository::class)
 */
class Formation
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="formation")
     */
    private $societe;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureFormation::class, mappedBy="formation")
     */
    private $candidatureFormation;
    /**
     * @var string
     */

    public function __construct()
    {
        $this->candidatureFormation = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSociete(): ?societe
    {
        return $this->societe;
    }

    public function setSociete(?societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * @return Collection|CandidatureFormation[]
     */
    public function getCandidatureFormation(): Collection
    {
        return $this->candidatureFormation;
    }

    public function addCandidatureFormation(candidatureFormation $candidatureFormation): self
    {
        if (!$this->candidatureFormation->contains($candidatureFormation)) {
            $this->candidatureFormation[] = $candidatureFormation;
            $candidatureFormation->setFormation($this);
        }

        return $this;
    }

    public function removeCandidatureFormation(candidatureFormation $candidatureFormation): self
    {
        if ($this->candidatureFormation->removeElement($candidatureFormation)) {
            // set the owning side to null (unless already changed)
            if ($candidatureFormation->getFormation() === $this) {
                $candidatureFormation->setFormation(null);
            }
        }

        return $this;
    }
}
