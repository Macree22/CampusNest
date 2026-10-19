<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allows the Android app to connect
header('Access-Control-Allow-Headers: Authorization, Content-Type');

require_once '../db.php';

function authenticate($pdo, $expectedRole = null) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (strpos($authHeader, 'Bearer ') !== false) {
        $token = str_replace('Bearer ', '', $authHeader);

        // Check all tables for this token
        $tables = ['Student', 'Landlord', 'Admin'];
        foreach ($tables as $table) {
            $idCol = strtolower($table) . 'ID';
            if ($table === 'Admin') $idCol = 'adminID';
            
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE api_token = ?");
            $stmt->execute([$token]);
            if ($user = $stmt->fetch()) {
                $role = strtolower($table);
                if ($expectedRole && $role !== $expectedRole) {
                    echo json_encode(['status' => 'error', 'message' => 'Unauthorized role']);
                    exit;
                }
                return ['user' => $user, 'role' => $role, 'id' => $user[$idCol]];
            }
        }
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Invalid or missing token.']);
    exit;
}
?>