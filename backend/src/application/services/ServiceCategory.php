<?php

namespace App\application\services;

use App\application\ports\api\ServiceCategoryInterface;
use App\application\usecases\ListCategoriesUseCase;
use App\application\usecases\CreateCategoryUseCase;
use App\application\usecases\UpdateCategoryUseCase;
use App\domain\entities\Category;

class ServiceCategory implements ServiceCategoryInterface
{
    public function __construct(
        private ListCategoriesUseCase $listCategoriesUseCase,
        private CreateCategoryUseCase $createCategoryUseCase,
        private UpdateCategoryUseCase $updateCategoryUseCase
    ) {}

    public function listerCategories(): array
    {
        return $this->listCategoriesUseCase->execute();
    }

    public function obtenirCategorie(int $id): ?Category
    {
        // Cette méthode sera implémentée si nécessaire
        return null;
    }

    public function creerCategorie(string $name): Category
    {
        return $this->createCategoryUseCase->execute($name);
    }

    public function update(int $id, string $name): Category
    {
        return $this->updateCategoryUseCase->execute($id, $name);
    }
}
