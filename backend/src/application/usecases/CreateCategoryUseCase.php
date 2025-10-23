<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\spi\CategoryRepositoryInterface;
use App\domain\entities\Category;

class CreateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(string $name): Category
    {
        $category = new Category(0, $name, '');
        $this->categoryRepository->save($category);
        
        // Récupérer l'ID généré - on va chercher la catégorie par nom
        $categories = $this->categoryRepository->findAll();
        foreach ($categories as $cat) {
            if ($cat->getName() === $name) {
                return $cat;
            }
        }
        
        throw new \Exception('Erreur lors de la création de la catégorie');
    }
}
