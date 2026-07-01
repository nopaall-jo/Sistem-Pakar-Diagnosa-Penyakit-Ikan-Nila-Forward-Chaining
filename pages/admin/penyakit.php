<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../config/database.php';
require_once '../../includes/header.php';

try {
    $penyakit = $pdo->query("SELECT * FROM tbl_penyakit ORDER BY CAST(SUBSTRING(kode_penyakit, 2) AS UNSIGNED) ASC")->fetchAll();
    $stmtKode = $pdo->query("SELECT kode_penyakit FROM tbl_penyakit ORDER BY CAST(SUBSTRING(kode_penyakit, 2) AS UNSIGNED) DESC LIMIT 1");
    $lastKode = $stmtKode->fetch();
    $kodeBaru = 'P' . str_pad(($lastKode ? ((int) substr($lastKode['kode_penyakit'], 1) + 1) : 1), 2, '0', STR_PAD_LEFT);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>



<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-virus me-2 text-primary"></i>Daftar Penyakit</h5>
        <div class="d-flex gap-2">
            <a href="../../process/print_penyakit.php?format=pdf" target="_blank" class="btn btn-outline-success btn-sm"><i class="bi bi-printer"></i> Cetak</a>
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahPenyakitModal"><i class="bi bi-plus-lg"></i> Tambah Data</button>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%">
                <thead class="table-light text-muted text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th width="55%">Nama Penyakit</th>
                        <th width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($penyakit as $key => $p): ?>
                    <tr>
                        <td class="text-center text-muted"><?= $key + 1 ?></td>
                        <td><span class="badge bg-light text-primary border border-primary-subtle px-2"><?= htmlspecialchars($p['kode_penyakit']) ?></span></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($p['nama_penyakit']) ?></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button type="button" class="btn btn-sm btn-light border text-info view-penyakit" data-id="<?= $p['kode_penyakit'] ?>"><i class="bi bi-eye"></i></button>
                                <button type="button" class="btn btn-sm btn-light border text-warning edit-penyakit" data-id="<?= $p['kode_penyakit'] ?>"><i class="bi bi-pencil-square"></i></button>
                                <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete('<?= $p['kode_penyakit'] ?>')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Penyakit -->
<div class="modal fade" id="tambahPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Data Penyakit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../../process/penyakit_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4 mb-3"><label class="form-label fw-bold">Kode Penyakit</label><input type="text" class="form-control" name="kode_penyakit" value="<?= $kodeBaru ?>" readonly></div>
                        <div class="col-md-8 mb-3"><label class="form-label fw-bold">Nama Penyakit</label><input type="text" class="form-control" name="nama_penyakit" required></div>
                    </div>
                    <div class="mb-3"><label class="form-label fw-bold">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="3"></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label fw-bold text-success">Solusi</label><textarea class="form-control border-success-subtle" name="solusi" rows="4"></textarea></div>
                        <div class="col-md-6 mb-3"><label class="form-label fw-bold text-info">Pencegahan</label><textarea class="form-control border-info-subtle" name="pencegahan" rows="4"></textarea></div>
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

<!-- Modal Edit Penyakit -->
<div class="modal fade" id="editPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Penyakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../../process/penyakit_process.php" method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="editKode" name="kode_penyakit">
                <div class="modal-body p-4">
                    <div class="mb-3"><label class="form-label fw-bold">Nama Penyakit</label><input type="text" class="form-control" id="editNama" name="nama_penyakit" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Deskripsi</label><textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label fw-bold text-success">Solusi</label><textarea class="form-control border-success-subtle" id="editSolusi" name="solusi" rows="4"></textarea></div>
                        <div class="col-md-6 mb-3"><label class="form-label fw-bold text-info">Pencegahan</label><textarea class="form-control border-info-subtle" id="editPencegahan" name="pencegahan" rows="4"></textarea></div>
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

<!-- Modal View Penyakit -->
<div class="modal fade" id="viewPenyakitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i>Detail Penyakit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.2rem;" id="viewKodeBadge"></div>
                    <div><h4 class="fw-bold mb-0" id="viewNama"></h4></div>
                </div>
                <div class="row g-3">
                    <div class="col-12"><label class="fw-bold small text-muted d-block">DESKRIPSI</label><div id="viewDeskripsi" class="p-3 bg-light border-start border-4 border-primary rounded"></div></div>
                    <div class="col-md-6"><label class="fw-bold small text-success d-block">SOLUSI</label><div id="viewSolusi" class="p-3 bg-success-subtle text-success-emphasis border-start border-4 border-success rounded"></div></div>
                    <div class="col-md-6"><label class="fw-bold small text-info d-block">PENCEGAHAN</label><div id="viewPencegahan" class="p-3 bg-info-subtle text-info-emphasis border-start border-4 border-info rounded"></div></div>
                </div>
            </div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>

<form id="formDeletePenyakit" action="../../process/penyakit_process.php" method="post" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="deleteKode" name="kode_penyakit">
</form>

<?php require_once '../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    window.setTimeout(function() { $(".alert").slideUp(500); }, 4000);

    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({ "pageLength": 10, "language": { "search": "Cari Penyakit:" } });
    }

    function getPenyakit(kode, callback) {
        $.ajax({
            url: '../../process/penyakit_process.php',
            type: 'GET',
            data: { action: 'read', kode_penyakit: kode },
            dataType: 'json',
            success: callback
        });
    }

    $(document).on('click', '.view-penyakit', function() {
        getPenyakit($(this).data('id'), function(res) {
            $('#viewKodeBadge').text(res.kode_penyakit);
            $('#viewNama').text(res.nama_penyakit);
            $('#viewDeskripsi').html((res.deskripsi || '-').replace(/\n/g, '<br>'));
            $('#viewSolusi').html((res.solusi || '-').replace(/\n/g, '<br>'));
            $('#viewPencegahan').html((res.pencegahan || '-').replace(/\n/g, '<br>'));
            $('#viewPenyakitModal').modal('show');
        });
    });

    $(document).on('click', '.edit-penyakit', function() {
        getPenyakit($(this).data('id'), function(res) {
            $('#editKode').val(res.kode_penyakit);
            $('#editNama').val(res.nama_penyakit);
            $('#editDeskripsi').val(res.deskripsi);
            $('#editSolusi').val(res.solusi);
            $('#editPencegahan').val(res.pencegahan);
            $('#editPenyakitModal').modal('show');
        });
    });
});

function confirmDelete(kode) {
    Swal.fire({
        title: 'Hapus Penyakit?',
        text: "Menghapus penyakit " + kode + " juga menghapus seluruh aturan terkait!",
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleteKode').val(kode);
            $('#formDeletePenyakit').submit();
        }
    });
}
</script>