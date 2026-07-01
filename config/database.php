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

// Helper untuk cetak tanggal ttd dengan nama Hari
function getTanggalTtdIndo() {
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return "Bojong Gede, " . $hari[date('l')] . ", " . date('d') . " " . $bulan[(int)date('m')] . " " . date('Y');
}
?>