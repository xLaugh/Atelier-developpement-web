<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\usecases\ProcessPaymentUseCase;
use App\application\usecases\TokenizeCardUseCase;

class ProcessPaymentAction
{
    public function __construct(
        private ProcessPaymentUseCase $processPaymentUseCase,
        private TokenizeCardUseCase $tokenizeCardUseCase
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            if (!$data) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Aucune donnée reçue'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $cardholder = $data['cardholder'] ?? '';
            $cardNumber = $data['cardNumber'] ?? '';
            $expiry = $data['expiry'] ?? '';
            $cvc = $data['cvc'] ?? '';
            $amount = (float)($data['amount'] ?? 0);

            // Validation des champs requis
            if (!$cardholder || !$cardNumber || !$expiry || !$cvc) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Champs manquants'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            if ($amount <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Montant invalide'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }
            $token = $this->tokenizeCardUseCase->execute($cardNumber);

            $paymentSuccess = $this->processPaymentUseCase->execute($token, $amount);

            if (!$paymentSuccess) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Paiement refusé'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(402)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $last4 = substr(preg_replace('/\D/', '', $cardNumber), -4);
            $this->logPayment($token, $last4, $cardholder, $amount);

            $response->getBody()->write(json_encode([
                'success' => true,
                'token' => $token,
                'last4' => $last4,
                'message' => 'Paiement validé'
            ], JSON_UNESCAPED_UNICODE));

            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }

    private function logPayment(string $token, string $last4, string $cardholder, float $amount): void
    {
        $logData = [
            'token' => $token,
            'last4' => $last4,
            'holder' => $cardholder,
            'amount' => $amount,
            'created' => date('c')
        ];

        $logDir = __DIR__ . '/../../data';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents(
            $logDir . '/payments.log',
            json_encode($logData) . PHP_EOL,
            FILE_APPEND
        );
    }
}
