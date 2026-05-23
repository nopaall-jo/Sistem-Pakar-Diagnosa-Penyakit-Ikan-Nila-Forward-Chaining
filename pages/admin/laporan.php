<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

require_once '../../config/database.php';
require_once '../../includes/header.php';

// 1️. Ambil dari filter atau default awal & akhir bulan
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date'] ?? date('Y-m-t');

// 2️. Normalisasi format tanggal (letaknya DI SINI)
$start_date = date('Y-m-d', strtotime($start_date));
$end_date   = date('Y-m-d', strtotime($end_date));

try {

    $query = "SELECT d.*, p.nama_penyakit 
              FROM tbl_diagnosa d
              LEFT JOIN tbl_penyakit p 
                     ON d.hasil_penyakit = p.kode_penyakit
              WHERE DATE(d.tanggal_diagnosa) 
                    BETWEEN :start_date AND :end_date
              ORDER BY d.tanggal_diagnosa DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':start_date' => $start_date,
        ':end_date'   => $end_date
    ]);

    $laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Query bermasalah: " . $e->getMessage());
}
?>
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="m-0 font-weight-bold text-dark">
            <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Laporan Diagnosa Penyakit Ikan Nila
        </h5>
        <div class="d-flex align-items-center gap-3">
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

            <div class="d-flex gap-2 shadow-sm">
                <a href="../../process/laporan_process.php?action=export_pdf&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                   class="btn btn-sm btn-outline-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                <a href="../../process/laporan_process.php?action=export_excel&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                   class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center rounded-3">
            <i class="bi bi-info-circle-fill fs-5 me-3"></i>
            <div>
                Menampilkan laporan diagnosa periode 
                <span class="fw-bold"><?= date('d M Y', strtotime($start_date)) ?></span> 
                sampai 
                <span class="fw-bold"><?= date('d M Y', strtotime($end_date)) ?></span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="text-center ps-4" width="5%">No</th>
                        <th width="15%">Tanggal Diagnosa</th>
                        <th width="20%">Nama Pembudidaya</th>
                        <th width="30%">Hasil Diagnosa</th>
                        <th class="text-center" width="15%">Kecocokan</th>
                        <th class="text-center pe-4" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($laporan) && count($laporan) > 0): ?>
                        <?php foreach ($laporan as $key => $row): 
                            // SINKRONISASI: Hitung persentase dari kolom confidence (misal: 0.85 -> 85%)
                            $conf = round($row['confidence'] * 100, 1);
                            $badge_color = $conf >= 75 ? 'success' : ($conf >= 50 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td class="text-center ps-4 text-muted small"><?= $key + 1 ?></td>
                            <td>
                                <div class="fw-medium text-dark"><?= date('d/m/Y', strtotime($row['tanggal_diagnosa'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($row['tanggal_diagnosa'])) ?> WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_pembudidaya']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($row['nama_penyakit'])): ?>
                                    <span class="fw-medium text-primary"><?= htmlspecialchars($row['nama_penyakit']) ?></span>
                                    <br><small class="text-muted text-uppercase"><?= $row['hasil_penyakit'] ?></small>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Tidak Terdeteksi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= $badge_color ?>-subtle text-<?= $badge_color ?> border border-<?= $badge_color ?>-subtle px-2 py-1">
                                    <?= $conf ?>%
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="diagnosa_detail.php?id=<?= $row['id_diagnosa'] ?>" class="btn btn-sm btn-light border text-info" title="Detail Riwayat">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                Tidak ada data diagnosa pada periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <td colspan="4" class="text-end pe-3"><strong>Total Diagnosa Ditemukan:</strong></td>
                        <td colspan="2" class="fw-bold text-primary">
                            <?= isset($laporan) ? count($laporan) : 0 ?> Kasus
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi DataTable tanpa fitur search (karena sudah pakai filter tanggal)
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "lengthChange": false, // Sembunyikan "Tampilkan X data"
            "searching": false,    // Sembunyikan form pencarian default
            "language": {
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ laporan",
                "infoEmpty": "Menampilkan 0 data",
                "paginate": {
                    "next": "Lanjut",
                    "previous": "Kembali"
                }
            }
        });
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>