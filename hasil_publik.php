<?php
require_once 'config/database.php'; 
include 'includes/header_publik.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location='konsultasi.php';</script>";
    exit();
}

$id_diagnosa = $_GET['id'];
$query = "SELECT r.*, p.nama_penyakit, p.deskripsi, p.solusi 
          FROM tbl_diagnosa r 
          JOIN tbl_penyakit p ON r.hasil_penyakit = p.kode_penyakit 
          WHERE r.id_diagnosa = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_diagnosa]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data hasil diagnosa tidak ditemukan!'); window.location='konsultasi.php';</script>";
    exit();
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-3">
                <a href="konsultasi.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left"></i> Kembali ke Form Diagnosa
                </a>
            </div>

            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3 class="mb-1 fw-bold"><i class="fas fa-file-medical-alt me-2"></i>Hasil Diagnosa Kepakaran</h3>
                    <p class="mb-0 small">Sistem Pakar Forward Chaining Ikan Nila</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-sm-6">
                            <span class="text-uppercase text-muted fw-bold small">Kode Sampel</span>
                            <h5 class="fw-bold mt-1 text-dark"><?= htmlspecialchars($data['kode_sampel']) ?></h5>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-uppercase text-muted fw-bold small">Tanggal Konsultasi</span>
                            <h5 class="fw-bold mt-1 text-dark"><?= date('d F Y', strtotime($data['tanggal_diagnosa'])) ?></h5>
                        </div>
                    </div>

                    <div class="text-center mb-5 bg-light p-4 rounded-4 border">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill">Kesimpulan Hasil Diagnosa</span>
                        <h2 class="text-danger fw-bold my-2"><?= $data['nama_penyakit'] ?></h2>
                        <div class="d-inline-block bg-white rounded-pill px-4 py-2 border border-danger mt-2">
                            <span class="text-dark">Tingkat Keyakinan: <strong class="text-danger"><?= round($data['confidence'] * 100, 2) ?>%</strong></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Penjelasan Penyakit</h5>
                        <p class="text-secondary lh-lg mb-0" style="text-align: justify;"><?= nl2br($data['deskripsi']) ?></p>
                    </div>

                    <div class="mb-0">
                        <h5 class="fw-bold text-dark mb-2"><i class="fas fa-pills text-success me-2"></i>Saran Penanganan / Pengobatan</h5>
                        <p class="text-secondary lh-lg mb-0" style="text-align: justify;"><?= nl2br($data['solusi']) ?></p>
                    </div>
                </div>
                
                <div class="card-footer bg-light text-center py-3">
                    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 me-2"><i class="fas fa-print"></i> Cetak</button>
                    <a href="index.php" class="btn btn-success rounded-pill px-4">Selesai</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer_publik.php'; ?>