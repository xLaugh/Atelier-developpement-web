<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\spi\ReservationRepositoryInterface;
use App\application\ports\spi\ItemRepositoryInterface;
use App\application\services\ServicePayment; // ✅ Ajout du service de paiement simulé
use App\domain\entities\Reservation;

class CreatePeriodReservationAction
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private ItemRepositoryInterface $itemRepository,
        private ServicePayment $paymentService // ✅ Injection du service
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $items = $data['items'] ?? [];
            $paymentToken = $data['payment_token'] ?? null; // ✅ Ajout du token
            $userId = 1; // ⚠️ à remplacer plus tard par l’utilisateur authentifié

            if (empty($items)) {
                $response->getBody()->write(json_encode([
                    'error' => 'no_items',
                    'message' => 'Aucun article dans la réservation'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            if (!$paymentToken) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_payment_token',
                    'message' => 'Le token de paiement est requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // 💳 Simulation du paiement avant la réservation
            $totalAmount = 0;
            foreach ($items as $item) {
                $prix = isset($item['prix_total']) ? (float)$item['prix_total'] : 10.0;
                $totalAmount += $prix;
            }

            $paymentSuccess = $this->paymentService->processPayment($paymentToken, $totalAmount);

            if (!$paymentSuccess) {
                $response->getBody()->write(json_encode([
                    'error' => 'payment_failed',
                    'message' => 'Le paiement a échoué (simulation)'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(402)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // ✅ Si paiement réussi → création des réservations
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

                // Vérifie la disponibilité
                $totalAvailable = $this->itemRepository->countLibresByModelId($modelId);
                $alreadyReserved = $this->reservationRepository->countOverlappingReservations($modelId, $start, $end);
                $availableForPeriod = $totalAvailable - $alreadyReserved;

                if ($availableForPeriod < $quantity) {
                    $errors[] = "Pas assez d'exemplaires disponibles pour l'outil ID: $modelId. Disponible: $availableForPeriod, Demandé: $quantity";
                    continue;
                }

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
                $response->getBody()->write(json_encode([
                    'error' => 'reservation_errors',
                    'errors' => $errors
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // 🧾 Réponse finale
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Réservation confirmée et paiement validé (simulation)',
                'payment_token' => $paymentToken,
                'reservations' => $created
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
