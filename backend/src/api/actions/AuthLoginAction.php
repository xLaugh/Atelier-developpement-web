<?php
declare(strict_types=1);

namespace App\api\actions;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthLoginAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_fields',
                    'message' => 'Email et mot de passe requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $pdo = $GLOBALS['db']->getConnection();
            $stmt = $pdo->prepare("SELECT id, prenom, nom, email, password FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $settings = require __DIR__ . '/../../../../config/Settings.php';
                $jwtConfig = $settings['jwt'];
                
                $payload = [
                    'iss' => 'charlymatloc',
                    'aud' => 'charlymatloc',
                    'iat' => time(),
                    'exp' => time() + $jwtConfig['expiration'],
                    'sub' => $user['id'],
                    'data' => [
                        'id' => $user['id'],
                        'prenom' => $user['prenom'],
                        'nom' => $user['nom'],
                        'email' => $user['email']
                    ]
                ];
                
                $token = JWT::encode($payload, $jwtConfig['secret'], $jwtConfig['algorithm']);

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => (int)$user['id'],
                        'prenom' => $user['prenom'],
                        'nom' => $user['nom'],
                        'email' => $user['email']
                    ]
                ], JSON_UNESCAPED_UNICODE));
                return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
            } else {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_credentials',
                    'message' => 'Email ou mot de passe incorrect'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
