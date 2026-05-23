<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    // --- PROSES HAPUS (DELETE) ---
    if ($_POST['action'] == 'delete') {
        // Menggunakan id_diagnosa sesuai nama kolom di tabelmu
        $id_diagnosa = $_POST['id_diagnosa'] ?? '';

        if (!empty($id_diagnosa)) {
            try {
                // Eksekusi hapus data dari database
                $stmt = $pdo->prepare("DELETE FROM tbl_diagnosa WHERE id_diagnosa = ?");
                $stmt->execute([$id_diagnosa]);
                
                $_SESSION['success'] = "Data riwayat diagnosa berhasil dihapus permanen.";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "ID Diagnosa tidak ditemukan!";
        }

        // Kembali ke halaman riwayat
        header("Location: ../pages/admin/riwayat.php");
        exit();
    }
}

// Jika diakses langsung lewat URL tanpa lewat form, tendang balik
header("Location: ../pages/admin/riwayat.php");
exit();
?>