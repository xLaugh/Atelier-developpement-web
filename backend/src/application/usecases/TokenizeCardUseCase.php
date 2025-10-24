<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\api\ServicePaymentInterface;

class TokenizeCardUseCase
{
    public function __construct(
        private ServicePaymentInterface $paymentService
    ) {}

    public function execute(string $cardNumber): string
    {
        return $this->paymentService->tokenizeCard($cardNumber);
    }
}
