<?php
declare(strict_types=1);

namespace App\middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class CorsMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $origin = $request->getHeaderLine('Origin');
        $allowedOrigins = [
            'http://localhost:13014',
            'http://docketu.iutnc.univ-lorraine.fr:13014'
        ];

        // Si c’est une requête OPTIONS réponse vide
        if ($request->getMethod() === 'OPTIONS') {
            $response = new SlimResponse(204);
        } else {
            $response = $handler->handle($request);
        }

        // Ajout dynamique des headers
        if (in_array($origin, $allowedOrigins, true)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        }

        return $response
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Vary', 'Origin');
    }
}
