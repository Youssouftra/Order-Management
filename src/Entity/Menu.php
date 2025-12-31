<?php

namespace App\Entity;

class Menu extends Produit
{
    public function getBurgers() { return $this->getBurger() ? [$this->getBurger()] : []; }
    public function getComplements() { return []; }
    public function addBurger($burger) { return $this; }
    public function addComplement($complement) { return $this; }
}
