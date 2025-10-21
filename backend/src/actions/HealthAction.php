<?php
declare(strict_types=1);

<<<<<<< HEAD
namespace App\actions;
use PDO;
=======
>>>>>>> a7b62f9192b9f3d2faf0fd595221cf4c5e8aa7e6
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HealthAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
    }
}
