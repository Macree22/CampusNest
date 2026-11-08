<?php
$host = 'campusnest-db.mysql.database.azure.com';
$db   = 'campusnest';
$user = 'campusnest_user';
$pass = 'Rend*ujS5'; // The password you used in Workbench
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Point to the certificate file in the same folder
    // Update this line in db.php
PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA') ?: __DIR__ . '/ssl/DigiCertGlobalRootCA2.crt.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>