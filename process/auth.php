<?php
session_start(); 
require_once '../config/database.php';

$base_url = "http://localhost/sistem_pakar_ikan_nila/"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'login') {
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);

            $_SESSION['id_admin']   = $admin['id_admin'];
            $_SESSION['username']   = $admin['username'];
            $_SESSION['nama_admin'] = $admin['nama_admin'];
            $_SESSION['status']     = 'login'; 
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

    elseif ($action == 'register') {
        $username   = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $nama_admin = htmlspecialchars(trim($_POST['nama_admin']), ENT_QUOTES, 'UTF-8');
        
        $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
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