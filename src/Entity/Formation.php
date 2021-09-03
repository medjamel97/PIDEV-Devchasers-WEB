<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\NotBlank(message="Veuillez saisir un nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir une filliere")
     */
    private $filiere;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(
     *     value = "today GMT+1",
     *     message = "La date debut doit dépasser la date d'aujourd'hui"
     * )
     */
    private $debut;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(
     *     value = "today GMT+1",
     *     message = "La date fin doit dépasser la date d'aujourd'hui"
     * )
     */
    private $fin;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="formation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(?string $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getDebut(): ?DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(DateTimeInterface $fin): self
    {
        $this->fin = $fin;

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
