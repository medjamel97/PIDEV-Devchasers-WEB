<?php

namespace App\Entity;

use App\Repository\ExperienceDeTravailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExperienceDeTravailRepository::class)
 */
class ExperienceDeTravail
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
     * @ORM\Column(type="string", length=255)
     */
    private $titreEmploi;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomEntreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $duree;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="experienceDeTravail")
     */
    private $candidat;

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

    public function getTitreEmploi(): ?string
    {
        return $this->titreEmploi;
    }

    public function setTitreEmploi(string $titreEmploi): self
    {
        $this->titreEmploi = $titreEmploi;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->Ville = $ville;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getCandidat(): ?candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }
}