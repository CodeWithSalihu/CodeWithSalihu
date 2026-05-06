<?php

$host     = getenv('DB_HOST') ?: 'localhost';
$dbname   = getenv('DB_NAME') ?: 'degrandh_app123';
$username = getenv('DB_USER') ?: 'degrandh';
$password = getenv('DB_PASS') ?: 'Pass@Barup';
$charset  = 'utf8mb4';
$timezone = getenv('TIMEZONE') ?: 'Africa/Lagos';


$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("DB Connection Failed: " . $e->getMessage());
    die("<div style='text-align:center; margin-top:100px; font-family:Arial;'>
            <h1 style='color:#00A651;'>DE GRAND HOTEL</h1>
            <h3>Service Temporarily Unavailable</h3>
            <p>Please try again later.</p>
         </div>");
}

date_default_timezone_set('Africa/Lagos');
?>