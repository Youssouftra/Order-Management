<?php

namespace App\Entity;

class Complement extends Produit
{
    public const TYPE_BOISSON = 'BOISSON';
    public const TYPE_FRITE = 'FRITES';
    public const TYPE_DESSERT = 'DESSERT';
    public const TYPE_SAUCE = 'SAUCE';
    
    public function getType(): ?string { return $this->getTypeComplement(); }
    public function setType(string $type): static { return $this->setTypeComplement($type); }
    public function getTypeLabel(): string { return $this->getTypeComplement() ?? ''; }
}
