<?php
require_once 'auth.php';
$authData = authenticate($pdo, 'student');

$stmt = $pdo->prepare("
    SELECT m.requestID, m.description, m.urgencyLevel, m.requestStatus, m.requestDate, 
           l.title 
    FROM MaintenanceRequest m
    JOIN Listing l ON m.listingID = l.listingID
    WHERE m.studentID = ?
    ORDER BY m.requestDate DESC
");
$stmt->execute([$authData['id']]);
$requests = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'data' => $requests]);
?>