<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\spi\ReservationRepositoryInterface;
use App\application\ports\spi\ItemRepositoryInterface;
use App\domain\entities\Reservation;

class CreatePeriodReservationAction
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private ItemRepositoryInterface $itemRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $items = $data['items'] ?? [];
            $userId = 1;

            if (empty($items)) {
                $response->getBody()->write(json_encode(['error' => 'no_items', 'message' => 'Aucun article dans la réservation'], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $errors = [];
            $created = [];

            foreach ($items as $item) {
                $modelId = (int)($item['outil_id'] ?? 0);
                $quantity = (int)($item['quantite'] ?? 0);
                $startRaw = $item['start_date'] ?? null;
                $endRaw = $item['end_date'] ?? null;
                $totalPrice = isset($item['prix_total']) ? (float)$item['prix_total'] : null;

                if (!$modelId || !$quantity || !$startRaw || !$endRaw) {
                    $errors[] = "Données invalides pour l'article";
                    continue;
                }

                $start = new \DateTimeImmutable($startRaw);
                $end = new \DateTimeImmutable($endRaw);
                if ($end < $start) {
                    $errors[] = 'La date de fin doit être postérieure à la date de début';
                    continue;
                }

                // regdrde si la disponibilité pour ce modèle
                $totalAvailable = $this->itemRepository->countLibresByModelId($modelId);
                $alreadyReserved = $this->reservationRepository->countOverlappingReservations($modelId, $start, $end);
                $availableForPeriod = $totalAvailable - $alreadyReserved;
                
                if ($availableForPeriod < $quantity) {
                    $errors[] = "Pas assez d'exemplaires disponibles pour l'outil ID: $modelId. Disponible: $availableForPeriod, Demandé: $quantity";
                    continue;
                }
                
                // test si la quantité est supérieure au stock 
                if ($quantity > $totalAvailable) {
                    $errors[] = "Quantité demandée ($quantity) supérieure au stock total ($totalAvailable) pour l'outil ID: $modelId";
                    continue;
                }

                $reservation = new Reservation(
                    id: 0,
                    userId: $userId,
                    modelId: $modelId,
                    quantity: $quantity,
                    startDate: $start,
                    endDate: $end,
                    status: Reservation::STATUS_PENDING,
                    totalPrice: $totalPrice,
                    createdAt: new \DateTimeImmutable()
                );
                $this->reservationRepository->save($reservation);
                $created[] = $reservation->toArray();
            }

            if ($errors) {
                $response->getBody()->write(json_encode(['error' => 'reservation_errors', 'errors' => $errors], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $response->getBody()->write(json_encode(['success' => true, 'reservations' => $created], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'server_error', 'message' => 'Erreur serveur: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}


