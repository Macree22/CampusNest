<?php
require_once 'auth.php';
$authData = authenticate($pdo, 'admin');

$data = json_decode(file_get_contents('php://input'), true);
$listingID = $data['listingID'] ?? null;

if (!$listingID) {
    echo json_encode(['status' => 'error', 'message' => 'Listing ID required']);
    exit;
}

$stmt = $pdo->prepare("UPDATE Listing SET isVerifiedByAdmin = 1 WHERE listingID = ?");
$stmt->execute([$listingID]);

echo json_encode(['status' => 'success', 'message' => 'Listing verified successfully']);
?>