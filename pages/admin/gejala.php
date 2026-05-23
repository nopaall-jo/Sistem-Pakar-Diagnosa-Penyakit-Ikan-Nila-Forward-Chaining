<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Ambil data gejala, diurutkan secara cerdas berdasarkan angka di belakang 'G'
try {
    $stmt = $pdo->query("SELECT * FROM tbl_gejala ORDER BY CAST(SUBSTRING(kode_gejala, 2) AS UNSIGNED) ASC");
    $gejala = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data gejala: " . $e->getMessage());
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
            <i class="bi bi-clipboard2-pulse-fill me-2 text-primary"></i>Data Gejala Ikan Nila
        </h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#printGejalaModal">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahGejalaModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Gejala
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light text-muted text-center">
                    <tr>
                        <th class="text-center" width="8%">No</th>
                        <th width="15%">Kode Gejala</th>
                        <th width="57%">Deskripsi Gejala Klinis</th>
                        <th class="text-center pe-4" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($gejala) > 0): ?>
                        <?php foreach ($gejala as $key => $g): ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $key + 1 ?></td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle px-2 py-2">
                                    <i class="bi bi-hash me-1"></i><?= htmlspecialchars($g['kode_gejala']) ?>
                                </span>
                            </td>
                            <td class="fw-medium text-dark"><?= htmlspecialchars($g['nama_gejala']) ?></td>
                            <td class="text-center">
                            <div class="btn-group shadow-sm" role="group">
                                <button type="button" class="btn btn-sm btn-light border text-warning edit-gejala" 
                                        data-id="<?= $g['kode_gejala'] ?>" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light border text-danger" 
                                        onclick="confirmDelete('<?= $g['kode_gejala'] ?>')" title="Hapus Data">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-clipboard-x d-block fs-1 text-muted mb-2"></i>
                                <span class="text-muted">Belum ada data gejala yang terdaftar.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="printGejalaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-printer me-2"></i>Cetak Laporan Gejala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPrintGejala" action="../../process/print_gejala.php" method="post" target="_blank">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold mb-3">Pilih Format Laporan</label>
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

                    <div class="mb-3">
                        <label for="jenisLaporan" class="form-label fw-bold">Lingkup Laporan</label>
                        <select class="form-select shadow-sm" id="jenisLaporan" name="jenis_laporan" onchange="toggleGejalaSelect(this.value)">
                            <option value="semua">Semua Gejala</option>
                            <option value="terpilih">Pilih Gejala Tertentu</option>
                        </select>
                    </div>

                    <div class="mb-0" id="kodeGejalaContainer" style="display:none;">
                        <label for="kodeGejala" class="form-label fw-bold text-primary">Pilih Gejala Terkait</label>
                        <select class="form-select shadow-sm" id="kodeGejala" name="kode_gejala[]" multiple style="height: 150px;">
                            <?php foreach ($gejala as $g): ?>
                            <option value="<?= $g['kode_gejala'] ?>"><?= $g['kode_gejala'] ?> - <?= $g['nama_gejala'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted mt-2 d-block">*Gunakan Ctrl + Klik untuk memilih lebih dari satu.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-printer me-1"></i> Mulai Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Gejala -->
<div class="modal fade" id="tambahGejalaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Gejala Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahGejala" action="<?= $base_url ?>process/gejala_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Gejala</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary fw-bold">G</span>
                            <input type="text" class="form-control" name="kode_gejala" placeholder="Contoh: 01" required>
                        </div>
                        <small class="text-muted">Gunakan angka saja jika sistem otomatis menambahkan prefix 'G'.</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Deskripsi Gejala Klinis</label>
                        <textarea class="form-control" name="nama_gejala" rows="3" placeholder="Contoh: Ikan berenang tidak beraturan atau megap-megap di permukaan air" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Gejala</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Gejala -->
<div class="modal fade" id="editGejalaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Perbarui Data Gejala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditGejala" action="<?= $base_url ?>process/gejala_process.php" method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="editKode" name="kode_gejala">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Gejala (Tetap)</label>
                        <input type="text" class="form-control bg-light" id="displayKode" disabled>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Deskripsi Gejala Klinis</label>
                        <textarea class="form-control" id="editNama" name="nama_gejala" rows="3" required></textarea>
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

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteGejalaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Gejala</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                <p class="mb-0">Apakah Anda yakin ingin menghapus data gejala ini?</p>
                <small class="text-muted text-wrap">Menghapus gejala akan memengaruhi aturan (relasi) pada basis pengetahuan sistem pakar.</small>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <form id="formDeleteGejala" action="<?= $base_url ?>process/gejala_process.php" method="post">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deleteKode" name="kode_gejala">
                    <button type="submit" ...>Ya, Hapus!</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // URL Backend - Pastikan path ini benar (naik 2 tingkat ke folder process)
    const processUrl = '../../process/gejala_process.php';

    // 2. Inisialisasi DataTable (Pencarian & Pagination)
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari Gejala:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "paginate": { "next": "Lanjut", "previous": "Kembali" }
            }
        });
    }

    // 3. Inisialisasi Select2 untuk Modal Cetak
    $('#kodeGejala').select2({
        dropdownParent: $('#printGejalaModal'),
        placeholder: "--- Pilih satu atau lebih gejala ---",
        allowClear: true,
        width: '100%'
    });

    // 4. Logic Tampilan Pilih Gejala pada Modal Cetak (Slide Toggle)
    $('#jenisLaporan').on('change', function() {
        if ($(this).val() === 'terpilih') {
            $('#kodeGejalaContainer').stop().slideDown(300);
        } else {
            $('#kodeGejalaContainer').stop().slideUp(300);
            $('#kodeGejala').val(null).trigger('change'); 
        }
    });

    // 5. Fungsi Edit Gejala (AJAX Read)
    // Menggunakan class .edit-gejala sesuai tombol di tabel
    $(document).on('click', '.edit-gejala', function() {
        var kode = $(this).data('id');
        $.ajax({
            url: processUrl,
            type: 'GET',
            data: { action: 'read', kode_gejala: kode },
            dataType: 'json',
            success: function(response) {
                if(response.error) {
                    Swal.fire('Error', response.error, 'error');
                    return;
                }
                
                // Sinkronisasi dengan ID Modal Edit kamu
                $('#editKode').val(response.kode_gejala);    // Hidden input
                $('#displayKode').val(response.kode_gejala); // Input disabled
                $('#editNama').val(response.nama_gejala);    // Textarea
                
                $('#editGejalaModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Gagal mengambil data dari database.', 'error');
            }
        });
    });

    // 6. Fungsi Hapus Gejala (Listener via Class)
    $(document).on('click', '.delete-gejala', function() {
        var kode = $(this).data('id');
        confirmDelete(kode); // Memanggil fungsi SweetAlert di bawah
    });
});

/**
 * 7. Fungsi Konfirmasi Hapus (SweetAlert2)
 * Dipanggil oleh tombol onclick="confirmDelete" atau listener class
 */
function confirmDelete(kode) {
    Swal.fire({
        title: 'Hapus Gejala ' + kode + '?',
        text: "Data relasi penyakit (tbl_aturan) yang menggunakan gejala ini juga akan terhapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Isi input hidden pada form delete dan submit
            $('#deleteKode').val(kode);
            $('#formDeleteGejala').submit();
        }
    });
}
</script>