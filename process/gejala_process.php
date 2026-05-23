<?php
// 1. WAJIB: Mulai session di baris paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Hubungkan ke database
require_once '../config/database.php';

// Tentukan URL tujuan balik (pake path relatif biar aman)
$return_url = "../pages/admin/gejala.php";

// ==========================================================
// A. LOGIKA READ (Untuk AJAX saat tombol Edit ditekan)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'read') {
    $kode = $_GET['kode_gejala'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM tbl_gejala WHERE kode_gejala = ?");
    $stmt->execute([$kode]);
    $gejala = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($gejala) {
        header('Content-Type: application/json');
        echo json_encode($gejala);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Data Gejala tidak ditemukan']);
    }
    exit();
}

// ==========================================================
// B. LOGIKA POST (Tambah, Edit, Hapus)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // --- 1. TAMBAH GEJALA ---
    if ($action == 'create') {
        $kode_input = trim($_POST['kode_gejala']);
        $nama = htmlspecialchars(trim($_POST['nama_gejala']));
        
        // PERBAIKAN LOGIKA PREFIX 'G': 
        // Bersihkan huruf G jika user terlanjur mengetiknya, lalu paksa tambah huruf 'G' di depannya
        $kode_bersih = ltrim(strtoupper($kode_input), 'G'); 
        $kode = 'G' . $kode_bersih; 
        
        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_gejala (kode_gejala, nama_gejala) VALUES (?, ?)");
            $stmt->execute([$kode, $nama]);
            
            $_SESSION['success'] = "Gejala <b>$kode</b> berhasil ditambahkan ke database!";
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal tambah: ' . ($e->getCode() == 23000 ? 'Kode Gejala sudah digunakan!' : $e->getMessage());
        }
    }
    
    // --- 2. EDIT/UPDATE GEJALA ---
    elseif ($action == 'update') {
        // Untuk update, kodenya diambil dari input hidden yang sudah pasti benar formatnya
        $kode = htmlspecialchars(trim($_POST['kode_gejala']));
        $nama = htmlspecialchars(trim($_POST['nama_gejala']));
        
        try {
            $stmt = $pdo->prepare("UPDATE tbl_gejala SET nama_gejala = ? WHERE kode_gejala = ?");
            $stmt->execute([$nama, $kode]);
            
            $_SESSION['success'] = "Gejala <b>$kode</b> berhasil diperbarui!";
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal perbarui: ' . $e->getMessage();
        }
    }
    
    // --- 3. HAPUS GEJALA ---
    elseif ($action == 'delete') {
        $kode = $_POST['kode_gejala'] ?? '';
        
        try {
            $pdo->beginTransaction();
            
            // Hapus di tbl_aturan dulu (karena foreign key)
            $stmtAturan = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_gejala = ?");
            $stmtAturan->execute([$kode]);
            
            // Hapus di tbl_gejala
            $stmtGejala = $pdo->prepare("DELETE FROM tbl_gejala WHERE kode_gejala = ?");
            $stmtGejala->execute([$kode]);
            
            $pdo->commit();
            $_SESSION['success'] = "Gejala <b>$kode</b> dan aturannya berhasil dihapus!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Gagal hapus: ' . $e->getMessage();
        }
    }

    // Balik ke halaman utama gejala
    header("Location: $return_url");
    exit();
}
?>