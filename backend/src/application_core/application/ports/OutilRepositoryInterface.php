<?php
declare(strict_types=1);

namespace App\application_core\application\ports;

use App\application_core\application\dto\OutilDto;

interface OutilRepositoryInterface
{
    /**
     * @return OutilDto[]
     */
    public function findAll(?int $categoryId = null): array;

    public function findById(int $id): ?OutilDto;

    /**
     * @return OutilDto[]
     */
    public function findByCategory(int $categoryId): array;
}
