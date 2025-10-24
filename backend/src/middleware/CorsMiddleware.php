<?php
declare(strict_types=1);

namespace App\middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CorsMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $request->getMethod() === 'OPTIONS'
            ? (new \Slim\Psr7\Response())->withStatus(204)
            : $handler->handle($request);

        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://docketu.iutnc.univ-lorraine.fr:13014')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
