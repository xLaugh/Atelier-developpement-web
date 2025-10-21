<?php

namespace App\domain\entities;

class Outil
{
    private int $id;
    private int $categoryId;
    private string $name;
    private ?string $brand;
    private ?string $imageUrl;
    private ?float $pricePerDay;
    private ?string $description;
    private \DateTimeImmutable $createdAt;
    private int $exemplairesCount;

    public function __construct(
        int $id,
        int $categoryId,
        string $name,
        ?string $brand = null,
        ?string $imageUrl = null,
        ?float $pricePerDay = null,
        ?string $description = null,
        \DateTimeImmutable $createdAt,
        int $exemplairesCount = 0
    ) {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->brand = $brand;
        $this->imageUrl = $imageUrl;
        $this->pricePerDay = $pricePerDay;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->exemplairesCount = $exemplairesCount;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getPricePerDay(): ?float
    {
        return $this->pricePerDay;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExemplairesCount(): int
    {
        return $this->exemplairesCount;
    }

    public function setExemplairesCount(int $count): void
    {
        $this->exemplairesCount = $count;
    }

    public function isAvailable(): bool
    {
        return $this->exemplairesCount > 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'image_url' => $this->imageUrl,
            'price_per_day' => $this->pricePerDay,
            'description' => $this->description,
            'exemplaires' => $this->exemplairesCount,
            'available' => $this->isAvailable(),
            '_links' => [
                'self' => ['href' => '/api/outils/' . $this->id],
                'collection' => ['href' => '/api/outils'],
            ]
        ];
    }
}
