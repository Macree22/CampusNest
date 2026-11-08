<?php
$host = 'campusnest-db.mysql.database.azure.com';
$user = 'campusnest_user';
$pass = 'Rend*ujS5'; 
$db   = 'campusnest';

// Use mysqli instead of PDO
$conn = mysqli_init();

// Set the SSL cert (if you turned SSL off in Azure, you can remove this line)
mysqli_ssl_set($conn, NULL, NULL, '/home/site/wwwroot/ssl/DigiCertGlobalRootG2.crt.pem', NULL, NULL);

// Connect
if (!mysqli_real_connect($conn, $host, $user, $pass, $db, 3306)) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully!";
?>