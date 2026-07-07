<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
require_once '../../includes/header.php';

try {
    // Ambil statistik data untuk mempercantik Pusat Laporan
    $total_gejala = $pdo->query("SELECT COUNT(*) FROM tbl_gejala")->fetchColumn();
    $total_penyakit = $pdo->query("SELECT COUNT(*) FROM tbl_penyakit")->fetchColumn();
    $total_aturan = $pdo->query("SELECT COUNT(*) FROM tbl_aturan")->fetchColumn();
    $total_diagnosa = $pdo->query("SELECT COUNT(*) FROM tbl_diagnosa")->fetchColumn();
} catch (PDOException $e) {
    die("Error mengambil data statistik: " . $e->getMessage());
}
?>

<!-- Header Section -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Pusat Laporan & Ekspor Data Penyakit Ikan Nila</h1>
        <p class="text-muted mb-0">Unduh seluruh berkas laporan sistem pakar dalam format PDF Resmi dan Excel spreadsheet.</p>
    </div>
</div>

<!-- Row Statistik & Informasi Singkat -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-gradient bg-primary text-white p-4">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <h4 class="fw-bold mb-2">Selamat Datang di Pusat Laporan Sistem</h4>
                    <p class="mb-0 text-white-50">
                        Di halaman ini Anda dapat mengekspor seluruh basis data utama mulai dari riwayat hasil diagnosa, data gejala, data jenis penyakit, basis relasi aturan, hingga data akun administrator yang mengelola sistem pakar ikan nila Dzawil Farm.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end text-center">
                    <div class="d-inline-block bg-white-50 rounded-pill px-4 py-2 border border-white-50">
                        <span class="text-white small">Total Riwayat Kasus: <strong><?= $total_diagnosa ?> Diagnosa</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 1: Utama & Master (3 Cards) -->
<div class="row g-4 mb-4">
    <!-- Card Riwayat Diagnosa -->
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-info-subtle text-info d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-clock-history fs-3 text-info"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Riwayat Diagnosa</h5>
                <p class="text-muted small mb-3">Total: <span class="fw-bold text-info"><?= $total_diagnosa ?> Diagnosa</span></p>
                <p class="text-secondary small mb-4">Unduh berkas laporan seluruh riwayat hasil diagnosa penyakit ikan nila yang dilakukan oleh pengguna.</p>
                <div class="d-grid gap-2">
                    <a href="../../process/laporan_process.php?action=export_pdf" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                    </a>
                    <a href="../../process/laporan_process.php?action=export_excel" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Penyakit -->
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-virus fs-3 text-danger"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Data Penyakit</h5>
                <p class="text-muted small mb-3">Total: <span class="fw-bold text-danger"><?= $total_penyakit ?> Penyakit</span></p>
                <p class="text-secondary small mb-4">Unduh berkas data penyakit ikan nila, gejala spesifik, beserta saran pengobatannya.</p>
                <div class="d-grid gap-2">
                    <a href="../../process/print_penyakit.php?format=pdf" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                    </a>
                    <a href="../../process/print_penyakit.php?format=excel" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Gejala -->
    <div class="col-lg-4 col-md-12">
        <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-clipboard2-data fs-3 text-primary"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Data Gejala</h5>
                <p class="text-muted small mb-3">Total: <span class="fw-bold text-primary"><?= $total_gejala ?> Gejala</span></p>
                <p class="text-secondary small mb-4">Unduh berkas laporan seluruh gejala klinis penyakit ikan nila yang terdaftar di sistem.</p>
                <div class="d-grid gap-2">
                    <a href="../../process/print_gejala.php?format=pdf" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                    </a>
                    <a href="../../process/print_gejala.php?format=excel" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Aturan & Pengguna (2 Cards) -->
<div class="row g-4 justify-content-center">
    <!-- Card Basis Aturan -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-diagram-3 fs-3 text-warning"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Basis Aturan</h5>
                <p class="text-muted small mb-3">Total: <span class="fw-bold text-warning"><?= $total_aturan ?> Aturan</span></p>
                <p class="text-secondary small mb-4">Unduh matriks relasi basis aturan keputusan Forward Chaining sistem pakar.</p>
                <div class="d-grid gap-2">
                    <a href="../../process/print_relasi.php?format=pdf" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                    </a>
                    <a href="../../process/print_relasi.php?format=excel" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Admin -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-people fs-3 text-success"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Data Admin</h5>
                <p class="text-muted small mb-3">Role: <span class="fw-bold text-success">Administrator</span></p>
                <p class="text-secondary small mb-4">Unduh data seluruh akun administrator pengelola sistem pakar ikan nila.</p>
                <div class="d-grid gap-2">
                    <a href="../../process/print_admin.php?format=pdf" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                    </a>
                    <a href="../../process/print_admin.php?format=excel" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>