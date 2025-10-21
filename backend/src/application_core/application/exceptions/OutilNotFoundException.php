<?php
declare(strict_types=1);

namespace App\application_core\application\exceptions;

use Exception;

class OutilNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Outil avec l'ID {$id} introuvable", 404);
    }
}
