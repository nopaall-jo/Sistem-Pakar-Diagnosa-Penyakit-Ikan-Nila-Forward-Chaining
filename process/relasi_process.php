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

            // Hapus jika sebelumnya sudah ada (untuk mencegah duplikasi)
            $delete = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_penyakit = ?");
            $delete->execute([$kode_penyakit]);

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
        $kode_penyakit = $_POST['kode_penyakit'] ?? '';
        $list_gejala = $_POST['kode_gejala'] ?? []; // Ini berupa Array dari checklist

        if (empty($kode_penyakit)) {
            $_SESSION['error'] = 'Penyakit tidak valid!';
            header("Location: $return_url");
            exit();
        }

        try {
            $pdo->beginTransaction();

            // Cari kode_aturan yang sudah ada untuk penyakit ini agar tetap konsisten
            $stmt_existing = $pdo->prepare("SELECT kode_aturan FROM tbl_aturan WHERE kode_penyakit = ? LIMIT 1");
            $stmt_existing->execute([$kode_penyakit]);
            $existing_row = $stmt_existing->fetch(PDO::FETCH_ASSOC);

            if ($existing_row) {
                $kode_aturan = $existing_row['kode_aturan'];
            } else {
                // Jika sebelumnya belum ada kode aturan, buat baru
                $stmt_max = $pdo->query("SELECT MAX(CAST(SUBSTRING(kode_aturan, 2) AS UNSIGNED)) as max_rule FROM tbl_aturan");
                $max_row = $stmt_max->fetch(PDO::FETCH_ASSOC);
                $next_num = ($max_row['max_rule'] ?? 0) + 1;
                $kode_aturan = 'R' . str_pad($next_num, 2, '0', STR_PAD_LEFT);
            }

            // 1. Hapus aturan lama untuk penyakit ini
            $delete = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_penyakit = ?");
            $delete->execute([$kode_penyakit]);

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
        $kode_penyakit = $_POST['kode_penyakit'] ?? '';

        try {
            if (empty($kode_penyakit)) {
                throw new Exception("Kode penyakit tidak ditemukan!");
            }

            $stmt = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_penyakit = ?");
            $stmt->execute([$kode_penyakit]);

            $_SESSION['success'] = "Seluruh aturan gejala untuk penyakit tersebut berhasil dihapus!";
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
