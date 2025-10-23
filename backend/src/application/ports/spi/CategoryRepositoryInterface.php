<?php

namespace App\application\ports\spi;

use App\domain\entities\Category;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;
    public function findAll(): array;
    public function save(Category $category): void;
    public function update(Category $category): Category;
}
