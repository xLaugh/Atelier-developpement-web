<?php

namespace App\application\ports\api;

use App\domain\entities\User;

interface ServiceUserInterface
{
    public function authenticate(string $email, string $password): ?User;
    public function findById(int $id): ?User;
    public function createUser(string $prenom, string $nom, string $email, string $password, string $role = 'user'): User;
}
