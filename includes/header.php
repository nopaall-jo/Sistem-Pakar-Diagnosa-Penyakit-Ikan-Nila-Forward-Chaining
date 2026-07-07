<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

require_once '../../config/database.php';

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

// Tentukan judul halaman admin secara dinamis
$current_page = basename($_SERVER['PHP_SELF']);
$title_page = "Panel Kontrol Administrator";

switch ($current_page) {
    case 'dashboard.php':
        $title_page = "Dashboard Utama Ikan Nila";
        break;
    case 'gejala.php':
        $title_page = "Kelola Data Gejala Ikan Nila";
        break;
    case 'penyakit.php':
        $title_page = "Kelola Data Penyakit Ikan Nila";
        break;
    case 'relasi.php':
        $title_page = "Kelola Basis Aturan Penyakit Ikan Nila";
        break;
    case 'riwayat.php':
        $title_page = "Riwayat Diagnosa Penyakit Ikan Nila";
        break;
    case 'detail_riwayat.php':
    case 'diagnosa_detail.php':
        $title_page = "Detail Riwayat Diagnosa Penyakit Ikan Nila";
        break;
    case 'diagnosa.php':
    case 'hasil_diagnosa.php':
        $title_page = "Uji Diagnosa Pakar Penyakit Ikan Nila";
        break;
    case 'data_admin.php':
        $title_page = "Kelola Data Admin Penyakit Ikan Nila";
        break;
    case 'profile.php':
        $title_page = "Profil Administrator Penyakit Ikan Nila";
        break;
    case 'laporan.php':
        $title_page = "Laporan Diagnosa Penyakit Ikan Nila";
        break;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Ikan Nila - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= $base_url ?>assets/img/logo3.png">

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

        <div class="main flex-grow-1 d-flex flex-column pt-5 pt-md-0 mt-4 mt-md-0">

            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top px-4"
                style="background-color: var(--coba); border-bottom: 2px solid var(--dark); height: 70px;">

                <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100">
                    <span class="text-dark small fw-bold px-3 py-2 rounded-pill" style="background-color: var(--light); border: 1px solid var(--dark);">
                        <i class="bi bi-calendar3 me-1" style="color: var(--primary-dark);"></i>
                        <?php
                        $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        echo $hari[date('l')] . ', ' . date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
                        ?>
                    </span>
                </div>

                <div class="position-absolute start-50 translate-middle-x text-center d-none d-md-block">
                    <span class="fw-bolder text-dark" style="font-size: 1.05rem; letter-spacing: -0.3px;">
                        <?= $title_page ?>
                    </span>
                    <div class="small fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.2px;">
                        Sistem Pakar Ikan Nila Dzawil Farm
                    </div>
                </div>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown no-arrow position-relative">
                        <a class="nav-link dropdown-toggle d-flex align-items-center rounded-3 p-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="transition: all 0.2s;">
                            <div class="text-end me-3 d-none d-lg-block">
                                <span class="d-block text-dark fw-bold" style="font-size: 0.95rem; line-height: 1.2;"><?= htmlspecialchars($admin['nama_admin']) ?></span>
                                <span class="d-block text-muted fw-semibold" style="font-size: 0.75rem;">Administrator</span>
                            </div>

                            <div class="img-profile rounded-circle d-flex align-items-center justify-content-center overflow-hidden"
                                style="border: 2px solid var(--dark); width: 40px; height: 40px; background-color: white;">
                                <img src="<?= $base_url ?>assets/img/logo4.png" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </a>

                        <div class="dropdown-menu shadow border border-dark rounded-3 py-2" aria-labelledby="userDropdown" style="position: absolute; top: 100%; right: 50%; transform: translateX(50%); margin-top: 1px; min-width: 220px; z-index: 1050;">
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