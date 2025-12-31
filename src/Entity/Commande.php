<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commandes')]
#[ORM\Index(columns: ['statut'])]
#[ORM\Index(columns: ['date_commande'])]
#[ORM\Index(columns: ['zone_id'])]
#[ORM\Index(columns: ['livreur_id'])]
#[ORM\Index(columns: ['client_id'])]
class Commande
{
    public const ETAT_EN_ATTENTE = 'EN_ATTENTE';
    public const ETAT_EN_PREPARATION = 'EN_PREPARATION';
    public const ETAT_PRETE = 'PRETE';
    public const ETAT_EN_LIVRAISON = 'EN_LIVRAISON';
    public const ETAT_LIVREE = 'TERMINEE';
    public const ETAT_ANNULEE = 'ANNULEE';

    public const ETATS = [
        'EN_ATTENTE' => 'En attente',
        'EN_PREPARATION' => 'En préparation',
        'PRETE' => 'Prête',
        'EN_LIVRAISON' => 'En livraison',
        'TERMINEE' => 'Livrée',
        'ANNULEE' => 'Annulée',
    ];

    public const ETATS_COLORS = [
        'EN_ATTENTE' => 'warning',
        'EN_PREPARATION' => 'info',
        'PRETE' => 'primary',
        'EN_LIVRAISON' => 'secondary',
        'TERMINEE' => 'success',
        'ANNULEE' => 'danger',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'client_id', nullable: false)]
    private ?User $client = null;

    #[ORM\Column(name: 'date_commande', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(columnDefinition: 'statut_commande_enum')]
    private ?string $statut = 'EN_ATTENTE';

    #[ORM\Column(name: 'type_livraison', columnDefinition: 'type_livraison_enum')]
    private ?string $typeLivraison = 'LIVRAISON';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]
    private ?District $quartier = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'zone_id', nullable: true)]
    private ?Zone $zone = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'livreur_id', nullable: true)]
    private ?Livreur $livreur = null;

    #[ORM\Column(name: 'montant_total', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantTotal = '0.00';

    #[ORM\Column(name: 'frais_livraison', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $fraisLivraison = '0.00';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAtReal = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->createdAtReal = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getClient(): ?User { return $this->client; }
    public function setClient(?User $client): static { $this->client = $client; return $this; }
    
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; $this->updatedAt = new \DateTime(); return $this; }
    
    public function getTypeLivraison(): ?string { return $this->typeLivraison; }
    public function setTypeLivraison(string $typeLivraison): static { $this->typeLivraison = $typeLivraison; return $this; }
    
    public function getQuartier(): ?District { return $this->quartier; }
    public function setQuartier(?District $quartier): static { $this->quartier = $quartier; return $this; }
    
    public function getZone(): ?Zone { return $this->zone; }
    public function setZone(?Zone $zone): static { 
        $this->zone = $zone; 
        if ($zone) $this->fraisLivraison = $zone->getFraisLivraison();
        $this->updatedAt = new \DateTime();
        return $this; 
    }
    
    public function getLivreur(): ?Livreur { return $this->livreur; }
    public function setLivreur(?Livreur $livreur): static { $this->livreur = $livreur; return $this; }
    
    public function getMontantTotal(): ?string { return $this->montantTotal; }
    public function setMontantTotal(string $montantTotal): static { $this->montantTotal = $montantTotal; return $this; }
    
    public function getFraisLivraison(): ?string { return $this->fraisLivraison; }
    public function setFraisLivraison(string $fraisLivraison): static { $this->fraisLivraison = $fraisLivraison; return $this; }
    
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static { $this->notes = $notes; return $this; }
    
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
    
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
    
    public function getLigneCommandes(): Collection { return $this->ligneCommandes; }
    
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

    public function getMontantFinal(): string
    {
        return bcadd($this->montantTotal, $this->fraisLivraison, 2);
    }

    public function getNumero(): string
    {
        return 'CMD-' . str_pad((string)$this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getEtat(): string
    {
        return $this->statut ?? 'EN_ATTENTE';
    }

    public function setEtat(string $etat): static
    {
        return $this->setStatut($etat);
    }

    public function getEtatLabel(): string
    {
        return self::ETATS[$this->getEtat()] ?? $this->getEtat();
    }

    public function getEtatColor(): string
    {
        return self::ETATS_COLORS[$this->getEtat()] ?? 'secondary';
    }

    public function getClientNom(): string
    {
        return $this->client ? $this->client->getNom() . ' ' . $this->client->getPrenom() : '';
    }

    public function getClientTelephone(): string
    {
        return $this->client ? $this->client->getTelephone() : '';
    }

    public function getAdresseLivraison(): string
    {
        return $this->client ? ($this->client->getAdresse() ?? '') : '';
    }

    public function getModeLivraison(): string
    {
        return $this->typeLivraison ?? 'LIVRAISON';
    }

    public function __toString(): string
    {
        return $this->getNumero();
    }
}
