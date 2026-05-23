<?php
session_start();

require_once 'config/database.php'; // 1. Panggil config untuk mendapatkan variabel $base_url

session_unset(); // 2. Bersihkan semua variabel session

session_destroy(); // 3. Hancurkan session

header("Location: " . $base_url); // 4. Arahkan ke root (halaman index utama)
exit();
?>