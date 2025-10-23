<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\spi\CategoryRepositoryInterface;
use App\domain\entities\Category;

class UpdateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(int $id, string $name): Category
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception('Catégorie non trouvée');
        }

        $category->setName($name);
        return $this->categoryRepository->update($category);
    }
}
