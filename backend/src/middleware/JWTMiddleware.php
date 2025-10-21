<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTMiddleware {
    private $jwtConfig;

    public function __construct($jwtConfig) {
        $this->jwtConfig = $jwtConfig;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $response = new \Slim\Psr7\Response(401);
            $response->getBody()->write(json_encode([
                'error' => 'no_token',
                'message' => 'Token d\'authentification requis'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $token = substr($authHeader, 7);
        
        try {
            $decoded = JWT::decode($token, new Key($this->jwtConfig['secret'], $this->jwtConfig['algorithm']));
            
            $request = $request->withAttribute('user', [
                'id' => $decoded->data->id,
                'prenom' => $decoded->data->prenom,
                'nom' => $decoded->data->nom,
                'email' => $decoded->data->email
            ]);
            
            return $handler->handle($request);
            
        } catch (Exception $e) {
            $response = new \Slim\Psr7\Response(401);
            $response->getBody()->write(json_encode([
                'error' => 'invalid_token',
                'message' => 'Token invalide ou expiré'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
