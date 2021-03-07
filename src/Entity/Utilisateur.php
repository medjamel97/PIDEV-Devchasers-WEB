<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motDePasse;

    /**
     * @ORM\OneToOne(targetEntity=Societe::class, inversedBy="utilisateur", cascade={"persist", "remove"})
     */
    private $societe;

    /**
     * @ORM\OneToOne(targetEntity=Candidat::class, inversedBy="utilisateur", cascade={"persist", "remove"})
     */
    private $candidat;

    /**
     * @ORM\Column(type="integer")
     */
    private $typeUtilisateur;


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

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(Societe $societe): self
    {
        $this->societe = $societe;

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

    public function getTypeUtilisateur(): ?int
    {
        return $this->typeUtilisateur;
    }

    public function setTypeUtilisateur(int $typeUtilisateur): self
    {
        $this->typeUtilisateur = $typeUtilisateur;

        return $this;
    }

}
