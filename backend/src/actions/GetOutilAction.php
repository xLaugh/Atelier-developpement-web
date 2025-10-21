<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceOutilInterface;
use App\domain\exceptions\OutilsNotFoundException;

class GetOutilAction
{
    public function __construct(
        private ServiceOutilInterface $serviceOutil
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_id',
                    'message' => 'Identifiant invalide'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $outil = $this->serviceOutil->obtenirOutil($id);

            if (!$outil) {
                $response->getBody()->write(json_encode([
                    'error' => 'not_found',
                    'message' => 'Outil introuvable'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $response->getBody()->write(json_encode($outil->toArray(), JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (OutilsNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'not_found',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
