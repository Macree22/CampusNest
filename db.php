<?php
$host = 'campusnest-db.mysql.database.azure.com';
$db   = 'campusnest';
$user = 'campusnest_user';
$pass = 'Rend*ujS5'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=3306;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // HARDCODED ABSOLUTE PATH
    PDO::MYSQL_ATTR_SSL_CA       => '/home/site/wwwroot/ssl/DigiCertGlobalRootG2.crt.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully!"; // Just for testing
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>