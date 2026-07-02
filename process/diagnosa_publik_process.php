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

    // Fetch all rules from tbl_aturan
    $stmt = $pdo->query("SELECT kode_penyakit, kode_aturan, kode_gejala FROM tbl_aturan");
    $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group rules by disease, then by rule code
    $disease_rules = [];
    foreach ($rules as $r) {
        $kp = $r['kode_penyakit'];
        $ka = !empty($r['kode_aturan']) ? $r['kode_aturan'] : $kp;
        $disease_rules[$kp][$ka][] = $r['kode_gejala'];
    }
    
    // Hitung Confidence per penyakit berdasarkan Jaccard Index terbaik dari ruleset
    $hasil_diagnosa = [];
    $total_input = count($gejala_input);
    
    foreach ($disease_rules as $kp => $ruleset) {
        $max_jaccard = 0;
        foreach ($ruleset as $ka => $gejala_aturan) {
            $total_aturan = count($gejala_aturan);
            if ($total_aturan > 0) {
                $cocok = count(array_intersect($gejala_input, $gejala_aturan));
                if ($cocok > 0) {
                    $union_size = $total_input + $total_aturan - $cocok;
                    $jaccard = $cocok / $union_size;
                    if ($jaccard > $max_jaccard) {
                        $max_jaccard = $jaccard;
                    }
                }
            }
        }
        
        if ($max_jaccard > 0) {
            $hasil_diagnosa[] = [
                'kode' => $kp, 
                'persentase' => $max_jaccard // Disimpan dalam desimal (0 s.d 1) konsisten dengan standard DB
            ];
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