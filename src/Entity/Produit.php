<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produits')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = '0.00';

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: 'type_produit', type: 'string', length: 50)]
    private ?string $typeProduit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ingredients = null;

    #[ORM\Column(name: 'type_complement', type: 'string', length: 50, nullable: true)]
    private ?string $typeComplement = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'burger_id', nullable: true)]
    private ?self $burger = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'boisson_id', nullable: true)]
    private ?self $boisson = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'frites_id', nullable: true)]
    private ?self $frites = null;

    #[ORM\Column]
    private bool $archived = false;

    #[ORM\Column]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): static { $this->prix = $prix; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): static { $this->image = $image; return $this; }
    public function getTypeProduit(): ?string { return $this->typeProduit; }
    public function setTypeProduit(string $typeProduit): static { $this->typeProduit = $typeProduit; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getIngredients(): ?string { return $this->ingredients; }
    public function setIngredients(?string $ingredients): static { $this->ingredients = $ingredients; return $this; }
    public function getTypeComplement(): ?string { return $this->typeComplement; }
    public function setTypeComplement(?string $typeComplement): static { $this->typeComplement = $typeComplement; return $this; }
    public function getBurger(): ?self { return $this->burger; }
    public function setBurger(?self $burger): static { $this->burger = $burger; return $this; }
    public function getBoisson(): ?self { return $this->boisson; }
    public function setBoisson(?self $boisson): static { $this->boisson = $boisson; return $this; }
    public function getFrites(): ?self { return $this->frites; }
    public function setFrites(?self $frites): static { $this->frites = $frites; return $this; }
    public function isArchived(): bool { return $this->archived; }
    public function setArchived(bool $archived): static { $this->archived = $archived; return $this; }
    public function isDisponible(): bool { return $this->disponible; }
    public function setDisponible(bool $disponible): static { $this->disponible = $disponible; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
    public function getLigneCommandes(): Collection { return $this->ligneCommandes; }
    public function isActive(): bool { return !$this->archived && $this->disponible; }
    public function __toString(): string { return $this->nom ?? ''; }
}
