<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionnaireRepository::class)
 */
class Questionnaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="questionnaire")
     */
    private $question;

    /**
     * @ORM\OneToOne(targetEntity=CandidatureMission::class, mappedBy="questionnaire", cascade={"persist", "remove"})
     */
    private $candidatureMission;

    public function __construct()
    {
        $this->question = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|question[]
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }

    public function addQuestion(question $question): self
    {
        if (!$this->question->contains($question)) {
            $this->question[] = $question;
            $question->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeQuestion(question $question): self
    {
        if ($this->question->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuestionnaire() === $this) {
                $question->setQuestionnaire(null);
            }
        }

        return $this;
    }

    public function getCandidatureMission(): ?CandidatureMission
    {
        return $this->candidatureMission;
    }

    public function setCandidatureMission(?CandidatureMission $candidatureMission): self
    {
        // unset the owning side of the relation if necessary
        if ($candidatureMission === null && $this->candidatureMission !== null) {
            $this->candidatureMission->setQuestionnaire(null);
        }

        // set the owning side of the relation if necessary
        if ($candidatureMission !== null && $candidatureMission->getQuestionnaire() !== $this) {
            $candidatureMission->setQuestionnaire($this);
        }

        $this->candidatureMission = $candidatureMission;

        return $this;
    }
}
