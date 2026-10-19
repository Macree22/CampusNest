<?php
require_once 'auth.php';
// The auth.php file ensures only a valid student can access this
$authData = authenticate($pdo, 'student');

$data = json_decode(file_get_contents('php://input'), true);
$listingID = $data['listingID'] ?? null;
$moveInDate = $data['moveInDate'] ?? date('Y-m-d', strtotime('+30 days'));

if (!$listingID) {
    echo json_encode(['status' => 'error', 'message' => 'Listing ID is required']);
    exit;
}

// Check if already applied
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Application WHERE studentID = ? AND listingID = ?");
$stmt->execute([$authData['id'], $listingID]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'You have already applied for this property']);
    exit;
}

// Insert Application
$pdo->prepare("INSERT INTO Application (studentID, listingID, preferredMoveInDate, applicationStatus) VALUES (?, ?, ?, 'Pending')")
    ->execute([$authData['id'], $listingID, $moveInDate]);

echo json_encode(['status' => 'success', 'message' => 'Application submitted successfully']);
?>