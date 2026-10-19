<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);
$firstName = $data['firstName'] ?? '';
$lastName = $data['lastName'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$studentNumber = $data['studentNumber'] ?? '';

if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($studentNumber)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM Student WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Email is already registered']);
        exit;
    }

    // Hash the password and insert
    $passHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO Student (firstName, lastName, email, password, studentNumber) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $email, $passHash, $studentNumber]);

    echo json_encode(['status' => 'success', 'message' => 'Account created successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>