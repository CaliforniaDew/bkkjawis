<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// config.php - Konfigurasi Database
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



$host = 'localhost';
$dbname = 'u393107046_bkk';
$username = 'u393107046_admin';
$password = 'bkk123Z@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

?>