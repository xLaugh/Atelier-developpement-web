<?php

namespace App\application\ports\spi;

use App\domain\entities\Item;

interface ItemRepositoryInterface
{
    public function findById(int $id): ?Item;
    public function findByModelId(int $modelId): array;
    public function findLibresByModelId(int $modelId): array;
    public function save(Item $item): void;
    public function update(Item $item): void;
    public function countByModelId(int $modelId): int;
    public function countLibresByModelId(int $modelId): int;
}
