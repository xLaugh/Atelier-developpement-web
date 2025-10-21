<?php

namespace App\application\usecases;

use App\application\ports\spi\OutilRepositoryInterface;
use App\application\ports\spi\ItemRepositoryInterface;
use App\domain\entities\Outil;
use App\domain\exceptions\OutilsNotFoundException;

class GetOutilUseCase
{
    public function __construct(
        private OutilRepositoryInterface $outilRepository,
        private ItemRepositoryInterface $itemRepository
    ) {}

    public function execute(int $id): Outil
    {
        $outil = $this->outilRepository->findById($id);
        
        if (!$outil) {
            throw new OutilsNotFoundException($id);
        }

        // Enrichir l'outil avec le nombre d'exemplaires disponibles
        $exemplairesCount = $this->itemRepository->countLibresByModelId($outil->getId());
        $outil->setExemplairesCount($exemplairesCount);

        return $outil;
    }
}
