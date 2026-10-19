<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Fetch only verified and vacant listings
$stmt = $pdo->query("
    SELECT listingID, title, address, city, monthlyRent, roomType, imagePath, amenities 
    FROM Listing 
    WHERE availabilityStatus = 'Vacant' AND isVerifiedByAdmin = 1 
    ORDER BY dateListingCreated DESC
");
$listings = $stmt->fetchAll();

if ($listings) {
    echo json_encode(['status' => 'success', 'data' => $listings]);
} else {
    echo json_encode(['status' => 'success', 'data' => []]);
}
?>