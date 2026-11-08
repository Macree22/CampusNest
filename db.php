<?php
// Use getenv() to read from Azure App Settings, fallback to hardcoded if not found
$host = getenv('DB_HOST') ?: 'campusnest-db.mysql.database.azure.com';
$db   = getenv('DB_DATABASE') ?: 'campusnest';
$user = getenv('DB_USERNAME') ?: 'campusnest_user';
$pass = getenv('DB_PASSWORD') ?: 'Rend*ujS5';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=3306;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA') ?: __DIR__ . '/ssl/DigiCertGlobalRootG2.crt.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>