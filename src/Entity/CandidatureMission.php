<?php

namespace App\Entity;

use App\Repository\CandidatureMissionRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidatureMissionRepository::class)
 */
class CandidatureMission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat = "non traité";

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Mission::class, inversedBy="candidatureMission")
     * @ORM\JoinColumn(name="mission_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $mission;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="candidatureMission")
     * @ORM\JoinColumn(name="candidat_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $candidat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): self
    {
        $this->mission = $mission;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }
}
