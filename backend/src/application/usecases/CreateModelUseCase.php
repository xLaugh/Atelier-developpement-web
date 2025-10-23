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

    public function execute(string $name): Model
    {
        $model = new Model();
        $model->setName($name);
        return $this->modelRepository->create($model);
    }
}
