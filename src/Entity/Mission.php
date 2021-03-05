<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MissionRepository::class)
 */
class Mission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbheure;

    /**
     * @ORM\Column(type="float")
     */
    private $prixH;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureMission::class, mappedBy="mission")
     */
    private $candidatureMission;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="mission")
     */
    private $societe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mission_name;

    /**
     * @ORM\Column(type="string", length=10383, nullable=true)
     */
    private $description;

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

    public function getNbheure(): ?int
    {
        return $this->nbheure;
    }

    public function setNbheure(int $nbheure): self
    {
        $this->nbheure = $nbheure;

        return $this;
    }

    public function getPrixH(): ?float
    {
        return $this->prixH;
    }

    public function setPrixH(float $prixH): self
    {
        $this->prixH = $prixH;

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

    public function getMissionName(): ?string
    {
        return $this->mission_name;
    }

    public function setMissionName(string $mission_name): self
    {
        $this->mission_name = $mission_name;

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