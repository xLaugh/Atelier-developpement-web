<?php

namespace App\application\usecases;

use App\application\ports\spi\UserRepositoryInterface;
use App\domain\entities\User;

class FindUserByIdUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }
}


