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
     * @ORM\OneToMany(targetEntity=CandidatureFormation::class, mappedBy="candidat", cascade={"remove"})
     */
    private $candidatureFormation;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureEvenement::class, mappedBy="candidat", cascade={"remove"})
     */
    private $candidatureEvenement;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureOffre::class, mappedBy="candidat", cascade={"remove"})
     */
    private $candidatureOffre;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureMission::class, mappedBy="candidat", cascade={"remove"})
     */
    private $candidatureMission;

    /**
     * @ORM\OneToMany(targetEntity=Publication::class, mappedBy="candidat", cascade={"remove"})
     */
    private $publication;

    /**
     * @ORM\OneToMany(targetEntity=ExperienceDeTravail::class, mappedBy="candidat", cascade={"remove"})
     */
    private $experienceDeTravail;

    /**
     * @ORM\OneToMany(targetEntity=Education::class, mappedBy="candidat", cascade={"remove"})
     */
    private $education;

    /**
     * @ORM\OneToMany(targetEntity=Competence::class, mappedBy="candidat", cascade={"remove"})
     */
    private $competence;

    /**
     * @ORM\OneToOne(targetEntity=Utilisateur::class, mappedBy="candidat", cascade={"persist", "remove"})
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="candidatExpediteur", cascade={"remove"})
     */
    private $messageEnvoye;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="candidatDestinataire", cascade={"remove"})
     */
    private $messageRecu;

    public function __construct()
    {
        $this->candidatureFormation = new ArrayCollection();
        $this->candidatureEvenement = new ArrayCollection();
        $this->candidatureOffre = new ArrayCollection();
        $this->candidatureMission = new ArrayCollection();
        $this->publication = new ArrayCollection();
        $this->experienceDeTravail = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->competence = new ArrayCollection();
        $this->messageEnvoye = new ArrayCollection();
        $this->messageRecu = new ArrayCollection();
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
     * @return Collection|CandidatureFormation[]
     */
    public function getCandidatureFormation(): Collection
    {
        return $this->candidatureFormation;
    }

    public function addCandidatureFormation(candidatureFormation $candidatureFormation): self
    {
        if (!$this->candidatureFormation->contains($candidatureFormation)) {
            $this->candidatureFormation[] = $candidatureFormation;
            $candidatureFormation->setCandidat($this);
        }

        return $this;
    }

    public function removeCandidatureFormation(candidatureFormation $candidatureFormation): self
    {
        if ($this->candidatureFormation->removeElement($candidatureFormation)) {
            // set the owning side to null (unless already changed)
            if ($candidatureFormation->getCandidat() === $this) {
                $candidatureFormation->setCandidat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CandidatureEvenement[]
     */
    public function getCandidatureEvenement(): Collection
    {
        return $this->candidatureEvenement;
    }

    public function addCandidatureEvenement(candidatureEvenement $candidatureEvenement): self
    {
        if (!$this->candidatureEvenement->contains($candidatureEvenement)) {
            $this->candidatureEvenement[] = $candidatureEvenement;
            $candidatureEvenement->setCandidat($this);
        }

        return $this;
    }

    public function removeCandidatureEvenement(candidatureEvenement $candidatureEvenement): self
    {
        if ($this->candidatureEvenement->removeElement($candidatureEvenement)) {
            // set the owning side to null (unless already changed)
            if ($candidatureEvenement->getCandidat() === $this) {
                $candidatureEvenement->setCandidat(null);
            }
        }

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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        // unset the owning side of the relation if necessary
        if ($utilisateur === null && $this->utilisateur !== null) {
            $this->utilisateur->setCandidat(null);
        }

        // set the owning side of the relation if necessary
        if ($utilisateur !== null && $utilisateur->getCandidat() !== $this) {
            $utilisateur->setCandidat($this);
        }

        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessageEnvoye(): Collection
    {
        return $this->messageEnvoye;
    }

    public function addMessageEnvoye(Message $messageEnvoye): self
    {
        if (!$this->messageEnvoye->contains($messageEnvoye)) {
            $this->messageEnvoye[] = $messageEnvoye;
            $messageEnvoye->setCandidatDestinataire($this);
        }

        return $this;
    }

    public function removeMessageEnvoye(Message $messageEnvoye): self
    {
        if ($this->messageEnvoye->removeElement($messageEnvoye)) {
            // set the owning side to null (unless already changed)
            if ($messageEnvoye->getCandidatDestinataire() === $this) {
                $messageEnvoye->setCandidatDestinataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessageRecu(): Collection
    {
        return $this->messageRecu;
    }

    public function addMessageRecu(Message $messageRecu): self
    {
        if (!$this->messageRecu->contains($messageRecu)) {
            $this->$messageRecu[] = $messageRecu;
            $messageRecu->setCandidatExpediteur($this);
        }

        return $this;
    }

    public function removeMessageRecu(Message $messageRecu): self
    {
        if ($this->$messageRecu->removeElement($messageRecu)) {
            // set the owning side to null (unless already changed)
            if ($messageRecu->getCandidatExpediteur() === $this) {
                $messageRecu->setCandidatExpediteur(null);
            }
        }

        return $this;
    }
}
