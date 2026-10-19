<?php
require_once 'auth.php';
$authData = authenticate($pdo); // Accepts any valid role

$user = $authData['user'];
$role = $authData['role'];

$response = [
    'status' => 'success',
    'data' => [
        'id' => $authData['id'],
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'email' => $user['email'],
        'role' => $role
    ]
];

// Add role-specific fields
if ($role === 'student') {
    $response['data']['studentNumber'] = $user['studentNumber'];
} elseif ($role === 'landlord') {
    $response['data']['companyName'] = $user['companyName'];
}

echo json_encode($response);
?>