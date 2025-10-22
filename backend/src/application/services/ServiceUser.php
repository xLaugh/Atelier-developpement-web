<?php

namespace App\application\services;

use App\application\ports\api\ServiceUserInterface;
use App\application\usecases\AuthenticateUserUseCase;
use App\application\usecases\CreateUserUseCase;
use App\application\usecases\FindUserByIdUseCase;
use App\domain\entities\User;

class ServiceUser implements ServiceUserInterface
{
    public function __construct(
        private AuthenticateUserUseCase $authenticateUserUseCase,
        private CreateUserUseCase $createUserUseCase,
        private FindUserByIdUseCase $findUserByIdUseCase
    ) {}

    public function authenticate(string $email, string $password): ?User
    {
        return $this->authenticateUserUseCase->execute($email, $password);
    }

    public function findById(int $id): ?User
    {
        return $this->findUserByIdUseCase->execute($id);
    }

    public function createUser(string $prenom, string $nom, string $email, string $password, string $role = 'user'): User
    {
        return $this->createUserUseCase->execute($prenom, $nom, $email, $password, $role);
    }
}
