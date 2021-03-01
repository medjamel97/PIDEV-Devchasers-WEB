<?php

namespace App\Entity;

use App\Repository\CandidatureEvenementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidatureEvenementRepository::class)
 */
class CandidatureEvenement
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
     * @ORM\ManyToOne(targetEntity=evenement::class, inversedBy="TESTEEEEE")
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity=candidat::class, inversedBy="candidatureEvenement")
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

    public function getEvenement(): ?evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?evenement $evenement): self
    {
        $this->evenement = $evenement;

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
