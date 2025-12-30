<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'La quantité doit être positive')]
    private int $quantite = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $sousTotal = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    private ?Burger $burger = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    private ?Complement $complement = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    private ?Menu $menu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        $this->calculerSousTotal();
        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        $this->calculerSousTotal();
        return $this;
    }

    public function getSousTotal(): ?string
    {
        return $this->sousTotal;
    }

    public function setSousTotal(string $sousTotal): static
    {
        $this->sousTotal = $sousTotal;
        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getBurger(): ?Burger
    {
        return $this->burger;
    }

    public function setBurger(?Burger $burger): static
    {
        $this->burger = $burger;
        if ($burger) {
            $this->prixUnitaire = $burger->getPrix();
            $this->calculerSousTotal();
        }
        return $this;
    }

    public function getComplement(): ?Complement
    {
        return $this->complement;
    }

    public function setComplement(?Complement $complement): static
    {
        $this->complement = $complement;
        if ($complement) {
            $this->prixUnitaire = $complement->getPrix();
            $this->calculerSousTotal();
        }
        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;
        if ($menu) {
            $this->prixUnitaire = $menu->getPrix();
            $this->calculerSousTotal();
        }
        return $this;
    }

    public function getProduit(): Burger|Complement|Menu|null
    {
        return $this->burger ?? $this->complement ?? $this->menu;
    }

    public function getProduitNom(): string
    {
        $produit = $this->getProduit();
        return $produit ? $produit->getNom() : 'Produit inconnu';
    }

    public function getProduitType(): string
    {
        if ($this->burger) return 'Burger';
        if ($this->complement) return 'Complément';
        if ($this->menu) return 'Menu';
        return 'Inconnu';
    }

    public function calculerSousTotal(): void
    {
        if ($this->prixUnitaire !== null) {
            $this->sousTotal = bcmul($this->prixUnitaire, (string)$this->quantite, 2);
        }
    }
}
