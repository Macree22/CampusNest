<?php
require_once 'auth.php';
$authData = authenticate($pdo, 'student');

$stmt = $pdo->prepare("
    SELECT a.applicationID, a.applicationStatus, a.preferredMoveInDate, a.applicationDate, 
           l.listingID, l.title, l.monthlyRent, l.address, l.imagePath
    FROM Application a 
    JOIN Listing l ON a.listingID = l.listingID 
    WHERE a.studentID = ?
    ORDER BY a.applicationDate DESC
");
$stmt->execute([$authData['id']]);
$apps = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'data' => $apps]);
?>