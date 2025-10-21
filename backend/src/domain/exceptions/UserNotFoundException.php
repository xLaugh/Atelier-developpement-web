<?php

namespace App\domain\exceptions;

class UserNotFoundException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Utilisateur avec l'email {$email} non trouvé");
    }
}
