<?php

namespace App\Entity;

use App\Repository\ComplementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplementRepository::class)]
#[ORM\Table(name: 'produits')]
class Complement extends Produit
{
    public function __construct()
    {
        parent::__construct();
        $this->setTypeProduit('COMPLEMENT');
    }
    
    public function getType(): ?string { return $this->getTypeComplement(); }
    public function setType(string $type): static { return $this->setTypeComplement($type); }
    public function getTypeLabel(): string { return $this->getTypeComplement() ?? ''; }
}
