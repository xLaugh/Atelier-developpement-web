<?php

namespace App\application\usecases;

use App\application\ports\spi\UserRepositoryInterface;
use App\domain\entities\User;

class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(
        string $prenom,
        string $nom,
        string $email,
        string $password,
        string $role = 'user'
    ): User {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(
            id: 0, // Sera défini par la base de données
            prenom: $prenom,
            nom: $nom,
            email: $email,
            password: $hashedPassword,
            role: $role,
            createdAt: new \DateTimeImmutable()
        );

        $this->userRepository->save($user);
        return $user;
    }
}
