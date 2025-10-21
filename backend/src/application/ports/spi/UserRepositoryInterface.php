<?php

namespace App\application\ports\spi;

use App\domain\entities\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;
    public function findAll(): array;
}
