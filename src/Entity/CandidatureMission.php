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
     * @ORM\OneToOne(targetEntity=Questionnaire::class, inversedBy="candidatureMission", cascade={"remove"})
     */
    private $questionnaire;

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