<?php
declare(strict_types=1);

namespace App\application_core\application\exceptions;

use Exception;

class InvalidCategoryException extends Exception
{
    public function __construct(int $categoryId)
    {
        parent::__construct("Catégorie avec l'ID {$categoryId} invalide", 400);
    }
}
