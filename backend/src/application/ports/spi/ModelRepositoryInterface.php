<?php

namespace App\application\ports\spi;

use App\domain\entities\Model;

interface ModelRepositoryInterface
{
    public function create(Model $model): Model;
    public function findAll(): array;
    public function findById(int $id): ?Model;
    public function update(Model $model): Model;
}
