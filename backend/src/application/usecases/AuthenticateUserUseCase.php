<?php

namespace App\application\usecases;

use App\application\ports\spi\UserRepositoryInterface;
use App\domain\entities\User;
use App\domain\exceptions\UserNotFoundException;

class AuthenticateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            throw new UserNotFoundException($email);
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }
}
