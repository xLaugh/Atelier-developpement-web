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

    public function searchOutils(string $search, int $page = 1, int $limit = 48): array
    {
        // Pour l'instant, on utilise la méthode existante et on filtre côté PHP
        // Dans une vraie application, on ferait la recherche en base de données
        $allOutils = $this->listerOutils();
        $filteredOutils = array_filter($allOutils, function($outil) use ($search) {
            return stripos($outil->getName(), $search) !== false || 
                   stripos($outil->getDescription() ?? '', $search) !== false;
        });
        
        $offset = ($page - 1) * $limit;
        return array_slice($filteredOutils, $offset, $limit);
    }

    public function countSearchOutils(string $search): int
    {
        $allOutils = $this->listerOutils();
        $filteredOutils = array_filter($allOutils, function($outil) use ($search) {
            return stripos($outil->getName(), $search) !== false || 
                   stripos($outil->getDescription() ?? '', $search) !== false;
        });
        return count($filteredOutils);
    }

    public function listerOutilsPaginated(int $page = 1, int $limit = 48, ?int $categoryId = null): array
    {
        $allOutils = $categoryId ? $this->listerOutilsParCategorie($categoryId) : $this->listerOutils();
        $offset = ($page - 1) * $limit;
        return array_slice($allOutils, $offset, $limit);
    }

    public function countOutils(?int $categoryId = null): int
    {
        $allOutils = $categoryId ? $this->listerOutilsParCategorie($categoryId) : $this->listerOutils();
        return count($allOutils);
    }
}
