<?php
require_once '../../includes/header.php';

// Ambil ID admin yang sedang login (untuk mengecualikan dirinya sendiri dari tombol hapus)
// Catatan: Variabel $user_id biasanya di-set di header.php. Jika namanya beda (misal $_SESSION['id_admin']), kodenya akan tetap aman menggunakan fallback ini.
// Ambil ID admin yang sedang login (Opsional, buat jaga-jaga kalau butuh)
$current_admin_id = $_SESSION['id_admin'] ?? $_SESSION['user_id'] ?? $user_id ?? 0;

// PERBAIKAN: Hapus klausa "WHERE id_admin != ?" agar admin yang sedang login juga tampil
$stmt = $pdo->prepare("SELECT * FROM tbl_admin ORDER BY nama_admin ASC");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Administrator Sistem</h6>
        <div>
            <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#printAdminModal">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahAdminModal">
                <i class="bi bi-plus"></i> Tambah Admin
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Username</th>
                        <th width="50%">Nama Administrator</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $key => $a): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= htmlspecialchars($a['username']) ?></td>
                        <td>
                            <?= htmlspecialchars($a['nama_admin']) ?>
                            <?php if($a['id_admin'] == $current_admin_id): ?>
                                <span class="badge bg-success ms-1">Anda</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button"class="btn btn-sm btn-light border text-warning edit-admin"data-id="<?= $a['id_admin'] ?>"title="Edit Admin">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button"class="btn btn-sm btn-light border text-danger delete-admin"data-id="<?= $a['id_admin'] ?>title="Hapus Admin">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="printAdminModal" tabindex="-1" aria-labelledby="printAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printAdminModalLabel">Cetak Data Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $base_url ?>process/print_user.php" method="post" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Format Cetak:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="formatPDF" value="pdf" checked>
                            <label class="form-check-label" for="formatPDF">Dokumen PDF Resmi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="formatExcel" value="excel">
                            <label class="form-check-label" for="formatExcel">Spreadsheet Excel</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahAdminModal" tabindex="-1" aria-labelledby="tambahAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahAdminModalLabel">Tambah Administrator Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $base_url ?>process/admin_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_admin" class="form-label">Nama Administrator</label>
                        <input type="text" class="form-control" id="nama_admin" name="nama_admin" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Edit Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $base_url ?>process/admin_process.php" method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="editId" name="id_admin">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editNama" class="form-label">Nama Administrator</label>
                        <input type="text" class="form-control" id="editNama" name="nama_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password Baru <small class="text-danger">(Kosongkan jika tidak diubah)</small></label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAdminModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus Administrator ini? Hak akses mereka akan dicabut secara permanen.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= $base_url ?>process/admin_process.php" method="post">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deleteId" name="id_admin">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Tombol Edit di-klik
    $(document).on('click', '.edit-admin', function() {
        var id = $(this).data('id');
        
        // Tambahkan loading spinner atau disable tombol jika mau (opsional)
        
        $.ajax({
            url: '/sistem_pakar_ikan_nila/process/admin_process.php',
            type: 'GET',
            data: {action: 'read', id_admin: id},
            dataType: 'json',
            success: function(response) {
                // Isi form modal dengan data dari database
                $('#editId').val(response.id_admin);
                $('#editUsername').val(response.username);
                $('#editNama').val(response.nama_admin);
                $('#editPassword').val(''); // Kosongkan field password
                
                // Tampilkan modal edit
                $('#editAdminModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Ada error AJAX: ", error);
                alert("Gagal mengambil data. Pastikan file admin_process.php tersedia.");
            }
        });
    });

    // Tombol Delete di-klik
    $(document).on('click', '.delete-admin', function() {
        var id = $(this).data('id');
        $('#deleteId').val(id); // Masukkan ID ke input hidden di modal hapus
        $('#deleteAdminModal').modal('show'); // Tampilkan modal hapus
    });
});
</script>