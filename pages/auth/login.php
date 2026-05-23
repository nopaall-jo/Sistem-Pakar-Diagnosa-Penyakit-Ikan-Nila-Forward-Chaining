<?php
session_start();
require_once '../../config/database.php';

// Jika sudah login, langsung lempar ke dashboard admin
if (isset($_SESSION['id_admin'])) {
    header("Location: " . $base_url . "pages/admin/dashboard.php");
    exit();
}

$error = ''; // Inisialisasi variabel error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Sesuaikan tabel ke tbl_admin
    $stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // 2. Verifikasi Password
    if ($admin && password_verify($password, $admin['password'])) {
        // 3. Set Session khusus Admin
        $_SESSION['id_admin']   = $admin['id_admin'];
        $_SESSION['username']   = $admin['username'];
        $_SESSION['nama_admin'] = $admin['nama_admin'];
        
        // Langsung redirect ke dashboard admin (karena aktor cuma 1)
        header("Location: " . $base_url . "pages/admin/dashboard.php");
        exit();
    } else {
        $error = "Username atau password Admin salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pakar Ikan Nila</title>
    <link rel="icon" type="image/png" href="../../assets/img/logo3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    :root {
        /* ELEMENTS */
        --bg-main: #fef6e4;
        --headline: #001858;
        --paragraph: #172c66;
        --button: #f582ae;
        --button-text: #001858;

        /* ILLUSTRATION */
        --stroke: #001858;
        --main: #f3d2c1;
        --highlight: #fef6e4;
        --secondary: #8bd3dd;
        --tertiary: #f582ae;

        --white: #ffffff;
    }

    /* BODY */
    body {
        background: var(--bg-main);
        font-family: 'Nunito', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        color: var(--paragraph);
    }

    /* CONTAINER */
    .auth-container {
        width: 100%;
        max-width: 400px;
        padding: 15px;
    }

    /* CARD */
    .auth-card {
        border: none;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 24, 88, 0.15);
        overflow: hidden;
        background: var(--white);
        transition: transform 0.3s ease;
    }

    /* HEADER */
    .auth-header {
        background: var(--secondary);
        color: var(--headline);
        padding: 2rem 1.5rem;
        text-align: center;
    }

    /* LOGO */
    .logo-icon-wrapper {
        width: 60px;
        height: 60px;
        background: var(--highlight);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2.5rem;
        color: var(--stroke);
        transition: all 0.4s ease;
    }

    .auth-card:hover .logo-icon-wrapper {
        transform: rotate(10deg) scale(1.1);
        background: var(--white);
        color: var(--tertiary);
    }

    /* BODY FORM */
    .auth-body {
        padding: 3.5rem 2rem;
    }

    /* LABEL */
    .form-label {
        font-weight: 700;
        color: var(--headline);
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }

    /* INPUT GROUP */
    .input-group {
        background: var(--highlight);
        border: 2px solid var(--main);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .input-group:focus-within {
        border-color: var(--tertiary);
        background: var(--white);
        box-shadow: 0 0 0 4px rgba(245,130,174,0.15);
    }

    .input-group-text {
        border: none;
        color: var(--paragraph);
        background: transparent;
    }

    /* INPUT */
    .form-control {
        border: none;
        background: transparent;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--headline);
    }

    .form-control:focus {
        box-shadow: none;
        background: transparent;
    }

    /* BUTTON */
    .btn-primary {
        background: var(--button);
        border: none;
        border-radius: 14px;
        padding: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
        color: var(--button-text);
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: var(--headline);
        color: var(--white);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,24,88,0.25);
    }

    /* REGISTER LINK */
    .register-link {
        color: var(--tertiary);
        font-weight: 800;
        text-decoration: none;
        transition: 0.3s;
        font-size: 0.85rem;
    }

    .register-link:hover {
        color: var(--headline);
        text-decoration: underline;
    }

    /* ANIMATION */
    .animate-in {
        animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="auth-card animate-in">
                    
                    <div class="auth-header text-center">
                        <div class="logo-icon-wrapper">
                            <i class="fa-solid fa-fish-fins"></i>
                        </div>
                        <h3 class="fw-bold mb-1">SISTEM PAKAR</h3>
                        <p class="mb-0 opacity-75">Diagnosis Kesehatan Ikan Nila</p>
                    </div>
                    
                    <div class="auth-body">
                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label class="form-label">Username Admin</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent pe-0">
                                        <i class="bi bi-person-circle"></i>
                                    </span>
                                    <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent pe-0">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                                    <button class="btn btn-link text-muted pe-3" type="button" id="togglePassword">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Masuk ke Panel <i class="bi bi-arrow-right-short"></i>
                                </button>
                            </div>
                            
                            <div class="text-center mt-5">
                                <p class="small text-muted mb-0">Belum memiliki akun akses?</p>
                                <a href="<?= $base_url ?>pages/auth/register.php" class="register-link">Hubungi Super Admin / Daftar</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 animate-in" style="animation-delay: 0.2s;">
                    <a href="<?= $base_url ?>" class="text-black text-decoration-none opacity-75 small">
                        <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            // Toggle tipe input
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye-fill');
            icon.classList.toggle('bi-eye-slash-fill');
            
            passwordInput.focus();
        });
    }

    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    const btn = form.querySelector('button[type="submit"]');
                    btn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Menghubungkan...
                    `;
                    btn.classList.add('disabled');
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>