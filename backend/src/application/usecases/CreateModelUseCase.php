<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\spi\ModelRepositoryInterface;
use App\domain\entities\Model;

class CreateModelUseCase
{
    public function __construct(
        private ModelRepositoryInterface $modelRepository
    ) {}

    public function execute(int $categoryId, string $name, ?string $imageUrl = null): Model
    {
        $model = new Model();
        $model->setCategoryId($categoryId);
        $model->setName($name);
        $model->setImageUrl($imageUrl);
        return $this->modelRepository->create($model);
    }
}
