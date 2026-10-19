<?php
// Prevent HTML errors from breaking the JSON response
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password required']);
    exit;
}

$authenticated = false;
$tables = ['Student', 'Landlord', 'Admin'];

foreach ($tables as $table) {
    $idCol = strtolower($table) . 'ID';
    if ($table === 'Admin') $idCol = 'adminID';

    try {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password'])) {
                // Generate new API Token
                $token = bin2hex(random_bytes(32));
                $pdo->prepare("UPDATE $table SET api_token = ? WHERE $idCol = ?")->execute([$token, $user[$idCol]]);
                
                echo json_encode([
                    'status' => 'success',
                    'token' => $token,
                    'role' => strtolower($table),
                    'name' => $user['firstName'],
                    'id' => $user[$idCol]
                ]);
                $authenticated = true;
                break;
            }
        }
    } catch (PDOException $e) {
        // Catch DB errors and send them as JSON instead of HTML
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
        exit;
    }
}

if (!$authenticated) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
}
?>