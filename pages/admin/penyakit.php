<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Ambil data dari tbl_penyakit sesuai struktur KKP kamu
try {
    $stmt = $pdo->query("SELECT * FROM tbl_penyakit ORDER BY CAST(SUBSTRING(kode_penyakit, 2) AS UNSIGNED) ASC");
    $penyakit = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
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

<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="m-0 font-weight-bold text-dark">
            <i class="bi bi-virus me-2 text-primary"></i>Daftar Penyakit Ikan Nila
        </h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#printPenyakitModal">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahPenyakitModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light text-muted text-center">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="30%">Nama Penyakit</th>
                        <th width="30%">Nama Latin / Ilmiah</th>
                        <th class="text-center pe-4" width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($penyakit) > 0): ?>
                        <?php foreach ($penyakit as $key => $p): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $key + 1 ?></td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle px-2">
                                    <?= htmlspecialchars($p['kode_penyakit']) ?>
                                </span>
                            </td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($p['nama_penyakit']) ?></td>
                            <td class="text-muted"><em><?= htmlspecialchars($p['nama_latin'] ?? '-') ?></em></td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm" role="group">
                                    <button type="button"  class="btn-sm btn-light border text-info view-penyakit" data-id="<?= $p['kode_penyakit'] ?>" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border text-warning edit-penyakit" 
                                            data-id="<?= $p['kode_penyakit'] ?>" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border text-danger delete-penyakit" 
                                            data-id="<?= $p['kode_penyakit'] ?>" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data penyakit.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="printPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-printer me-2 text-success"></i>Ekspor Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPrintPenyakit" action="../../process/print_penyakit.php" method="post" target="_blank">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold mb-3">Pilih Format Laporan</label>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="format" id="formatPDF" value="pdf" checked>
                            <label class="btn btn-outline-danger w-100 py-3 rounded-3" for="formatPDF">
                                <i class="bi bi-file-earmark-pdf fs-2 d-block mb-1"></i> PDF
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="format" id="formatExcel" value="excel">
                            <label class="btn btn-outline-success w-100 py-3 rounded-3" for="formatExcel">
                                <i class="bi bi-file-earmark-excel fs-2 d-block mb-1"></i> Excel
                            </label>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="jenisLaporan" class="form-label fw-bold">Lingkup Data</label>
                        <select class="form-select shadow-sm border-primary-subtle" id="jenisLaporan" name="jenis_laporan">
                            <option value="semua">Semua Data Penyakit</option>
                            <option value="ringkas">Ringkasan (Kode & Nama Saja)</option>
                            <option value="lengkap">Lengkap (Dengan Deskripsi & Solusi)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-printer me-1"></i> Mulai Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Penyakit -->
<div class="modal fade" id="tambahPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Data Penyakit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahPenyakit" action="<?= $base_url ?>process/penyakit_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Kode Penyakit</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control" name="kode_penyakit" placeholder="Contoh: P01" required>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Penyakit</label>
                            <input type="text" class="form-control" name="nama_penyakit" placeholder="Masukkan nama penyakit ikan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Latin / Ilmiah</label>
                        <input type="text" class="form-control" name="nama_latin" placeholder="Contoh: Ichthyophthirius multifiliis">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Penyakit</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai penyakit ini..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-success"><i class="bi bi-check2-circle me-1"></i>Solusi Pengobatan</label>
                            <textarea class="form-control border-success-subtle" name="solusi" rows="4" placeholder="Langkah-langkah pengobatan..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-info"><i class="bi bi-shield-plus me-1"></i>Langkah Pencegahan</label>
                            <textarea class="form-control border-info-subtle" name="pencegahan" rows="4" placeholder="Cara mencegah agar tidak menular..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Penyakit -->
<div class="modal fade" id="editPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Penyakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPenyakit" action="<?= $base_url ?>process/penyakit_process.php" method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="editKode" name="kode_penyakit">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Nama Penyakit</label>
                            <input type="text" class="form-control" id="editNama" name="nama_penyakit" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Latin</label>
                        <input type="text" class="form-control" id="editLatin" name="nama_latin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-success">Solusi</label>
                            <textarea class="form-control border-success-subtle" id="editSolusi" name="solusi" rows="4"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-info">Pencegahan</label>
                            <textarea class="form-control border-info-subtle" id="editPencegahan" name="pencegahan" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Penyakit -->
