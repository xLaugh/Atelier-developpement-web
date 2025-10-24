<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\spi\ModelRepositoryInterface;
use App\domain\entities\Model;

class UpdateModelUseCase
{
    public function __construct(
        private ModelRepositoryInterface $modelRepository
    ) {}

    public function execute(int $id, string $name): Model
    {
        $model = $this->modelRepository->findById($id);
        if (!$model) {
            throw new \Exception('Modèle non trouvé');
        }

        $model->setName($name);
        return $this->modelRepository->update($model);
    }
}
