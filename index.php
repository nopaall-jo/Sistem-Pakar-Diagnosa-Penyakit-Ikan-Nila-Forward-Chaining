<?php
require_once 'config/database.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: " . $base_url . "pages/admin/dashboard.php");
    } else {
        header("Location: " . $base_url . "pages/user/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Ikan Nila | Diagnosa Penyakit Metode Forward Chaining</title>
    <link rel="icon" type="image/png" href="assets/img/logo3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* PRIMARY → headline */
            --primary: #00473e;
            --primary-dark: #00332c;
            --primary-soft: #e0f2ef;

            /* TERTIARY / ACCENT → button */
            --tertiary: #faae2b;
            --tertiary-dark: #e0991f;
            --tertiary-soft: #fff3d6;

            /* NEUTRAL */
            --secondary: #00473e;
            --gray: #475d5b;      
            --light: #f2f7f5;      
            --border: #dfeeea;
            --white: #ffffff;

            /* STATUS (opsional biar masih konsisten) */
            --success: #16a34a;
            --danger: #dc2626;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light);
            color: var(--secondary);
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }

        /* ================= HERO ================= */
        .hero-section {
            background: linear-gradient( 135deg, rgba(0,71,62,0.92), rgba(255,168,186,0.50) ), url('assets/img/lungs-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--white);
            padding: 9rem 0 7rem;
            text-align: center;
            position: relative;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 100px;
            background: var(--light);
            transform: skewY(-2deg);
        }

        /* ================= SECTION TITLE ================= */
        .section-title {
            position: relative;
            display: inline-block;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 3px;
            background: var(--tertiary);
            border-radius: 3px;
        }

        /* ================= SCROLL OFFSET ================= */
        html {
            scroll-behavior: smooth;
        }

        section {
            padding-top: 80px;
            padding-bottom: 80px;
        }

        section[id] {
            scroll-margin-top: 90px;
        }

        /* ================= CARD BASE ================= */
        .feature-card,
        .testimonial-card,
        .stats-item {
            background: var(--white);
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .feature-card:hover,
        .testimonial-card:hover,
        .stats-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(0,0,0,0.08);
        }

        /* ================= FEATURE ================= */
        .feature-card {
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 4px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .feature-card:hover::before {
            width: 8px;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
            background: var(--primary-soft);
            width: 75px;
            height: 75px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        /* ================= BUTTON ================= */
        .btn-custom {
            padding: 0.75rem 2.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.25s ease;
        }

        .btn-primary-custom {
            background: var(--tertiary);
            border: var(--tertiary);
            color: var(--primary);
        }

        .btn-primary-custom:hover {
            background: var(--tertiary-dark);
            box-shadow: 0 8px 20px rgba(250,174,43,0.25);
        }

        .btn-tertiary {
            background: var(--tertiary);
            border: var(--tertiary);
            color: var(--white);
        }

        .btn-tertiary:hover {
            background: var(--tertiary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245,158,11,0.25);
        }


        /* ================= TESTIMONIAL ================= */
        .testimonial-card {
            padding: 2rem;
            position: relative;
        }

        .testimonial-card::before {
            content: '\201C';
            font-family: Georgia, serif;
            font-size: 4rem;
            color: var(--tertiary-soft);
            position: absolute;
            top: 15px;
            left: 20px;
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 3px solid var(--primary);
        }

        /* ================= STATS ================= */
        .stats-item {
            padding: 2rem;
            text-align: center;
        }

        .stats-number {
            font-size: 2.3rem;
            font-weight: 700;
            color: var(--tertiary);
        }

        .stats-label {
            color: var(--gray);
            font-weight: 500;
        }

        /* ================= NAV ================= */
        .nav-pills .nav-link.active {
            background: var(--light);
        }

        .nav-pills .nav-link {
            color: var(--gray);
        }

        /* ================= NAVBAR MENU ================= */
        .navbar .nav-link {
            color: var(--white);
            font-weight: 500;
            position: relative;
            transition: 0.3s;
        }

        .navbar .nav-link:hover {
            color: var(--tertiary);
        }

        .navbar .nav-link.active {
            color: var(--tertiary) !important;
        }

        .navbar .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 0%;
            height: 2px;
            background: var(--tertiary);
            transition: 0.3s;
        }

        .navbar .nav-link:hover::after,
        .navbar .nav-link.active::after {
            width: 100%;
        }

        /* ================= FOOTER ================= */
        footer {
            background: #ffa8ba  ;
            color: var(--primary);
            padding: 4rem 0 2rem;
            position: relative;
        }

        footer::before {
            content: '';
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            height: 100px;
            background: var(--light);
            transform: skewY(-2deg);
        }

        .footer-links a {
            color: rgba(0, 71, 62,0.7);
            text-decoration: none;
            transition: color 0.25s ease;
        }

        .footer-links a:hover {
            color: var(--border);
        }

        .social-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: var(--tertiary);
            transform: translateY(-3px);
        }
    </style>
</head>

<body> 
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: rgba(0, 71, 62, 0.9); backdrop-filter: blur(12px); border-bottom: 1px solid #faae2b;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-fish-fins" style="color: #ffa8ba; font-size: 1.5rem;"></i>
                <span class="fw-bold fs-4" style="color: var(--white);">Sistem Pakar Ikan Nila<span style="color: var(--tertiary);">+</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#diagnosis">Diagnosis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>konsultasi.php">Konsultasi</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="<?= $base_url ?>pages/auth/login.php" class="btn btn-primary-custom btn-custom">Masuk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-lg-start text-center mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">
                        Sistem Pakar Diagnosa Penyakit Ikan Nila
                    </h1>

                    <p class="lead mb-4 fw-normal text-white-50 mx-auto animate__animated animate__fadeInUp animate__delay-1s" style="max-width: 700px;">
                        Aplikasi ini membantu deteksi dini penyakit pada ikan nila berdasarkan gejala yang dipilih menggunakan metode  <strong>Forward Chaining</strong>. sehingga mendukung proses identifikasi penyakit secara sistematis bagi peternak di wilayah Bojong Gede dan sekitarnya.
                    </p>

                    <div class="animate__animated animate__fadeInUp animate__delay-2s">
                        <a href="<?= $base_url ?>pages/auth/login.php"
                        class="btn btn-warning btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm text-dark pulse-animation">
                        <i class="fas fa-stethoscope me-2"></i> Mulai Diagnosa
                    </a>

                        <a href="#features" 
                        class="btn btn-outline-custom btn-custom">
                        Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 animate__animated animate__fadeIn animate__delay-1s">
                    <img src="assets/img/dokterhewan.png" 
                        alt="Ilustrasi Ikan Nila" 
                        class="img-fluid floating-animation" 
                        style="max-height: 500px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5" style="background-color: var(--white); position: relative; z-index: 2;">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">20+</div>
                        <div class="stats-label">Data Gejala</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">8+</div>
                        <div class="stats-label">Data Penyakit</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">Forward</div>
                        <div class="stats-label">Metode Chaining</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">Admin</div>
                        <div class="stats-label">Satu Aktor Sistem</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title">Keunggulan Sistem</h2>
                <p class="text-muted">
                    Sistem pakar ini dirancang berbasis web untuk diagnosa penyakit ikan nila 
                    menggunakan metode Forward Chaining yang terstruktur dan sistematis.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card rounded-4 h-100 bg-white text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:60px;height:60px;">
                                <i class="fa-solid fa-user-doctor fs-4"></i>
                            </span>
                        </div>
                        <h5 class="fw-semibold">Identifikasi Penyakit</h5>
                        <p class="text-muted small">Proses diagnosa dilakukan berdasarkan gejala yang dipilih, yang selanjutnya diproses dengan metode Forward Chaining untuk menghasilkan kemungkinan penyakit berdasarkan aturan IF-THEN.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card rounded-4 h-100 bg-white text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white" style="width:60px;height:60px;">
                                <i class="bi-list-check fs-4"></i>
                            </span>
                        </div>
                        <h5 class="fw-semibold">Manajemen Basis Pengetahuan</h5>
                        <p class="text-muted small">Admin dapat mengelola data gejala, penyakit, dan solusi yang tersimpan dalam database melalui proses tambah, ubah, dan hapus untuk mendukung diagnosa sistem.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card rounded-4 h-100 bg-white text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white" style="width:60px;height:60px;">
                                <i class="bi bi-diagram-3 fs-4"></i>
                            </span>
                        </div>
                        <h5 class="fw-semibold">Manajemen Basis Aturan</h5>
                        <p class="text-muted small">
                            Admin mengelola relasi antara gejala dan penyakit dalam bentuk aturan IF-THEN yang digunakan oleh mesin inferensi berbasis Forward Chaining untuk menghasilkan kesimpulan.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card rounded-4 h-100 bg-white text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white" style="width:60px;height:60px;">
                                <i class="bi bi-clipboard-check fs-4"></i>
                            </span>
                        </div>
                        <h5 class="fw-semibold">Rekomendasi Penanganan</h5>
                        <p class="text-muted small">
                            Sistem memberikan solusi penanganan berdasarkan 
                            hasil diagnosa yang diperoleh.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card rounded-4 h-100 bg-white text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info text-white" style="width:60px;height:60px;">
                                <i class="bi bi-clock-history fs-4"></i>
                            </span>
                        </div>
                        <h5 class="fw-semibold">Riwayat Diagnosa</h5>
                        <p class="text-muted small">
                            Setiap hasil diagnosa tersimpan sebagai dokumentasi 
                            dan bahan evaluasi.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card rounded-4 h-100 bg-white text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white" style="width:60px;height:60px;">
                                <i class="bi bi-printer fs-4"></i>
                            </span>
                        </div>
                        <h5 class="fw-semibold">Laporan Cetak</h5>
                        <p class="text-muted small">
                            Hasil diagnosa dapat dicetak sebagai laporan resmi 
                            untuk kebutuhan dokumentasi.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-6">
                    <div class="pe-lg-4">
                        <h2 class="fw-bold section-title mb-4">Tentang Sistem Pakar</h2>

                        <p class="text-muted">
                            Sistem Pakar Diagnosa Penyakit Ikan Nila merupakan aplikasi berbasis web 
                            yang dirancang untuk membantu proses identifikasi penyakit secara terstruktur 
                            melalui pendekatan berbasis aturan.
                        </p>

                        <p class="text-muted">
                            Sistem menggunakan metode <strong>Forward Chaining</strong> sebagai mesin inferensi 
                            untuk menganalisis gejala yang dipilih dan menghasilkan kesimpulan berupa 
                            jenis penyakit beserta rekomendasi penanganan.
                        </p>

                        <div class="row mt-4">
                            <div class="col-6">
                                <h5 class="fw-semibold">✔ Berbasis Aturan</h5>
                                <small class="text-muted">Menggunakan relasi IF-THEN</small>
                            </div>
                            <div class="col-6">
                                <h5 class="fw-semibold">✔ Database Terintegrasi</h5>
                                <small class="text-muted">Gejala & penyakit terstruktur</small>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="<?= $base_url ?>pages/auth/login.php"
                            class="btn btn-primary-custom px-4 me-2">
                            Mulai Diagnosa
                            </a>
                            <a href="#features"
                            class="btn btn-outline-primary px-4">
                            Lihat Fitur
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="assets/img/doktor.png"
                        alt="Sistem Pakar Diagnosa Ikan Nila"
                        class="img-fluid rounded-4 shadow-sm">
                </div>
            </div>
        </div>
    </section>

    <!-- Diagnosis Process Section -->
    <section id="diagnosis" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-success fw-bold text-uppercase">Panduan Diagnosa</h6>
                <h2 class="fw-bold section-title">3 Langkah Diagnosa Penyakit Ikan Nila</h2>
                <p class="text-muted">
                    Proses diagnosa dilakukan dengan memilih gejala yang dialami ikan nila.
                    Sistem kemudian menganalisis data menggunakan metode 
                    <strong>Forward Chaining</strong> untuk menghasilkan kemungkinan
                    penyakit beserta rekomendasi penanganannya.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- STEP 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card bg-white text-center p-4 rounded-4 shadow-sm h-100">                
                        <div class="mb-3">
                            <span class="badge bg-primary rounded-circle"
                            style="width:60px;height:60px;line-height:50px;font-size:1.2rem;">
                            1
                            </span>
                        </div>
                        <h5 class="fw-bold mb-3">Isi Data Peternak</h5>
                        <p class="text-muted small mb-0">
                            Peternak memasukkan nama sebagai identitas konsultasi.
                            Data ini digunakan untuk mencatat riwayat diagnosa
                            yang tersimpan dalam sistem.
                        </p>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card bg-white text-center p-4 rounded-4 shadow-sm h-100">
                        <div class="mb-3">
                            <span class="badge bg-warning rounded-circle text-dark"
                            style="width:60px;height:60px;line-height:50px;font-size:1.2rem;">
                            2
                            </span>
                        </div>
                        <h5 class="fw-bold mb-3">Pilih Gejala Ikan</h5>
                        <p class="text-muted small mb-0">
                            Pengguna mencentang gejala yang muncul pada ikan nila.
                            Gejala yang dipilih akan menjadi fakta awal
                            dalam proses analisis sistem pakar.
                        </p>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card bg-white text-center p-4 rounded-4 shadow-sm h-100">
                        <div class="mb-3">
                            <span class="badge bg-success rounded-circle"
                            style="width:60px;height:60px;line-height:50px;font-size:1.2rem;">
                            3
                            </span>
                        </div>
                        <h5 class="fw-bold mb-3">Hasil Diagnosa</h5>
                        <p class="text-muted small mb-0">
                            Sistem akan mencocokkan gejala dengan aturan IF–THEN
                            menggunakan metode Forward Chaining untuk menghasilkan
                            kemungkinan penyakit dan solusi penanganan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="mt-5">
                <div class="bg-white rounded-4 p-5 text-center shadow-sm border">

                    <h3 class="fw-bold mb-3">
                        Ikan Nila Anda Menunjukkan Gejala Penyakit?
                    </h3>

                    <p class="text-muted mb-4">
                        Lakukan diagnosa sekarang untuk mengetahui kemungkinan
                        penyakit dan rekomendasi penanganannya.
                    </p>

                    <a href="<?= $base_url ?>konsultasi.php"
                    class="btn btn-success btn-lg rounded-pill px-5 shadow">
                    <i class="fas fa-stethoscope me-2"></i>
                    Mulai Konsultasi
                    </a>

                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title fw-semibold">Apa Kata Pengguna?</h2>
                <p class="section-subtitle mx-auto">
                    Testimoni dari pembudidaya dan praktisi perikanan yang telah menggunakan 
                    Sistem Pakar Diagnosa Penyakit Ikan Nila
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content text-center">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" 
                                alt="Abiyu Ramzi" 
                                class="testimonial-img mb-3">
                            <h5 class="mb-1">Abiyu Ramzi</h5>
                            <span class="testimonial-role">Pembudidaya Ikan Nila</span>
                            <p class="testimonial-text mt-3">
                                Sistem ini membantu saya mengidentifikasi gejala penyakit lebih cepat. 
                                Saya bisa langsung melakukan penanganan sebelum kondisi ikan semakin parah.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content text-center">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" 
                                alt="Salwa Aliya" 
                                class="testimonial-img mb-3">
                            <h5 class="mb-1">Salwa Aliya</h5>
                            <span class="testimonial-role">Penyuluh Perikanan</span>
                            <p class="testimonial-text mt-3">
                                Fitur diagnosa berbasis gejala memudahkan proses edukasi kepada petani ikan. 
                                Informasi yang ditampilkan jelas dan sistematis.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content text-center">
                            <img src="https://randomuser.me/api/portraits/men/75.jpg" 
                                alt="Arief Muhammad" 
                                class="testimonial-img mb-3">
                            <h5 class="mb-1">Arief Muhammad</h5>
                            <span class="testimonial-role">Teknisi Budidaya Perikanan</span>
                            <p class="testimonial-text mt-3">
                                Sistem pakar ini membantu dalam analisis awal penyakit ikan nila. 
                                Rekomendasi penanganan yang diberikan sangat membantu di lapangan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title">Pertanyaan Umum</h2>
                <p class="text-muted">Informasi seputar penggunaan Sistem Pakar Diagnosa Penyakit Ikan Nila</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">

                        <div class="accordion-item mb-3 border-0 shadow-sm">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    Apakah hasil diagnosa dapat menggantikan peran ahli perikanan?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Tidak. Sistem pakar ini berfungsi sebagai alat bantu dalam proses identifikasi awal penyakit ikan nila berdasarkan gejala yang dipilih. Keputusan akhir dan tindakan lanjutan tetap disarankan melalui konsultasi dengan penyuluh atau ahli perikanan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Bagaimana cara kerja sistem pakar ini?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sistem menggunakan metode forward chaining. Pengguna memilih gejala yang muncul pada ikan nila. Sistem kemudian mencocokkan gejala tersebut dengan basis pengetahuan yang tersimpan dalam database untuk menghasilkan kemungkinan jenis penyakit dan rekomendasi penanganan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    Siapa yang dapat menggunakan sistem ini?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sistem ini dapat digunakan oleh pembudidaya ikan nila, teknisi budidaya, mahasiswa perikanan, maupun penyuluh lapangan yang membutuhkan identifikasi penyakit secara cepat dan terstruktur.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                    Apakah sistem ini memerlukan koneksi internet?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sistem berbasis web sehingga memerlukan koneksi internet saat diakses melalui browser. Namun sistem dapat dijalankan secara lokal menggunakan server seperti XAMPP untuk kebutuhan pengembangan dan pengujian.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container pt-5">
            <div class="row g-4">

                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                        <i class="fa-solid fa-fish" style="color: var(--primary); font-size: 1.5rem;"></i>
                        <span>Sistem Pakar <span style="color: var(--primary);">Ikan Nila</span></span>
                    </h5>
                    <p>
                        Sistem pakar berbasis web untuk mendiagnosa penyakit ikan nila 
                        menggunakan metode forward chaining guna membantu proses identifikasi 
                        penyakit secara cepat dan terstruktur.
                    </p>
                    <div class="mt-4">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h5 class="fw-bold mb-4">Tautan</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="#home">Beranda</a></li>
                        <li class="mb-2"><a href="#features">Fitur</a></li>
                        <li class="mb-2"><a href="#about">Tentang Sistem</a></li>
                        <li class="mb-2"><a href="#diagnosis">Diagnosa</a></li>
                        <li class="mb-2"><a href="#faq">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5 class="fw-bold mb-4">Fitur Sistem</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="#">Diagnosa Berbasis Gejala</a></li>
                        <li class="mb-2"><a href="#">Basis Pengetahuan Penyakit</a></li>
                        <li class="mb-2"><a href="#">Manajemen Data Gejala</a></li>
                        <li class="mb-2"><a href="#">Manajemen Data Penyakit</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5 class="fw-bold mb-4">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="bi bi-envelope me-2"></i> admin@sistempakarikan.com</li>
                        <li class="mb-3"><i class="bi bi-telephone me-2"></i> +62 812 3456 7890</li>
                        <li class="mb-3"><i class="bi bi-geo-alt me-2"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>

            </div>

            <hr class="my-4" style="border-color: rgba(250, 174, 43, 0.5);">

            <div class="text-center pt-3">
                <p class="mb-0">
                    &copy; <?= date('Y') ?> Sistem Pakar Diagnosis Penyakit Ikan Nila | Skripsi Teknik Informatika | Naufal Rafif (202243501684)
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        /* 1. Smooth Scrolling untuk Navigasi Anchor */
        const anchors = document.querySelectorAll('a[href^="#"]');
        anchors.forEach(anchor => {
            anchor.addEventListener("click", function (e) {
                const targetID = this.getAttribute("href");
                if (targetID.length > 1) {
                    const targetElement = document.querySelector(targetID);
                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({
                            behavior: "smooth",
                            block: "start"
                        });
                    }
                }
            });
        });

        /* 2. Perubahan Background Navbar Saat Scroll */
        const navbar = document.querySelector(".navbar");
        window.addEventListener("scroll", function () {
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = "var(--primary)";
                navbar.style.boxShadow = "0 4px 12px rgba(0,0,0,0.1)";
            } else {
                navbar.style.backgroundColor = "rgba(44, 62, 80, 0.9)";
                navbar.style.boxShadow = "none";
            }
        });

        /* 3. Animasi Elemen Saat Muncul di Layar */
        const animatedElements = document.querySelectorAll(
            ".feature-card, .stats-item, .testimonial-card"
        );
        function animateOnScroll() {
            const triggerPoint = window.innerHeight * 0.85;
            animatedElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                if (elementTop < triggerPoint) {
                    element.classList.add(
                        "animate__animated",
                        "animate__fadeInUp"
                    );
                }
            });
        }

        window.addEventListener("scroll", animateOnScroll);
        window.addEventListener("load", animateOnScroll);
    });
    </script>
</body>
</html>