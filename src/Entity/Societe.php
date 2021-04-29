<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocieteRepository::class)
 */
class Societe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idPhoto;

    /**
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="societe", cascade={"remove"})
     */
    private $mission;

    /**
     * @ORM\OneToMany(targetEntity=OffreDeTravail::class, mappedBy="societe", cascade={"remove"})
     */
    private $offreDeTravail;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="societe", cascade={"remove"})
     */
    private $evenement;

    /**
     * @ORM\OneToMany(targetEntity=Formation::class, mappedBy="societe", cascade={"remove"})
     */
    private $formation;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="societe", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->mission = new ArrayCollection();
        $this->offreDeTravail = new ArrayCollection();
        $this->evenement = new ArrayCollection();
        $this->formation = new ArrayCollection();
        $this->formation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getIdPhoto(): ?string
    {
        return $this->idPhoto;
    }

    public function setIdPhoto(string $idPhoto): self
    {
        $this->idPhoto = $idPhoto;

        return $this;
    }

    /**
     * @return Collection|Mission[]
     */
    public function getMission(): Collection
    {
        return $this->mission;
    }

    public function addMission(mission $mission): self
    {
        if (!$this->mission->contains($mission)) {
            $this->mission[] = $mission;
            $mission->setSociete($this);
        }

        return $this;
    }

    public function removeMission(mission $mission): self
    {
        if ($this->mission->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getSociete() === $this) {
                $mission->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OffreDeTravail[]
     */
    public function getOffreDeTravail(): Collection
    {
        return $this->offreDeTravail;
    }

    public function addOffreDeTravail(offreDeTravail $offreDeTravail): self
    {
        if (!$this->offreDeTravail->contains($offreDeTravail)) {
            $this->offreDeTravail[] = $offreDeTravail;
            $offreDeTravail->setSociete($this);
        }

        return $this;
    }

    public function removeOffreDeTravail(offreDeTravail $offreDeTravail): self
    {
        if ($this->offreDeTravail->removeElement($offreDeTravail)) {
            // set the owning side to null (unless already changed)
            if ($offreDeTravail->getSociete() === $this) {
                $offreDeTravail->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement[] = $evenement;
            $evenement->setSociete($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getSociete() === $this) {
                $evenement->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormation(): Collection
    {
        return $this->formation;
    }

    public function addFormation(formation $formation): self
    {
        if (!$this->formation->contains($formation)) {
            $this->formation[] = $formation;
            $formation->setSociete($this);
        }

        return $this;
    }

    public function removeFormation(formation $formation): self
    {
        if ($this->formation->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getSociete() === $this) {
                $formation->setSociete(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getSociete() !== $this) {
            $user->setSociete($this);
        }

        $this->user = $user;

        return $this;
    }
}
