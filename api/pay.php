<?php
require_once 'auth.php';
$authData = authenticate($pdo, 'student');

$data = json_decode(file_get_contents('php://input'), true);
$applicationID = $data['applicationID'] ?? null;
$listingID = $data['listingID'] ?? null;
$amount = $data['amount'] ?? 0;
$method = $data['paymentMethod'] ?? 'Credit Card';

if (!$applicationID || !$listingID || $amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid payment details']);
    exit;
}

// Generate a mock transaction reference
$txRef = 'TXN-APP-' . strtoupper(uniqid());

// Insert Payment
$pdo->prepare("INSERT INTO Payment (studentID, applicationID, listingID, amountPaid, paymentMethod, paymentStatus, transactionReference) VALUES (?, ?, ?, ?, ?, 'Successful', ?)")
    ->execute([$authData['id'], $applicationID, $listingID, $amount, $method, $txRef]);

// Update Listing Status
$pdo->prepare("UPDATE Listing SET availabilityStatus = 'Occupied' WHERE listingID = ?")->execute([$listingID]);

echo json_encode([
    'status' => 'success', 
    'message' => 'Payment successful', 
    'reference' => $txRef
]);
?>