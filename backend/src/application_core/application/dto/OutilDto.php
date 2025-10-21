<?php
declare(strict_types=1);

namespace App\application_core\application\dto;

class OutilDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $brand,
        public readonly ?string $imageUrl,
        public readonly ?float $pricePerDay,
        public readonly ?string $description,
        public readonly string $category,
        public readonly int $exemplaires
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'image_url' => $this->imageUrl,
            'price_per_day' => $this->pricePerDay,
            'description' => $this->description,
            'category' => $this->category,
            'exemplaires' => $this->exemplaires,
            '_links' => [
                'self' => ['href' => '/api/outils/' . $this->id],
            ],
        ];
    }
}
