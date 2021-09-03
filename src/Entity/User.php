<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSetUp = false;

    /**
     * @ORM\OneToMany(targetEntity=Publication::class, mappedBy="user", cascade={"remove"})
     */
    private $publication;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="userExpediteur", cascade={"remove"})
     */
    private $conversationIsProperty;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="userDestinataire", cascade={"remove"})
     */
    private $conversationIsNotProperty;

    /**
     * @ORM\OneToOne(targetEntity=Candidat::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private $candidat;

    /**
     * @ORM\OneToOne(targetEntity=Societe::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private $societe;


    public function __construct()
    {
        $this->publication = new ArrayCollection();
        $this->conversationIsProperty = new ArrayCollection();
        $this->conversationIsNotProperty = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getIsSetUp(): bool
    {
        return $this->isSetUp;
    }

    public function setIsSetUp(bool $isSetUp): self
    {
        $this->isSetUp = $isSetUp;
        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublication(): Collection
    {
        return $this->publication;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publication->contains($publication)) {
            $this->publication[] = $publication;
            $publication->setUser($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publication->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getUser() === $this) {
                $publication->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getConversationIsProperty(): Collection
    {
        return $this->conversationIsProperty;
    }

    public function addConversationIsProperty(Publication $conversationIsProperty): self
    {
        if (!$this->conversationIsProperty->contains($conversationIsProperty)) {
            $this->conversationIsProperty[] = $conversationIsProperty;
            $conversationIsProperty->setUser($this);
        }

        return $this;
    }

    public function removeConversationIsProperty(Publication $conversationIsProperty): self
    {
        if ($this->conversationIsProperty->removeElement($conversationIsProperty)) {
            // set the owning side to null (unless already changed)
            if ($conversationIsProperty->getUser() === $this) {
                $conversationIsProperty->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getConversationIsNotProperty(): Collection
    {
        return $this->conversationIsNotProperty;
    }

    public function addConversationIsNotProperty(Publication $conversationIsNotProperty): self
    {
        if (!$this->conversationIsNotProperty->contains($conversationIsNotProperty)) {
            $this->conversationIsNotProperty[] = $conversationIsNotProperty;
            $conversationIsNotProperty->setUser($this);
        }

        return $this;
    }

    public function removeConversation(Publication $conversationIsNotProperty): self
    {
        if ($this->conversationIsNotProperty->removeElement($conversationIsNotProperty)) {
            // set the owning side to null (unless already changed)
            if ($conversationIsNotProperty->getUser() === $this) {
                $conversationIsNotProperty->setUser(null);
            }
        }

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

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;
        return $this;
    }
}
