<?php

namespace App\Entity;

use App\Repository\RevueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RevueRepository::class)
 */
class Revue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="is required")
     */
    private $nbEtoiles;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="is required")
     */
    private $objet;

    /**
     * @ORM\Column(type="string", length=5000)
     * @Assert\NotBlank(message="is required")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\ManyToOne(targetEntity=CandidatureOffre::class, inversedBy="revue")
     */
    private $candidatureOffre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbEtoiles(): ?int
    {
        return $this->nbEtoiles;
    }

    public function setNbEtoiles(int $nbEtoiles): self
    {
        $this->nbEtoiles = $nbEtoiles;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCandidatureOffre(): ?CandidatureOffre
    {
        return $this->candidatureOffre;
    }

    public function setCandidatureOffre(?CandidatureOffre $candidatureOffre): self
    {
        $this->candidatureOffre = $candidatureOffre;

        return $this;
    }
}
