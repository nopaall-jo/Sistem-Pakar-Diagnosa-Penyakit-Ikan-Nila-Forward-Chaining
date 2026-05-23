<?php
session_start();
require_once '../../config/database.php';

// Jika sudah login, tidak perlu daftar lagi
if (isset($_SESSION['id_admin'])) {
    header("Location: " . $base_url . "pages/admin/dashboard.php");
    exit();
}

// Query untuk mengambil ID terakhir
$queryId = $pdo->query("SELECT MAX(id_admin) AS last_id FROM tbl_admin");
$rowId = $queryId->fetch();
$next_id = ($rowId['last_id'] ?? 0) + 1; // Jika kosong, mulai dari 1

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username   = $_POST['username'];
    // Enkripsi password agar aman di database
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_admin = $_POST['nama_admin'];

    try {
        // Sesuaikan dengan tabel tbl_admin milikmu
        $stmt = $pdo->prepare("INSERT INTO tbl_admin (username, password, nama_admin) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $nama_admin]);
        
        // Redirect ke login dengan pesan sukses
        header("Location: " . $base_url . "pages/auth/login.php?register=success");
        exit();
    } catch (PDOException $e) {
        // Jika username sudah ada (duplicate entry) atau error lainnya
        $error = "Registrasi gagal: Username mungkin sudah digunakan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Admin - Ikan Nila</title>
    <link rel="icon" type="image/png" href="../../assets/img/logo3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    :root {
    /* ELEMENTS */
    --bg-main: #fec7d7;
    --headline: #0e172c;
    --paragraph: #0e172c;
    --button: #0e172c;
    --button-text: #fffffe;

    /* ILLUSTRATION */
    --stroke: #0e172c;
    --main: #f9f8fc;
    --highlight: #fec7d7;
    --secondary: #d9d4e7;
    --tertiary: #a786df;

    --white: #ffffff;
}

body {
    background: var(--bg-main);
    font-family: 'Nunito', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 20px;
    color: var(--paragraph);
}

/* CONTAINER */
.auth-container {
    width: 100%;
    max-width: 420px;
    animation: fadeInUp 0.6s ease-out;
}

/* CARD */
.auth-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(14, 23, 44, 0.25);
    overflow: hidden;
    background: var(--main);
}

/* HEADER */
.auth-header {
    background: var(--tertiary);
    color: var(--main);
    padding: 1.25rem 1rem;
    text-align: center;
}

/* LOGO */
.logo-box {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.4);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.8rem;
    color: var(--main);
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.auth-card:hover .logo-box {
    transform: scale(1.1) rotate(5deg);
    background: var(--white);
    color: var(--tertiary);
}

.auth-header h3 {
    font-size: 1.3rem;
    font-weight: 800;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* BODY */
.auth-body {
    padding: 2rem;
}

/* LABEL */
.form-label {
    font-weight: 700;
    color: var(--headline);
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
}

/* INPUT */
.input-group {
    background: var(--main);
    border: 1.5px solid var(--secondary);
    border-radius: 10px;
    overflow: hidden;
    transition: 0.3s;
}

.input-group:focus-within {
    border-color: var(--tertiary);
    box-shadow: 0 0 0 4px rgba(167,134,223,0.2);
    background: var(--white);
}

.input-group-text {
    border: none;
    background: transparent;
    color: var(--paragraph);
    padding-right: 0;
}

.form-control {
    border: none;
    background: transparent;
    padding: 10px 15px;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--headline);
}

.form-control:focus {
    box-shadow: none;
    background: transparent;
}

/* READONLY */
.form-control[readonly] {
    color: var(--stroke);
    opacity: 0.8;
}

/* BUTTON */
.btn-primary {
    background: var(--button);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 800;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--button-text);
    transition: 0.3s;
}

.btn-primary:hover {
    background: var(--tertiary);
    color: var(--headline);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(167,134,223,0.35);
}

/* FOOTER LINK */
.auth-footer a {
    color: var(--tertiary);
    font-weight: 700;
    text-decoration: none;
}

.auth-footer a:hover {
    color: var(--headline);
    text-decoration: underline;
}

/* ANIMATION */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body class="bg-main;">
    <div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-box">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h3>Pendaftaran Admin</h3>
            <p class="small opacity-75 mb-0">Tambahkan pengelola sistem baru</p>
        </div>

        <div class="auth-body">
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger py-2 small rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <form method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label">ID Admin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                        <input type="text" class="form-control" name="id_admin" value="<?= $next_id ?>" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" class="form-control" name="nama_admin" placeholder="Nama admin" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username login" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" minlength="8" placeholder="Minimal 8 karakter" required>
                        <button class="btn btn-link text-muted" type="button" id="togglePassword">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Daftar Admin Baru</button>
                </div>

                <div class="auth-footer text-center mt-4">
                    <p class="small text-muted mb-0">Sudah punya akses? <a href="<?= $base_url ?>pages/auth/login.php">Login di sini</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye-slash-fill');
            this.querySelector('i').classList.toggle('bi-eye-fill');
        });
        
        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>