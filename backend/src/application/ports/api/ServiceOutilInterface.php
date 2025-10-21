<?php

namespace App\application\ports\api;

use App\domain\entities\Outil;

interface ServiceOutilInterface
{
    public function listerOutils(): array;
    public function listerOutilsParCategorie(int $categoryId): array;
    public function obtenirOutil(int $id): ?Outil;
}
