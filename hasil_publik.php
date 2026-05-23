<?php
require_once 'config/database.php'; 
include 'includes/header_publik.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location='konsultasi.php';</script>";
    exit();
}

$id_diagnosa = $_GET['id'];

// Query JOIN menggunakan PDO Prepare
$query = "SELECT r.*, p.nama_penyakit, p.deskripsi, p.solusi 
          FROM tbl_riwayat r 
          JOIN tbl_penyakit p ON r.id_penyakit = p.id_penyakit 
          WHERE r.id_diagnosa = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_diagnosa]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan
if (!$data) {
    $_SESSION['error'] = "Data hasil diagnosa tidak ditemukan!";
    echo "<script>window.location='konsultasi.php';</script>";
    exit();
}
?>

<!-- Style khusus print -->
<style>
    .print-only { display: none; }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block; }
        body { background-color: #fff; }
        .card { border: none !important; box-shadow: none !important; }
        main, .container { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        .bg-primary { background-color: #f8f9fa !important; color: #000 !important; } 
    }
</style>

<div class="container py-3 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            
            <div class="mb-3 no-print">
                <a href="konsultasi.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Form Diagnosa
                </a>
            </div>

            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary bg-gradient text-white text-center py-4 py-md-5 border-0">
                    <h3 class="mb-1 fw-bold"><i class="fas fa-file-medical-alt me-2"></i>Hasil Diagnosa Kepakaran</h3>
                    <p class="mb-0 opacity-75">Sistem Pakar Forward Chaining Ikan Nila</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <div class="row mb-5 pb-4 border-bottom border-2">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Nama Peternak</span>
                            <h4 class="fw-bold mt-1 mb-0 text-dark"><?= htmlspecialchars($data['nama_peternak']) ?></h4>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Tanggal Konsultasi</span>
                            <h4 class="fw-bold mt-1 mb-0 text-dark"><?= date('d F Y', strtotime($data['tanggal_diagnosa'])) ?></h4>
                        </div>
                    </div>

                    <div class="text-center mb-5 bg-light p-4 rounded-4 border">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill"><i class="fas fa-search me-1"></i> Kesimpulan Sistem</span>
                        <h2 class="text-danger fw-bolder my-3" style="letter-spacing: -0.5px;"><?= $data['nama_penyakit'] ?></h2>
                        
                        <div class="d-inline-block bg-white rounded-pill px-4 py-2 border border-2 border-danger shadow-sm mt-2">
                            <span class="fs-5 text-dark">Tingkat Keyakinan: <strong class="text-danger fs-4"><?= $data['hasil_persentase'] ?>%</strong></span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-info text-white fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark border-bottom border-2 border-warning pb-1 flex-grow-1">Penjelasan Penyakit</h5>
                        </div>
                        <p class="text-secondary lh-lg mb-0 ps-md-5" style="text-align: justify;">
                            <?= nl2br($data['deskripsi']) ?>
                        </p>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-pills text-white fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark border-bottom border-2 border-success pb-1 flex-grow-1">Saran Penanganan / Pengobatan</h5>
                        </div>
                        <p class="text-secondary lh-lg mb-0 ps-md-5" style="text-align: justify;">
                            <?= nl2br($data['solusi']) ?>
                        </p>
                    </div>
                </div>
                
                <div class="card-footer bg-light text-center py-4 no-print border-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 px-md-5">
                        <button onclick="window.print()" class="btn btn-primary btn-lg flex-sm-fill rounded-pill shadow-sm">
                            <i class="fas fa-print me-2"></i> Cetak Dokumen
                        </button>
                        <a href="index.php" class="btn btn-success btn-lg flex-sm-fill rounded-pill shadow-sm">
                            <i class="fas fa-home me-2"></i> Selesai & Tutup
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer_publik.php'; ?>