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

echo "<br><b>Migration Complete!</b> You can now go back to Android Studio and log in.";
?>