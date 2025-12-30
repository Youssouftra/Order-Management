<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Index(columns: ['etat'])]
#[ORM\Index(columns: ['created_at'])]
#[ORM\Index(columns: ['zone_id'])]
#[ORM\Index(columns: ['livreur_id'])]
class Commande
{
    public const ETAT_EN_ATTENTE = 'en_attente';
    public const ETAT_EN_PREPARATION = 'en_preparation';
    public const ETAT_PRETE = 'prete';
    public const ETAT_EN_LIVRAISON = 'en_livraison';
    public const ETAT_LIVREE = 'livree';
    public const ETAT_ANNULEE = 'annulee';

    public const ETATS = [
        self::ETAT_EN_ATTENTE => 'En attente',
        self::ETAT_EN_PREPARATION => 'En préparation',
        self::ETAT_PRETE => 'Prête',
        self::ETAT_EN_LIVRAISON => 'En livraison',
        self::ETAT_LIVREE => 'Livrée',
        self::ETAT_ANNULEE => 'Annulée',
    ];

    public const ETATS_COLORS = [
        self::ETAT_EN_ATTENTE => 'warning',
        self::ETAT_EN_PREPARATION => 'info',
        self::ETAT_PRETE => 'primary',
        self::ETAT_EN_LIVRAISON => 'secondary',
        self::ETAT_LIVREE => 'success',
        self::ETAT_ANNULEE => 'danger',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom du client est obligatoire')]
    private ?string $clientNom = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le téléphone du client est obligatoire')]
    private ?string $clientTelephone = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'L\'adresse de livraison est obligatoire')]
    private ?string $adresseLivraison = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = self::ETAT_EN_ATTENTE;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $fraisLivraison = '0.00';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Zone $zone = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Livreur $livreur = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->ligneCommandes = new ArrayCollection();
        $this->numero = 'CMD-' . strtoupper(uniqid());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function getClientNom(): ?string
    {
        return $this->clientNom;
    }

    public function setClientNom(string $clientNom): static
    {
        $this->clientNom = $clientNom;
        return $this;
    }

    public function getClientTelephone(): ?string
    {
        return $this->clientTelephone;
    }

    public function setClientTelephone(string $clientTelephone): static
    {
        $this->clientTelephone = $clientTelephone;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
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

    public function getEtatLabel(): string
    {
        return self::ETATS[$this->etat] ?? $this->etat;
    }

    public function getEtatColor(): string
    {
        return self::ETATS_COLORS[$this->etat] ?? 'secondary';
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getFraisLivraison(): ?string
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(string $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;
        return $this;
    }

    public function getMontantFinal(): string
    {
        return bcadd($this->montantTotal, $this->fraisLivraison, 2);
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;
        if ($zone) {
            $this->fraisLivraison = $zone->getFraisLivraison();
        }
        return $this;
    }

    public function getLivreur(): ?Livreur
    {
        return $this->livreur;
    }

    public function setLivreur(?Livreur $livreur): static
    {
        $this->livreur = $livreur;
        return $this;
    }

    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }
        return $this;
    }

    public function calculerMontantTotal(): void
    {
        $total = '0.00';
        foreach ($this->ligneCommandes as $ligne) {
            $total = bcadd($total, $ligne->getSousTotal(), 2);
        }
        $this->montantTotal = $total;
    }

    public function getModeLivraison(): ?string
    {
        return 'livraison';
    }

    public function getQuartier(): ?string
    {
        return null;
    }

    public function __toString(): string
    {
        return $this->numero ?? '';
    }
}
