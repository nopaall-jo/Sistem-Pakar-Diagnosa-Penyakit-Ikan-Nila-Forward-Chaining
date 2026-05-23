<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';

// 1. Proteksi Session: Pastikan ada data hasil diagnosa
if (!isset($_SESSION['hasil_diagnosa'])) {
    header("Location: diagnosa.php");
    exit();
}

$hasil_diagnosa = $_SESSION['hasil_diagnosa'];
$nama_pembudidaya = $_SESSION['nama_pembudidaya'] ?? 'Tidak Diketahui';

// PERBAIKAN: Gunakan ?? null agar tidak error jika array kosong
$penyakit_teratas = $hasil_diagnosa[0] ?? null;

require_once '../../includes/header.php';
?>

<div class="row justify-content-center mb-5">
    <div class="col-lg-10">
        
        <?php if ($penyakit_teratas): 
            // Tentukan warna dan ikon berdasarkan tingkat confidence (kecocokan)
            $confidence_percent = round($penyakit_teratas['confidence'] * 100, 2);
            $alert_color = $confidence_percent >= 70 ? 'success' : ($confidence_percent >= 40 ? 'warning' : 'danger');
            $alert_icon = $confidence_percent >= 70 ? 'bi-check-circle-fill' : ($confidence_percent >= 40 ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill');
            
            // Ambil detail lengkap penyakit dari database
            $stmt = $pdo->prepare("SELECT * FROM tbl_penyakit WHERE kode_penyakit = ?");
            $stmt->execute([$penyakit_teratas['kode_penyakit']]);
            $penyakit = $stmt->fetch();
        ?>

        <div class="alert alert-<?= $alert_color ?> border-0 shadow-sm rounded-4 mb-4 p-4 d-flex align-items-center">
            <i class="bi <?= $alert_icon ?> display-4 me-4"></i>
            <div>
                <h4 class="alert-heading fw-bold mb-1">Hasil Diagnosa Selesai</h4>
                <p class="mb-0">Mesin inferensi menyimpulkan kemungkinan terbesar ikan di kolam milik <strong><?= htmlspecialchars($_SESSION['nama_pembudidaya'] ?? 'Pelanggan') ?></strong> terserang penyakit <strong><?= htmlspecialchars($penyakit_teratas['nama_penyakit']) ?></strong>.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Tingkat Kecocokan Gejala Tertinggi</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="fs-4 fw-bold text-<?= $alert_color ?>"><?= $confidence_percent ?>%</span>
                    <span class="text-muted small">
                        Cocok <strong><?= $penyakit_teratas['gejala_cocok'] ?></strong> dari <?= $penyakit_teratas['total_gejala'] ?> gejala (Basis Aturan)
                    </span>
                </div>
                <div class="progress shadow-sm" style="height: 1.5rem; border-radius: 1rem;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-<?= $alert_color ?>" role="progressbar" style="width: <?= $confidence_percent ?>%"></div>
                </div>

                <?php if ($penyakit): ?>
                <div class="row mt-4 g-4">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                            <h6 class="fw-bold text-uppercase small text-muted mb-2">
                                Deskripsi Klinis (<em><?= htmlspecialchars($penyakit['nama_latin']) ?></em>)
                            </h6>
                            <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($penyakit['deskripsi'])) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-success-subtle text-success-emphasis rounded-3 border-top border-4 border-success h-100 shadow-sm">
                            <h6 class="fw-bold mb-3"><i class="bi bi-capsule me-2"></i>Rekomendasi Solusi</h6>
                            <p class="mb-0 small"><?= nl2br(htmlspecialchars($penyakit['solusi'])) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-info-subtle text-info-emphasis rounded-3 border-top border-4 border-info h-100 shadow-sm">
                            <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2"></i>Saran Pencegahan</h6>
                            <p class="mb-0 small"><?= nl2br(htmlspecialchars($penyakit['pencegahan'])) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($hasil_diagnosa) > 1): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-muted">Kemungkinan Penyakit Lainnya:</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php 
                    foreach ($hasil_diagnosa as $i => $hasil): 
                        if ($i == 0) continue; // Skip ranking 1
                        
                        $conf_lain = round($hasil['confidence'] * 100, 2);
                        $bar_color = $conf_lain >= 50 ? 'warning' : 'secondary';
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <span class="fw-bold text-dark d-block"><?= htmlspecialchars($hasil['nama_penyakit']) ?></span>
                            <small class="text-muted">Cocok <?= $hasil['gejala_cocok'] ?> dari <?= $hasil['total_gejala'] ?> gejala</small>
                        </div>
                        <div class="text-end" style="width: 150px;">
                            <span class="fw-bold text-<?= $bar_color ?>"><?= $conf_lain ?>%</span>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-<?= $bar_color ?>" role="progressbar" style="width: <?= $conf_lain ?>%"></div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 mb-3">
        <?php if(isset($_SESSION['diagnosa_id'])): ?>
            <a href="../../process/print_riwayat.php?id=<?= $_SESSION['diagnosa_id'] ?>&format=pdf"target="_blank"class="btn btn-success d-flex align-items-center px-4 py-2 fw-semibold shadow-sm rounded-3">
                <i class="bi bi-printer-fill me-2"></i>Cetak Hasil PDF
            </a>
        <?php endif; ?>
        <a href="diagnosa.php"class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold shadow-sm rounded-3">
            <i class="bi bi-arrow-repeat me-2"></i>Diagnosa Ulang
        </a>

        <a href="riwayat.php"class="btn btn-outline-secondary text-black d-flex align-items-center px-4 py-2 fw-semibold shadow-sm rounded-3">
            <i class="bi bi-clock-history me-2"></i>Cek Riwayat
        </a>
    </div>

        <?php else: ?>
        <div class="card shadow-sm border-0 rounded-4 text-center p-5">
            <i class="bi bi-shield-x text-danger mb-3" style="font-size: 4rem;"></i>
            <h4 class="fw-bold text-dark">Tidak Ditemukan Penyakit</h4>
            <p class="text-muted mb-4">Gejala yang Anda pilih tidak memiliki kecocokan dengan basis aturan penyakit apa pun di dalam sistem.</p>
            <div>
                <a href="diagnosa.php" class="btn btn-primary px-4"><i class="bi bi-arrow-left me-1"></i> Kembali & Coba Lagi</a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>