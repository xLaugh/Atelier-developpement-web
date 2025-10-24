<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Aucune donnée reçue']);
    exit;
}

$cardholder = $data['cardholder'] ?? '';
$cardNumber = $data['cardNumber'] ?? '';
$expiry = $data['expiry'] ?? '';
$cvc = $data['cvc'] ?? '';

if (!$cardholder || !$cardNumber || !$expiry || !$cvc) {
    echo json_encode(['success' => false, 'message' => 'Champs manquants']);
    exit;
}

// ✅ simulation de vérification
$last4 = substr(preg_replace('/\D/', '', $cardNumber), -4);
$token = 'tok_' . bin2hex(random_bytes(5));

// Tu pourrais aussi logger ce token en base ou fichier
file_put_contents(__DIR__ . '/../data/payments.log', json_encode([
    'token' => $token,
    'last4' => $last4,
    'holder' => $cardholder,
    'created' => date('c')
]) . PHP_EOL, FILE_APPEND);

echo json_encode([
    'success' => true,
    'token' => $token,
    'last4' => $last4,
    'message' => 'Paiement factice validé'
]);
