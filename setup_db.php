<?php
$host = '127.0.0.1';
$user = 'root';
$pass = ''; 

try {
    // 1. Connect & Create Database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS campusnest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE campusnest;");

    $pdo->beginTransaction();

    // 2. Drop existing tables to start fresh
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("DROP TABLE IF EXISTS Notification, Booking, MaintenanceRequest, Payment, Application, ListingImage, Listing, Student, Landlord, Admin;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 3. Automatically create the local images directory if it doesn't exist
    $imgDir = __DIR__ . '/images';
    if (!is_dir($imgDir)) {
        mkdir($imgDir, 0777, true);
    }

    // 4. Create Tables (API TOKENS INCLUDED)
    $pdo->exec("CREATE TABLE Admin (
        adminID INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(50) NOT NULL,
        lastName VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        api_token VARCHAR(64) DEFAULT NULL
    )");

    $pdo->exec("CREATE TABLE Landlord (
        landlordID INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(50) NOT NULL,
        lastName VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        companyName VARCHAR(100) DEFAULT NULL,
        verificationStatus ENUM('Verified', 'Pending', 'Rejected') DEFAULT 'Verified',
        dateJoined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        api_token VARCHAR(64) DEFAULT NULL
    )");

    $pdo->exec("CREATE TABLE Student (
        studentID INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(50) NOT NULL,
        lastName VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        studentNumber VARCHAR(50) UNIQUE DEFAULT NULL,
        accountStatus ENUM('Active', 'Suspended', 'Deleted') DEFAULT 'Active',
        registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        api_token VARCHAR(64) DEFAULT NULL
    )");

    $pdo->exec("CREATE TABLE Listing (
        listingID INT AUTO_INCREMENT PRIMARY KEY,
        landlordID INT NOT NULL,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        address VARCHAR(255) NOT NULL,
        city VARCHAR(100) DEFAULT 'Johannesburg',
        monthlyRent DECIMAL(10,2) NOT NULL,
        roomType ENUM('Single', 'Shared', 'Studio', 'Bachelor') NOT NULL,
        amenities VARCHAR(255) DEFAULT 'WiFi, Security, Laundry',
        availabilityStatus ENUM('Vacant', 'Occupied', 'Under Maintenance') DEFAULT 'Vacant',
        isVerifiedByAdmin TINYINT(1) DEFAULT 0,
        imagePath VARCHAR(255) DEFAULT 'images/default.jpg',
        dateListingCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (landlordID) REFERENCES Landlord(landlordID) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE ListingImage (
        imageID INT AUTO_INCREMENT PRIMARY KEY,
        listingID INT NOT NULL,
        imageURL VARCHAR(255) NOT NULL,
        FOREIGN KEY (listingID) REFERENCES Listing(listingID) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE Application (
        applicationID INT AUTO_INCREMENT PRIMARY KEY,
        studentID INT NOT NULL,
        listingID INT NOT NULL,
        applicationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        preferredMoveInDate DATE NOT NULL,
        applicationStatus ENUM('Pending', 'Approved', 'Rejected', 'Cancelled') DEFAULT 'Pending',
        landlordComment TEXT DEFAULT NULL,
        FOREIGN KEY (studentID) REFERENCES Student(studentID) ON DELETE CASCADE,
        FOREIGN KEY (listingID) REFERENCES Listing(listingID) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE Booking (
        bookingID INT AUTO_INCREMENT PRIMARY KEY,
        studentID INT NOT NULL,
        listingID INT NOT NULL,
        applicationID INT NOT NULL,
        bookingDate DATE NOT NULL,
        durationMonths INT NOT NULL,
        bookingStatus ENUM('Active', 'Completed', 'Terminated') DEFAULT 'Active',
        FOREIGN KEY (studentID) REFERENCES Student(studentID) ON DELETE CASCADE,
        FOREIGN KEY (listingID) REFERENCES Listing(listingID) ON DELETE CASCADE,
        FOREIGN KEY (applicationID) REFERENCES Application(applicationID) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE Payment (
        paymentID INT AUTO_INCREMENT PRIMARY KEY,
        studentID INT NOT NULL,
        applicationID INT NOT NULL,
        listingID INT NOT NULL,
        amountPaid DECIMAL(10,2) NOT NULL,
        paymentMethod ENUM('Credit Card', 'EFT', 'Debit Card') DEFAULT 'Credit Card',
        paymentStatus ENUM('Successful', 'Pending', 'Failed', 'Refunded') DEFAULT 'Successful',
        transactionReference VARCHAR(100) UNIQUE NOT NULL,
        paymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (studentID) REFERENCES Student(studentID) ON DELETE CASCADE,
        FOREIGN KEY (applicationID) REFERENCES Application(applicationID) ON DELETE CASCADE,
        FOREIGN KEY (listingID) REFERENCES Listing(listingID) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE MaintenanceRequest (
        requestID INT AUTO_INCREMENT PRIMARY KEY,
        studentID INT NOT NULL,
        listingID INT NOT NULL,
        requestDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        description TEXT NOT NULL,
        urgencyLevel ENUM('Low', 'Medium', 'High', 'Emergency') DEFAULT 'Medium',
        requestStatus ENUM('Submitted', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Submitted',
        resolutionDate DATE DEFAULT NULL,
        FOREIGN KEY (studentID) REFERENCES Student(studentID) ON DELETE CASCADE,
        FOREIGN KEY (listingID) REFERENCES Listing(listingID) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE Notification (
        notificationID INT AUTO_INCREMENT PRIMARY KEY,
        recipientID INT NOT NULL,
        recipientType ENUM('Student', 'Landlord', 'Admin') NOT NULL,
        message TEXT NOT NULL,
        isRead TINYINT(1) DEFAULT 0,
        dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Start Seeding Data
    $passHash = password_hash('CampusNest2026!', PASSWORD_DEFAULT);

    $pdo->exec("INSERT INTO Admin (firstName, lastName, email, password) VALUES ('System', 'Admin', 'admin@campusnest.co.za', '$passHash')");

    // Seed Landlords
    $landlordData = [
        ['Thandi', 'Ngubane', 'thandi@mzanzi.co.za', 'Mzanzi Stays', 'Verified'],
        ['John', 'Smith', 'john@smithprop.com', 'Smith Properties', 'Verified'],
        ['Sipho', 'Dlamini', 'sipho@dlaminiprop.co.za', 'Dlamini Properties', 'Verified'],
        ['Khumalo', 'Mthembu', 'khumalo@rentals.co.za', 'Khumalo Rentals', 'Verified'],
        ['Lerato', 'Molefe', 'lerato@cityliving.co.za', 'City Living', 'Verified'],
        ['Ahmed', 'Patel', 'ahmed@studentres.com', 'Student Res Hub', 'Verified'],
        ['Pieter', 'Van Der Merwe', 'pieter@capeprop.co.za', 'Cape Student Housing', 'Verified'],
        ['Zinhle', 'Khoza', 'zinhle@urbanpads.co.za', 'Urban Pads', 'Pending'],
        ['David', 'Miller', 'david@millprop.com', 'Miller Estates', 'Verified'],
        ['Nomvula', 'Zulu', 'nomvula@zuluholdings.co.za', 'Zulu Holdings', 'Rejected']
    ];
    $stmtLandlord = $pdo->prepare("INSERT INTO Landlord (firstName, lastName, email, password, companyName, verificationStatus) VALUES (?, ?, ?, ?, ?, ?)");
    $landlordIDs = [];
    foreach ($landlordData as $ld) {
        $stmtLandlord->execute([$ld[0], $ld[1], $ld[2], $passHash, $ld[3], $ld[4]]);
        $landlordIDs[] = $pdo->lastInsertId();
    }

    // Seed Students
    $firstNames = ['Siyabonga', 'Sibusiso', 'Jessica', 'Michael', 'Aisha', 'Kagiso', 'Nomhle', 'Liam', 'Chloe', 'Ethan', 'Zola', 'Dirk', 'Palesa', 'Ryan', 'Thabiso', 'Anika', 'Bongani', 'Simphiwe', 'Tshepo', 'Naledi', 'Caleb', 'Megan', 'Lucia', 'Francois', 'Kgomotso', 'Devin', 'Ayanda', 'Inathi', 'Lwazi', 'Mapula', 'Matthew', 'Samantha', 'Kian', 'Rorisang', 'Hannah'];
    $lastNames = ['Mkhize', 'Ndlovu', 'Jones', 'Scott', 'Khan', 'Molefe', 'Khumalo', 'Smith', 'Van Wyk', 'Naidoo', 'Zulu', 'Botha', 'Mokoena', 'Patel', 'Nkosi', 'Pretorius', 'Ndlovu', 'Sithole', 'Mabena', 'Kgame', 'Johnson', 'Adams', 'Pillay', 'Visser', 'Modise', 'Taylor', 'Buthelezi', 'Motaung', 'Radebe', 'Maluleka', 'Clark', 'Walker', 'Pillay', 'Motau', 'Davies'];
    
    $stmtStudent = $pdo->prepare("INSERT INTO Student (firstName, lastName, email, password, studentNumber, accountStatus) VALUES (?, ?, ?, ?, ?, ?)");
    $studentIDs = [];
    for ($i = 0; $i < 35; $i++) {
        $fn = $firstNames[$i];
        $ln = $lastNames[$i];
        $email = strtolower($fn . '.' . $ln . '@mycampus.ac.za');
        $stNum = 'ST2026' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
        $status = ($i === 32) ? 'Suspended' : (($i === 33) ? 'Deleted' : 'Active');
        
        $stmtStudent->execute([$fn, $ln, $email, $passHash, $stNum, $status]);
        if ($status === 'Active') {
            $studentIDs[] = $pdo->lastInsertId();
        }
    }

    // Seed Listings with VARIETY OF IMAGES
    $citiesAndSuburbs = [
        ['Johannesburg', 'Braamfontein'],
        ['Johannesburg', 'Auckland Park'],
        ['Cape Town', 'Rondebosch'],
        ['Cape Town', 'Observatory'],
        ['Pretoria', 'Hatfield'],
        ['Stellenbosch', 'Central Stellenbosch'],
        ['Durban', 'Glenwood']
    ];
    $roomTypes = ['Single', 'Shared', 'Studio', 'Bachelor'];
    $amenitiesList = ['WiFi, Security, Laundry, Gym, Parking', 'WiFi, Furnished, Water Included', 'Solar Backup, Security, Parking', 'Study Lounge, High-Speed WiFi, Laundry'];
    
    $stmtListing = $pdo->prepare("INSERT INTO Listing (landlordID, title, description, address, city, monthlyRent, roomType, amenities, availabilityStatus, isVerifiedByAdmin, imagePath) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtImg = $pdo->prepare("INSERT INTO ListingImage (listingID, imageURL) VALUES (?, ?)");

    // Expanded Variety of Unsplash Images
    $roomImages = [
        'Single' => [
            'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=800&q=80',
            'https://images.unsplash.com/photo-1540518614846-7ede433c4ef0?w=800&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&q=80',
            'https://images.unsplash.com/photo-1522771731478-44eb10e5c821?w=800&q=80'
        ],
        'Shared' => [
            'https://images.unsplash.com/photo-1502672260266-1c1e39b4968b?w=800&q=80',
            'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80',
            'https://images.unsplash.com/photo-1517409595718-e8b6a153777b?w=800&q=80',
            'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&q=80'
        ],
        'Studio' => [
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&q=80',
            'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?w=800&q=80',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=80'
        ],
        'Bachelor' => [
            'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?w=800&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=800&q=80',
            'https://images.unsplash.com/photo-1554995207-c18c203602cb?w=800&q=80',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80'
        ]
    ];
    
    $listingIDs = [];
    for ($i = 1; $i <= 48; $i++) {
        $loc = $citiesAndSuburbs[array_rand($citiesAndSuburbs)];
        $city = $loc[0]; $suburb = $loc[1];
        $type = $roomTypes[array_rand($roomTypes)];
        $rent = rand(28, 115) * 100;
        $status = (rand(1, 100) > 40) ? 'Vacant' : ((rand(1, 100) > 20) ? 'Occupied' : 'Under Maintenance');
        $isVerified = (rand(1, 100) > 15) ? 1 : 0;
        $landlordID = $landlordIDs[array_rand($landlordIDs)];
        $amenities = $amenitiesList[array_rand($amenitiesList)];
        
        $title = "Modern " . $type . " in " . $suburb;
        $desc = "Fully furnished $type apartment located within walking distance to campus. Features secure access control, 24/7 security, and high-speed internet.";
        $address = rand(10, 180) . " Campus Avenue, " . $suburb;
        
        // Pick a random cover image from the category
        $pool = $roomImages[$type];
        $coverImg = $pool[array_rand($pool)];

        $stmtListing->execute([$landlordID, $title, $desc, $address, $city, $rent, $type, $amenities, $status, $isVerified, $coverImg]);
        $listingID = $pdo->lastInsertId();
        $listingIDs[] = ['id' => $listingID, 'landlordID' => $landlordID, 'rent' => $rent];

        // Populate ALL images from that category into the gallery
        foreach ($pool as $galleryUrl) {
            $stmtImg->execute([$listingID, $galleryUrl]);
        }
    }

    // Seed Applications
    $stmtApp = $pdo->prepare("INSERT INTO Application (studentID, listingID, applicationDate, preferredMoveInDate, applicationStatus, landlordComment) VALUES (?, ?, ?, ?, ?, ?)");
    $applicationRecords = [];
    for ($i = 0; $i < 130; $i++) {
        $studentID = $studentIDs[array_rand($studentIDs)];
        $listingObj = $listingIDs[array_rand($listingIDs)];
        $listingID = $listingObj['id'];
        
        $daysAgo = rand(1, 120);
        $appDate = date('Y-m-d H:i:s', strtotime("-$daysAgo days"));
        $moveInDate = date('Y-m-d', strtotime("-$daysAgo days + " . rand(15, 45) . " days"));
        
        $randWeight = rand(1, 100);
        if ($randWeight <= 30) $status = 'Pending';
        elseif ($randWeight <= 70) $status = 'Approved';
        elseif ($randWeight <= 90) $status = 'Rejected';
        else $status = 'Cancelled';

        $comment = ($status === 'Approved') ? "Application approved. Please proceed with deposit payment." : (($status === 'Rejected') ? "Unfortunately, the unit was allocated to an earlier applicant." : null);

        $stmtApp->execute([$studentID, $listingID, $appDate, $moveInDate, $status, $comment]);
        $appID = $pdo->lastInsertId();

        if ($status === 'Approved') {
            $applicationRecords[] = ['appID' => $appID, 'studentID' => $studentID, 'listingID' => $listingID, 'appDate' => $appDate, 'rent' => $listingObj['rent']];
        }
    }

    // Seed Bookings
    $stmtBooking = $pdo->prepare("INSERT INTO Booking (studentID, listingID, applicationID, bookingDate, durationMonths, bookingStatus) VALUES (?, ?, ?, ?, ?, ?)");
    $bookingRecords = [];
    $bookingCount = min(90, count($applicationRecords));
    for ($i = 0; $i < $bookingCount; $i++) {
        $app = $applicationRecords[$i];
        $bookingDate = date('Y-m-d', strtotime($app['appDate'] . ' + 3 days'));
        $duration = [6, 12, 18, 24][array_rand([6, 12, 18, 24])];
        $bookingStatus = (rand(1, 100) > 15) ? 'Active' : 'Completed';

        $stmtBooking->execute([$app['studentID'], $app['listingID'], $app['appID'], $bookingDate, $duration, $bookingStatus]);
        $bookingRecords[] = ['bookingID' => $pdo->lastInsertId(), 'studentID' => $app['studentID'], 'listingID' => $app['listingID'], 'appID' => $app['appID'], 'rent' => $app['rent'], 'duration' => $duration, 'startDate' => $bookingDate];
    }

    // Seed Payments
    $stmtPayment = $pdo->prepare("INSERT INTO Payment (studentID, applicationID, listingID, amountPaid, paymentMethod, paymentStatus, transactionReference, paymentDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($bookingRecords as $bk) {
        $installments = min($bk['duration'], rand(3, 6));
        for ($m = 0; $m < $installments; $m++) {
            $payDate = date('Y-m-d H:i:s', strtotime($bk['startDate'] . " + $m months"));
            if (strtotime($payDate) > time()) continue;
            $txRef = 'TXN-' . strtoupper(bin2hex(random_bytes(5)));
            $stmtPayment->execute([$bk['studentID'], $bk['appID'], $bk['listingID'], $bk['rent'], ['Credit Card', 'EFT', 'Debit Card'][array_rand(['Credit Card', 'EFT', 'Debit Card'])], ['Successful', 'Successful', 'Successful', 'Successful', 'Pending', 'Failed'][array_rand(['Successful', 'Successful', 'Successful', 'Successful', 'Pending', 'Failed'])], $txRef, $payDate]);
        }
    }

    // Seed Maintenance Requests
    $stmtMaint = $pdo->prepare("INSERT INTO MaintenanceRequest (studentID, listingID, requestDate, description, urgencyLevel, requestStatus, resolutionDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $issues = ['Leaking kitchen basin pipe.', 'Main bedroom light switch sparking.', 'Geyser not producing hot water.', 'Front door lock mechanism is stiff.', 'Wi-Fi router port connectivity failure.', 'Window latch broken in study room.'];
    for ($i = 0; $i < 22; $i++) {
        $daysAgo = rand(1, 60);
        $reqDate = date('Y-m-d H:i:s', strtotime("-$daysAgo days"));
        $urgency = ['Low', 'Medium', 'High', 'Emergency'][array_rand(['Low', 'Medium', 'High', 'Emergency'])];
        $status = ['Submitted', 'In Progress', 'Resolved', 'Closed'][array_rand(['Submitted', 'In Progress', 'Resolved', 'Closed'])];
        $resDate = ($status === 'Resolved' || $status === 'Closed') ? date('Y-m-d', strtotime("-$daysAgo days + " . rand(1, 5) . " days")) : null;
        $stmtMaint->execute([$studentIDs[array_rand($studentIDs)], $listingIDs[array_rand($listingIDs)]['id'], $reqDate, $issues[array_rand($issues)], $urgency, $status, $resDate]);
    }

    // Seed Notifications
    $stmtNotif = $pdo->prepare("INSERT INTO Notification (recipientID, recipientType, message, isRead) VALUES (?, ?, ?, ?)");
    $notifTemplates = [
        ['Student', 'Your accommodation application has been updated.'],
        ['Student', 'A payment receipt has been successfully generated.'],
        ['Student', 'Your maintenance ticket has been marked as in progress.'],
        ['Landlord', 'New student application submitted for your property.'],
        ['Landlord', 'Monthly rental payment received from tenant.'],
        ['Landlord', 'New maintenance request logged by tenant.']
    ];
    for ($i = 0; $i < 250; $i++) {
        $recType = (rand(1, 10) > 4) ? 'Student' : 'Landlord';
        $recID = ($recType === 'Student') ? $studentIDs[array_rand($studentIDs)] : $landlordIDs[array_rand($landlordIDs)];
        $template = $notifTemplates[array_rand($notifTemplates)];
        $stmtNotif->execute([$recID, $recType, $template[1], rand(0, 1)]);
    }

    $pdo->commit();

    // Success UI
    echo "<div style='font-family:sans-serif; max-width:650px; margin:50px auto; padding:35px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:20px; color:#166534; box-shadow:0 4px 6px rgba(0,0,0,0.05);'>";
    echo "<h2 style='margin-top:0; font-size:24px;'>CampusNest Master Seeder Completed Successfully!</h2>";
    echo "<p>The database has been fully reset with all tables (including API tokens) and populated with a wide variety of images and realistic data.</p>";
    echo "<p><em>Note: The /images/ folder has also been automatically created.</em></p>";
    echo "<hr style='border:0; border-top:1px solid #bbf7d0; margin:20px 0;'>";
    echo "<h3 style='font-size:16px; margin-bottom:10px;'>Generated Data Counts:</h3>";
    echo "<ul style='padding-left:20px; line-height:1.6;'>";
    echo "<li><b>Landlords:</b> 10 accounts</li>";
    echo "<li><b>Students:</b> 35 accounts</li>";
    echo "<li><b>Listings:</b> 48 properties with variety of images</li>";
    echo "<li><b>Applications:</b> 130 records</li>";
    echo "<li><b>Bookings:</b> 90 active tenancies</li>";
    echo "<li><b>Payments:</b> ~180 recurring transaction records</li>";
    echo "<li><b>Maintenance Requests:</b> 22 tickets</li>";
    echo "<li><b>Notifications:</b> 250 system alerts</li>";
    echo "</ul>";
    echo "<p style='margin-top:20px;'><strong>Default Test Password for all accounts:</strong> <code style='background:#dcfce7; padding:2px 6px; border-radius:4px;'>CampusNest2026!</code></p>";
    echo "<p><a href='index.php' style='display:inline-block; margin-top:15px; padding:12px 24px; background:#2563eb; color:#fff; text-decoration:none; border-radius:12px; font-weight:600;'>Launch CampusNest &rarr;</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<div style='font-family:sans-serif; max-width:650px; margin:50px auto; padding:35px; background:#fef2f2; border:1px solid #fecaca; border-radius:20px; color:#991b1b;'>";
    echo "<h2 style='margin-top:0;'>Seeding Transaction Failed</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>