<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../../config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit();
}

$diagnosa_id = $_GET['id'];
$is_print = isset($_GET['print']);

// 1. Ambil data diagnosa (Sesuai Screenshot: id, tanggal_diagnosa, hasil_penyakit, confidence)
$stmt = $pdo->prepare("SELECT d.*, p.nama_penyakit, p.nama_latin, p.deskripsi, p.solusi, p.pencegahan 
                       FROM tbl_diagnosa d 
                       LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                       WHERE d.id_diagnosa = ?");
$stmt->execute([$diagnosa_id]);
$diagnosa = $stmt->fetch();

if (!$diagnosa) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='riwayat.php';</script>";
    exit();
}

// 2. Ambil daftar gejala yang dipilih (Sesuai Screenshot: id_diagnosa, kode_gejala)
$stmt = $pdo->prepare("SELECT g.kode_gejala, g.nama_gejala 
                       FROM tbl_diagnosa_detail dd 
                       JOIN tbl_gejala g ON dd.kode_gejala = g.kode_gejala 
                       WHERE dd.id_diagnosa = ?");
$stmt->execute([$diagnosa_id]);
$gejala = $stmt->fetchAll();

// ====================================================================
//                             MODE CETAK
// ====================================================================
if ($is_print) {
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Diagnosa #<?= $diagnosa_id ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Arial", sans-serif; line-height: 1.5; }
        body { padding: 30px; font-size: 12pt; color: #333; }
        .print-container { max-width: 100%; margin: 0 auto; }
        .print-header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #0dbb94; }
        .print-header h1 { font-size: 22pt; margin-bottom: 5px; color: #0dbb94; text-transform: uppercase; }
        .print-header p { font-size: 11pt; color: #666; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 14pt; font-weight: bold; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #ddd; color: #333; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .info-item strong { display: inline-block; min-width: 130px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: avoid; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11pt; }
        th { background-color: #f4fbf9; color: #0dbb94; font-weight: bold; }
        .text-content { white-space: pre-line; margin-top: 5px; padding: 10px; border-left: 3px solid #0dbb94; background-color: #fafafa; }
        .footer { text-align: center; margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 10pt; color: #888; }
        @page { size: A4; margin: 15mm 15mm; }
        @media print { body { padding: 0; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-header">
            <h1>Sistem Pakar Ikan Nila</h1>
            <p>Laporan Resmi Hasil Diagnosa Penyakit (Metode Forward Chaining)</p>
        </div>
        
        <div class="section">
            <div class="info-grid">
                <div class="info-item">
                    <strong>ID Diagnosa:</strong> #<?= str_pad($diagnosa_id, 4, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="info-item">
                    <strong>Tanggal Periksa:</strong> <?= date('d M Y, H:i', strtotime($diagnosa['tanggal_diagnosa'] ?? 'now')) ?> WIB
                </div>
                </div>
                <div class="info-item">
                    <strong>Peternak/Lokasi:</strong> <?= htmlspecialchars($diagnosa['nama_pembudidaya'] ?? '-') ?>
                </div>
                <div class="info-item">
                    <strong>Tingkat Kepastian:</strong> <?= round(($diagnosa['confidence'] * 100), 2) ?>%
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">Kesimpulan Sistem: <?= htmlspecialchars($diagnosa['nama_penyakit'] ?? 'Tidak Dikenali') ?></h3>
        </div>
        
        <div class="section">
            <h3 class="section-title">Daftar Gejala Klinis</h3>
            <table>
                <thead>
                    <tr>
                        <th width="15%">Kode</th>
                        <th>Nama Gejala Teramati</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gejala as $g): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($g['kode_gejala']) ?></strong></td>
                        <td><?= htmlspecialchars($g['nama_gejala']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($diagnosa['hasil_penyakit'])): ?>
        <div class="section">
            <h3 class="section-title">Rekomendasi Medis & Penanganan</h3>
            
            <div style="margin-bottom: 15px;">
                <strong>Nama Ilmiah / Latin:</strong> <em><?= htmlspecialchars($diagnosa['nama_latin'] ?? '-') ?></em>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Deskripsi Penyakit:</strong>
                <div class="text-content"><?= htmlspecialchars($diagnosa['deskripsi'] ?? '-') ?></div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Langkah Pengobatan:</strong>
                <div class="text-content" style="border-left-color: #198754;"><?= htmlspecialchars($diagnosa['solusi'] ?? '-') ?></div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Pencegahan Lanjutan:</strong>
                <div class="text-content" style="border-left-color: #0dcaf0;"><?= htmlspecialchars($diagnosa['pencegahan'] ?? '-') ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>Sistem Pakar Diagnosa Penyakit Ikan Nila &copy; <?= date('Y') ?> | Dicetak pada <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>
    
    <script>
        // Otomatis buka dialog print saat halaman dimuat
        window.onload = function() {
            setTimeout(function() { window.print(); }, 300);
            // Kembali secara otomatis jika jendela print ditutup
            window.onafterprint = function() { window.close(); };
        };
    </script>
</body>
</html>
<?php
    exit();
}
// ====================================================================

// TAMPILAN WEB NORMAL (Bukan Print)
require_once '../../includes/header.php';
?>

<div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom-0">
        <h5 class="m-0 font-weight-bold text-dark">
            <i class="bi bi-file-earmark-medical text-primary me-2"></i>Detail Riwayat Diagnosa
        </h5>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <!-- Grup Tombol Cetak -->
            <div class="d-flex gap-2">
                <a href="?id=<?= $_GET['id'] ?>&print=true" 
                class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Biasa
                </a>

                <a href="../../process/print_riwayat.php?id=<?= $_GET['id'] ?>&format=pdf" 
                target="_blank" 
                class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF Resmi
                </a>
            </div>
            <!-- Tombol Kembali -->
            <a href="riwayat.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card-body bg-light p-4">
        
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="bg-white p-4 rounded-3 shadow-sm border h-100">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3 border-bottom pb-2">Data Transaksi</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" width="40%">ID Diagnosa</td>
                            <td class="fw-bold">#<?= str_pad($diagnosa['id_diagnosa'], 4, '0', STR_PAD_LEFT) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Cek</td>
                            <td class="fw-bold"><?= date('d M Y, H:i', strtotime($diagnosa['tanggal_diagnosa'])) ?> WIB</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Pembudidaya</td>
                            <td class="fw-blod text-primary"><?= htmlspecialchars($diagnosa['nama_pembudidaya'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>  
            </div>
            <div class="col-md-6">
                <div class="bg-primary text-white p-4 rounded-3 shadow-sm h-100 d-flex flex-column justify-content-center">
                    <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Kesimpulan Mesin Inferensi</h6>
                    <h4 class="fw-bold mb-1 lh-base"><?= htmlspecialchars($diagnosa['nama_penyakit'] ?? 'Penyakit Tidak Dikenali') ?></h4>
                    <span class="badge bg-light text-primary w-50" style="font-size: 0.9rem;">
                        Akurasi: <?= round(($diagnosa['confidence'] * 100), 2) ?>%
                    </span>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-3 shadow-sm border mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check me-2 text-primary"></i>Gejala yang Ditemukan:</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%" class="text-center">Kode</th>
                            <th>Deskripsi Gejala Klinis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gejala as $g): ?>
                        <tr>
                            <td class="text-center"><span class="badge bg-secondary-subtle text-secondary border"><?= htmlspecialchars($g['kode_gejala']) ?></span></td>
                            <td class="fw-medium text-dark"><?= htmlspecialchars($g['nama_gejala']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if (!empty($diagnosa['hasil_penyakit'])): ?>
        <h6 class="fw-bold text-dark mt-4 mb-3 ps-1">Tindakan Lanjutan & Edukasi</h6>
        <div class="row g-3">
            <div class="col-12">
                <div class="p-3 bg-white rounded-3 border-start border-4 border-primary shadow-sm">
                    <span class="badge bg-primary mb-2">Deskripsi (<?= htmlspecialchars($diagnosa['nama_latin'] ?? '-') ?>)</span>
                    <p class="small mb-0 text-secondary"><?= nl2br(htmlspecialchars($diagnosa['deskripsi'] ?? '-')) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-success-subtle rounded-3 border-top border-4 border-success h-100 shadow-sm">
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-capsule me-2"></i>Langkah Pengobatan</h6>
                    <p class="small mb-0 text-success-emphasis"><?= nl2br(htmlspecialchars($diagnosa['solusi'] ?? '-')) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-info-subtle rounded-3 border-top border-4 border-info h-100 shadow-sm">
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-shield-check me-2"></i>Pencegahan Masa Depan</h6>
                    <p class="small mb-0 text-info-emphasis"><?= nl2br(htmlspecialchars($diagnosa['pencegahan'] ?? '-')) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>