<?php
declare(strict_types=1);

namespace App\application\services;

class ServicePayment
{
    public function tokenizeCard(string $cardNumber): string
    {
        return 'tok_' . bin2hex(random_bytes(5));
    }

    public function processPayment(string $token, float $amount): bool
    {
        // 90% de chances que ça réussisse
        return mt_rand(1, 100) <= 90;
    }
}
