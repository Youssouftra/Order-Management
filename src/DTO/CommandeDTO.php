<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CommandeDTO
{
    #[Assert\NotBlank(message: 'Le nom du client est obligatoire')]
    public ?string $clientNom = null;

    #[Assert\NotBlank(message: 'Le téléphone est obligatoire')]
    public ?string $clientTelephone = null;

    #[Assert\NotBlank(message: 'L\'adresse est obligatoire')]
    public ?string $adresseLivraison = null;

    public ?int $zoneId = null;

    public ?string $notes = null;

    public array $items = [];
}
