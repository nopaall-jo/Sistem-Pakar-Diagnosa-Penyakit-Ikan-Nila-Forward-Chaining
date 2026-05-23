<?php
// Cek apakah session sudah berjalan atau belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// WAJIB: Atur zona waktu ke WIB (Waktu Indonesia Barat)
date_default_timezone_set('Asia/Jakarta');

require_once '../../config/database.php';
    
// Proteksi Session
if (!isset($_SESSION['id_admin'])) {
    header("Location: " . $base_url . "pages/auth/login.php");
    exit();
}

$id_admin = $_SESSION['id_admin'];
$stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE id_admin = ?");
$stmt->execute([$id_admin]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    session_destroy();
    header("Location: " . $base_url . "pages/auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Ikan Nila - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= $base_url ?>assets/img/logo4.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">
    
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper d-flex">
        
        <?php include 'sidebar.php'; ?>
        
        <div class="main flex-grow-1 d-flex flex-column">
            
            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top px-4" 
                 style="background-color: var(--coba); border-bottom: 2px solid var(--dark); height: 70px;">
                
                <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100">
                    <span class="text-dark small fw-bold px-3 py-2 rounded-pill" style="background-color: var(--light); border: 1px solid var(--dark);">
                        <i class="bi bi-calendar3 me-1" style="color: var(--primary-dark);"></i> <?= date('d F Y') ?>
                    </span>
                </div>
                
                <div class="position-absolute start-50 translate-middle-x text-center d-none d-md-block">
                    <span class="fw-bolder text-dark" style="font-size: 1rem; letter-spacing: -0.5px;">
                        Sistem Pakar Diagnosa Penyakit Ikan Nila
                    </span>
                    <div class="small fw-bold text-muted" style="font-size: 0.75rem;">
                        Naufal Rafif (202243501684)
                    </div>
                </div>
                
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle d-flex align-items-center rounded-3 p-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="transition: all 0.2s;">
                            <div class="text-end me-3 d-none d-lg-block">
                                <span class="d-block text-dark fw-bold" style="font-size: 0.95rem; line-height: 1.2;"><?= htmlspecialchars($admin['nama_admin']) ?></span>
                                <span class="d-block text-muted fw-semibold" style="font-size: 0.75rem;">Administrator</span>
                            </div>
                            
                            <div class="img-profile rounded-circle d-flex align-items-center justify-content-center" 
                                    style="background: var(--info); border: 2px solid var(--dark); width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill text-dark" style="font-size: 1.2rem;"></i>
                            </div>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-end shadow border border-dark rounded-3 py-2 aria-labelledby="userDropdown">
                            <a class="dropdown-item py-2 fw-semibold" href="<?= $base_url ?>pages/admin/data_admin.php">
                                <i class="bi bi-person-gear me-2 text-primary-dark"></i> Pengaturan Admin
                            </a>
                            <div class="dropdown-divider border-dark opacity-25"></div>
                            <a class="dropdown-item py-2 fw-bold text-danger" href="<?= $base_url ?>logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <div class="container-fluid px-4 flex-grow-1 pb-5">