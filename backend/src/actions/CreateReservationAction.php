<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\spi\ItemRepositoryInterface;
use App\application\services\ServicePayment; // ✅ Nouveau service pour paiement simulé
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CreateReservationAction
{
    public function __construct(
        private ItemRepositoryInterface $itemRepository,
        private ServicePayment $paymentService // ✅ Injection du service de paiement
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            // 🔐 Vérification du token JWT
            $authHeader = $request->getHeaderLine('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                $response->getBody()->write(json_encode([
                    'error' => 'no_token',
                    'message' => 'Token manquant'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $token = substr($authHeader, 7);
            $settings = require __DIR__ . '/../../config/settings.php';
            $jwtConfig = $settings['jwt'];

            try {
                $decoded = JWT::decode($token, new Key($jwtConfig['secret'], $jwtConfig['algorithm']));
                $userId = $decoded->data->id;
            } catch (\Exception $e) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_token',
                    'message' => 'Token invalide'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // 📦 Données de la réservation
            $data = $request->getParsedBody();
            $items = $data['items'] ?? [];
            $paymentToken = $data['payment_token'] ?? null;

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
                $quantite = $item['quantite'] ?? 0;
                $prixUnitaire = $item['prix'] ?? 10.0; // prix fictif
                $totalAmount += $prixUnitaire * $quantite;
            }

            $paymentSuccess = $this->paymentService->processPayment($paymentToken, $totalAmount);

            if (!$paymentSuccess) {
                $response->getBody()->write(json_encode([
                    'error' => 'payment_failed',
                    'message' => 'Le paiement a échoué (simulation)'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(402)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // ✅ Si paiement ok → réserver les items
            $reservedItems = [];
            $errors = [];

            foreach ($items as $item) {
                $outilId = $item['outil_id'] ?? null;
                $quantite = $item['quantite'] ?? 0;
                $date = $item['date'] ?? null;

                if (!$outilId || $quantite <= 0 || !$date) {
                    $errors[] = "Données invalides pour l'article";
                    continue;
                }

                // Vérifier la disponibilité
                $itemsLibres = $this->itemRepository->findLibresByModelId($outilId);

                if (count($itemsLibres) < $quantite) {
                    $errors[] = "Pas assez d'exemplaires disponibles pour l'outil ID: $outilId";
                    continue;
                }

                // Réserver les items
                $itemsToReserve = array_slice($itemsLibres, 0, $quantite);
                foreach ($itemsToReserve as $itemToReserve) {
                    $itemToReserve->prendre();
                    $this->itemRepository->update($itemToReserve);
                    $reservedItems[] = [
                        'item_id' => $itemToReserve->getId(),
                        'outil_id' => $outilId,
                        'date' => $date,
                        'user_id' => $userId
                    ];
                }
            }

            if (!empty($errors)) {
                $response->getBody()->write(json_encode([
                    'error' => 'reservation_errors',
                    'message' => 'Erreurs lors de la réservation',
                    'errors' => $errors
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // 🧾 Réponse finale
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Réservation confirmée et paiement validé (simulation)',
                'payment_token' => $paymentToken,
                'reserved_items' => $reservedItems,
                'total_items' => count($reservedItems)
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
