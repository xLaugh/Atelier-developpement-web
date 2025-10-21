<?php
declare(strict_types=1);

namespace App\actions;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthRegisterAction
{
    public function __invoke(Request $request, Response $response): Response
    {
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

            $pdo = $GLOBALS['db']->getConnection();
            
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
    }
}
