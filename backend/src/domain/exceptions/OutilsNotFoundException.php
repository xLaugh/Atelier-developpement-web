<?php

namespace App\domain\exceptions;

class OutilsNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Outil avec l'ID {$id} non trouvé");
    }
}
