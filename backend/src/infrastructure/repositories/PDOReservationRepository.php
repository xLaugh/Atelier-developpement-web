<?php
declare(strict_types=1);

namespace App\infrastructure\repositories;

use App\application\ports\spi\ReservationRepositoryInterface;
use App\domain\entities\Reservation;
use PDO;

class PDOReservationRepository implements ReservationRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function save(Reservation $reservation): void
    {
        $sql = "INSERT INTO reservations (user_id, model_id, quantity, start_date, end_date, status, total_price, created_at)
                VALUES (:user_id, :model_id, :quantity, :start_date, :end_date, :status, :total_price, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $reservation->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':model_id', $reservation->getModelId(), PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $reservation->getQuantity(), PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $reservation->getStartDate()->format('Y-m-d'));
        $stmt->bindValue(':end_date', $reservation->getEndDate()->format('Y-m-d'));
        $stmt->bindValue(':status', $reservation->getStatus());
        $stmt->bindValue(':total_price', $reservation->getTotalPrice());
        $stmt->bindValue(':created_at', $reservation->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function findOverlappingReservations(int $modelId, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, user_id, model_id, quantity, start_date, end_date, status, total_price, created_at
             FROM reservations
             WHERE model_id = :model_id
               AND status IN ('pending','confirmed')
               AND (start_date <= :end_date AND end_date >= :start_date)"
        );
        $stmt->bindValue(':model_id', $modelId, PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startDate->format('Y-m-d'));
        $stmt->bindValue(':end_date', $endDate->format('Y-m-d'));
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countOverlappingReservations(int $modelId, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(quantity),0) FROM reservations
             WHERE model_id = :model_id
               AND status IN ('pending','confirmed')
               AND (start_date <= :end_date AND end_date >= :start_date)"
        );
        $stmt->bindValue(':model_id', $modelId, PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startDate->format('Y-m-d'));
        $stmt->bindValue(':end_date', $endDate->format('Y-m-d'));
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}


