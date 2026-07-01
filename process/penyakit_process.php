<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'read') {
    $kode = $_GET['kode_penyakit'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM tbl_penyakit WHERE kode_penyakit = ?");
    $stmt->execute([$kode]);
    $penyakit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($penyakit) {
        header('Content-Type: application/json');
        echo json_encode($penyakit);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Data Penyakit tidak ditemukan']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    $return_url = "../pages/admin/penyakit.php"; 

    if ($action == 'create') {
        $kode_input = trim($_POST['kode_penyakit'] ?? '');
        $nama       = htmlspecialchars(trim($_POST['nama_penyakit'] ?? ''));
        $deskripsi  = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $solusi     = htmlspecialchars(trim($_POST['solusi'] ?? ''));
        $pencegahan = htmlspecialchars(trim($_POST['pencegahan'] ?? ''));
        
        $kode_bersih = ltrim(strtoupper($kode_input), 'P');
        $kode = 'P' . $kode_bersih;
        
        try {
            if (empty($kode_input) || empty($nama)) {
                throw new Exception("Kode dan Nama Penyakit wajib diisi!");
            }
            
            $stmt = $pdo->prepare("INSERT INTO tbl_penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi, pencegahan) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$kode, $nama, $deskripsi, $solusi, $pencegahan]);
            
            $_SESSION['success'] = "Data penyakit <b>$kode</b> berhasil ditambahkan.";
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menambah data: ' . ($e->getCode() == 23000 ? "Kode penyakit <b>$kode</b> sudah ada di database." : $e->getMessage());
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header("Location: $return_url");
        exit();
    }
    
    elseif ($action == 'update') {
        $kode       = htmlspecialchars(trim($_POST['kode_penyakit'] ?? ''));
        $nama       = htmlspecialchars(trim($_POST['nama_penyakit'] ?? ''));
        $deskripsi  = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $solusi     = htmlspecialchars(trim($_POST['solusi'] ?? ''));
        $pencegahan = htmlspecialchars(trim($_POST['pencegahan'] ?? ''));
        
        try {
            if (empty($nama)) {
                throw new Exception("Nama Penyakit tidak boleh kosong!");
            }
            
            $stmt = $pdo->prepare("UPDATE tbl_penyakit SET 
                                  nama_penyakit = ?, deskripsi = ?, solusi = ?, pencegahan = ? 
                                  WHERE kode_penyakit = ?");
            $stmt->execute([$nama, $deskripsi, $solusi, $pencegahan, $kode]);
            
            $_SESSION['success'] = "Perubahan data penyakit <b>$kode</b> berhasil disimpan.";
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal memperbarui data: ' . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header("Location: $return_url");
        exit();
    }
    
    elseif ($action == 'delete') {
        $kode = $_POST['kode_penyakit'] ?? '';
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("DELETE FROM tbl_aturan WHERE kode_penyakit = ?");
            $stmt->execute([$kode]);
            
            $stmt = $pdo->prepare("DELETE FROM tbl_penyakit WHERE kode_penyakit = ?");
            $stmt->execute([$kode]);
            
            $pdo->commit();
            $_SESSION['success'] = "Data penyakit <b>$kode</b> dan seluruh relasinya berhasil dihapus permanen.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Sistem gagal menghapus data: ' . $e->getMessage();
        }
        
        header("Location: $return_url");
        exit();
    }
}
header("Location: ../pages/admin/penyakit.php");
exit();
?>