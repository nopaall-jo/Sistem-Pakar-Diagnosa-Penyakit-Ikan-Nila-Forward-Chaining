<?php
$host = 'localhost';
$dbname = 'db_ikan_nila';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database ke $dbname gagal: " . $e->getMessage());
}

// Set base URL
$base_url = 'http://localhost/sistem_pakar_ikan_nila/';
?>