<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use DateTimeInterface;
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="conversationIsProperty")
     */
    private $userExpediteur;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="conversationIsNotProperty")
     */
    private $userDestinataire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    public function __construct()
    {
        $this->message = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDernierMessage(): ?DateTimeInterface
    {
        return $this->dateDernierMessage;
    }

    public function setDateDernierMessage(DateTimeInterface $dateDernierMessage): self
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

    public function getUserExpediteur(): ?User
    {
        return $this->userExpediteur;
    }

    public function setUserExpediteur(?User $userExpediteur): self
    {
        $this->userExpediteur = $userExpediteur;

        return $this;
    }

    public function getUserDestinataire(): ?User
    {
        return $this->userDestinataire;
    }

    public function setUserDestinataire(?User $userDestinataire): self
    {
        $this->userDestinataire = $userDestinataire;

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
}
