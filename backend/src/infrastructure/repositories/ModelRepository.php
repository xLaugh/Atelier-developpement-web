<?php

namespace App\infrastructure\repositories;

use App\application\ports\spi\ModelRepositoryInterface;
use App\domain\entities\Model;
use PDO;

class ModelRepository implements ModelRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function create(Model $model): Model
    {
        $stmt = $this->pdo->prepare("INSERT INTO models (name) VALUES (?)");
        $stmt->execute([$model->getName()]);
        
        $model->setId($this->pdo->lastInsertId());
        return $model;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM models ORDER BY name");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function($row) {
            $model = new Model();
            $model->setId($row['id']);
            $model->setName($row['name']);
            return $model;
        }, $results);
    }

    public function findById(int $id): ?Model
    {
        $stmt = $this->pdo->prepare("SELECT * FROM models WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        $model = new Model();
        $model->setId($row['id']);
        $model->setName($row['name']);
        return $model;
    }

    public function update(Model $model): Model
    {
        $stmt = $this->pdo->prepare("UPDATE models SET name = ? WHERE id = ?");
        $stmt->execute([$model->getName(), $model->getId()]);
        
        return $model;
    }
}
