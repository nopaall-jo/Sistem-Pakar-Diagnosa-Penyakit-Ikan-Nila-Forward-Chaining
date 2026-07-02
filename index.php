<?php
require_once 'config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['id_admin'])) {
    header("Location: " . $base_url . "pages/admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Ikan Nila Dzawil Farm</title>
    <link rel="icon" type="image/png" href="assets/img/logo3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafd;
        }

        .hero-bg {
            background: linear-gradient(135deg, #00473e 0%, #002d27 100%);
            color: #ffffff;
            padding: 8rem 0 6rem;
        }
    </style>
</head>

<body>

    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm" style="background-color: #002d27 !important; border-bottom: 2px solid #faae2b;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="assets/img/Logo2.png" alt="Logo" height="35" class="rounded">
                <span class="fw-bold">Ikan Nila Dzawil Farm Pakar</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link text-white" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#flow">Cara Kerja</a></li>
                    <li class="nav-item ms-lg-3 my-2 my-lg-0">
                        <a href="konsultasi.php" class="btn btn-warning px-4 fw-bold rounded-pill text-dark">
                            <i class="bi bi-heart-pulse me-1"></i> Cek Kesehatan Ikan
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a href="pages/auth/login.php" class="btn btn-outline-light px-4 rounded-pill">
                            <i class="bi bi-shield-lock"></i> Login Pakar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-bg text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-lg-7">
                    <img src="assets/img/Logo2.png" alt="Logo Dzawil Farm" height="85" class="mb-3 rounded shadow" style="background-color: white; padding: 5px;">
                    <h1 class="display-4 fw-bold mb-3">Diagnosa Penyakit Ikan Nila Secara Cepat & Akurat</h1>
                    <p class="lead opacity-75 mb-4">
                        Sistem pakar berbasis web dengan metode <strong>Forward Chaining</strong> untuk mengidentifikasi jenis penyakit pada ikan nila berdasarkan gejala klinis yang dialami. Membantu peternak mengambil tindakan pencegahan secara cepat.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                        <a href="konsultasi.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold text-dark rounded-pill shadow">
                            <i class="bi bi-heart-pulse"></i> Cek Kesehatan Sekarang
                        </a>
                        <a href="#about" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill">
                            Pelajari Sistem
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0 d-none d-lg-block">
                    <img src="assets/img/dokterhewan.png" alt="Pakar Hewan" class="img-fluid" style="max-height: 290px; filter: drop-shadow(0px 8px 16px rgba(0,0,0,0.15));">
                </div>
            </div>
        </div>
    </header>

    <!-- Tentang / Deskripsi -->
    <section id="about" class="py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-4 text-center">
                    <img src="assets/img/nila.png" alt="Budidaya Ikan Nila" class="img-fluid rounded-4 shadow-sm border border-light" style="max-height: 220px; width: 100%; object-fit: cover;">
                </div>
                <div class="col-lg-4">
                    <h3 class="fw-bold mb-3 text-dark border-bottom border-primary pb-2 d-inline-block">Metode Forward Chaining</h3>
                    <p class="text-secondary small mb-0" style="text-align: justify;">
                        Sistem pakar ini menggunakan teknik penalaran <strong>Forward Chaining</strong> (pelacakan ke depan). Proses dimulai dari pengumpulan fakta berupa gejala-gejala klinis yang diamati pada kolam atau tubuh ikan nila, untuk kemudian disesuaikan dengan aturan (rule) inferensi hingga mencapai kesimpulan berupa diagnosis penyakit beserta saran pengobatannya.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-check2-circle text-success me-2"></i>Keunggulan Sistem</h5>
                        <ul class="list-unstyled mb-0 text-secondary small">
                            <li class="mb-2"><i class="bi bi-dot text-primary fs-5 align-middle"></i> Kemudahan akses diagnosis 24 jam.</li>
                            <li class="mb-2"><i class="bi bi-dot text-primary fs-5 align-middle"></i> Basis aturan terverifikasi dari pakar perikanan.</li>
                            <li class="mb-2"><i class="bi bi-dot text-primary fs-5 align-middle"></i> Saran penanganan dan pencegahan instan.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Dokumentasi Kegiatan Budidaya -->
            <div class="mt-5 border-top pt-4">
                <h5 class="fw-bold mb-4 text-center text-dark"><i class="bi bi-images text-primary me-2"></i>Dokumentasi Budidaya Dzawil Farm</h5>
                <div class="row g-3 justify-content-center">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm overflow-hidden rounded-3">
                            <img src="assets/img/foto1.png" alt="Dokumentasi 1" class="img-fluid" style="height: 140px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm overflow-hidden rounded-3">
                            <img src="assets/img/foto2.png" alt="Dokumentasi 2" class="img-fluid" style="height: 140px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm overflow-hidden rounded-3">
                            <img src="assets/img/foto3.png" alt="Dokumentasi 3" class="img-fluid" style="height: 140px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm overflow-hidden rounded-3">
                            <img src="assets/img/foto4.png" alt="Dokumentasi 4" class="img-fluid" style="height: 140px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Kerja -->
    <section id="flow" class="bg-light py-5">
        <div class="container text-center py-4">
            <h2 class="fw-bold mb-5">3 Langkah Mudah Diagnosa</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <i class="bi bi-1-circle text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">Pilih Gejala</h5>
                    <p class="text-secondary small px-3">Centang gejala fisik atau perilaku abnormal yang tampak pada ikan nila Anda.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-2-circle text-warning fs-1 mb-3"></i>
                    <h5 class="fw-bold">Proses Inferensi</h5>
                    <p class="text-secondary small px-3">Mesin inferensi akan mencocokkan fakta gejala dengan aturan penyakit di basis pengetahuan.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-3-circle text-success fs-1 mb-3"></i>
                    <h5 class="fw-bold">Hasil & Solusi</h5>
                    <p class="text-secondary small px-3">Dapatkan hasil diagnosa penyakit, persentase keyakinan, serta saran solusi pengobatan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 text-center text-white" style="background-color: #002d27;">
        <div class="container">
            <p class="mb-0 opacity-75 small">Sistem Pakar Diagnosa Penyakit Ikan Nila | Skripsi Teknik Informatika | Naufal Rafif (202243501684) &copy; <?= date('Y') ?> Dzawil Farm - Bojonggede, Bogor</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>