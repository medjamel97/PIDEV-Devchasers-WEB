<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $contenu;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("post:read")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("post:read")
     */
    private $estProprietaire;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("post:read")
     */
    private $estVu;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="message")
     */
    private $conversation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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

    public function getEstProprietaire(): ?bool
    {
        return $this->estProprietaire;
    }

    public function setEstProprietaire(bool $estProprietaire): self
    {
        $this->estProprietaire = $estProprietaire;

        return $this;
    }

    public function getEstVu(): ?bool
    {
        return $this->estVu;
    }

    public function setEstVu(bool $estVu): self
    {
        $this->estVu = $estVu;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}