<?php

namespace App\domain\entities;

class Item
{
    public const STATUS_LIBRE = 0;
    public const STATUS_PRIS = 1;

    private int $id;
    private int $modelId;
    private int $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        int $id,
        int $modelId,
        int $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->modelId = $modelId;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModelId(): int
    {
        return $this->modelId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isLibre(): bool
    {
        return $this->status === self::STATUS_LIBRE;
    }

    public function isPris(): bool
    {
        return $this->status === self::STATUS_PRIS;
    }

    public function prendre(): void
    {
        if ($this->isPris()) {
            throw new \RuntimeException('Item déjà pris');
        }
        $this->status = self::STATUS_PRIS;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function liberer(): void
    {
        if ($this->isLibre()) {
            throw new \RuntimeException('Item déjà libre');
        }
        $this->status = self::STATUS_LIBRE;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'model_id' => $this->modelId,
            'status' => $this->status,
            'is_libre' => $this->isLibre(),
            'is_pris' => $this->isPris(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}
