<?php

namespace App\domain\entities;

class User
{
    private int $id;
    private string $prenom;
    private string $nom;
    private string $email;
    private string $password;
    private string $role;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        int $id,
        string $prenom,
        string $nom,
        string $email,
        string $password,
        string $role,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}
