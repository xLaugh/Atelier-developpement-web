<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\spi\ItemRepositoryInterface;
use App\application\ports\spi\ReservationRepositoryInterface;

class CheckAvailabilityAction
{
    public function __construct(
        private ItemRepositoryInterface $itemRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $modelId = (int)($queryParams['model_id'] ?? 0);
            $startDate = $queryParams['start_date'] ?? null;
            $endDate = $queryParams['end_date'] ?? null;
            $quantity = (int)($queryParams['quantity'] ?? 1);

            if (!$modelId || !$startDate || !$endDate) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_params',
                    'message' => 'model_id, start_date et end_date sont requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $start = new \DateTimeImmutable($startDate);
            $end = new \DateTimeImmutable($endDate);

            if ($end < $start) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_dates',
                    'message' => 'La date de fin doit être postérieure à la date de début'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // Vérifier la disponibilité
            $totalAvailable = $this->itemRepository->countLibresByModelId($modelId);
            $alreadyReserved = $this->reservationRepository->countOverlappingReservations($modelId, $start, $end);
            $availableForPeriod = $totalAvailable - $alreadyReserved;

            $isAvailable = $availableForPeriod >= $quantity;

            $response->getBody()->write(json_encode([
                'available' => $isAvailable,
                'total_stock' => $totalAvailable,
                'already_reserved' => $alreadyReserved,
                'available_for_period' => $availableForPeriod,
                'requested_quantity' => $quantity,
                'can_reserve' => $isAvailable
            ], JSON_UNESCAPED_UNICODE));
            
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
