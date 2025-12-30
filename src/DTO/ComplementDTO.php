<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ComplementDTO
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    public ?string $nom = null;

    public ?string $description = null;

    #[Assert\NotBlank(message: 'Le prix est obligatoire')]
    #[Assert\Positive(message: 'Le prix doit être positif')]
    public ?float $prix = null;

    #[Assert\NotBlank(message: 'Le type est obligatoire')]
    public ?string $type = null;

    public ?string $image = null;

    public bool $isActive = true;
}
