<?php
header('Content-Type: application/json');
require_once 'auth.php';

// Authenticate the user
$authData = authenticate($pdo);
$role = $authData['role'];
$id = $authData['id'];
$table = ucfirst($role);
$idCol = $role . 'ID';
if ($role === 'admin') $idCol = 'adminID';

$data = json_decode(file_get_contents('php://input'), true);
$firstName = $data['firstName'] ?? '';
$lastName = $data['lastName'] ?? '';
$email = $data['email'] ?? '';
$newPassword = $data['newPassword'] ?? '';

if (empty($firstName) || empty($lastName) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Name and email are required']);
    exit;
}

try {
    if (!empty($newPassword)) {
        // Update with password
        $passHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE $table SET firstName = ?, lastName = ?, email = ?, password = ? WHERE $idCol = ?");
        $stmt->execute([$firstName, $lastName, $email, $passHash, $id]);
    } else {
        // Update without password
        $stmt = $pdo->prepare("UPDATE $table SET firstName = ?, lastName = ?, email = ? WHERE $idCol = ?");
        $stmt->execute([$firstName, $lastName, $email, $id]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>