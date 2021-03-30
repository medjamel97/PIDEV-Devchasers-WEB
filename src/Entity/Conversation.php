<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("post:read")
     */
    private $dateDernierMessage;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="conversation", cascade={"remove"})
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="conversation")
     */
    private $candidatExpediteur;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="conversationCandidatDestinataire")
     */
    private $candidatDestinataire;

    public function __construct()
    {
        $this->participantConversation = new ArrayCollection();
        $this->message = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDernierMessage(): ?\DateTimeInterface
    {
        return $this->dateDernierMessage;
    }

    public function setDateDernierMessage(\DateTimeInterface $dateDernierMessage): self
    {
        $this->dateDernierMessage = $dateDernierMessage;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessage(): Collection
    {
        return $this->message;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->message->contains($message)) {
            $this->message[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->message->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

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