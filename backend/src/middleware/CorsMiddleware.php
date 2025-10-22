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
        // Gérer les requêtes OPTIONS (preflight) sans ajouter d'en-têtes (gérés par Apache)
        if ($request->getMethod() === 'OPTIONS') {
            return new SlimResponse(204);
        }

        // Pass-through; les en-têtes CORS sont appliqués par Apache (.htaccess)
        return $handler->handle($request);
    }
}
