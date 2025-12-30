<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LivreurDTO
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    public ?string $nom = null;

    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    public ?string $prenom = null;

    #[Assert\NotBlank(message: 'Le téléphone est obligatoire')]
    public ?string $telephone = null;

    public ?int $zoneId = null;

    public ?string $photo = null;

    public string $statut = 'disponible';

    public bool $isActive = true;
}
