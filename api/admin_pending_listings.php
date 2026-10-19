<?php
require_once 'auth.php';
// Ensure only admins can access this
$authData = authenticate($pdo, 'admin');

$stmt = $pdo->query("
    SELECT l.listingID, l.title, l.address, l.city, l.monthlyRent, l.roomType, land.firstName, land.lastName 
    FROM Listing l 
    JOIN Landlord land ON l.landlordID = land.landlordID 
    WHERE l.isVerifiedByAdmin = 0
");
$listings = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'data' => $listings]);
?>