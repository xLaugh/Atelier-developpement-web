<?php

namespace App\infrastructure\repositories;

use App\application\ports\spi\ItemRepositoryInterface;
use App\domain\entities\Item;
use PDO;

class PDOItemRepository implements ItemRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Item
    {
        $sql = "SELECT id, model_id, status, created_at, updated_at FROM items WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToItem($row);
    }

    public function findByModelId(int $modelId): array
    {
        $sql = "SELECT id, model_id, status, created_at, updated_at FROM items WHERE model_id = :model_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':model_id', $modelId, PDO::PARAM_INT);
        $stmt->execute();

        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $this->mapRowToItem($row);
        }

        return $items;
    }

    public function findLibresByModelId(int $modelId): array
    {
        $sql = "SELECT id, model_id, status, created_at, updated_at FROM items 
                WHERE model_id = :model_id AND status = :status";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':model_id', $modelId, PDO::PARAM_INT);
        $stmt->bindValue(':status', Item::STATUS_LIBRE, PDO::PARAM_INT);
        $stmt->execute();

        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $this->mapRowToItem($row);
        }

        return $items;
    }

    public function save(Item $item): void
    {
        $sql = "INSERT INTO items (model_id, status, created_at, updated_at) 
                VALUES (:model_id, :status, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':model_id', $item->getModelId(), PDO::PARAM_INT);
        $stmt->bindValue(':status', $item->getStatus(), PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $item->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':updated_at', $item->getUpdatedAt()->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function update(Item $item): void
    {
        $sql = "UPDATE items SET status = :status, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $item->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':status', $item->getStatus(), PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $item->getUpdatedAt()->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function countByModelId(int $modelId): int
    {
        $sql = "SELECT COUNT(*) FROM items WHERE model_id = :model_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':model_id', $modelId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function countLibresByModelId(int $modelId): int
    {
        $sql = "SELECT COUNT(*) FROM items WHERE model_id = :model_id AND status = :status";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':model_id', $modelId, PDO::PARAM_INT);
        $stmt->bindValue(':status', Item::STATUS_LIBRE, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function mapRowToItem(array $row): Item
    {
        return new Item(
            id: (int) $row['id'],
            modelId: (int) $row['model_id'],
            status: (int) $row['status'],
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
    }
}
