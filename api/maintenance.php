<?php
require_once 'auth.php';
$authData = authenticate($pdo, 'student');

$data = json_decode(file_get_contents('php://input'), true);
$listingID = $data['listingID'] ?? null;
$description = $data['description'] ?? '';
$urgency = $data['urgencyLevel'] ?? 'Medium';

if (!$listingID || empty($description)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Verify the student actually has an active booking for this listing
$check = $pdo->prepare("SELECT bookingID FROM Booking WHERE studentID = ? AND listingID = ? AND bookingStatus = 'Active'");
$check->execute([$authData['id'], $listingID]);

if ($check->fetchColumn() == 0) {
    echo json_encode(['status' => 'error', 'message' => 'You do not have an active lease for this property']);
    exit;
}

$pdo->prepare("INSERT INTO MaintenanceRequest (studentID, listingID, description, urgencyLevel) VALUES (?, ?, ?, ?)")
    ->execute([$authData['id'], $listingID, $description, $urgency]);

echo json_encode(['status' => 'success', 'message' => 'Maintenance request logged successfully']);
?>