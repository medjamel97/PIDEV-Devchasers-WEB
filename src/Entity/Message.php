<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 */
class Message
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
    private $contenu;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreation;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="messageEnvoye")
     */
    private $candidatExpediteur;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="messageRecu")
     */
    private $candidatDestinataire;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCandidatExpediteur(): ?Candidat
    {
        return $this->candidatExpediteur;
    }

    public function setCandidatExpediteur(?Candidat $candidatExpediteur): self
    {
        $this->candidatExpediteur = $candidatExpediteur;

        return $this;
    }

    public function getCandidatDestinataire(): ?Candidat
    {
        return $this->candidatDestinataire;
    }

    public function setCandidatDestinataire(?Candidat $candidatDestinataire): self
    {
        $this->candidatDestinataire = $candidatDestinataire;

        return $this;
    }
}
