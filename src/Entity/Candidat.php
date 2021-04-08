<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CandidatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidatRepository::class)
 * use Symfony\Component\Validator\Constraints as Assert;
 */
class Candidat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un nom")
     * @Assert\Length(min=1, max=20, minMessage="Taille minimale (1)", maxMessage="Taille maximale (20) depassé")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idPhoto;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureOffre::class, mappedBy="candidat")
     */
    private $candidatureOffre;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureMission::class, mappedBy="candidat")
     */
    private $candidatureMission;

    /**
     * @ORM\OneToMany(targetEntity=Publication::class, mappedBy="candidat")
     */
    private $publication;

    /**
     * @ORM\OneToMany(targetEntity=ExperienceDeTravail::class, mappedBy="candidat")
     */
    private $experienceDeTravail;

    /**
     * @ORM\OneToMany(targetEntity=Education::class, mappedBy="candidat")
     */
    private $education;

    /**
     * @ORM\OneToMany(targetEntity=Competence::class, mappedBy="candidat")
     */
    private $competence;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="candidat", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->candidatureOffre = new ArrayCollection();
        $this->candidatureMission = new ArrayCollection();
        $this->publication = new ArrayCollection();
        $this->experienceDeTravail = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->competence = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
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
     * @return Collection|CandidatureOffre[]
     */
    public function getCandidatureOffre(): Collection
    {
        return $this->candidatureOffre;
    }

    public function addCandidatureOffre(candidatureOffre $candidatureOffre): self
    {
        if (!$this->candidatureOffre->contains($candidatureOffre)) {
            $this->candidatureOffre[] = $candidatureOffre;
            $candidatureOffre->setCandidat($this);
        }

        return $this;
    }

    public function removeCandidatureOffre(candidatureOffre $candidatureOffre): self
    {
        if ($this->candidatureOffre->removeElement($candidatureOffre)) {
            // set the owning side to null (unless already changed)
            if ($candidatureOffre->getCandidat() === $this) {
                $candidatureOffre->setCandidat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CandidatureMission[]
     */
    public function getCandidatureMission(): Collection
    {
        return $this->candidatureMission;
    }

    public function addCandidatureMission(candidatureMission $candidatureMission): self
    {
        if (!$this->candidatureMission->contains($candidatureMission)) {
            $this->candidatureMission[] = $candidatureMission;
            $candidatureMission->setCandidat($this);
        }

        return $this;
    }

    public function removeCandidatureMission(candidatureMission $candidatureMission): self
    {
        if ($this->candidatureMission->removeElement($candidatureMission)) {
            // set the owning side to null (unless already changed)
            if ($candidatureMission->getCandidat() === $this) {
                $candidatureMission->setCandidat(null);
            }
        }

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
            $publication->setCandidat($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publication->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getCandidat() === $this) {
                $publication->setCandidat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ExperienceDeTravail[]
     */
    public function getExperienceDeTravail(): Collection
    {
        return $this->experienceDeTravail;
    }

    public function addExperienceDeTravail(ExperienceDeTravail $experienceDeTravail): self
    {
        if (!$this->experienceDeTravail->contains($experienceDeTravail)) {
            $this->experienceDeTravail[] = $experienceDeTravail;
            $experienceDeTravail->setCandidat($this);
        }

        return $this;
    }

    public function removeExperienceDeTravail(ExperienceDeTravail $experienceDeTravail): self
    {
        if ($this->experienceDeTravail->removeElement($experienceDeTravail)) {
            // set the owning side to null (unless already changed)
            if ($experienceDeTravail->getCandidat() === $this) {
                $experienceDeTravail->setCandidat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Education[]
     */
    public function getEducation(): Collection
    {
        return $this->education;
    }

    public function addEducation(Education $education): self
    {
        if (!$this->education->contains($education)) {
            $this->education[] = $education;
            $education->setCandidat($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): self
    {
        if ($this->education->removeElement($education)) {
            // set the owning side to null (unless already changed)
            if ($education->getCandidat() === $this) {
                $education->setCandidat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Competence[]
     */
    public function getCompetence(): Collection
    {
        return $this->competence;
    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competence->contains($competence)) {
            $this->competence[] = $competence;
            $competence->setCandidat($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        if ($this->competence->removeElement($competence)) {
            // set the owning side to null (unless already changed)
            if ($competence->getCandidat() === $this) {
                $competence->setCandidat(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setCandidat(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getCandidat() !== $this) {
            $user->setCandidat($this);
        }

        $this->user = $user;

        return $this;
    }
}
