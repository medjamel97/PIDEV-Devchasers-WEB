<?php

namespace App\Entity;

use App\Repository\CandidatureOffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidatureOffreRepository::class)
 */
class CandidatureOffre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Interview::class, mappedBy="candidatureOffre")
     */
    private $interview;

    /**
     * @ORM\OneToMany(targetEntity=Revue::class, mappedBy="candidatureOffre")
     */
    private $revue;

    /**
     * @ORM\ManyToOne(targetEntity=OffreDeTravail::class, inversedBy="candidatureOffre")
     */
    private $offreDeTravail;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="candidatureOffre")
     */
    private $candidat;

    public function __construct()
    {
        $this->interview = new ArrayCollection();
        $this->revue = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Interview[]
     */
    public function getInterview(): Collection
    {
        return $this->interview;
    }

    public function addInterview(interview $interview): self
    {
        if (!$this->interview->contains($interview)) {
            $this->interview[] = $interview;
            $interview->setCandidatureOffre($this);
        }

        return $this;
    }

    public function removeInterview(interview $interview): self
    {
        if ($this->interview->removeElement($interview)) {
            // set the owning side to null (unless already changed)
            if ($interview->getCandidatureOffre() === $this) {
                $interview->setCandidatureOffre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Revue[]
     */
    public function getRevue(): Collection
    {
        return $this->revue;
    }

    public function addRevue(revue $revue): self
    {
        if (!$this->revue->contains($revue)) {
            $this->revue[] = $revue;
            $revue->setCandidatureOffre($this);
        }

        return $this;
    }

    public function removeRevue(revue $revue): self
    {
        if ($this->revue->removeElement($revue)) {
            // set the owning side to null (unless already changed)
            if ($revue->getCandidatureOffre() === $this) {
                $revue->setCandidatureOffre(null);
            }
        }

        return $this;
    }

    public function getOffreDeTravail(): ?offreDeTravail
    {
        return $this->offreDeTravail;
    }

    public function setOffreDeTravail(?offreDeTravail $offreDeTravail): self
    {
        $this->offreDeTravail = $offreDeTravail;

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
