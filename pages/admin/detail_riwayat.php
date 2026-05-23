<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

// Pastikan hanya admin yang bisa mengakses
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: riwayat.php");
    exit();
}

$id_diagnosa = $_GET['id'];

// Ambil data diagnosa
$stmt = $pdo->prepare("SELECT d.*, p.nama_penyakit, p.deskripsi, p.solusi,p.pencegahan, u.nama_lengkap as nama_user 
                      FROM diagnosa d 
                      LEFT JOIN penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                      LEFT JOIN users u ON d.id_user = u.id
                      WHERE d.id = ?");
$stmt->execute([$id_diagnosa]);
$diagnosa = $stmt->fetch();

if (!$diagnosa) {
    header("Location: riwayat.php");
    exit();
}

// Ambil gejala yang dipilih
$stmt = $pdo->prepare("SELECT g.kode_gejala, g.nama_gejala 
                      FROM diagnosa_detail dd 
                      JOIN gejala g ON dd.kode_gejala = g.kode_gejala 
                      WHERE dd.id_diagnosa = ?");
$stmt->execute([$id_diagnosa]);
$gejala = $stmt->fetchAll();
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Detail Diagnosa</h6>
        <a href="riwayat.php" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Informasi User</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nama User</th>
                        <td><?= htmlspecialchars($diagnosa['nama_user']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Diagnosa</th>
                        <td><?= date('d/m/Y H:i', strtotime($diagnosa['tanggal_diagnosa'])) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Hasil Diagnosa</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Penyakit</th>
                        <td><?= $diagnosa['nama_penyakit'] ?? 'Tidak diketahui' ?></td>
                    </tr>
                    <tr>
                        <th>Tingkat Kecocokan</th>
                        <td><?= round(($diagnosa['confidence'] * 100), 2) ?>%</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h5>Gejala yang Dipilih</h5>
                <ul class="list-group">
                    <?php foreach ($gejala as $g): ?>
                    <li class="list-group-item">
                         <?= htmlspecialchars($g['kode_gejala']) ?> | <?= htmlspecialchars($g['nama_gejala']) ?>
                        <span class="badge badge-primary float-right">CF: <?= $g['cf_pakar'] ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php if (!empty($diagnosa['nama_penyakit'])): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <h5>Informasi Penyakit</h5>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Deskripsi</h6>
                        <p class="card-text"><?= nl2br(htmlspecialchars($diagnosa['deskripsi'])) ?></p>
                        <h6 class="card-title mt-3">Solusi</h6>
                        <p class="card-text"><?= nl2br(htmlspecialchars($diagnosa['solusi'])) ?></p>
                         <h6 class="card-title mt-3">Pencegahan</h6>
                        <p class="card-text"><?= nl2br(htmlspecialchars($diagnosa['pencegahan'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>