<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MissionRepository::class)
 */
class Mission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\Length(min=10, max=200, minMessage="Taille minimale (10)", maxMessage="Taille maximale (100) depassé")
     * @Groups("post:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=10383, nullable=true)
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Groups("post:read")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     * @Groups("post:read")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\Range(
     *      min = 1,
     *      max = 10,
     *      notInRangeMessage = "You must be between {{ min }}cm and {{ max }}cm tall to enter",
     * )
     * @Groups("post:read")
     */
    private $nombreHeures;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\Range(
     *      min = 1,
     *      max = 1000,
     *      notInRangeMessage = "You must be between {{ min }}cm and {{ max }}cm tall to enter",
     * )
     * @Groups("post:read")
     */
    private $prixHeure;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureMission::class, mappedBy="mission")
     */
    private $candidatureMission;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="mission")
     * @Groups("post:read")
     */
    private $societe;

    public function __construct()
    {
        $this->candidatureMission = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNbHeures(): ?int
    {
        return $this->nombreHeures;
    }

    public function setNbHeures(int $nombreHeures): self
    {
        $this->nombreHeures = $nombreHeures;

        return $this;
    }

    public function getPrixHeure(): ?float
    {
        return $this->prixHeure;
    }

    public function setPrixHeure(float $prixHeure): self
    {
        $this->prixHeure = $prixHeure;

        return $this;
    }

    /**
     * @return Collection|CandidatureMission[]
     */
    public function getCandidatureMission(): Collection
    {
        return $this->candidatureMission;
    }

    public function addCandidatureMission(CandidatureMission $candidatureMission): self
    {
        if (!$this->candidatureMission->contains($candidatureMission)) {
            $this->candidatureMission[] = $candidatureMission;
            $candidatureMission->setMission($this);
        }

        return $this;
    }

    public function removeCandidatureMission(CandidatureMission $candidatureMission): self
    {
        if ($this->candidatureMission->removeElement($candidatureMission)) {
            // set the owning side to null (unless already changed)
            if ($candidatureMission->getMission() === $this) {
                $candidatureMission->setMission(null);
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}