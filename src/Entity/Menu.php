<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'produits')]
class Menu extends Produit
{
    public function __construct()
    {
        parent::__construct();
        $this->setTypeProduit('MENU');
    }
    
    public function getBurgers() { return $this->getBurger() ? [$this->getBurger()] : []; }
    public function getComplements() { return []; }
}
