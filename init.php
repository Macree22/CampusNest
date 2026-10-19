<?php 
session_start(); 
require_once 'db.php';

function randFormat($amount) {     
    return 'R' . number_format($amount, 0, '.', ','); 
}

$flash = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $action = $_POST['action'] ?? '';     
    
    // Authentication: Login
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $authenticated = false;

        $stmt = $pdo->prepare("SELECT * FROM Student WHERE email = ?");
        $stmt->execute([$email]);
        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['studentID'];
                $_SESSION['role'] = 'student';
                $_SESSION['name'] = $user['firstName'];
                $authenticated = true;
            }
        }
        
        if (!$authenticated) {
            $stmt = $pdo->prepare("SELECT * FROM Landlord WHERE email = ?");
            $stmt->execute([$email]);
            if ($user = $stmt->fetch()) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['landlordID'];
                    $_SESSION['role'] = 'landlord';
                    $_SESSION['name'] = $user['firstName'];
                    $authenticated = true;
                }
            }
        }

        if (!$authenticated) {
            $stmt = $pdo->prepare("SELECT * FROM Admin WHERE email = ?");
            $stmt->execute([$email]);
            if ($user = $stmt->fetch()) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['adminID'];
                    $_SESSION['role'] = 'admin';
                    $_SESSION['name'] = $user['firstName'];
                    $authenticated = true;
                }
            }
        }

        if ($authenticated) {
            header("Location: index.php");
            exit;
        } else {
            $flash = "Invalid email or password.";
        }
    }

    // Authentication: Register
    if ($action === 'register') {
        $role = $_POST['role'] ?? 'student';
        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            if ($role === 'student') {
                $studentNumber = htmlspecialchars($_POST['studentNumber']);
                $stmt = $pdo->prepare("INSERT INTO Student (firstName, lastName, email, password, studentNumber) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $email, $password, $studentNumber]);
            } else {
                $companyName = htmlspecialchars($_POST['companyName']);
                $stmt = $pdo->prepare("INSERT INTO Landlord (firstName, lastName, email, password, companyName) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $email, $password, $companyName]);
            }
            $flash = "Account created successfully. Please log in.";
        } catch (PDOException $e) {
            $flash = "Registration failed. Email may already be registered.";
        }
    }

    if ($action === 'logout') {
        session_destroy();
        header("Location: index.php");
        exit;
    }
    
    // Authenticated Actions
    if (isset($_SESSION['user_id'])) {

        // Profile Update
        if ($action === 'update_profile') {
            $firstName = htmlspecialchars($_POST['firstName']);
            $lastName = htmlspecialchars($_POST['lastName']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $role = $_SESSION['role'];
            $table = ucfirst($role);
            $idCol = $role . 'ID';
            if ($role === 'admin') $idCol = 'adminID';

            if (!empty($_POST['newPassword'])) {
                $passHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE $table SET firstName = ?, lastName = ?, email = ?, password = ? WHERE $idCol = ?");
                $stmt->execute([$firstName, $lastName, $email, $passHash, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE $table SET firstName = ?, lastName = ?, email = ? WHERE $idCol = ?");
                $stmt->execute([$firstName, $lastName, $email, $_SESSION['user_id']]);
            }

            if ($role === 'student' && isset($_POST['studentNumber'])) {
                $pdo->prepare("UPDATE Student SET studentNumber = ? WHERE studentID = ?")->execute([htmlspecialchars($_POST['studentNumber']), $_SESSION['user_id']]);
            } elseif ($role === 'landlord' && isset($_POST['companyName'])) {
                $pdo->prepare("UPDATE Landlord SET companyName = ? WHERE landlordID = ?")->execute([htmlspecialchars($_POST['companyName']), $_SESSION['user_id']]);
            }

            $_SESSION['name'] = $firstName;
            $flash = "Profile updated successfully.";
        }
        
        // Student Application
        if ($action === 'apply' && $_SESSION['role'] === 'student' && !empty($_POST['listingID'])) {         
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Application WHERE studentID = ? AND listingID = ?");
            $stmt->execute([$_SESSION['user_id'], $_POST['listingID']]);
            if ($stmt->fetchColumn() == 0) {             
                $moveInDate = !empty($_POST['moveInDate']) ? $_POST['moveInDate'] : date('Y-m-d', strtotime('+30 days'));
                $pdo->prepare("INSERT INTO Application (studentID, listingID, preferredMoveInDate, applicationStatus) VALUES (?, ?, ?, 'Pending')")
                    ->execute([$_SESSION['user_id'], $_POST['listingID'], $moveInDate]);
                $flash = 'Application submitted successfully.';         
            } else {             
                $flash = 'You have already applied for this property.';         
            }     
        }     
        
        // Landlord Add Listing
        if ($action === 'add_listing' && $_SESSION['role'] === 'landlord') {         
            $imagePath = 'images/default.jpg';
            if (isset($_FILES['listingImage']) && $_FILES['listingImage']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $targetFilePath = $uploadDir . time() . '_' . basename($_FILES['listingImage']['name']);
                if (move_uploaded_file($_FILES['listingImage']['tmp_name'], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                }
            }

            $pdo->prepare("INSERT INTO Listing (landlordID, title, address, monthlyRent, roomType, availabilityStatus, isVerifiedByAdmin, imagePath) VALUES (?, ?, ?, ?, ?, 'Vacant', 0, ?)")
                ->execute([$_SESSION['user_id'], htmlspecialchars($_POST['title']), htmlspecialchars($_POST['address']), (int)$_POST['monthlyRent'], $_POST['roomType'], $imagePath]);
            $flash = 'Listing added and pending admin verification.';     
        } 

        // Landlord Update Listing
        if ($action === 'update_listing' && $_SESSION['role'] === 'landlord') {
            $listingID = $_POST['listingID'] ?? '';
            $rent = (float)($_POST['monthlyRent'] ?? 0);
            $status = $_POST['availabilityStatus'] ?? 'Vacant';

            // Verify ownership
            $check = $pdo->prepare("SELECT listingID FROM Listing WHERE listingID = ? AND landlordID = ?");
            $check->execute([$listingID, $_SESSION['user_id']]);
            if ($check->fetch()) {
                $upd = $pdo->prepare("UPDATE Listing SET monthlyRent = ?, availabilityStatus = ? WHERE listingID = ?");
                $upd->execute([$rent, $status, $listingID]);
                $flash = "Property listing updated successfully.";
            } else {
                $flash = "Unauthorized action.";
            }
        }
        
        // Landlord Approve/Reject
        if (in_array($action, ['approve', 'reject']) && $_SESSION['role'] === 'landlord' && !empty($_POST['applicationID'])) {         
            $newStatus = $action === 'approve' ? 'Approved' : 'Rejected';
            $verify = $pdo->prepare("SELECT a.applicationID FROM Application a JOIN Listing l ON a.listingID = l.listingID WHERE a.applicationID = ? AND l.landlordID = ?");
            $verify->execute([$_POST['applicationID'], $_SESSION['user_id']]);
            
            if ($verify->fetch()) {
                $pdo->prepare("UPDATE Application SET applicationStatus = ? WHERE applicationID = ?")->execute([$newStatus, $_POST['applicationID']]);
                $flash = "Application {$newStatus}.";     
            }
        }

        // Student Payment
        if ($action === 'pay' && $_SESSION['role'] === 'student') {
            $txRef = 'TXN-' . strtoupper(uniqid());
            $pdo->prepare("INSERT INTO Payment (studentID, applicationID, listingID, amountPaid, paymentMethod, paymentStatus, transactionReference) VALUES (?, ?, ?, ?, ?, 'Successful', ?)")
                ->execute([$_SESSION['user_id'], $_POST['applicationID'], $_POST['listingID'], $_POST['amount'], $_POST['paymentMethod'], $txRef]);
            $pdo->prepare("UPDATE Listing SET availabilityStatus = 'Occupied' WHERE listingID = ?")->execute([$_POST['listingID']]);
            $flash = "Payment successful! Ref: $txRef";
        }

        // Maintenance Request Submission
        if ($action === 'submit_maintenance' && $_SESSION['role'] === 'student') {
            $pdo->prepare("INSERT INTO MaintenanceRequest (studentID, listingID, description, urgencyLevel) VALUES (?, ?, ?, ?)")
                ->execute([$_SESSION['user_id'], $_POST['listingID'], htmlspecialchars($_POST['description']), $_POST['urgencyLevel']]);
            $flash = "Maintenance request submitted.";
        }

        // Landlord Maintenance Status Update
        if ($action === 'update_maintenance' && $_SESSION['role'] === 'landlord') {
            $verify = $pdo->prepare("SELECT m.requestID FROM MaintenanceRequest m JOIN Listing l ON m.listingID = l.listingID WHERE m.requestID = ? AND l.landlordID = ?");
            $verify->execute([$_POST['requestID'], $_SESSION['user_id']]);
            if ($verify->fetch()) {
                $pdo->prepare("UPDATE MaintenanceRequest SET requestStatus = ? WHERE requestID = ?")->execute([$_POST['requestStatus'], $_POST['requestID']]);
                $flash = "Maintenance status updated.";
            }
        }

        // Admin Listing Verification
        if ($action === 'verify_listing' && $_SESSION['role'] === 'admin' && !empty($_POST['listingID'])) {
            $pdo->prepare("UPDATE Listing SET isVerifiedByAdmin = 1 WHERE listingID = ?")->execute([$_POST['listingID']]);
            $flash = "Listing verified successfully.";
        }
    }
}
?>