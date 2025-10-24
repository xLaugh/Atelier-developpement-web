<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\spi\OutilRepositoryInterface;
use App\domain\entities\Outil;

class UpdateOutilUseCase
{
    public function __construct(
        private OutilRepositoryInterface $outilRepository
    ) {}

    public function execute(int $id, string $name, string $description, int $categoryId, int $modelId): Outil
    {
        $outil = $this->outilRepository->findById($id);
        if (!$outil) {
            throw new \Exception('Outil non trouvé');
        }

        $outil->setName($name);
        $outil->setDescription($description);
        $outil->setCategoryId($categoryId);
        $outil->setModelId($modelId);
        
        return $this->outilRepository->update($outil);
    }
}
