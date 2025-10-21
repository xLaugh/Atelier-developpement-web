<?php

namespace App\infrastructure\repositories;

use App\application\ports\spi\CategoryRepositoryInterface;
use App\domain\entities\Category;
use PDO;

class PDOCategoryRepository implements CategoryRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Category
    {
        $sql = "SELECT id, name, description FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToCategory($row);
    }

    public function findAll(): array
    {
        $sql = "SELECT id, name, description FROM categories ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $this->mapRowToCategory($row);
        }

        return $categories;
    }

    public function save(Category $category): void
    {
        $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $category->getName());
        $stmt->bindValue(':description', $category->getDescription());
        $stmt->execute();
    }

    private function mapRowToCategory(array $row): Category
    {
        return new Category(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description']
        );
    }
}
