<?php
// Connessione al database
$host = 'localhost';
$db   = 'negozio_online';
$user = 'root';         
$pass = '';            
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// Opzioni PDO
// Queste opzioni sono per gestire gli errori, il fetch mode e le preparazioni di query.
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$conn = new PDO($dsn, $user, $pass, $opt);
?>