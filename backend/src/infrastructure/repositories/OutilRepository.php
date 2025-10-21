<?php
declare(strict_types=1);

namespace App\infrastructure\repositories;

use App\application_core\application\ports\OutilRepositoryInterface;
use App\application_core\application\dto\OutilDto;
use PDO;

class OutilRepository implements OutilRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    /**
     * @return OutilDto[]
     */
    public function findAll(?int $categoryId = null): array
    {
        if ($categoryId !== null) {
            return $this->findByCategory($categoryId);
        }

        $stmt = $this->pdo->query(
            "SELECT m.id, m.name, m.brand, m.image_url, m.price_per_day, m.description, c.name AS category, COUNT(i.id) AS exemplaires
             FROM models m
             JOIN categories c ON c.id = m.category_id
             LEFT JOIN items i ON i.model_id = m.id
             GROUP BY m.id"
        );

        return $this->mapToDtos($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById(int $id): ?OutilDto
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.id, m.name, m.brand, m.image_url, m.price_per_day, m.description, c.name AS category, COUNT(i.id) AS exemplaires
             FROM models m
             JOIN categories c ON c.id = m.category_id
             LEFT JOIN items i ON i.model_id = m.id
             WHERE m.id = :id
             GROUP BY m.id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapToDto($row);
    }

    /**
     * @return OutilDto[]
     */
    public function findByCategory(int $categoryId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.id, m.name, m.brand, m.image_url, m.price_per_day, m.description, c.name AS category, COUNT(i.id) AS exemplaires
             FROM models m
             JOIN categories c ON c.id = m.category_id
             LEFT JOIN items i ON i.model_id = m.id
             WHERE c.id = :cid
             GROUP BY m.id"
        );
        $stmt->execute(['cid' => $categoryId]);

        return $this->mapToDtos($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param array[] $rows
     * @return OutilDto[]
     */
    private function mapToDtos(array $rows): array
    {
        return array_map([$this, 'mapToDto'], $rows);
    }

    private function mapToDto(array $row): OutilDto
    {
        return new OutilDto(
            id: (int)$row['id'],
            name: $row['name'],
            brand: $row['brand'],
            imageUrl: $row['image_url'],
            pricePerDay: $row['price_per_day'] ? (float)$row['price_per_day'] : null,
            description: $row['description'],
            category: $row['category'],
            exemplaires: (int)$row['exemplaires']
        );
    }
}
