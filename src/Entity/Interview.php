<?php

namespace App\Entity;

use App\Repository\InterviewRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InterviewRepository::class)
 */
class Interview
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez choisir une difficulte")
     */
    private $difficulte;

    /**
     * @ORM\Column(type="string", length=5000)
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\Length(
     *     min=10,
     *     max=5000,
     *     minMessage="Votre description doit avoir au minimum 10 caractères.",
     *     maxMessage="Votre description doit avoir au maximum 5000 caractères.")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\ManyToOne(targetEntity=CandidatureOffre::class, inversedBy="interview")
     */
    private $candidatureOffre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDifficulte(): ?int
    {
        return $this->difficulte;
    }

    public function setDifficulte(int $difficulte): self
    {
        $this->difficulte = $difficulte;

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

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(DateTimeInterface $dateCreation): self
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
