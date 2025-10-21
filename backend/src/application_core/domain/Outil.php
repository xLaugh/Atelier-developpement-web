<?php
declare(strict_types=1);

namespace App\application_core\domain;

class Outil
{
    public function __construct(
        private int $id,
        private string $name,
        private ?string $brand,
        private ?string $imageUrl,
        private ?float $pricePerDay,
        private ?string $description,
        private string $category,
        private int $exemplaires
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name;}
    public function getBrand(): ?string { return $this->brand;}
    public function getImageUrl(): ?string{ return $this->imageUrl;}
    public function getPricePerDay(): ?float{ return $this->pricePerDay;}
    public function getDescription(): ?string{ return $this->description;}
    public function getCategory(): string{ return $this->category;}
    public function getExemplaires(): int{ return $this->exemplaires;}
    public function isAvailable(): bool{ return $this->exemplaires > 0;}
}
