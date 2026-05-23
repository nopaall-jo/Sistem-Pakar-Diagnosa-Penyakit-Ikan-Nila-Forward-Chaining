<?php 
session_start();
require_once '../config/database.php';

$base_url = "http://localhost/sistem_pakar_ikan_nila/";

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'read') {
    $id_admin = $_GET['id_admin'] ?? '';

    $stmt = $pdo->prepare("SELECT id_admin, username, nama_admin FROM tbl_admin WHERE id_admin = ?");
    $stmt->execute([$id_admin]);
    
    // PERBAIKAN: Masukkan hasilnya ke variabel $admin, BUKAN $stmt
    $admin = $stmt->fetch(PDO::FETCH_ASSOC); 

    if ($admin) {
        header('Content-Type: application/json');
        echo json_encode($admin);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Administrator tidak ditemukan']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $username = $_POST['username'] ?? '';
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama_admin = $_POST['nama_admin'] ?? '';

        try {
            $cek = $pdo->prepare("SELECT id_admin FROM tbl_admin WHERE username = ?");
            $cek->execute([$username]);
            if ($cek->rowCount() > 0){
                $_SESSION['error'] = 'Gagal: Username sudah digunakan!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO tbl_admin (username, password, nama_admin) VALUES (?, ?, ?)");
                $stmt->execute([$username, $password, $nama_admin]);
                $_SESSION['success'] = 'Administrator baru berhasil ditambahkan!';   
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menambahkan admin: ' . $e->getMessage();
        }
        header("Location: ". $base_url . "pages/admin/data_admin.php");
        exit();
    }

    if ($action == 'update') {
        $id_admin = $_POST['id_admin'] ?? '';
        $nama_admin = $_POST['nama_admin'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE tbl_admin SET nama_admin = ?, password = ? WHERE id_admin = ?");
                $stmt->execute([$nama_admin, $password_hash, $id_admin]);
            } else {
                $stmt = $pdo->prepare("UPDATE tbl_admin SET nama_admin = ? WHERE id_admin = ?");
                $stmt->execute([$nama_admin, $id_admin]);
            }

            $_SESSION['success'] = 'Data Administrator berhasil diperbarui!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal memperbarui admin: ' . $e->getMessage();
        }
        header("Location: " . $base_url . "pages/admin/data_admin.php");
    }

    if ($action == 'delete') {
        $id_admin = $_POST['id_admin'] ?? '';

        try {
            $stmt = $pdo->prepare("DELETE FROM tbl_admin WHERE id_admin = ?");
            $stmt->execute([$id_admin]);

            $_SESSION['success'] = 'Administrator berhasil dihapus!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menghapus admin: ' . $e->getMessage();
        }
        header("Location: " . $base_url . "pages/admin/data_admin.php");
    }
}
?>