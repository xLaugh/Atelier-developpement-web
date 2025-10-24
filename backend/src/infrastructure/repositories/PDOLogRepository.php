<?php
declare(strict_types=1);

namespace App\infrastructure\repositories;

use PDO;

class PDOLogRepository
{
    public function __construct(private PDO $pdo) {}

    public function logAction(int $userId, ?int $itemId, string $action, ?string $details = null): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO logs (user_id, item_id, action, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $itemId, $action]);
    }

    public function getLogsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, m.name as model_name, c.name as category_name
            FROM logs l
            LEFT JOIN models m ON l.item_id = m.id
            LEFT JOIN categories c ON m.category_id = c.id
            WHERE l.user_id = ?
            ORDER BY l.created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
