<?php
$host = 'localhost';
$db   = 'negozio_online';
$user = 'root';         // Sostituisci con il tuo user DB
$pass = '';             // Sostituisci con la tua password DB
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$conn = new PDO($dsn, $user, $pass, $opt);
?>