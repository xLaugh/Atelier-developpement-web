<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

return function ($app, $db) {
    $settings = require __DIR__ . '/../config/Settings.php';
    $jwtConfig = $settings['jwt'];
    $app->post('/api/auth/login', function (Request $request, Response $response) use ($db) {
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

            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT id, prenom, nom, email, password FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $payload = [
                    'iss' => 'charlymatloc', // Issuer
                    'aud' => 'charlymatloc', // Audience
                    'iat' => time(), // Issued at
                    'exp' => time() + $jwtConfig['expiration'], // Expiration
                    'sub' => $user['id'], // Subject (user ID)
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
    });

    $app->post('/api/auth/register', function (Request $request, Response $response) use ($db) {
        try {
            $data = $request->getParsedBody();
            $prenom = $data['prenom'] ?? '';
            $nom = $data['nom'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_fields',
                    'message' => 'Tous les champs sont requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $pdo = $db->getConnection();
            
            // Vérifier si l'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $response->getBody()->write(json_encode([
                    'error' => 'user_exists',
                    'message' => 'Email déjà utilisé'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // Créer l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$prenom, $nom, $email, $hashedPassword]);
            $userId = $pdo->lastInsertId();

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Compte créé avec succès',
                'user' => [
                    'id' => (int)$userId,
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email
                ]
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    });

    $app->get('/api/auth/me', function (Request $request, Response $response) use ($db, $jwtConfig) {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                $response->getBody()->write(json_encode([
                    'error' => 'no_token',
                    'message' => 'Token manquant'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $token = substr($authHeader, 7);
            
            try {
                $decoded = JWT::decode($token, new Key($jwtConfig['secret'], $jwtConfig['algorithm']));
                
                $response->getBody()->write(json_encode([
                    'user' => [
                        'id' => $decoded->data->id,
                        'prenom' => $decoded->data->prenom,
                        'nom' => $decoded->data->nom,
                        'email' => $decoded->data->email
                    ]
                ], JSON_UNESCAPED_UNICODE));
                return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
                
            } catch (Exception $e) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_token',
                    'message' => 'Token invalide ou expiré'
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
    });
};
