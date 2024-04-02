<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $durée = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimiteinscription = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infosSortie = null;

    #[ORM\Column(length: 20)]
    #[ORM\ManyToOne(targetEntity: Etat::class)]
    #[ORM\JoinColumn(name: 'etat', referencedColumnName: 'libelle')]
    private ?Etat $etat = null;

    #[ORM\Column(length: 255)]
    #[ORM\ManyToOne(targetEntity: Lieu::class)]
    #[ORM\JoinColumn(name: 'lieu', referencedColumnName: 'nom')]
    private ?Lieu $Lieu = null;

    #[ORM\Column(length: 255)]
    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(name: 'site', referencedColumnName: 'nom')]
    private ?Site $Site = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDurée(): ?\DateTimeInterface
    {
        return $this->durée;
    }

    public function setDurée(\DateTimeInterface $durée): static
    {
        $this->durée = $durée;

        return $this;
    }

    public function getDateLimiteinscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteinscription;
    }

    public function setDateLimiteinscription(\DateTimeInterface $dateLimiteinscription): static
    {
        $this->dateLimiteinscription = $dateLimiteinscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(?int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->Lieu;
    }

    public function setLieu(string $Lieu): static
    {
        $this->Lieu = $Lieu;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->Site;
    }

    public function setSite(string $Site): static
    {
        $this->Site = $Site;

        return $this;
    }
}
