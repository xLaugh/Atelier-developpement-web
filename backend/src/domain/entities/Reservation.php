<?php
declare(strict_types=1);

namespace App\domain\entities;

class Reservation
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    public function __construct(
        private int $id,
        private int $userId,
        private int $modelId,
        private int $quantity,
        private \DateTimeImmutable $startDate,
        private \DateTimeImmutable $endDate,
        private string $status,
        private ?float $totalPrice,
        private \DateTimeImmutable $createdAt
    ) {}

    public function getId(): int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getModelId(): int { return $this->modelId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getStartDate(): \DateTimeImmutable { return $this->startDate; }
    public function getEndDate(): \DateTimeImmutable { return $this->endDate; }
    public function getStatus(): string { return $this->status; }
    public function getTotalPrice(): ?float { return $this->totalPrice; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getDuration(): int { return $this->startDate->diff($this->endDate)->days + 1; }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'model_id' => $this->getModelId(),
            'quantity' => $this->getQuantity(),
            'start_date' => $this->getStartDate()->format('Y-m-d'),
            'end_date' => $this->getEndDate()->format('Y-m-d'),
            'status' => $this->getStatus(),
            'total_price' => $this->getTotalPrice(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'duration' => $this->getDuration(),
        ];
    }
}


