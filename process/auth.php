<?php
session_start(); // Pastikan session dimulai di paling atas
require_once '../config/database.php';

// Ambil base_url dari config jika ada, jika tidak, tentukan manual
$base_url = "http://localhost/sistem_pakar_ikan_nila/"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    // --- LOGIKA LOGIN ADMIN ---
    if ($action == 'login') {
        // SECURITY 1: Bersihkan input dari spasi berlebih dan tag HTML/JS berbahaya (Anti-XSS)
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'];

        // Sesuaikan dengan tabel tbl_admin
        $stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // Verifikasi password (asumsinya password di DB sudah di-hash dengan password_hash)
        if ($admin && password_verify($password, $admin['password'])) {
            // SECURITY 3: Mencegah Session Hijacking/Fixation (Penting untuk Skripsi!)
            session_regenerate_id(true);

            $_SESSION['id_admin']   = $admin['id_admin'];
            $_SESSION['username']   = $admin['username'];
            $_SESSION['nama_admin'] = $admin['nama_admin'];
            $_SESSION['status']     = 'login'; // Penanda bahwa admin sudah masuk
            
            $response = [
                'status'   => 'success',
                'redirect' => $base_url . 'pages/admin/dashboard.php'
            ];
        } else {
            $response = [
                'status'  => 'error', 
                'message' => 'Username atau password Admin salah!'
            ];
        }
        
        echo json_encode($response);
    } 

    // --- LOGIKA REGISTER ADMIN (Opsional) ---
    elseif ($action == 'register') {
        $username   = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $nama_admin = htmlspecialchars(trim($_POST['nama_admin']), ENT_QUOTES, 'UTF-8');
        
        $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            // Sesuaikan kolom dengan tbl_admin
            $stmt = $pdo->prepare("INSERT INTO tbl_admin (username, password, nama_admin) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $nama_admin]);
            
            $response = [
                'status'   => 'success', 
                'redirect' => $base_url . 'pages/auth/login.php?register=success'
            ];
        } catch (PDOException $e) {
            $response = [
                'status'  => 'error', 
                'message' => 'Gagal menambah Admin: ' . $e->getMessage()
            ];
        }
        
        echo json_encode($response);
    }
}
?>