<?php

namespace App\application\usecases;

use App\application\ports\spi\CategoryRepositoryInterface;

class ListCategoriesUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(): array
    {
        return $this->categoryRepository->findAll();
    }
}
