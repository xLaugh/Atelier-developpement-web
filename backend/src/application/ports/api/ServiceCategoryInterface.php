<?php

namespace App\application\ports\api;

use App\domain\entities\Category;

interface ServiceCategoryInterface
{
    public function listerCategories(): array;
    public function obtenirCategorie(int $id): ?Category;
}
