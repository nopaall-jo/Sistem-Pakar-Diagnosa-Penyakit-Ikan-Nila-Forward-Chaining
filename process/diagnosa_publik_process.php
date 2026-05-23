<?php
session_start();
require_once '../config/database.php'; 

if (isset($_POST['submit_diagnosa'])) {
    
    $nama_peternak = isset($_POST['nama_peternak']) && !empty($_POST['nama_peternak']) ? $_POST['nama_peternak'] : 'Peternak NN';
    
    if (!isset($_POST['gejala_terpilih']) || empty($_POST['gejala_terpilih'])) {
        $_SESSION['error'] = "Anda harus memilih minimal satu gejala klinis!";
        header("Location: ../konsultasi.php");
        exit();
    }

    $gejala_input = $_POST['gejala_terpilih']; 
    $tanggal_diagnosa = date('Y-m-d');

    // MESIN INFERENSI FORWARD CHAINING (Versi PDO)
    $hasil_diagnosa = []; 

    $stmt_penyakit = $pdo->query("SELECT id_penyakit FROM tbl_penyakit");
    $penyakit_list = $stmt_penyakit->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($penyakit_list as $penyakit) {
        $id_penyakit = $penyakit['id_penyakit'];

        // Ambil aturan menggunakan PDO Prepare
        $stmt_aturan = $pdo->prepare("SELECT kode_gejala FROM tbl_relasi WHERE id_penyakit = ?");
        $stmt_aturan->execute([$id_penyakit]);
        $aturan_list = $stmt_aturan->fetchAll(PDO::FETCH_ASSOC);

        $gejala_penyakit = []; 
        foreach ($aturan_list as $aturan) {
            $gejala_penyakit[] = $aturan['kode_gejala']; // Ganti di sini juga
        }

        $total_gejala_penyakit = count($gejala_penyakit);
        
        if ($total_gejala_penyakit > 0) {
            $gejala_cocok = 0;
            foreach ($gejala_input as $input) {
                if (in_array($input, $gejala_penyakit)) {
                    $gejala_cocok++; 
                }
            }

            $persentase = ($gejala_cocok / $total_gejala_penyakit) * 100;

            if ($persentase > 0) {
                $hasil_diagnosa[] = [
                    'id_penyakit' => $id_penyakit,
                    'persentase' => round($persentase, 2)
                ];
            }
        }
    }

    // PENENTUAN HASIL & SIMPAN
    if (empty($hasil_diagnosa)) {
        $_SESSION['error'] = "Gejala yang dipilih tidak mengarah pada penyakit ikan nila manapun yang ada di sistem.";
        header("Location: ../konsultasi.php");
        exit();
    }

    usort($hasil_diagnosa, function($a, $b) {
        return $b['persentase'] <=> $a['persentase'];
    });

    $penyakit_tertinggi = $hasil_diagnosa[0];
    $id_penyakit_final = $penyakit_tertinggi['id_penyakit'];
    $persentase_final = $penyakit_tertinggi['persentase'];

    // Simpan ke database menggunakan PDO
    try {
        $stmt_simpan = $pdo->prepare("INSERT INTO tbl_riwayat (nama_peternak, tanggal_diagnosa, id_penyakit, hasil_persentase) VALUES (?, ?, ?, ?)");
        $stmt_simpan->execute([$nama_peternak, $tanggal_diagnosa, $id_penyakit_final, $persentase_final]);
        
        $id_riwayat_baru = $pdo->lastInsertId(); // Ambil ID terakhir
        
        $_SESSION['success'] = "Diagnosa berhasil dilakukan!";
        header("Location: ../hasil_publik.php?id=" . $id_riwayat_baru);
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal menyimpan riwayat: " . $e->getMessage();
        header("Location: ../konsultasi.php");
        exit();
    }

} else {
    header("Location: ../konsultasi.php");
    exit();
}
?>