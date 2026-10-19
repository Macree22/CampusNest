<?php
header('Content-Type: application/json');
require_once 'auth.php';

// Authenticate the user (accepts Student, Landlord, or Admin)
$authData = authenticate($pdo);
$user = $authData['user'];
$role = $authData['role'];

$response = [
    'status' => 'success',
    'data' => [
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'email' => $user['email'],
        'role' => $role
    ]
];

// Add student number if they are a student
if ($role === 'student' && isset($user['studentNumber'])) {
    $response['data']['studentNumber'] = $user['studentNumber'];
}

echo json_encode($response);
?>