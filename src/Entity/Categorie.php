<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
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
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomCategorie;

    /**
     * @ORM\OneToOne(targetEntity=OffreDeTravail::class, inversedBy="categorie", cascade={"persist", "remove"})
     */
    private $offreDeTravail;

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

    public function getOffreDeTravail(): ?offredetravail
    {
        return $this->offreDeTravail;
    }

    public function setOffreDeTravail(?offredetravail $offreDeTravail): self
    {
        $this->offreDeTravail = $offreDeTravail;

        return $this;
    }
}
