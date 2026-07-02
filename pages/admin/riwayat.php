<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Ambil parameter filter tanggal jika ada
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';

try {
    if (!empty($start_date) && !empty($end_date)) {
        $start_date_db = date('Y-m-d', strtotime($start_date));
        $end_date_db   = date('Y-m-d', strtotime($end_date));
        $stmt = $pdo->prepare("SELECT d.*, p.nama_penyakit 
                             FROM tbl_diagnosa d 
                             LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                             WHERE DATE(d.tanggal_diagnosa) BETWEEN ? AND ?
                             ORDER BY d.tanggal_diagnosa DESC");
        $stmt->execute([$start_date_db, $end_date_db]);
    } else {
        $stmt = $pdo->query("SELECT d.*, p.nama_penyakit 
                             FROM tbl_diagnosa d 
                             LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                             ORDER BY d.tanggal_diagnosa DESC");
    }
    $riwayat = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data riwayat: " . $e->getMessage());
}
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-dark">
            <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Laporan Sistem Pakar Diagnosa Penyakit Ikan Nila
        </h5>
        <div>
            <button class="btn btn-sm btn-secondary" id="btnRefresh">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <!-- Form Filter Tanggal & Cetak Laporan -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <form method="get" class="d-flex align-items-center">
                <div class="input-group input-group-sm gap-2">
                    <input type="date" class="form-control border-primary-subtle" name="start_date" value="<?= $start_date ?>">
                    <span class="input-group-text bg-primary text-white border-primary">s/d</span>
                    <input type="date" class="form-control border-primary-subtle" name="end_date" value="<?= $end_date ?>">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
            <div class="d-flex gap-2">
                <a href="../../process/laporan_process.php?action=export_pdf&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                   class="btn btn-sm btn-outline-danger shadow-sm" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Cetak Laporan PDF
                </a>
                <a href="../../process/laporan_process.php?action=export_excel&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                   class="btn btn-sm btn-outline-success shadow-sm">
                    <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                </a>
            </div>
        </div>

        <!-- Alert Periode Tanggal -->
        <div class="alert alert-info border-0 shadow-sm my-3 d-flex align-items-center rounded-3">
            <i class="bi bi-info-circle-fill fs-5 me-3"></i>
            <div>
                <?php if (!empty($start_date) && !empty($end_date)): ?>
                    Menampilkan laporan diagnosa periode 
                    <span class="fw-bold"><?= date('d M Y', strtotime($start_date)) ?></span> 
                    sampai 
                    <span class="fw-bold"><?= date('d M Y', strtotime($end_date)) ?></span>
                <?php else: ?>
                    Menampilkan seluruh riwayat diagnosa sistem pakar.
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light text-muted text-center">
                    <tr>
                        <th class="text-center ps-4" width="5%">No</th>
                        <th width="15%">Waktu Diagnosa</th>
                        <th width="25%">Kode Sampel</th>
                        <th width="25%">Hasil Identifikasi</th>
                        <th class="text-center" width="15%">Tingkat Akurasi</th>
                        <th class="text-center pe-4" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data riwayat diagnosa.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($riwayat as $row): 
                            // Tentukan warna badge akurasi
                            $conf = round($row['confidence'] * 100, 2);
                            $badge_color = $conf >= 70 ? 'success' : ($conf >= 40 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="fw-medium text-dark"><?= date('d M Y', strtotime($row['tanggal_diagnosa'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($row['tanggal_diagnosa'])) ?> WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"> <?= htmlspecialchars($row['kode_sampel'] ?? '-') ?> </div>
                            </td>
                            <td>
                                <span class="fw-medium text-dark"><?= htmlspecialchars($row['nama_penyakit'] ?? 'Tidak Dikenali') ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $badge_color ?>-subtle text-<?= $badge_color ?> border border-<?= $badge_color ?>-subtle px-2 py-1">
                                    <?= $conf ?>%
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="../../process/print_riwayat.php?id=<?= $row['id_diagnosa'] ?>&format=pdf" target="_blank" class="btn btn-sm btn-outline-success rounded-3 me-1" title="Cetak PDF Hasil">
                                    <i class="bi bi-printer"></i>
                                </a>    
                                <a href="diagnosa_detail.php?id=<?= $row['id_diagnosa'] ?>" class="btn btn-sm btn-outline-info rounded-3 me-1" title="Lihat Detail Gejala">
                                    <i class="bi bi-eye"></i>
                                </a>   
                                <button onclick="confirmDelete(<?= $row['id_diagnosa'] ?>, 'delete_riwayat.php')" class="btn btn-sm btn-outline-danger rounded-3" ...>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// ==========================================
// 1. KODE BARU: AUTO-CLOSE ALERT (KOTAK HIJAU)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');

        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";

            setTimeout(function() {
                alert.style.display = "none";
                alert.remove();
            }, 500);
        });
    }, 3000);
});

// ==========================================
// 2. KODE LAMA: FUNGSI HAPUS (SWEETALERT)
// ==========================================
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Riwayat?',
        text: "Data riwayat dan detail gejala akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete_riwayat.php?id=' + id;
        }
    });
}

// ==========================================
// 3. KODE LAMA: FUNGSI TOMBOL REFRESH
// ==========================================
document.getElementById('btnRefresh').addEventListener('click', function() {
    window.location.reload();
});
</script>

<?php require_once '../../includes/footer.php'; ?>