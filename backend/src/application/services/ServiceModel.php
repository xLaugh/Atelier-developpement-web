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

    public function findAll(): array
    {
        return $this->modelRepository->findAll();
    }

    public function findById(int $id): ?Model
    {
        return $this->modelRepository->findById($id);
    }

    public function create(Model $model): Model
    {
        return $this->modelRepository->create($model);
    }

    public function update(int $id, string $name): Model
    {
        $model = $this->modelRepository->findById($id);
        if (!$model) {
            throw new \Exception('Modèle non trouvé');
        }

        $model->setName($name);
        return $this->modelRepository->update($model);
    }
}
