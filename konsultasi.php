<?php
require_once 'config/database.php'; 

$stmt = $pdo->query("SELECT * FROM tbl_gejala ORDER BY kode_gejala ASC");
$gejala_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate kode sampel otomatis untuk publik
$stmtKode = $pdo->query("
    SELECT kode_sampel
    FROM tbl_diagnosa
    WHERE kode_sampel IS NOT NULL
    ORDER BY id_diagnosa DESC
    LIMIT 1
");
$lastKode = $stmtKode->fetch();
if ($lastKode && preg_match('/SPL-(\d+)/', $lastKode['kode_sampel'], $match)) {
    $nomorBaru = (int)$match[1] + 1;
} else {
    $nomorBaru = 1;
}
$kodeSampelBaru = 'SPL-' . str_pad($nomorBaru, 3, '0', STR_PAD_LEFT);

include 'includes/header_publik.php';
?>

<style>
    .gejala-card { transition: transform 0.2s; }
    .gejala-card:hover { transform: scale(1.02); border-color: #0dbb94; background-color: #f8fffb; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-microscope"></i> Form Konsultasi Diagnosa</h4>
                    <p class="mb-0 small">Pilih gejala yang dialami oleh ikan nila Anda</p>
                </div>
                
                <div class="card-body p-4">
                    <form action="process/diagnosa_publik_process.php" method="POST">
                        
                        <div class="mb-4 bg-light p-3 rounded-3 border">
                            <label for="kode_sampel" class="form-label fw-bold text-secondary"><i class="fas fa-barcode me-2 text-primary"></i>Kode Sampel (Otomatis)</label>
                            <input type="text" class="form-control fw-bold text-dark" name="kode_sampel" value="<?= $kodeSampelBaru ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                            <div class="form-text mt-1"><i class="fas fa-info-circle me-1"></i>Kode identifikasi unik untuk melacak sampel diagnosa Anda.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Gejala Klinis yang Dialami (Bisa pilih lebih dari 1):</label>
                            
                            <?php if(count($gejala_list) > 0) { ?>
                                <div class="list-group">
                                    <?php foreach($gejala_list as $row) { ?>
                                        <label class="list-group-item gejala-card d-flex gap-3 align-items-center">
                                            <input class="form-check-input flex-shrink-0 fs-5" type="checkbox" name="gejala_terpilih[]" value="<?= $row['kode_gejala'] ?>">
                                            <span>
                                                <strong><?= $row['kode_gejala'] ?></strong> - <?= $row['nama_gejala'] ?>
                                            </span>
                                        </label>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning">Data gejala belum tersedia di database.</div>
                            <?php } ?>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg" name="submit_diagnosa">
                                <i class="fas fa-search-plus"></i> Proses Diagnosa Sekarang
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">Batal & Kembali ke Beranda</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer_publik.php'; ?>