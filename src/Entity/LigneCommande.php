<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
#[ORM\Table(name: 'commande_lignes')]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $quantite = 1;

    #[ORM\Column(name: 'prix_unitaire', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(name: 'sous_total', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $sousTotal = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(name: 'commande_id', nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(name: 'produit_id')]
    private ?Produit $produit = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; $this->calculerSousTotal(); return $this; }
    public function getPrixUnitaire(): ?string { return $this->prixUnitaire; }
    public function setPrixUnitaire(string $prixUnitaire): static { $this->prixUnitaire = $prixUnitaire; $this->calculerSousTotal(); return $this; }
    public function getSousTotal(): ?string { return $this->sousTotal; }
    public function setSousTotal(string $sousTotal): static { $this->sousTotal = $sousTotal; return $this; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }
    
    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): static { 
        $this->produit = $produit; 
        if ($produit) { 
            $this->prixUnitaire = $produit->getPrix(); 
            $this->calculerSousTotal(); 
        } 
        return $this; 
    }
    
    public function getBurger(): ?Burger { return $this->produit instanceof Burger ? $this->produit : null; }
    public function setBurger(?Burger $burger): static { return $this->setProduit($burger); }
    public function getComplement(): ?Complement { return $this->produit instanceof Complement ? $this->produit : null; }
    public function setComplement(?Complement $complement): static { return $this->setProduit($complement); }
    public function getMenu(): ?Menu { return $this->produit instanceof Menu ? $this->produit : null; }
    public function setMenu(?Menu $menu): static { return $this->setProduit($menu); }
    
    public function getProduitNom(): string { return $this->produit ? $this->produit->getNom() : 'Produit inconnu'; }
    public function getProduitType(): string { return $this->produit?->getTypeProduit() ?? 'Inconnu'; }
    
    public function calculerSousTotal(): void
    {
        if ($this->prixUnitaire !== null) {
            $this->sousTotal = bcmul($this->prixUnitaire, (string)$this->quantite, 2);
        }
    }
}
