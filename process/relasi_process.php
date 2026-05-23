<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $return_url = "../pages/admin/relasi.php";

    // --- LOGIKA TAMBAH ATURAN (CREATE) ---
    if ($action == 'create') {
        $kode_penyakit = $_POST['kode_penyakit'] ?? '';
        $list_gejala = $_POST['kode_gejala'] ?? []; // Ini berupa Array dari Select2

        if (empty($kode_penyakit) || empty($list_gejala)) {
            $_SESSION['error'] = 'Penyakit dan setidaknya satu gejala harus dipilih!';
            header("Location: $return_url");
            exit();
        }

        try {
            $pdo->beginTransaction();
            
            $success_count = 0;
            $duplicate_count = 0;

            // Loop karena admin bisa memilih banyak gejala sekaligus
            foreach ($list_gejala as $kode_gejala) {
                // 1. Cek apakah aturan ini sudah ada (SINKRON: tbl_aturan)
                $check = $pdo->prepare("SELECT * FROM tbl_aturan WHERE kode_penyakit = ? AND kode_gejala = ?");
                $check->execute([$kode_penyakit, $kode_gejala]);

                if ($check->rowCount() == 0) {
                    // 2. Jika belum ada, masukkan ke tbl_aturan
                    $insert = $pdo->prepare("INSERT INTO tbl_aturan (kode_penyakit, kode_gejala) VALUES (?, ?)");
                    $insert->execute([$kode_penyakit, $kode_gejala]);
                    $success_count++;
                } else {
                    $duplicate_count++;
                }
            }

            $pdo->commit();

            if ($success_count > 0) {
                $_SESSION['success'] = "$success_count aturan baru berhasil ditambahkan.";
            }
            if ($duplicate_count > 0) {
                $_SESSION['error'] = "$duplicate_count aturan sudah ada sebelumnya (duplikat) dan dilewati.";
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Gagal menyimpan aturan: ' . $e->getMessage();
        }
        
        header("Location: $return_url");
        exit();
    }
    
    // --- LOGIKA HAPUS ATURAN (DELETE) ---
    if ($action == 'delete') {
        // SINKRON: Menggunakan id_aturan sesuai struktur tabel kamu
        $id_aturan = $_POST['id_aturan'] ?? '';
        
        try {
            if (empty($id_aturan)) {
                throw new Exception("ID Aturan tidak ditemukan!");
            }

            $stmt = $pdo->prepare("DELETE FROM tbl_aturan WHERE id_aturan = ?");
            $stmt->execute([$id_aturan]);
            
            $_SESSION['success'] = "Aturan berhasil dihapus dari basis pengetahuan!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal hapus aturan: " . $e->getMessage();
        }

        header("Location: $return_url");
        exit();
    }
}

// Tendang balik jika akses tidak sah
header("Location: ../pages/admin/relasi.php");
exit();