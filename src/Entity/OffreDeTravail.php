<?php

namespace App\Entity;

use App\Form\OffreDeTravailType;
use App\Repository\OffreDeTravailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreDeTravailRepository::class)
 */
class OffreDeTravail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     * pattern = "/^[a-zA-Z]+$/i",
     * message = "vous ne devez saisir que des lettres"
     * )
     * @Groups("get:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=5000)
     * @Assert\Regex(
     * pattern = "/^[a-zA-Z]+$/i",
     * message = "vous ne devez saisir que des lettres"
     * )
     * @Groups("get:read")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="offreDeTravail")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="offreDeTravail")
     * @ORM\JoinColumn(nullable=true)
     */
    private $societe;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureOffre::class, mappedBy="offreDeTravail", cascade={"remove"})
     */
    private $candidatureOffre;

    public function __construct()
    {
        $this->candidatureOffre = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    function setId($id)
    {

        $this->id = $id;


        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

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

    /**
     * @return Collection|CandidatureOffre[]
     */
    public function getCandidatureOffre(): Collection
    {
        return $this->candidatureOffre;
    }

    public function addCandidatureOffre(CandidatureOffre $candidatureOffre): self
    {
        if (!$this->candidatureOffre->contains($candidatureOffre)) {
            $this->candidatureOffre[] = $candidatureOffre;
            $candidatureOffre->setOffreDeTravail($this);
        }

        return $this;
    }

    public function removeCandidatureOffre(CandidatureOffre $candidatureOffre): self
    {
        if ($this->candidatureOffre->removeElement($candidatureOffre)) {
            // set the owning side to null (unless already changed)
            if ($candidatureOffre->getOffreDeTravail() === $this) {
                $candidatureOffre->setOffreDeTravail(null);
            }
        }

        return $this;
    }
}
