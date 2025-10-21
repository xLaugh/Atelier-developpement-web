<?php

namespace App\application\services;

use App\application\ports\api\ServiceCategoryInterface;
use App\application\usecases\ListCategoriesUseCase;
use App\domain\entities\Category;

class ServiceCategory implements ServiceCategoryInterface
{
    public function __construct(
        private ListCategoriesUseCase $listCategoriesUseCase
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
}
