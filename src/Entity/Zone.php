<?php

namespace App\Entity;

use App\Repository\ZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
class Zone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Les frais de livraison sont obligatoires')]
    #[Assert\PositiveOrZero(message: 'Les frais de livraison doivent être positifs ou nuls')]
    private ?string $fraisLivraison = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Livreur::class)]
    private Collection $livreurs;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: District::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $districts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->livreurs = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->districts = new ArrayCollection();
    }

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

    public function getFraisLivraison(): ?string
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(string $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
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

    public function getLivreurs(): Collection
    {
        return $this->livreurs;
    }

    public function addLivreur(Livreur $livreur): static
    {
        if (!$this->livreurs->contains($livreur)) {
            $this->livreurs->add($livreur);
            $livreur->setZone($this);
        }
        return $this;
    }

    public function removeLivreur(Livreur $livreur): static
    {
        if ($this->livreurs->removeElement($livreur)) {
            if ($livreur->getZone() === $this) {
                $livreur->setZone(null);
            }
        }
        return $this;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function getDistricts(): Collection
    {
        return $this->districts;
    }

    public function addDistrict(District $district): static
    {
        if (!$this->districts->contains($district)) {
            $this->districts->add($district);
            $district->setZone($this);
        }
        return $this;
    }

    public function removeDistrict(District $district): static
    {
        if ($this->districts->removeElement($district)) {
            if ($district->getZone() === $this) {
                $district->setZone(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}