<div class="modal fade" id="viewPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i>Detail Informasi Penyakit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4 align-items-center">
                    <div class="col-md-auto mb-3 mb-md-0">
                        <div class="bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 1.5rem;" id="viewKodeBadge">
                            </div>
                    </div>
                    <div class="col-md">
                        <h3 class="fw-bold mb-1" id="viewNama" style="color: var(--dark);"></h3>
                        <p class="text-muted mb-0"><i class="bi bi-tag me-1"></i> Nama Latin: <em id="viewLatin"></em></p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <label class="fw-bold text-uppercase small text-muted mb-2 d-block">Deskripsi Penyakit</label>
                        <div id="viewDeskripsi" class="p-3 rounded-3 border-start border-4 border-primary bg-light" style="line-height: 1.6;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-uppercase small text-success mb-2 d-block"><i class="bi bi-capsule me-1"></i> Rekomendasi Solusi</label>
                        <div id="viewSolusi" class="p-3 rounded-3 border-start border-4 border-success bg-success-subtle text-success-emphasis" style="min-height: 100px;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-uppercase small text-info mb-2 d-block"><i class="bi bi-shield-check me-1"></i> Tindakan Pencegahan</label>
                        <div id="viewPencegahan" class="p-3 rounded-3 border-start border-4 border-info bg-info-subtle text-info-emphasis" style="min-height: 100px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Selesai Membaca</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                <p class="mb-0">Apakah Anda yakin ingin menghapus data penyakit ini?</p>
                <small class="text-muted">Tindakan ini juga akan menghapus aturan (relasi) yang terkait.</small>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formDeletePenyakit" action="<?= $base_url ?>process/penyakit_process.php" method="post">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deleteKode" name="kode_penyakit">
                    <button type="submit" class="btn btn-danger px-4">Ya, Hapus!</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Inisialisasi DataTable (Jika belum ada di script.js)
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari Penyakit:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "paginate": {
                    "next": "Lanjut",
                    "previous": "Kembali"
                }
            }
        });
    }

    // 1. View Penyakit (AJAX)
    $(document).on('click', '.view-penyakit', function() {
        var kode = $(this).data('id');
        $.ajax({
            url: '<?= $base_url ?>process/penyakit_process.php',
            type: 'GET',
            data: {action: 'read', kode_penyakit: kode},
            dataType: 'json',
            success: function(response) {
                // Isi Badge Kode di Modal View yang tadi kita buat
                $('#viewKodeBadge').text(response.kode_penyakit);
                $('#viewNama').text(response.nama_penyakit);
                $('#viewLatin').text(response.nama_latin || '-');
                
                // Format text agar baris baru jadi <br>
                $('#viewDeskripsi').html(response.deskripsi ? response.deskripsi.replace(/\n/g, '<br>') : '<em class="text-muted">Tidak ada deskripsi.</em>');
                $('#viewSolusi').html(response.solusi ? response.solusi.replace(/\n/g, '<br>') : '<em class="text-muted">Belum ada solusi.</em>');
                $('#viewPencegahan').html(response.pencegahan ? response.pencegahan.replace(/\n/g, '<br>') : '<em class="text-muted">Belum ada data pencegahan.</em>');
                
                $('#viewPenyakitModal').modal('show');
            }
        });
    });

    // 2. Edit Penyakit (AJAX)
    $(document).on('click', '.edit-penyakit', function() {
        var kode = $(this).data('id');
        $.ajax({
            url: '<?= $base_url ?>process/penyakit_process.php',
            type: 'GET',
            data: {action: 'read', kode_penyakit: kode},
            dataType: 'json',
            success: function(response) {
                $('#editKode').val(response.kode_penyakit);
                $('#editNama').val(response.nama_penyakit);
                $('#editLatin').val(response.nama_latin);
                $('#editDeskripsi').val(response.deskripsi);
                $('#editSolusi').val(response.solusi);
                $('#editPencegahan').val(response.pencegahan);
                $('#editPenyakitModal').modal('show');
            }
        });
    });

    // 3. Delete Penyakit (SweetAlert2 Integration)
    $(document).on('click', '.delete-penyakit', function() {
        var kode = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Data Penyakit?',
            text: "Kode " + kode + " dan aturan terkait akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim form delete secara otomatis
                $('#deleteKode').val(kode);
                $('#formDeletePenyakit').submit();
            }
        });
    });
});
</script>