<?php

namespace App\infrastructure\repositories;

use App\application\ports\spi\OutilRepositoryInterface;
use App\domain\entities\Outil;
use PDO;

class PDOOutilRepository implements OutilRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Outil
    {
        $sql = "SELECT id, category_id, name, brand, image_url, price_per_day, description, created_at 
                FROM models WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToOutil($row);
    }

    public function findAll(): array
    {
        $sql = "SELECT id, category_id, name, brand, image_url, price_per_day, description, created_at 
                FROM models ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $outils = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $outils[] = $this->mapRowToOutil($row);
        }

        return $outils;
    }

    public function findByCategoryId(int $categoryId): array
    {
        $sql = "SELECT id, category_id, name, brand, image_url, price_per_day, description, created_at 
                FROM models WHERE category_id = :category_id ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        $outils = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $outils[] = $this->mapRowToOutil($row);
        }

        return $outils;
    }

    public function save(Outil $outil): void
    {
        $sql = "INSERT INTO models (category_id, name, brand, image_url, price_per_day, description, created_at) 
                VALUES (:category_id, :name, :brand, :image_url, :price_per_day, :description, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $outil->getCategoryId(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $outil->getName());
        $stmt->bindValue(':brand', $outil->getBrand());
        $stmt->bindValue(':image_url', $outil->getImageUrl());
        $stmt->bindValue(':price_per_day', $outil->getPricePerDay());
        $stmt->bindValue(':description', $outil->getDescription());
        $stmt->bindValue(':created_at', $outil->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function update(Outil $outil): Outil
    {
        $sql = "UPDATE models SET category_id = :category_id, name = :name, brand = :brand, 
                image_url = :image_url, price_per_day = :price_per_day, description = :description 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $outil->getCategoryId(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $outil->getName());
        $stmt->bindValue(':brand', $outil->getBrand());
        $stmt->bindValue(':image_url', $outil->getImageUrl());
        $stmt->bindValue(':price_per_day', $outil->getPricePerDay());
        $stmt->bindValue(':description', $outil->getDescription());
        $stmt->bindValue(':id', $outil->getId(), PDO::PARAM_INT);
        $stmt->execute();

        return $outil;
    }

    private function mapRowToOutil(array $row): Outil
    {
        return new Outil(
            id: (int) $row['id'],
            categoryId: (int) $row['category_id'],
            name: $row['name'],
            brand: $row['brand'],
            imageUrl: $row['image_url'],
            pricePerDay: $row['price_per_day'] ? (float) $row['price_per_day'] : null,
            description: $row['description'],
            createdAt: new \DateTimeImmutable($row['created_at'])
        );
    }
}
