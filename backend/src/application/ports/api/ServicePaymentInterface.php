<?php
declare(strict_types=1);

namespace App\application\ports\api;

interface ServicePaymentInterface
{
    public function tokenizeCard(string $cardNumber): string;
    public function processPayment(string $token, float $amount): bool;
}
