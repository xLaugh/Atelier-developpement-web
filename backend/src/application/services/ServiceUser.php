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

    public function getUserReservations(int $userId): array
    {
        // Connexion à la base de données
        $db = \App\db\Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                r.quantity,
                r.start_date,
                r.end_date,
                r.status,
                r.total_price,
                r.created_at,
                m.name as model_name,
                m.description as model_description,
                m.image_url,
                c.name as category_name
            FROM reservations r
            JOIN models m ON r.model_id = m.id
            JOIN categories c ON m.category_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");
        
        $stmt->execute([$userId]);
        $reservations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $reservations;
    }
}
