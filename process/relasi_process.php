<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $return_url = "../pages/admin/relasi.php";

    // --- LOGIKA TAMBAH ATURAN (CREATE) ---
    if ($action == 'create') {
        $kode_penyakit = $_POST['kode_penyakit'] ?? '';
        $list_gejala = $_POST['kode_gejala'] ?? []; // Ini berupa Array dari checklist

        if (empty($kode_penyakit) || empty($list_gejala)) {
            $_SESSION['error'] = 'Penyakit dan setidaknya satu gejala harus dipilih!';
            header("Location: $return_url");
            exit();
        }

        try {
            $pdo->beginTransaction();

            // Cari nomor aturan tertinggi saat ini
            $stmt_max = $pdo->query("SELECT MAX(CAST(SUBSTRING(kode_aturan, 2) AS UNSIGNED)) as max_rule FROM tbl_aturan");
            $max_row = $stmt_max->fetch(PDO::FETCH_ASSOC);
            $next_num = ($max_row['max_rule'] ?? 0) + 1;
            $kode_aturan = 'R' . str_pad($next_num, 2, '0', STR_PAD_LEFT);

            // No deletion needed when creating a new rule, as a fresh kode_aturan is generated.

            $success_count = 0;
            $insert = $pdo->prepare("INSERT INTO tbl_aturan (kode_aturan, kode_penyakit, kode_gejala) VALUES (?, ?, ?)");
            foreach ($list_gejala as $kode_gejala) {
                $insert->execute([$kode_aturan, $kode_penyakit, $kode_gejala]);
                $success_count++;
            }

            $pdo->commit();
            $_SESSION['success'] = "Aturan baru ($kode_aturan) berhasil ditambahkan dengan $success_count gejala.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Gagal menyimpan aturan: ' . $e->getMessage();
        }

        header("Location: $return_url");
        exit();
    }

    // --- LOGIKA UPDATE ATURAN (UPDATE) ---
    if ($action == 'update') {
        $kode_aturan = $_POST['kode_aturan'] ?? '';
        $kode_penyakit = $_POST['kode_penyakit'] ?? '';
        $list_gejala = $_POST['kode_gejala'] ?? []; // Ini berupa Array dari checklist

        if (empty($kode_aturan) || empty($kode_penyakit)) {
            $_SESSION['error'] = 'Aturan atau Penyakit tidak valid!';
            header("Location: $return_url");
            exit();
        }

        try {
            $pdo->beginTransaction();

            // 1. Hapus aturan lama berdasarkan kode_aturan
            $delete = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_aturan = ?");
            $delete->execute([$kode_aturan]);

            // 2. Masukkan aturan baru jika ada gejala yang dicentang
            $success_count = 0;
            if (!empty($list_gejala)) {
                $insert = $pdo->prepare("INSERT INTO tbl_aturan (kode_aturan, kode_penyakit, kode_gejala) VALUES (?, ?, ?)");
                foreach ($list_gejala as $kode_gejala) {
                    $insert->execute([$kode_aturan, $kode_penyakit, $kode_gejala]);
                    $success_count++;
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "Aturan ($kode_aturan) berhasil diperbarui. $success_count gejala dihubungkan.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Gagal memperbarui aturan: ' . $e->getMessage();
        }

        header("Location: $return_url");
        exit();
    }

    // --- LOGIKA HAPUS ATURAN (DELETE) ---
    if ($action == 'delete') {
        $kode_aturan = $_POST['kode_aturan'] ?? '';

        try {
            if (empty($kode_aturan)) {
                throw new Exception("Kode aturan tidak ditemukan!");
            }

            $stmt = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_aturan = ?");
            $stmt->execute([$kode_aturan]);

            $_SESSION['success'] = "Aturan $kode_aturan berhasil dihapus!";
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
