<?php

namespace App\Entity;

class Burger extends Produit
{
    public function setUpdatedAt($date) { return $this; }
}
