<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../config/database.php';
require_once '../../includes/header.php';

try {
    $gejala = $pdo->query("SELECT * FROM tbl_gejala ORDER BY CAST(SUBSTRING(kode_gejala, 2) AS UNSIGNED) ASC")->fetchAll();
    $stmtKode = $pdo->query("SELECT kode_gejala FROM tbl_gejala ORDER BY CAST(SUBSTRING(kode_gejala, 2) AS UNSIGNED) DESC LIMIT 1");
    $lastKode = $stmtKode->fetch();
    $kodeBaru = 'G' . str_pad(($lastKode ? ((int) substr($lastKode['kode_gejala'], 1) + 1) : 1), 2, '0', STR_PAD_LEFT);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>



<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-clipboard2-pulse-fill me-2 text-primary"></i>Data Gejala</h5>
        <div class="d-flex gap-2">
            <a href="../../process/print_gejala.php?format=pdf" target="_blank" class="btn btn-outline-success btn-sm"><i class="bi bi-printer"></i> Cetak</a>
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahGejalaModal"><i class="bi bi-plus-lg"></i> Tambah Gejala</button>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%">
                <thead class="table-light text-muted text-center">
                    <tr>
                        <th width="8%">No</th>
                        <th width="15%">Kode Gejala</th>
                        <th width="57%">Deskripsi Gejala Klinis</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gejala as $key => $g): ?>
                    <tr>
                        <td class="text-center text-muted small"><?= $key + 1 ?></td>
                        <td><span class="badge bg-light text-primary border border-primary-subtle px-2 py-2"><i class="bi bi-hash me-1"></i><?= htmlspecialchars($g['kode_gejala']) ?></span></td>
                        <td class="fw-medium text-dark"><?= htmlspecialchars($g['nama_gejala']) ?></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button type="button" class="btn btn-sm btn-light border text-warning edit-gejala" data-id="<?= $g['kode_gejala'] ?>"><i class="bi bi-pencil-square"></i></button>
                                <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete('<?= $g['kode_gejala'] ?>')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Gejala -->
<div class="modal fade" id="tambahGejalaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Gejala</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../../process/gejala_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Gejala</label>
                        <input type="text" class="form-control bg-light" name="kode_gejala" value="<?= $kodeBaru ?>" readonly>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Deskripsi Gejala Klinis</label>
                        <textarea class="form-control" name="nama_gejala" rows="3" placeholder="Contoh: Ikan megap-megap di permukaan air" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
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
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Gejala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../../process/gejala_process.php" method="post">
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
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Delete Gejala -->
<form id="formDeleteGejala" action="../../process/gejala_process.php" method="post" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="deleteKode" name="kode_gejala">
</form>

<?php require_once '../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    window.setTimeout(function() { $(".alert").slideUp(500); }, 4000);

    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({ "pageLength": 10, "language": { "search": "Cari Gejala:" } });
    }

    $(document).on('click', '.edit-gejala', function() {
        var kode = $(this).data('id');
        $.ajax({
            url: '../../process/gejala_process.php',
            type: 'GET',
            data: { action: 'read', kode_gejala: kode },
            dataType: 'json',
            success: function(response) {
                if (response.error) { Swal.fire('Error', response.error, 'error'); return; }
                $('#editKode').val(response.kode_gejala);
                $('#displayKode').val(response.kode_gejala);
                $('#editNama').val(response.nama_gejala);
                $('#editGejalaModal').modal('show');
            }
        });
    });
});

function confirmDelete(kode) {
    Swal.fire({
        title: 'Hapus Gejala?',
        text: "Seluruh aturan relasi yang memakai gejala " + kode + " juga akan terhapus!",
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleteKode').val(kode);
            $('#formDeleteGejala').submit();
        }
    });
}
</script>