<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\api\ServicePaymentInterface;

class ProcessPaymentUseCase
{
    public function __construct(
        private ServicePaymentInterface $paymentService
    ) {}

    public function execute(string $token, float $amount): bool
    {
        return $this->paymentService->processPayment($token, $amount);
    }
}
