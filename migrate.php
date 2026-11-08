<?php
require_once 'db.php';

echo "<h2>CampusNest Database Migration</h2>";

try {
    // 1. Update Student Table
    $pdo->exec("ALTER TABLE Student ADD COLUMN api_token VARCHAR(64) DEFAULT NULL;");
    echo "✅ Added 'api_token' to Student table.<br>";
} catch (PDOException $e) { echo "⚡ Student table already has 'api_token'.<br>"; }

try {
    // 2. Update Landlord Table
    $pdo->exec("ALTER TABLE Landlord ADD COLUMN api_token VARCHAR(64) DEFAULT NULL;");
    echo "✅ Added 'api_token' to Landlord table.<br>";
} catch (PDOException $e) { echo "⚡ Landlord table already has 'api_token'.<br>"; }

try {
    // 3. Update Admin Table
    $pdo->exec("ALTER TABLE Admin ADD COLUMN api_token VARCHAR(64) DEFAULT NULL;");
    echo "✅ Added 'api_token' to Admin table.<br>";
} catch (PDOException $e) { echo "⚡ Admin table already has 'api_token'.<br>"; }

try {
    // 4. Update Booking Table
    $pdo->exec("ALTER TABLE Booking ADD COLUMN studentAccepted TINYINT(1) DEFAULT 0;");
    echo "✅ Added 'studentAccepted' to Booking table.<br>";
} catch (PDOException $e) { echo "⚡ Booking table already has 'studentAccepted'.<br>"; }

try {
    $pdo->exec("ALTER TABLE Booking ADD COLUMN totalContractValue DECIMAL(10,2) DEFAULT 0;");
    echo "✅ Added 'totalContractValue' to Booking table.<br>";
} catch (PDOException $e) { echo "⚡ Booking table already has 'totalContractValue'.<br>"; }

try {
    $pdo->exec("ALTER TABLE Booking MODIFY bookingStatus ENUM('Pending', 'Active', 'Completed', 'Terminated') DEFAULT 'Pending';");
    echo "✅ Updated Booking bookingStatus values to include 'Pending'.<br>";
} catch (PDOException $e) { echo "⚡ Booking bookingStatus already includes 'Pending'.<br>"; }

try {
    // 4. Update MaintenanceRequest Table
    $pdo->exec("ALTER TABLE MaintenanceRequest ADD COLUMN landlordComment TEXT DEFAULT NULL;");
    echo "✅ Added 'landlordComment' to MaintenanceRequest table.<br>";
} catch (PDOException $e) { echo "⚡ MaintenanceRequest table already has 'landlordComment'.<br>"; }

echo "<br><b>Migration Complete!</b> You can now go back to Android Studio and log in.";
?>