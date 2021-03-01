<?php

namespace App\Entity;

use App\Repository\CandidatureMissionRepository;
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
     * @ORM\Column(type="string", length=255)
     */
    private $nomFormation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=questionnaire::class, inversedBy="candidatureMission", cascade={"persist", "remove"})
     */
    private $questionnaire;

    /**
     * @ORM\ManyToOne(targetEntity=Mission::class, inversedBy="candidatureMission")
     */
    private $mission;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="candidatureMission")
     */
    private $candidat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFormation(): ?string
    {
        return $this->nomFormation;
    }

    public function setNomFormation(string $nomFormation): self
    {
        $this->nomFormation = $nomFormation;

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

    public function getQuestionnaire(): ?questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

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
