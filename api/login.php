<?php
header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$authenticated = false;
$tables = ['Student', 'Landlord', 'Admin'];

foreach ($tables as $table) {
    $idCol = strtolower($table) . 'ID';
    if ($table === 'Admin') $idCol = 'adminID';

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    if ($user = $stmt->fetch()) {
        if (password_verify($password, $user['password'])) {
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
}

if (!$authenticated) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
}
?>