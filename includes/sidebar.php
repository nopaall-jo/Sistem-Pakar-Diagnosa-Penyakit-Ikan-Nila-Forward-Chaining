<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .custom-sidebar .nav-pills .nav-link {
        color: #ed5f59 !important; /* Warna coral/merah untuk teks menu tidak aktif */
        font-weight: 500;
        border-radius: 8px;
    }
    .custom-sidebar .nav-pills .nav-link i {
        color: #ed5f59 !important; /* Warna ikon tidak aktif */
    }
    .custom-sidebar .nav-pills .nav-link:hover {
        background-color: rgba(237, 95, 89, 0.1) !important;
        color: #ed5f59 !important;
    }
    .custom-sidebar .nav-pills .nav-link.active {
        background-color: #0b7c79 !important; /* Warna toska gelap untuk background menu aktif */
        color: #ffffff !important; /* Teks putih untuk menu aktif */
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .custom-sidebar .nav-pills .nav-link.active i {
        color: #ffffff !important; /* Ikon putih untuk menu aktif */
    }
</style>

<nav class="navbar fixed-top d-md-none px-3 shadow-sm w-100" style="background-color: var(--light); border-bottom: 2px solid var(--dark); z-index: 1040;">
    <div class="container-fluid p-0">
        <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="<?= $base_url ?>pages/admin/dashboard.php">
            <img src="<?= $base_url ?>assets/img/Logo2.png" alt="Logo" height="24" class="me-2 rounded-1"> Pakar Nila Dzawil Farm
        </a>
        <button class="navbar-toggler border-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarResponsive" aria-controls="sidebarResponsive">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="offcanvas-md offcanvas-start custom-sidebar d-flex flex-column flex-shrink-0 p-3 shadow-sm" tabindex="-1" id="sidebarResponsive" style="background-color: var(--light); border-right: 2px solid var(--dark); width: 260px; min-height: 100vh;">
    
    <div class="offcanvas-header d-md-none border-bottom border-dark mb-3 p-0 pb-3">
        <h5 class="offcanvas-title fw-bold">Menu Navigasi</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" data-bs-target="#sidebarResponsive" aria-label="Close"></button>
    </div>

    <a href="<?= $base_url ?>pages/admin/dashboard.php" class="sidebar-brand d-flex align-items-center mb-0 mt-0 px-2 text-decoration-none">
        <div class="d-flex align-items-center justify-content-center rounded-3 me-2"
             style="min-width: 40px; height: 40px; border: 2px solid var(--dark); box-shadow: 3px 3px 0px var(--dark); background-color: white; overflow: hidden;">
            <img src="<?= $base_url ?>assets/img/Logo2.png" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="d-flex flex-column justify-content-center">
            <span class="fw-bold fs-5 text-dark lh-sm" style="letter-spacing: -0.3px;">
                Sistem Pakar <br><span style="color: #ed5f59;">Ikan Nila</span> <br>
                <span class="badge border fw-bold text-uppercase mt-1" style="font-size: 0.6rem; letter-spacing: 0.5px; background-color: #e0f2f1; color: #00796b; border-color: #b2dfdb !important; padding: 2px 6px;">Dzawil Farm</span>
            </span>
        </div>
    </a>
    
    <ul class="nav nav-pills flex-column mb-auto gap-2 mt-2">
        
        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill me-3"></i> Dashboard
            </a>
        </li>
        
        <li class="mt-3 mb-1 px-3">
            <div class="fw-bold text-uppercase" style="font-size: 0.75rem; color: #1a202c; letter-spacing: 1px;">Data Master</div>
        </li>

        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/penyakit.php" class="nav-link <?= ($current_page == 'penyakit.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-fish me-3"></i> Data Penyakit
            </a>
        </li>
        
        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/gejala.php" class="nav-link <?= ($current_page == 'gejala.php') ? 'active' : '' ?>">
                <i class="bi bi-thermometer-half me-3"></i> Data Gejala
            </a>
        </li>
        
        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/relasi.php" class="nav-link <?= ($current_page == 'relasi.php') ? 'active' : '' ?>">
                <i class="bi bi-diagram-3-fill me-3"></i> Basis Aturan
            </a>
        </li>
        
        <li class="mt-4 mb-1 px-3">
            <div class="fw-bold text-uppercase" style="font-size: 0.75rem; color: #1a202c; letter-spacing: 1px;">Proses Pakar</div>
        </li>

        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/diagnosa.php" class="nav-link <?= in_array($current_page, ['diagnosa.php', 'hasil_diagnosa.php']) ? 'active' : '' ?>">
                <i class="bi bi-heart-pulse-fill me-3"></i> Mulai Diagnosa
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/riwayat.php" class="nav-link <?= in_array($current_page, ['riwayat.php', 'diagnosa_detail.php']) ? 'active' : '' ?>">
                <i class="bi bi-clock-history me-3"></i> Riwayat Diagnosa
            </a>
        </li>
        
        <li class="mt-4 mb-1 px-3">
            <div class="fw-bold text-uppercase" style="font-size: 0.75rem; color: #1a202c; letter-spacing: 1px;">Laporan</div>
        </li>

        <li class="nav-item">
            <a href="<?= $base_url ?>pages/admin/laporan.php" class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : '' ?>">
                <i class="bi bi-printer-fill me-3"></i> Cetak Laporan
            </a>
        </li>
    </ul>
    
    <hr class="mt-4 mb-3 opacity-25" style="border-top: 2px dashed var(--dark);">
    
    <div class="dropup px-2 mb-2">
        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle p-2 rounded-3 dropdown-user" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="img-profile rounded-circle overflow-hidden d-flex align-items-center justify-content-center" style="background: white; border: 2px solid var(--dark); width: 35px; height: 35px;">
                <img src="../../assets/img/logo4.png" alt="Logo Sistem" style="width: 70%; height: 70%; object-fit: contain;">
            </div>
            <strong class="ms-2 text-truncate" style="max-width: 140px;">
                <?= htmlspecialchars($_SESSION['nama_admin'] ?? 'Administrator') ?>
            </strong>
        </a>
        <ul class="dropdown-menu shadow border border-dark rounded-3" aria-labelledby="dropdownUser1" style="background-color: var(--highlight);">
            <li>
                <a class="dropdown-item py-2 fw-semibold text-dark" href="<?= $base_url ?>pages/admin/data_admin.php">
                    <i class="bi bi-person-gear me-2" style="color: var(--primary-dark);"></i> Pengaturan Akun Admin
                </a>
            </li>
            <li><hr class="dropdown-divider border-dark opacity-25"></li>
            <li>
                <a class="dropdown-item py-2 fw-bold text-danger" href="<?= $base_url ?>logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Sign out
                </a>
            </li>
        </ul>
    </div>
</div>