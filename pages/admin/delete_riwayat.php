<?php
// Mulai session dan panggil koneksi database SAJA (Jangan panggil header.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Cek apakah parameter id ada dan tidak kosong
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Data ID riwayat tidak valid!";
    header("Location: riwayat.php");
    exit();
}

$id_diagnosa = $_GET['id'];

// Mulai Transaksi Database (Mencegah data terhapus setengah-setengah)
$pdo->beginTransaction();

try {
    // 1. Pertama, hapus data anak (gejala yang dicentang) di tabel tbl_diagnosa_detail
    $stmt = $pdo->prepare("DELETE FROM tbl_diagnosa_detail WHERE id_diagnosa = ?");
    $stmt->execute([$id_diagnosa]);
    
    // 2. Kemudian, hapus data induk (riwayat) di tabel tbl_diagnosa
    // Perbaikan: WHERE id_diagnosa = ?, bukan WHERE id = ?
    $stmt = $pdo->prepare("DELETE FROM tbl_diagnosa WHERE id_diagnosa = ?");
    $stmt->execute([$id_diagnosa]);
    
    // 3. Simpan perubahan secara permanen (Commit) jika kedua query di atas sukses
    $pdo->commit();
    
    // Set pesan sukses
    $_SESSION['success'] = "Riwayat diagnosa berhasil dihapus permanen.";
} catch (PDOException $e) {
    // 4. Batalkan semua penghapusan (Rollback) jika terjadi error di tengah jalan
    $pdo->rollBack();
    $_SESSION['error'] = "Sistem Gagal Menghapus: " . $e->getMessage();
}

// Kembalikan Admin ke halaman tabel riwayat
header("Location: riwayat.php");
exit();
?>