<?php

namespace App\application\services;

use App\application\ports\spi\ModelRepositoryInterface;
use App\domain\entities\Model;

class ServiceModel
{
    public function __construct(
        private ModelRepositoryInterface $modelRepository
    ) {}

    public function create(Model $model): Model
    {
        return $this->modelRepository->create($model);
    }

    public function createModel(int $categoryId, string $name, ?string $imageUrl = null): Model
    {
        $model = new Model();
        $model->setCategoryId($categoryId);
        $model->setName($name);
        $model->setImageUrl($imageUrl);
        return $this->modelRepository->create($model);
    }

    public function findAll(): array
    {
        return $this->modelRepository->findAll();
    }

    public function findById(int $id): ?Model
    {
        return $this->modelRepository->findById($id);
    }

    public function update(int $id, string $name, ?string $imageUrl = null, ?int $categoryId = null): Model
    {
        $model = $this->modelRepository->findById($id);
        if (!$model) {
            throw new \Exception('Modèle non trouvé');
        }

        $model->setName($name);
        $model->setImageUrl($imageUrl);
        if ($categoryId !== null) {
            $model->setCategoryId($categoryId);
        }
        return $this->modelRepository->update($model);
    }
}
