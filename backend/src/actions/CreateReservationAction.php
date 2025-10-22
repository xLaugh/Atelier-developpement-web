<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\spi\ItemRepositoryInterface;

class CreateReservationAction
{
    public function __construct(
        private ItemRepositoryInterface $itemRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            // Mode sans JWT: utiliser un userId par défaut (ex: 1) ou null
            $userId = 1;

            // Récupérer les données de la réservation
            $data = $request->getParsedBody();
            $items = $data['items'] ?? [];

            if (empty($items)) {
                $response->getBody()->write(json_encode([
                    'error' => 'no_items',
                    'message' => 'Aucun article dans la réservation'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $reservedItems = [];
            $errors = [];

            // Traiter chaque article
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

                // Réserver les items (mettre le statut à 1)
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

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Réservation confirmée avec succès',
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
