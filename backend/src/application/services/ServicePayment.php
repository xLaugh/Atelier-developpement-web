<?php
declare(strict_types=1);

namespace App\application\services;

use App\application\ports\api\ServicePaymentInterface;

class ServicePayment implements ServicePaymentInterface
{
    public function tokenizeCard(string $cardNumber): string
    {
        // Validation basique du numéro de carte
        $cleanNumber = preg_replace('/\D/', '', $cardNumber);
        if (strlen($cleanNumber) < 13 || strlen($cleanNumber) > 19) {
            throw new \InvalidArgumentException('Numéro de carte invalide');
        }
        
        return 'tok_' . bin2hex(random_bytes(5));
    }

    public function processPayment(string $token, float $amount): bool
    {
        if (!str_starts_with($token, 'tok_')) {
            throw new \InvalidArgumentException('Token de paiement invalide');
        }
        
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Montant invalide');
        }
        
        // 90% de chances que ça réussisse (simulation)
        return mt_rand(1, 100) <= 90;
    }
}
