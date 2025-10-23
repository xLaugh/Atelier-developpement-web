<?php

namespace App\application\services;

use App\application\ports\api\ServiceOutilInterface;
use App\application\usecases\ListOutilsUseCase;
use App\application\usecases\GetOutilUseCase;
use App\application\usecases\CreateOutilUseCase;
use App\application\usecases\UpdateOutilUseCase;
use App\domain\entities\Outil;

class ServiceOutil implements ServiceOutilInterface
{
    public function __construct(
        private ListOutilsUseCase $listOutilsUseCase,
        private GetOutilUseCase $getOutilUseCase,
        private CreateOutilUseCase $createOutilUseCase,
        private UpdateOutilUseCase $updateOutilUseCase
    ) {}

    public function listerOutils(): array
    {
        return $this->listOutilsUseCase->execute();
    }

    public function listerOutilsParCategorie(int $categoryId): array
    {
        return $this->listOutilsUseCase->execute($categoryId);
    }

    public function obtenirOutil(int $id): ?Outil
    {
        try {
            return $this->getOutilUseCase->execute($id);
        } catch (\App\domain\exceptions\OutilsNotFoundException $e) {
            return null;
        }
    }

    public function create(Outil $outil): Outil
    {
        return $this->createOutilUseCase->execute(
            $outil->getName(),
            $outil->getDescription(),
            $outil->getCategoryId(),
            $outil->getModelId() ?? 1 // Utilise le modelId de l'outil ou 1 par défaut
        );
    }

    public function update(int $id, string $name, string $description, int $categoryId, int $modelId): Outil
    {
        return $this->updateOutilUseCase->execute($id, $name, $description, $categoryId, $modelId);
    }
}
