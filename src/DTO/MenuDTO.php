<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MenuDTO
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    public ?string $nom = null;

    public ?string $description = null;

    #[Assert\NotBlank(message: 'Le prix est obligatoire')]
    #[Assert\Positive(message: 'Le prix doit être positif')]
    public ?float $prix = null;

    public ?string $image = null;

    public array $burgerIds = [];

    public array $complementIds = [];

    public bool $isActive = true;
}
