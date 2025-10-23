<?php

namespace App\domain\entities;

class Category
{
    private int $id;
    private string $name;
    private ?string $description;

    public function __construct(
        int $id,
        string $name,
        ?string $description = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            '_links' => [
                'self' => ['href' => '/api/categories/' . $this->id],
                'outils' => ['href' => '/api/outils?category_id=' . $this->id],
            ]
        ];
    }
}
