<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';

$user_id = $_SESSION['id_admin'];
$stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE id_admin = ? ");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
die('Data admin tidak ditemukan');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_admin = trim($_POST['nama_admin'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        if (!empty($password)) {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE tbl_admin SET nama_admin = ?, password = ? WHERE id_admin = ?");
            $stmt->execute([$nama_admin, $password_hash, $user_id]);
        } else {

            $stmt = $pdo->prepare("UPDATE tbl_admin SET nama_admin = ? WHERE id_admin = ?");
            $stmt->execute([$nama_admin, $user_id]);
        }

        $_SESSION['success'] = 'Profil berhasil diperbarui';
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal memperbarui profil: ' . $e->getMessage();
    }
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Profile Saya</h6>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_admin" value="<?= htmlspecialchars($user['nama_admin']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="created_at" class="form-label">Tanggal Registrasi</label>
                        <input type="text" class="form-control" id="created_at" value="<?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>" readonly>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>