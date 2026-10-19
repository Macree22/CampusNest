<?php
// Secure Database Connection via PDO
$host = '127.0.0.1';
$db   = 'campusnest';
$user = 'root';
$pass = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("<div style='font-family:sans-serif; padding:20px; color:#991b1b; background:#fef2f2; border:1px solid #fecaca; border-radius:12px; max-width:600px; margin:40px auto;'>
        <h3 style='margin-top:0;'>Database Connection Error</h3>
        <p>Unable to connect to the CampusNest database. Please ensure MySQL is running and that you have executed <b>setup_db.php</b>.</p>
        <p style='font-size:12px; color:#6b7280;'>Error details: " . htmlspecialchars($e->getMessage()) . "</p>
    </div>");
}
?>