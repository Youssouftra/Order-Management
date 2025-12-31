<?php

namespace App\Entity;

use App\Repository\BurgerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BurgerRepository::class)]
#[ORM\Table(name: 'produits')]
class Burger extends Produit
{
    public function __construct()
    {
        parent::__construct();
        $this->setTypeProduit('BURGER');
    }
}
