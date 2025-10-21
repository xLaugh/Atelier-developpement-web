<?php

namespace App\application\usecases;

use App\application\ports\spi\OutilRepositoryInterface;
use App\application\ports\spi\ItemRepositoryInterface;

class ListOutilsUseCase
{
    public function __construct(
        private OutilRepositoryInterface $outilRepository,
        private ItemRepositoryInterface $itemRepository
    ) {}

    public function execute(?int $categoryId = null): array
    {
        if ($categoryId) {
            $outils = $this->outilRepository->findByCategoryId($categoryId);
        } else {
            $outils = $this->outilRepository->findAll();
        }

        // Enrichir chaque outil avec le nombre d'exemplaires disponibles
        foreach ($outils as $outil) {
            $exemplairesCount = $this->itemRepository->countLibresByModelId($outil->getId());
            $outil->setExemplairesCount($exemplairesCount);
        }

        return $outils;
    }
}
