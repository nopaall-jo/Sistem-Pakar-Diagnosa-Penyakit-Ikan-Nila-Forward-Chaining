<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php';

if (isset($_POST['submit_diagnosa'])) {
    $kode_sampel = htmlspecialchars($_POST['kode_sampel']);
    $gejala_input = $_POST['gejala_terpilih'] ?? [];

    if (empty($gejala_input)) {
        $_SESSION['error'] = "Pilih minimal satu gejala klinis!";
        header("Location: ../konsultasi.php");
        exit();
    }

    // Algoritma Forward Chaining
    $penyakit = $pdo->query("SELECT kode_penyakit FROM tbl_penyakit")->fetchAll(PDO::FETCH_ASSOC);
    $hasil_diagnosa = [];

    foreach ($penyakit as $p) {
        $kp = $p['kode_penyakit'];
        // Ambil daftar gejala untuk penyakit ini dari tbl_aturan
        $stmt = $pdo->prepare("SELECT kode_gejala FROM tbl_aturan WHERE kode_penyakit = ?");
        $stmt->execute([$kp]);
        $gejala_aturan = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $total_aturan = count($gejala_aturan);
        if ($total_aturan > 0) {
            // Bandingkan kecocokan fakta (input) dengan rule base (aturan)
            $cocok = count(array_intersect($gejala_input, $gejala_aturan));
            $persentase = ($cocok / $total_aturan) * 100;
            if ($persentase > 0) {
                $hasil_diagnosa[] = ['kode' => $kp, 'persentase' => round($persentase, 2)];
            }
        }
    }

    if (empty($hasil_diagnosa)) {
        $_SESSION['error'] = "Gejala tidak cocok dengan penyakit manapun.";
        header("Location: ../konsultasi.php");
        exit();
    }

    // Urutkan persentase tertinggi
    usort($hasil_diagnosa, fn($a, $b) => $b['persentase'] <=> $a['persentase']);
    $final = $hasil_diagnosa[0];

    try {
        $pdo->beginTransaction();
        // Simpan ke tabel diagnosa utama
        $stmt = $pdo->prepare("INSERT INTO tbl_diagnosa (id_admin, kode_sampel, hasil_penyakit, confidence, tanggal_diagnosa) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([null, $kode_sampel, $final['kode'], $final['persentase']]);
        $id_diagnosa = $pdo->lastInsertId();

        // Simpan detail gejala yang dipilih
        $stmt_detail = $pdo->prepare("INSERT INTO tbl_diagnosa_detail (id_diagnosa, kode_gejala) VALUES (?, ?)");
        foreach ($gejala_input as $g) {
            $stmt_detail->execute([$id_diagnosa, $g]);
        }
        $pdo->commit();

        $_SESSION['success'] = "Diagnosa berhasil dilakukan!";
        header("Location: ../hasil_publik.php?id=" . $id_diagnosa);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Gagal memproses diagnosa: " . $e->getMessage();
    }
}
header("Location: ../konsultasi.php");
exit();