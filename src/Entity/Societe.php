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
    private $nomSociete;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreationSociete;

    /**
     * @ORM\Column(type="integer")
     */
    private $numTelSociete;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idPhotoSociete;

    /**
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="societe")
     * @ORM\JoinColumn(name="mission_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $mission;

    /**
     * @ORM\OneToMany(targetEntity=OffreDeTravail::class, mappedBy="societe")
     * @ORM\JoinColumn(name="offreDeTravail_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $offreDeTravail;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="societe")
     * @ORM\JoinColumn(name="evenement_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $evenement;

    /**
     * @ORM\OneToMany(targetEntity=Formation::class, mappedBy="societe")
     * @ORM\JoinColumn(name="formation_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $formation;

    /**
     * @ORM\OneToOne(targetEntity=Utilisateur::class, mappedBy="societe", cascade={"persist", "remove"})
     */
    private $utilisateur;

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

    public function getNomSociete(): ?string
    {
        return $this->nomSociete;
    }

    public function setNomSociete(string $nomSociete): self
    {
        $this->nomSociete = $nomSociete;

        return $this;
    }

    public function getDateCreationSociete(): ?\DateTimeInterface
    {
        return $this->dateCreationSociete;
    }

    public function setDateCreationSociete(\DateTimeInterface $dateCreationSociete): self
    {
        $this->dateCreationSociete = $dateCreationSociete;

        return $this;
    }

    public function getNumTelSociete(): ?int
    {
        return $this->numTelSociete;
    }

    public function setNumTelSociete(int $numTelSociete): self
    {
        $this->numTelSociete = $numTelSociete;

        return $this;
    }

    public function getIdPhotoSociete(): ?string
    {
        return $this->idPhotoSociete;
    }

    public function setIdPhotoSociete(string $idPhotoSociete): self
    {
        $this->idPhotoSociete = $idPhotoSociete;

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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        // set the owning side of the relation if necessary
        if ($utilisateur->getSociete() !== $this) {
            $utilisateur->setSociete($this);
        }

        $this->utilisateur = $utilisateur;

        return $this;
    }
}
