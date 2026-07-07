<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../config/database.php';
require_once '../../includes/header.php';

try {
    // 0. Self-Healing Schema
    $check_col = $pdo->query("SHOW COLUMNS FROM tbl_aturan LIKE 'kode_aturan'")->rowCount();
    if ($check_col === 0) {
        $pdo->exec("ALTER TABLE tbl_aturan ADD COLUMN kode_aturan VARCHAR(10) AFTER id_aturan");
        $stmt_old = $pdo->query("SELECT kode_penyakit, MIN(id_aturan) as min_id FROM tbl_aturan GROUP BY kode_penyakit ORDER BY min_id ASC");
        $no = 1;
        $update_old = $pdo->prepare("UPDATE tbl_aturan SET kode_aturan = ? WHERE kode_penyakit = ?");
        foreach ($stmt_old->fetchAll(PDO::FETCH_ASSOC) as $d) {
            $update_old->execute(['R' . str_pad($no++, 2, '0', STR_PAD_LEFT), $d['kode_penyakit']]);
        }
    }

    // 1. Fetch Grouped Rules
    $relasi = $pdo->query("SELECT a.kode_aturan, p.kode_penyakit, p.nama_penyakit, 
                          GROUP_CONCAT(g.kode_gejala ORDER BY CAST(SUBSTRING(g.kode_gejala, 2) AS UNSIGNED) ASC) as gejala_codes, 
                          GROUP_CONCAT(g.nama_gejala ORDER BY CAST(SUBSTRING(g.kode_gejala, 2) AS UNSIGNED) ASC SEPARATOR '||') as gejala_names
                   FROM tbl_aturan a
                   JOIN tbl_penyakit p ON a.kode_penyakit = p.kode_penyakit
                   JOIN tbl_gejala g ON a.kode_gejala = g.kode_gejala
                   GROUP BY a.kode_aturan, p.kode_penyakit, p.nama_penyakit
                   ORDER BY CAST(SUBSTRING(a.kode_aturan, 2) AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);

    // 2. Diseases list (all diseases)
    $penyakit = $pdo->query("SELECT kode_penyakit, nama_penyakit FROM tbl_penyakit ORDER BY kode_penyakit ASC")->fetchAll(PDO::FETCH_ASSOC);

    // 3. Symptoms list
    $gejala = $pdo->query("SELECT kode_gejala, nama_gejala FROM tbl_gejala ORDER BY CAST(SUBSTRING(kode_gejala, 2) AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);

    // 4. Next rule code
    $stmt_max = $pdo->query("SELECT MAX(CAST(SUBSTRING(kode_aturan, 2) AS UNSIGNED)) as max_rule FROM tbl_aturan");
    $next_kode_aturan = 'R' . str_pad(($stmt_max->fetch(PDO::FETCH_ASSOC)['max_rule'] ?? 0) + 1, 2, '0', STR_PAD_LEFT);

} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>



<!-- Main Table Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-diagram-3-fill me-2"></i>Aturan (Relasi)</h6>
        <div class="d-flex gap-2">
            <a href="../../process/print_relasi.php?format=pdf&group_by=penyakit" target="_blank" class="btn btn-outline-success btn-sm"><i class="bi bi-printer"></i> Cetak</a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahRelasiModal"><i class="bi bi-plus-lg"></i> Tambah Aturan</button>
        </div>
    </div> 
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle" id="dataTable" width="100%">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Aturan</th>
                        <th width="30%">Penyakit</th>
                        <th width="40%">Gejala Terkait</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($relasi as $key => $r): 
                        $gejala_codes = explode(',', $r['gejala_codes']);
                        $gejala_names = explode('||', $r['gejala_names']);
                    ?>
                    <tr>
                        <td class="text-center text-muted small"><?= $key + 1 ?></td>
                        <td class="text-center fw-bold text-primary"><?= htmlspecialchars($r['kode_aturan']) ?></td>
                        <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1"><?= $r['kode_penyakit'] ?></span> <strong><?= htmlspecialchars($r['nama_penyakit']) ?></strong></td>
                        <td>
                            <ul class="list-unstyled mb-0 pl-0 small">
                                <?php foreach ($gejala_codes as $idx => $gc): ?>
                                <li class="mb-1 text-secondary">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle me-1"><?= $gc ?></span> <?= htmlspecialchars($gejala_names[$idx] ?? '') ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light border text-warning edit-relasi" data-kode-aturan="<?= $r['kode_aturan'] ?>" data-kode-penyakit="<?= $r['kode_penyakit'] ?>" data-nama-penyakit="<?= $r['nama_penyakit'] ?>" data-gejala-list="<?= $r['gejala_codes'] ?>"><i class="bi bi-pencil-square"></i></button>
                                <button type="button" class="btn btn-sm btn-light border text-danger delete-relasi" data-kode-aturan="<?= $r['kode_aturan'] ?>" data-kode-penyakit="<?= $r['kode_penyakit'] ?>" data-nama-penyakit="<?= $r['nama_penyakit'] ?>"><i class="bi bi-trash3"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Relasi -->
<div class="modal fade" id="tambahRelasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Aturan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahRelasi" action="../../process/relasi_process.php" method="post">
                <input type="hidden" name="action" value="create">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Aturan</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" value="<?= $next_kode_aturan ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Penyakit</label>
                        <?php if (count($penyakit) > 0): ?>
                        <select class="form-select select2-penyakit-tambah" name="kode_penyakit" required>
                            <option value="">-- Cari Penyakit --</option>
                            <?php foreach ($penyakit as $p): ?>
                            <option value="<?= $p['kode_penyakit'] ?>">[<?= $p['kode_penyakit'] ?>] <?= htmlspecialchars($p['nama_penyakit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php else: ?>
                        <div class="alert alert-warning border-0 py-2 small mb-0">Semua penyakit sudah memiliki aturan.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Cari Gejala</label>
                        <input type="text" id="searchGejalaTambah" class="form-control mb-2" placeholder="Cari kode/nama gejala...">
                        <label class="form-label fw-bold d-flex justify-content-between"><span>Pilih Gejala Terkait</span><span id="selectedCountTambah" class="text-muted small">0 terpilih</span></label>
                        <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;" id="gejalaContainerTambah">
                            <div class="row g-2">
                                <?php foreach ($gejala as $g): ?>
                                <div class="col-md-6 gejala-item">
                                    <div class="card h-100 border-0 p-2 bg-white rounded shadow-sm">
                                        <div class="form-check">
                                            <input class="form-check-input gejala-checkbox-tambah" type="checkbox" name="kode_gejala[]" value="<?= $g['kode_gejala'] ?>" id="gt_<?= $g['kode_gejala'] ?>">
                                            <label class="form-check-label small" for="gt_<?= $g['kode_gejala'] ?>"><span class="badge bg-success-subtle text-success me-1"><?= $g['kode_gejala'] ?></span> <?= htmlspecialchars($g['nama_gejala']) ?></label>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" <?= count($penyakit) == 0 ? 'disabled' : '' ?>>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Relasi -->
<div class="modal fade" id="editRelasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Aturan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditRelasi" action="../../process/relasi_process.php" method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="kode_penyakit" id="editKodePenyakit">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Aturan</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" name="kode_aturan" id="editKodeAturanDisplay" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Penyakit</label>
                        <input type="text" class="form-control bg-light fw-bold" id="editNamaPenyakitDisplay" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Cari Gejala</label>
                        <input type="text" id="searchGejalaEdit" class="form-control mb-2" placeholder="Cari kode/nama gejala...">
                        <label class="form-label fw-bold d-flex justify-content-between"><span>Pilih Gejala Terkait</span><span id="selectedCountEdit" class="text-muted small">0 terpilih</span></label>
                        <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;" id="gejalaContainerEdit">
                            <div class="row g-2">
                                <?php foreach ($gejala as $g): ?>
                                <div class="col-md-6 gejala-item">
                                    <div class="card h-100 border-0 p-2 bg-white rounded shadow-sm">
                                        <div class="form-check">
                                            <input class="form-check-input gejala-checkbox-edit" type="checkbox" name="kode_gejala[]" value="<?= $g['kode_gejala'] ?>" id="ge_<?= $g['kode_gejala'] ?>">
                                            <label class="form-check-label small" for="ge_<?= $g['kode_gejala'] ?>"><span class="badge bg-success-subtle text-success me-1"><?= $g['kode_gejala'] ?></span> <?= htmlspecialchars($g['nama_gejala']) ?></label>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="formDeleteRelasi" action="../../process/relasi_process.php" method="POST" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="deleteKodeAturanInput" name="kode_aturan">
</form>

<?php require_once '../../includes/footer.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    window.setTimeout(function() { $(".alert").slideUp(500); }, 4000);

    if ($.fn.select2) {
        $('.select2-penyakit-tambah').select2({ dropdownParent: $('#tambahRelasiModal'), theme: 'bootstrap-5', width: '100%' });
    }

    // Live search function
    function setupSearch(inputEl, containerEl) {
        $(inputEl).on('keyup', function() {
            var val = $(this).val().toLowerCase();
            $(containerEl + ' .gejala-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
            });
        });
    }
    setupSearch('#searchGejalaTambah', '#gejalaContainerTambah');
    setupSearch('#searchGejalaEdit', '#gejalaContainerEdit');

    $('.gejala-checkbox-tambah').on('change', function() {
        $('#selectedCountTambah').text($('.gejala-checkbox-tambah:checked').length + ' terpilih');
    });
    $('.gejala-checkbox-edit').on('change', function() {
        $('#selectedCountEdit').text($('.gejala-checkbox-edit:checked').length + ' terpilih');
    });

    // Form submit validation
    function validateForm(formId, checkboxClass) {
        $(formId).on('submit', function(e) {
            if ($(checkboxClass + ':checked').length === 0) {
                e.preventDefault();
                Swal.fire({ title: 'Pilih Gejala!', text: 'Pilih minimal satu gejala klinis.', icon: 'warning' });
            }
        });
    }
    validateForm('#formTambahRelasi', '.gejala-checkbox-tambah');
    validateForm('#formEditRelasi', '.gejala-checkbox-edit');

    // Bind Edit Modal data
    $(document).on('click', '.edit-relasi', function() {
        var kp = $(this).data('kode-penyakit');
        $('#editKodeAturanDisplay').val($(this).data('kode-aturan'));
        $('#editKodePenyakit').val(kp);
        $('#editNamaPenyakitDisplay').val('[' + kp + '] ' + $(this).data('nama-penyakit'));

        $('.gejala-checkbox-edit').prop('checked', false);
        ($(this).data('gejala-list') || '').toString().split(',').forEach(function(g) {
            $('#ge_' + g.trim()).prop('checked', true);
        });

        $('#selectedCountEdit').text($('.gejala-checkbox-edit:checked').length + ' terpilih');
        $('#editRelasiModal').modal('show');
    });

    // Delete relation
    $(document).on('click', '.delete-relasi', function(e) {
        e.preventDefault();
        var ka = $(this).data('kode-aturan');
        var kp = $(this).data('kode-penyakit');
        var np = $(this).data('nama-penyakit');
        Swal.fire({
            title: 'Hapus Aturan?',
            text: "Hapus aturan [" + ka + "] untuk penyakit [" + kp + "] " + np + " beserta gejalanya?",
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteKodeAturanInput').val(ka);
                $('#formDeleteRelasi').submit();
            }
        });
    });

    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({ "pageLength": 10, "language": { "search": "Cari Aturan:" } });
    }
});
</script>