<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceUserInterface;
use App\application\ports\spi\UserRepositoryInterface;

class AuthRegisterAction
{
    public function __construct(
        private ServiceUserInterface $serviceUser,
        private UserRepositoryInterface $userRepository
    ) {}

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

            // Vérifier si l'email existe déjà
            if ($this->userRepository->findByEmail($email)) {
                $response->getBody()->write(json_encode([
                    'error' => 'email_exists',
                    'message' => 'Cet email est déjà utilisé'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $user = $this->serviceUser->createUser($prenom, $nom, $email, $password, 'user');

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user->toArray()
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
