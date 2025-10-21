<?php

namespace App\application\services;

use App\application\ports\api\ServiceOutilInterface;
use App\application\usecases\ListOutilsUseCase;
use App\application\usecases\GetOutilUseCase;
use App\domain\entities\Outil;

class ServiceOutil implements ServiceOutilInterface
{
    public function __construct(
        private ListOutilsUseCase $listOutilsUseCase,
        private GetOutilUseCase $getOutilUseCase
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
}
