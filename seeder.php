<?php
require_once 'db.php';

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; 
                TRUNCATE TABLE ListingImage; 
                TRUNCATE TABLE Payment;
                TRUNCATE TABLE Application;
                TRUNCATE TABLE Listing; 
                TRUNCATE TABLE Student;
                TRUNCATE TABLE Landlord;
                SET FOREIGN_KEY_CHECKS = 1;");

    // Admin Table Creation & Seeding
    $pdo->exec("CREATE TABLE IF NOT EXISTS Admin (
        adminID INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(50), lastName VARCHAR(50),
        email VARCHAR(100) UNIQUE, password VARCHAR(255)
    )");
    $pdo->exec("TRUNCATE TABLE Admin");
    $passHash = password_hash('password123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO Admin (firstName, lastName, email, password) VALUES ('System', 'Admin', 'admin@campusnest.co.za', '$passHash')");

    // Seed Landlords
    $landlords = [
        ['Thandi', 'Ngubane', 'thandi@mzanzi.co.za', 'Mzanzi Stays'],
        ['John', 'Smith', 'john@smithprop.com', 'Smith Properties'],
        ['Lerato', 'Molefe', 'lerato@cityliving.co.za', 'City Living'],
        ['Ahmed', 'Patel', 'ahmed@studentres.com', 'Student Res Hub'],
    ];
    $insertLandlord = $pdo->prepare("INSERT INTO Landlord (firstName, lastName, email, password, companyName, verificationStatus) VALUES (?, ?, ?, ?, ?, 'Verified')");
    $landlordIDs = [];
    foreach ($landlords as $ld) {
        $insertLandlord->execute([$ld[0], $ld[1], $ld[2], $passHash, $ld[3]]);
        $landlordIDs[] = $pdo->lastInsertId();
    }

    // Seed Students
    $students = [
        ['Brandon', 'Dlamini', 'brandon@student.co.za', 'ST2024001'],
        ['Siyabonga', 'Mkhize', 'siya@student.co.za', 'ST2024002'],
        ['Jessica', 'Jones', 'jess@student.co.za', 'ST2024003'],
        ['Michael', 'Scott', 'mike@student.co.za', 'ST2024004'],
        ['Aisha', 'Khan', 'aisha@student.co.za', 'ST2024005'],
    ];
    $insertStudent = $pdo->prepare("INSERT INTO Student (firstName, lastName, email, password, studentNumber, accountStatus) VALUES (?, ?, ?, ?, ?, 'Active')");
    $studentIDs = [];
    foreach ($students as $st) {
        $insertStudent->execute([$st[0], $st[1], $st[2], $passHash, $st[3]]);
        $studentIDs[] = $pdo->lastInsertId();
    }

    // Reliable Image URLs
    $images = [
        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
        'https://images.unsplash.com/photo-1502672260266-1c1e39b4968b?w=800&q=80',
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&q=80',
        'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=80',
        'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?w=800&q=80'
    ];

    // Seed Listings
    $suburbs = ['Braamfontein', 'Auckland Park', 'Hatfield', 'Rondebosch'];
    $roomTypes = ['Single', 'Shared', 'Studio', 'Bachelor'];
    $insertListing = $pdo->prepare("INSERT INTO Listing (landlordID, title, description, address, city, monthlyRent, roomType, availabilityStatus, isVerifiedByAdmin, imagePath) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
    $listingIDs = [];
    
    for ($i = 1; $i <= 40; $i++) {
        $suburb = $suburbs[array_rand($suburbs)];
        $type = $roomTypes[array_rand($roomTypes)];
        $rent = rand(35, 90) * 100;
        $status = rand(1, 100) > 30 ? 'Vacant' : 'Occupied';
        $img = $images[array_rand($images)];
        
        $insertListing->execute([
            $landlordIDs[array_rand($landlordIDs)], 
            "Modern $type in $suburb", 
            "Walking distance to campus. Secure and fully equipped.", 
            rand(10, 150) . " Campus Drive, $suburb", 
            $suburb, 
            $rent, 
            $type, 
            $status, 
            $img
        ]);
        $listingIDs[] = $pdo->lastInsertId();
    }

    // Seed Applications
    $insertApp = $pdo->prepare("INSERT INTO Application (studentID, listingID, preferredMoveInDate, applicationStatus) VALUES (?, ?, ?, ?)");
    $statuses = ['Pending', 'Approved', 'Rejected'];
    
    for ($i = 0; $i < 20; $i++) {
        $insertApp->execute([
            $studentIDs[array_rand($studentIDs)],
            $listingIDs[array_rand($listingIDs)],
            date('Y-m-d', strtotime('+' . rand(5, 30) . ' days')),
            $statuses[array_rand($statuses)]
        ]);
    }

    echo "<h3>Database Seeded Successfully!</h3>";
    echo "<p><strong>All Passwords:</strong> password123</p>";
    echo "<ul><li>Admin: admin@campusnest.co.za</li><li>Student: brandon@student.co.za</li><li>Landlord: thandi@mzanzi.co.za</li></ul>";

} catch (PDOException $e) {
    die("Seeding failed: " . $e->getMessage());
}
?>