<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\NotBlank(message="Veuillez saisir un titre")
     */
    private $titre;

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
     * @ORM\Column(type="string", length=5000)
     * @Assert\Regex(
     * pattern = "/^[a-zA-Z\s]+$/i",
     * message = "Vous ne devez saisir que des lettres et des espaces"
     * )
     * @Assert\NotBlank(message="Veuillez saisir une description")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $allDay;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="evenement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;

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
