<?php
// Pastikan session sudah dimulai untuk menangkap pesan sukses/error
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../config/database.php';
require_once '../../includes/header.php';

try {
    // 1. Ambil data relasi (Aturan) 
    // SINKRONISASI: Menggunakan id_aturan sesuai struktur tabel kamu
    $sql_relasi = "SELECT a.id_aturan, p.kode_penyakit, p.nama_penyakit, g.kode_gejala, g.nama_gejala 
                   FROM tbl_aturan a
                   JOIN tbl_penyakit p ON a.kode_penyakit = p.kode_penyakit
                   JOIN tbl_gejala g ON a.kode_gejala = g.kode_gejala
                   ORDER BY p.kode_penyakit ASC, CAST(SUBSTRING(g.kode_gejala, 2) AS UNSIGNED) ASC";
    
    $stmt = $pdo->query($sql_relasi);
    $relasi = $stmt->fetchAll();

    // 2. Ambil data penyakit untuk dropdown Modal Tambah
    $sql_penyakit = "SELECT kode_penyakit, nama_penyakit FROM tbl_penyakit ORDER BY kode_penyakit ASC";
    $penyakit = $pdo->query($sql_penyakit)->fetchAll();

    // 3. Ambil data gejala untuk dropdown Modal Tambah
    // Diurutkan secara numerik (G1, G2, G10) agar admin tidak bingung saat memilih
    $sql_gejala = "SELECT kode_gejala, nama_gejala 
                   FROM tbl_gejala 
                   ORDER BY CAST(SUBSTRING(kode_gejala, 2) AS UNSIGNED) ASC";
    $gejala = $pdo->query($sql_gejala)->fetchAll();

} catch (PDOException $e) {
    // Menampilkan pesan error yang lebih spesifik jika kueri gagal
    die("Error mengambil data relasi: " . $e->getMessage());
}
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-diagram-3-fill me-2"></i>Basis Pengetahuan (Aturan)
        </h6>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#printRelasiModal">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahRelasiModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Aturan
            </button>
        </div>
    </div> 
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light text-center">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="40%">Data Penyakit</th>
                        <th width="40%">Gejala Terkait</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($relasi) > 0): ?>
                        <?php foreach ($relasi as $key => $r): ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $key + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-2">
                                        <?= htmlspecialchars($r['kode_penyakit']) ?>
                                    </span>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($r['nama_penyakit']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle me-2">
                                        <?= htmlspecialchars($r['kode_gejala']) ?>
                                    </span>
                                    <span class="text-secondary"><?= htmlspecialchars($r['nama_gejala']) ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <button type="button" class="btn btn-sm btn-light border text-danger delete-relasi" 
                                            data-id="<?= $r['id_aturan'] ?>" title="Hapus Aturan">
                                        <i class="bi bi-trash3" style="pointer-events: none;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-node-plus d-block fs-1 text-muted mb-2"></i>
                                <span class="text-muted">Belum ada aturan IF-THEN yang dikonfigurasi.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="printRelasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-printer me-2"></i>Cetak Basis Pengetahuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPrintRelasi" action="<?= $base_url ?>process/print_relasi.php" method="post" target="_blank">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold mb-3">Pilih Format Dokumen</label>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="format" id="formatPDF" value="pdf" checked>
                            <label class="btn btn-outline-danger w-100 py-3" for="formatPDF">
                                <i class="bi bi-file-earmark-pdf fs-2 d-block mb-1"></i> PDF
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="format" id="formatExcel" value="excel">
                            <label class="btn btn-outline-success w-100 py-3" for="formatExcel">
                                <i class="bi bi-file-earmark-excel fs-2 d-block mb-1"></i> Excel
                            </label>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="groupBy" class="form-label fw-bold">Metode Pengelompokan</label>
                        <select class="form-select shadow-sm" id="groupBy" name="group_by">
                            <option value="none">Data Mentah (Sesuai Input)</option>
                            <option value="penyakit">Urutkan Per Penyakit</option>
                            <option value="gejala">Urutkan Per Gejala</option>
                        </select>
                        <small class="text-muted mt-2 d-block">Membantu proses pengecekan aturan yang tumpang tindih.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-printer me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Relasi -->
<div class="modal fade" id="tambahRelasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Aturan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahRelasi" action="../../process/relasi_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm small mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Pilih satu penyakit dan <strong>satu atau lebih gejala</strong> untuk membentuk basis pengetahuan <strong>Forward Chaining</strong>.
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="bi bi-bug me-1 text-danger"></i>Pilih Penyakit</label>
                        <select class="form-select select2-relasi" name="kode_penyakit" required>
                            <option value="">-- Cari Penyakit --</option>
                            <?php foreach ($penyakit as $p): ?>
                            <option value="<?= $p['kode_penyakit'] ?>">
                                [<?= $p['kode_penyakit'] ?>] <?= htmlspecialchars($p['nama_penyakit']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold"><i class="bi bi-thermometer-half me-1 text-success"></i>Pilih Gejala Terkait</label>
                        <select class="form-select select2-relasi" name="kode_gejala[]" multiple="multiple" required>
                            <?php foreach ($gejala as $g): ?>
                            <option value="<?= $g['kode_gejala'] ?>">
                                [<?= $g['kode_gejala'] ?>] <?= htmlspecialchars($g['nama_gejala']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted d-block mt-2">*Anda dapat memilih beberapa gejala sekaligus.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteRelasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Aturan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                <p class="mb-0">Apakah Anda yakin ingin menghapus relasi penyakit-gejala ini?</p>
                <small class="text-muted">Aturan ini akan dihapus dari mesin inferensi sistem pakar.</small>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formDeleteRelasi" action="../../process/relasi_process.php" method="POST" style="display:none;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deleteIdAturan" name="id_aturan">
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // --- KODE BARU: Auto Close Alert Notifikasi ---

    // 1. Alert akan otomatis naik dan menghilang setelah 3 detik (3000 milidetik)
    window.setTimeout(function() {
        $(".alert").not(".modal .alert").slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5791);

    var modalAlertTimer;// Variabel untuk menampung hitungan mundur modal
    // 2. KODE BARU: Jalankan timer SETIAP KALI modal "Tambah Aturan" selesai terbuka
    $('#tambahRelasiModal').on('shown.bs.modal', function () {
        var $alertInfo = $(this).find('.alert-info');
        $alertInfo.show(); // Pastikan alert biru tampil dulu setiap modal dibuka
        clearTimeout(modalAlertTimer);
        modalAlertTimer = setTimeout(function() {
            $alertInfo.slideUp(500);
        }, 5000);
    });

    // 3. Bersihkan timer kalau modal ditutup sebelum 5 detik (opsional, biar rapi)
    $('#tambahRelasiModal').on('hidden.bs.modal', function () {
        clearTimeout(modalAlertTimer);
    });
    
    // 4. Inisialisasi Select2 untuk Modal Tambah Aturan
    $('.select2-relasi').select2({
        dropdownParent: $('#tambahRelasiModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "--- Pilih satu atau lebih gejala ---",
        allowClear: true
    });

    // 5. Delete Relasi dengan SweetAlert2
    $(document).on('click', '.delete-relasi', function(e) {
        e.preventDefault();
        // Mengambil id_aturan dari atribut data-id pada tombol
        var idRelasi = $(this).data('id'); 
        
        Swal.fire({
            title: 'Hapus Aturan?',
            text: "Hubungan penyakit dan gejala ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus Aturan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Memasukkan id_aturan ke input hidden dan submit form
                $('#deleteIdAturan').val(idRelasi);
                $('#formDeleteRelasi').submit();
            }
        });
    });

    // 6. DataTable Initialization (Pencarian & Pagination)
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari Aturan:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada aturan yang cocok",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ aturan",
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