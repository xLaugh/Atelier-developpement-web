<?php

namespace App\infrastructure\repositories;

use App\application\ports\spi\UserRepositoryInterface;
use App\domain\entities\User;
use PDO;

class PDOUserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?User
    {
        $sql = "SELECT id, prenom, nom, email, password, role, created_at FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT id, prenom, nom, email, password, role, created_at FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    public function save(User $user): void
    {
        $sql = "INSERT INTO users (prenom, nom, email, password, role, created_at) 
                VALUES (:prenom, :nom, :email, :password, :role, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':prenom', $user->getPrenom());
        $stmt->bindValue(':nom', $user->getNom());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':role', $user->getRole());
        $stmt->bindValue(':created_at', $user->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function findAll(): array
    {
        $sql = "SELECT id, prenom, nom, email, password, role, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapRowToUser($row);
        }

        return $users;
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            prenom: $row['prenom'],
            nom: $row['nom'],
            email: $row['email'],
            password: $row['password'],
            role: $row['role'],
            createdAt: new \DateTimeImmutable($row['created_at'])
        );
    }
}
