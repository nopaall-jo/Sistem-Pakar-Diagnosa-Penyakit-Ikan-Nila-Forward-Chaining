<?php
// 1. CEK SESSION & KONEKSI DI AWAL
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';

// Pastikan Admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
$id_admin = $_SESSION['id_admin'];

// ====================================================================
// LOGIKA FORWARD CHAINING
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gejala'])) {
    $nama_pembudidaya = htmlspecialchars($_POST['nama_pembudidaya']);
    $selected_gejala = $_POST['gejala'];
    
    // PERBAIKAN: Nama tabel diubah menjadi tbl_aturan sesuai SQL terbaru
    $stmt = $pdo->query("SELECT r.kode_penyakit, r.kode_gejala, p.nama_penyakit 
                         FROM tbl_aturan r 
                         JOIN tbl_penyakit p ON r.kode_penyakit = p.kode_penyakit");
    $relasi = $stmt->fetchAll();
    
    $penyakit_gejala = [];
    
    // A. Hitung total syarat gejala per penyakit
    foreach ($relasi as $r) {
        if (!isset($penyakit_gejala[$r['kode_penyakit']])) {
            $penyakit_gejala[$r['kode_penyakit']] = [
                'nama' => $r['nama_penyakit'],
                'total_gejala' => 0,
                'gejala_cocok' => 0
            ];
        }
        $penyakit_gejala[$r['kode_penyakit']]['total_gejala']++;
    }
    
    // B. Hitung gejala yang cocok
    foreach ($selected_gejala as $gejala_kode) {
        foreach ($relasi as $r) {
            if ($r['kode_gejala'] == $gejala_kode) {
                $penyakit_gejala[$r['kode_penyakit']]['gejala_cocok']++;
            }
        }
    }
    
    // C. Hitung Confidence
    $hasil_diagnosa = [];
    foreach ($penyakit_gejala as $kode => $data) {
        if ($data['gejala_cocok'] > 0) {
            $confidence = $data['gejala_cocok'] / $data['total_gejala'];
            $hasil_diagnosa[] = [
                'kode_penyakit' => $kode,
                'nama_penyakit' => $data['nama'],
                'confidence' => $confidence,
                'gejala_cocok' => $data['gejala_cocok'],
                'total_gejala' => $data['total_gejala']
            ];
        }
    }
    
    usort($hasil_diagnosa, function($a, $b) {
        return $b['confidence'] <=> $a['confidence'];
    });
    
    $hasil_penyakit = !empty($hasil_diagnosa) ? $hasil_diagnosa[0]['kode_penyakit'] : null;
    $confidence_level = !empty($hasil_diagnosa) ? $hasil_diagnosa[0]['confidence'] : 0;
    
    // D. Simpan Riwayat ke tbl_diagnosa
    $stmt = $pdo->prepare("INSERT INTO tbl_diagnosa (id_admin, nama_pembudidaya, hasil_penyakit, confidence, tanggal_diagnosa) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$id_admin, $nama_pembudidaya, $hasil_penyakit, $confidence_level]);
    $diagnosa_id = $pdo->lastInsertId();
    
    // E. Simpan ke tbl_diagnosa_detail
    foreach ($selected_gejala as $gejala_kode) {
        $stmt = $pdo->prepare("INSERT INTO tbl_diagnosa_detail (id_diagnosa, kode_gejala) VALUES (?, ?)");
        $stmt->execute([$diagnosa_id, $gejala_kode]);
    }
    
    $_SESSION['hasil_diagnosa'] = $hasil_diagnosa;
    $_SESSION['nama_pembudidaya'] = $nama_pembudidaya;
    
    header("Location: hasil_diagnosa.php");
    $_SESSION['hasil_diagnosa'] = $hasil_diagnosa;
    $_SESSION['nama_pembudidaya'] = $nama_pembudidaya;
    
    $_SESSION['diagnosa_id'] = $diagnosa_id; // <--- TAMBAHKAN BARIS INI
    
    header("Location: hasil_diagnosa.php");
    exit();
}

require_once '../../includes/header.php';

// Ambil data gejala
$stmt = $pdo->query("SELECT * FROM tbl_gejala ORDER BY CAST(SUBSTRING(kode_gejala, 2) AS UNSIGNED) ASC");
$gejala = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="card shadow-sm border-0 rounded-4 mb-5">
            <div class="card-header bg-primary py-3">
               <h5 class="m-0 font-weight-bold text-white"><i class="bi bi-search me-2"></i>Mulai Diagnosa Baru</h5>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form method="post" action="">
                    
                    <div class="mb-5 bg-light p-4 rounded-3 border">
                        <label for="nama_pembudidaya" class="form-label fw-bold text-dark"><i class="bi bi-person-badge me-2 text-primary"></i>Nama Pembudidaya / Lokasi Kolam</label>
                        <input type="text" class="form-control form-control-lg border-primary-subtle" id="nama_pembudidaya" name="nama_pembudidaya" placeholder="Contoh: Pak Supri - Tambak Nila Blok A" required>
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Data ini akan disimpan ke dalam riwayat sistem untuk pelaporan.</div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-ui-checks-grid me-2 text-success"></i>Pilih Gejala Teramati:</h5>
                            <hr class="flex-grow-1 ms-3 opacity-25">
                        </div>

                        <div class="row g-3">
                            <?php foreach ($gejala as $g): ?>
                            <div class="col-md-6">
                                <label class="symptom-card d-flex align-items-center p-3 rounded-3 border h-100" for="gejala<?= $g['kode_gejala'] ?>">
                                    <div class="form-check m-0">
                                        <input class="form-check-input me-3" type="checkbox" name="gejala[]" value="<?= $g['kode_gejala'] ?>" id="gejala<?= $g['kode_gejala'] ?>">
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-primary border mb-1"><?= htmlspecialchars($g['kode_gejala']) ?></span>
                                        <p class="mb-0 small fw-bold text-dark lh-sm"><?= htmlspecialchars($g['nama_gejala']) ?></p>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <hr class="my-5 opacity-25">

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="bi bi-exclamation-triangle me-1 text-warning"></i> Pastikan semua gejala sudah dicentang dengan benar.</span>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow rounded-3">
                            Proses Forward Chaining <i class="bi bi-cpu ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Efek Kotak Gejala Biar Keren Banget Waktu Diklik Admin */
.symptom-card {
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    background-color: #ffffff;
}

.symptom-card:hover {
    border-color: var(--primary) !important;
    background-color: #f4fbf9;
    transform: translateY(-2px);
}

.form-check-input:checked ~ div p {
    color: var(--primary) !important;
}

.symptom-card:has(input:checked) {
    border-color: var(--primary) !important;
    background-color: #f4fbf9;
    box-shadow: 0 4px 12px rgba(13, 187, 148, 0.15);
}

.form-check-input {
    width: 1.2em;
    height: 1.2em;
    cursor: pointer;
}
</style>

<?php require_once '../../includes/footer.php'; ?>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    // Cari semua checkbox gejala yang sedang dicentang
    var checkedSymptoms = document.querySelectorAll('input[name="gejala[]"]:checked');
    
    // Kalau tidak ada yang dicentang sama sekali
    if (checkedSymptoms.length === 0) {
        e.preventDefault(); // Hentikan proses submit
        alert('Tunggu! Anda harus memilih minimal 1 gejala sebelum memproses diagnosa.');
    }
});
</script>