<?php
require_once 'auth.php';
$authData = authenticate($pdo, 'student');

$data = json_decode(file_get_contents('php://input'), true);
$bookingID = $data['bookingID'] ?? null;

if (!$bookingID) {
    echo json_encode(['status' => 'error', 'message' => 'Booking ID required']);
    exit;
}

// Verify this booking belongs to the student
$stmt = $pdo->prepare("SELECT bookingID FROM Booking WHERE bookingID = ? AND studentID = ?");
$stmt->execute([$bookingID, $authData['id']]);

if ($stmt->fetch()) {
    $pdo->prepare("UPDATE Booking SET studentAccepted = 1, bookingStatus = 'Active' WHERE bookingID = ?")->execute([$bookingID]);
    echo json_encode(['status' => 'success', 'message' => 'Lease signed successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or booking not found']);
}
?>