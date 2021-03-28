<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir une description")
     * @Assert\Length(min=10, max=200, minMessage="Taille minimale (10)", maxMessage="Taille maximale (100) depassé")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="publication")
     */
    private $candidat;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="publication", cascade={"remove"})
     */
    private $commentaire;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="publication",cascade={"remove"})
     */
    private $likeid;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $nbr_like;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $all_like;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $date;

    public function __construct()
    {
        $this->commentaire = new ArrayCollection();
        $this->likeid = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCandidat(): ?candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaire(): Collection
    {
        return $this->commentaire;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaire->contains($commentaire)) {
            $this->commentaire[] = $commentaire;
            $commentaire->setPublication($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaire->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPublication() === $this) {
                $commentaire->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikeid(): Collection
    {
        return $this->likeid;
    }

    public function addLikeid(Like $likeid): self
    {
        if (!$this->likeid->contains($likeid)) {
            $this->likeid[] = $likeid;
            $likeid->setPublication($this);
        }

        return $this;
    }

    public function removeLikeid(Like $likeid): self
    {
        if ($this->likeid->removeElement($likeid)) {
            // set the owning side to null (unless already changed)
            if ($likeid->getPublication() === $this) {
                $likeid->setPublication(null);
            }
        }

        return $this;
    }

    public function getNbrLike(): ?int
    {
        return $this->nbr_like;
    }

    public function setNbrLike(int $nbr_like): self
    {
        $this->nbr_like = $nbr_like;

        return $this;
    }

    public function getAllLike(): ?int
    {
        return $this->all_like;
    }

    public function setAllLike(int $all_like): self
    {
        $this->all_like = $all_like;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
