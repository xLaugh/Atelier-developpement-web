<?php
declare(strict_types=1);

namespace App\application\ports\spi;

use App\domain\entities\Reservation;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;
    public function findOverlappingReservations(int $modelId, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array;
    public function countOverlappingReservations(int $modelId, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): int;
}


