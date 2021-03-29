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
    private $job;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     * pattern = "/^[a-zA-Z]+$/i",
     * message = "vous ne devez saisir que des lettres"
     * )
     * @Groups("get:read")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=CandidatureOffre::class, mappedBy="offreDeTravail")
     */
    private $candidatureOffres;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="offreDeTravail")
     */
    private $categorie;

    public function __construct()
    {
        $this->candidatureOffres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): self
    {
        $this->job = $job;

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


    /**
     * @return Collection|CandidatureOffre[]
     */
    public function getCandidatureOffres(): Collection
    {
        return $this->candidatureOffres;
    }

    public function addCandidatureOffre(CandidatureOffre $candidatureOffre): self
    {
        if (!$this->candidatureOffres->contains($candidatureOffre)) {
            $this->candidatureOffres[] = $candidatureOffre;
            $candidatureOffre->setOffreDeTravail($this);
        }

        return $this;
    }

    public function removeCandidatureOffre(CandidatureOffre $candidatureOffre): self
    {
        if ($this->candidatureOffres->removeElement($candidatureOffre)) {
            // set the owning side to null (unless already changed)
            if ($candidatureOffre->getOffreDeTravail() === $this) {
                $candidatureOffre->setOffreDeTravail(null);
            }
        }

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
}
