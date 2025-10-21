<?php
declare(strict_types=1);

namespace App\application_core\application\usecases;

use App\application_core\application\ports\OutilRepositoryInterface;
use App\application_core\application\dto\OutilDto;

class GetOutilsUseCase
{
    public function __construct(
        private OutilRepositoryInterface $outilRepository
    ) {}

    /**
     * @return OutilDto[]
     */
    public function execute(?int $categoryId = null): array
    {
        if ($categoryId !== null) {
            return $this->outilRepository->findByCategory($categoryId);
        }
        
        return $this->outilRepository->findAll();
    }
}
