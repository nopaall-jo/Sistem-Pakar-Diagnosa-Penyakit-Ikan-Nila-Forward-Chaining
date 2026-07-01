<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Diagnosa Ikan Nila</title>
    <link rel="icon" type="image/png" href="assets/img/logo3.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>

    <!-- Navbar Publik -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #002d27 !important; border-bottom: 2px solid #faae2b;">
        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $base_url ?>index.php">
                <img src="<?= $base_url ?>assets/img/Logo2.png" alt="Logo" height="30" class="rounded bg-white p-1">
                <span>Sistem Pakar Nila</span>
            </a>

            <!-- Hamburger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>index.php">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active fw-semibold" href="<?= $base_url ?>konsultasi.php">
                            <i class="fas fa-stethoscope me-1"></i> Konsultasi
                        </a>
                    </li>

                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="<?= $base_url ?>pages/auth/login.php"
                            class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="fas fa-sign-in-alt me-1"></i> Login Pakar
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>

    <!-- Awal Konten Utama -->
    <main class="py-4">