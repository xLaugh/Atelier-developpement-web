<?php

namespace App\application\ports\spi;

use App\domain\entities\Outil;

interface OutilRepositoryInterface
{
    public function findById(int $id): ?Outil;
    public function findAll(): array;
    public function findByCategoryId(int $categoryId): array;
    public function save(Outil $outil): void;
    public function update(Outil $outil): Outil;
}
