<?php
// Header sudah melakukan session_start dan koneksi database
require_once '../../includes/header.php';

// Menghitung statistik untuk kotak atas (Card)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_penyakit");
$penyakit = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_gejala");
$gejala = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_aturan");
$aturan = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_diagnosa");
$diagnosa = $stmt->fetch()['total'];
?>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 custom-card-hover">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Data Penyakit</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $penyakit ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-bug-fill fa-2x text-gray-300 icon-hover"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 custom-card-hover">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Data Gejala</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $gejala ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-thermometer-half fa-2x text-gray-300 icon-hover"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 custom-card-hover">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Basis Aturan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $aturan ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-diagram-3-fill fa-2x text-gray-300 icon-hover"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 custom-card-hover">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Riwayat Diagnosa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $diagnosa ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clipboard2-data-fill fa-2x text-gray-300 icon-hover"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4 border-0 rounded-lg">
            <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-clock-history me-2"></i>5 Riwayat Diagnosa Terakhir</h6>
                <a href="<?= $base_url ?>pages/admin/riwayat.php" class="btn btn-sm btn-outline-primary">Lihat Semua Riwayat</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Tanggal</th>
                                <th width="25%">Penyakit Terdeteksi</th>
                                <th width="55%">Hasil / Kesimpulan</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
try {
    $stmt = $pdo->query("SELECT d.*, p.nama_penyakit 
                        FROM tbl_diagnosa d 
                        LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                        ORDER BY d.tanggal_diagnosa DESC LIMIT 5");

    if ($stmt->rowCount() > 0) {
        $no = 1;
        while ($row = $stmt->fetch()) {
            $nama_penyakit = $row['nama_penyakit'] ? htmlspecialchars($row['nama_penyakit']) : '<span class="text-muted fst-italic">Tidak Teridentifikasi</span>';
            $tanggal = date('d M Y, H:i', strtotime($row['tanggal_diagnosa']));
            
            // Perbaikan: Bulatkan persentase agar rapi (Contoh: 75.33%)
            $persentase = round($row['confidence'] * 100, 2) . '%';
            
            $pembudidaya = htmlspecialchars($row['nama_pembudidaya'] ?? 'Tidak diketahui');

            echo "<tr>
                    <td class='text-center'>$no</td>
                    <td><span class='badge bg-light text-dark border'>$tanggal</span></td>
                    <td class='fw-bold text-danger'>$nama_penyakit</td>
                    <td>
                        <span class='d-block text-dark'>Hasil diagnosa menunjukkan tingkat kecocokan algoritma sebesar: <b>$persentase</b></span>
                        <small class='text-muted'><i class='bi bi-person'></i> Peternak / Kolam: $pembudidaya</small>
                    </td>
                  </tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='4' class='text-center py-4 text-muted'><i class='bi bi-inbox fs-4 d-block mb-2'></i>Belum ada riwayat diagnosa dilakukan.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4' class='text-center text-danger py-4'>Gagal memuat data: " . $e->getMessage() . "</td></tr>";
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>