-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Apr 2026 pada 06.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ikan_nila`
--
CREATE DATABASE IF NOT EXISTS `db_ikan_nila` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_ikan_nila`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `username`, `password`, `nama_admin`) VALUES
(1, 'admin', '$2y$10$bVsvqz8.0cy2QMGJL.Q2jekjf9nAp94uqA4XhUN7ICgXKmBSZ1Gtq', 'Aditya Rahman');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_aturan`
--

CREATE TABLE `tbl_aturan` (
  `id_aturan` int(11) NOT NULL,
  `kode_penyakit` varchar(10) NOT NULL,
  `kode_gejala` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_aturan`
--

INSERT INTO `tbl_aturan` (`id_aturan`, `kode_penyakit`, `kode_gejala`) VALUES
(13, 'P01', 'G01'),
(14, 'P01', 'G04'),
(15, 'P01', 'G20'),
(16, 'P01', 'G22'),
(17, 'P02', 'G02'),
(18, 'P02', 'G05'),
(19, 'P02', 'G08'),
(20, 'P02', 'G21'),
(21, 'P02', 'G23'),
(22, 'P03', 'G02'),
(23, 'P03', 'G03'),
(24, 'P03', 'G04'),
(25, 'P03', 'G22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_diagnosa`
--

CREATE TABLE `tbl_diagnosa` (
  `id_diagnosa` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `nama_pembudidaya` varchar(100) NOT NULL,
  `tanggal_diagnosa` timestamp NOT NULL DEFAULT current_timestamp(),
  `hasil_penyakit` varchar(10) DEFAULT NULL,
  `confidence` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_diagnosa_detail`
--

CREATE TABLE `tbl_diagnosa_detail` (
  `id_detail` int(11) NOT NULL,
  `id_diagnosa` int(11) NOT NULL,
  `kode_gejala` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_gejala`
--

CREATE TABLE `tbl_gejala` (
  `kode_gejala` varchar(10) NOT NULL,
  `nama_gejala` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_gejala`
--

INSERT INTO `tbl_gejala` (`kode_gejala`, `nama_gejala`) VALUES
('G01', 'Ikan berenang lamban atau malas bergerak'),
('G02', 'Ikan sering mengap-mengap di permukaan air'),
('G03', 'Ikan sering melompat atau berenang tidak beraturan (abnormal)'),
('G04', 'Ikan menggosokkan badan ke dinding/dasar/benda (iritasi kulit)'),
('G05', 'Ikan berkumpul di area air masuk/daerah oksigen tinggi'),
('G06', 'Nafsu makan menurun drastis'),
('G07', 'Pergerakan tidak terarah/berputar/kejang-kejang'),
('G08', 'Ikan tampak gelisah/sering muncul di permukaan'),
('G09', 'Ikan terlihat lemah dan kesadarannya menurun'),
('G10', 'Warna tubuh pucat atau menjadi lebih gelap dari normal'),
('G11', 'Terdapat bercak putih pada tubuh, sirip, atau insang (indikasi Ich)'),
('G12', 'Luka pada kulit yang kemudian berkembang menjadi borok (ulcer)'),
('G13', 'Terdapat pendarahan (hemoragi) di kulit, sirip, atau tutup insang'),
('G14', 'Adanya benang halus menyerupai kapas (Saprolegnia)'),
('G15', 'Terlihat Hifa/miselia putih-kecoklatan di sekitar luka'),
('G16', 'Sirip rusak, geripis, atau menguncup'),
('G17', 'Warna kemerahan (inflamasi) di area penempelan parasit (Lernaea)'),
('G18', 'Mata menonjol (exophthalmus)'),
('G19', 'Permukaan kulit menunjukkan tanda nekrosis (jaringan mati)'),
('G20', 'Insang berwarna merah cerah (tanda awal infeksi)'),
('G21', 'Insang pucat atau membengkak'),
('G22', 'Produksi mukus berlebih pada insang'),
('G23', 'Insang terinfeksi dan tampak berlendir tebal'),
('G25', 'Mata katarak'),
('G26', 'Perut bengkak'),
('G27', 'Organ bengkak'),
('G28', 'Kematian massal'),
('G30', 'Suhu tinggi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_penyakit`
--

CREATE TABLE `tbl_penyakit` (
  `kode_penyakit` varchar(10) NOT NULL,
  `nama_penyakit` varchar(100) NOT NULL,
  `nama_latin` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `solusi` text DEFAULT NULL,
  `pencegahan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_penyakit`
--

INSERT INTO `tbl_penyakit` (`kode_penyakit`, `nama_penyakit`, `nama_latin`, `deskripsi`, `solusi`, `pencegahan`) VALUES
('P01', 'Penyakit Trichodiniasis', 'Trichodina sp.', 'Penyakit gatal yang disebabkan oleh ektoparasit protozoa. Menyerang kulit dan sirip, sering terjadi saat kualitas air buruk dan populasi ikan terlalu padat.', 'Perendaman formalin 25 ppm 30 menit atau garam 5-10 g/L selama 10-15 menit. Ganti air kolam secara berkala dan kurangi kepadatan tebar.', 'Jaga kualitas air tetap bersih, kurangi kepadatan tebar, dan pastikan sirkulasi air lancar.'),
('P02', 'Penyakit Jamur Insang', 'Branchiomyces sp.', 'Infeksi jamur pada insang yang menyebabkan insang membusuk (grot gill), menghambat pernapasan ikan, dan sering memicu kematian mendadak.', 'Bersihkan kolam, ganti air, rendam ikan dengan fungisida ringan (malachite green / KMnO4).', 'Hindari pemberian pakan berlebih (sisa pakan memicu jamur), jaga kadar oksigen terlarut tinggi, dan bersihkan dasar kolam dari bahan organik.'),
('P03', 'Penyakit Cacing Insang', 'Dactylogyrus sp.', 'Parasit cacing monogenea yang menyerang filamen insang. Ikan akan memproduksi lendir berlebih di insang untuk melindungi diri, namun justru menghambat pernapasan.', 'Gunakan larutan formalin 25 ppm atau NaCl 2% selama 10-15 menit. Keringkan kolam dan kurangi lumpur.', 'Keringkan dan kapur dasar kolam sebelum tebar benih untuk memutus siklus hidup parasit.'),
('P04', 'Penyakit Cacing Kulit', 'Gyrodactylus sp.', 'Parasit cacing yang menyerang permukaan tubuh dan sirip. Menyebabkan iritasi, sirip geripis, dan luka yang bisa memicu infeksi sekunder.', 'Lakukan perendaman ikan dengan formalin 25–50 ppm atau air garam 2–5%. Jaga kualitas air tetap jernih dan oksigen cukup.', 'Karantina ikan baru sebelum dimasukkan ke kolam utama dan jaga kebersihan air.'),
('P05', 'Penyakit Streptococcosis', 'Streptococcus agalactiae / iniae', 'Penyakit bakteri ganas yang menyerang sistem saraf dan organ dalam. Gejala khasnya adalah mata menonjol (pop-eye) dan berenang berputar.', 'Berikan antibiotik seperti oksitetrasiklin 50–75 mg/kg pakan selama 5–7 hari. Isolasi ikan sakit dan jaga suhu air di bawah 30°C.', 'Hindari kepadatan tinggi, jangan memberi pakan berlebih (overfeeding), dan rutin berikan probiotik atau Vitamin C pada pakan.'),
('P06', 'Penyakit Jamur Kapas', 'Saprolegnia sp.', 'Infeksi jamur sekunder yang biasanya menyerang ikan yang sudah terluka fisik. Tumbuh menyerupai kapas putih pada bagian tubuh yang luka.', 'Bersihkan kolam, hilangkan ikan mati, dan rendam ikan dalam larutan NaCl 2% selama 10 menit. Gunakan fungisida malachite green 0,1 ppm bila perlu.', 'Hindari penanganan ikan yang kasar agar tidak luka, dan jaga suhu air agar tidak terlalu dingin (jamur suka suhu rendah).'),
('P07', 'Penyakit Cacing Jangkar', 'Lernaea sp.', 'Parasit crustacea yang menancapkan kepalanya ke tubuh ikan seperti jangkar, menyebabkan luka berdarah dan ikan menjadi kurus.', 'Angkat parasit secara manual dengan pinset lalu rendam ikan dalam larutan KMnO4 2 ppm selama 30 menit. Tambahkan garam 5 g/liter untuk desinfeksi.', 'Pasang filter pada saluran masuk air untuk mencegah larva parasit masuk, dan karantina ikan baru.'),
('P08', 'Penyakit Tilapia Lake Virus (TiLV)', 'Tilapia tilapinevirus', 'Penyakit virus mematikan yang spesifik menyerang ikan nila. Menyebabkan kematian massal, mata katarak/cekung, dan tubuh menghitam.', 'Belum ada obat spesifik, lakukan karantina ikan sakit, buang ikan mati segera, dan tingkatkan biosekuriti kolam. Hindari stres dan perubahan suhu mendadak.', 'Gunakan benih bersertifikat bebas TiLV (SPF), batasi lalu lintas orang/alat ke area kolam (biosekuriti ketat).'),
('P09', 'Penyakit Busuk Sirip', 'Flavobacterium columnare / Aeromonas sp.', 'Infeksi bakteri yang menyebabkan sirip ikan pecah, geripis, dan membusuk. Sering terjadi akibat kualitas air yang buruk atau kepadatan tinggi.', 'Lakukan perendaman dengan antibiotik oksitetrasiklin 10 mg/l atau KMnO4 2 ppm. Kurangi kepadatan tebar dan perbaiki aerasi kolam.', 'Perbaiki manajemen kualitas air dan hindari penumpukan bahan organik di dasar kolam.'),
('P10', 'Penyakit Bintik Putih', 'Ichthyophthirius multifiliis (Ich)', 'Penyakit parasit yang memunculkan bintik-bintik putih seukuran garam pada kulit dan sirip. Sangat menular dan menyebabkan ikan sering menggosokkan badan.', 'Gunakan larutan formalin 25 ppm atau malachite green 0,1 ppm. Jaga suhu air pada 30–32°C untuk mempercepat siklus hidup parasit.', 'Jaga kestabilan suhu air (gunakan heater jika perlu pada akuarium/pembenihan) dan karantina ikan baru.'),
('P11', 'Penyakit Mata Menonjol', 'Exophthalmia (Gejala klinis)', 'Kondisi mata ikan membengkak keluar dari rongganya. Biasanya merupakan tanda infeksi bakteri internal sistemik atau kualitas air yang sangat buruk.', 'Lakukan perendaman ikan dalam larutan formalin 25 ppm selama 30 menit dan ganti air secara berkala.', 'Jaga kebersihan lingkungan budidaya dan berikan pakan dengan nutrisi seimbang untuk meningkatkan imunitas.');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `tbl_aturan`
--
ALTER TABLE `tbl_aturan`
  ADD PRIMARY KEY (`id_aturan`),
  ADD KEY `kode_penyakit` (`kode_penyakit`),
  ADD KEY `kode_gejala` (`kode_gejala`);

--
-- Indeks untuk tabel `tbl_diagnosa`
--
ALTER TABLE `tbl_diagnosa`
  ADD PRIMARY KEY (`id_diagnosa`),
  ADD KEY `hasil_penyakit` (`hasil_penyakit`);

--
-- Indeks untuk tabel `tbl_diagnosa_detail`
--
ALTER TABLE `tbl_diagnosa_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `kode_gejala` (`kode_gejala`);

--
-- Indeks untuk tabel `tbl_gejala`
--
ALTER TABLE `tbl_gejala`
  ADD PRIMARY KEY (`kode_gejala`),
  ADD KEY `kode_gejala` (`kode_gejala`);

--
-- Indeks untuk tabel `tbl_penyakit`
--
ALTER TABLE `tbl_penyakit`
  ADD PRIMARY KEY (`kode_penyakit`),
  ADD KEY `kode_penyakit` (`kode_penyakit`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbl_aturan`
--
ALTER TABLE `tbl_aturan`
  MODIFY `id_aturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `tbl_diagnosa`
--
ALTER TABLE `tbl_diagnosa`
  MODIFY `id_diagnosa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tbl_diagnosa_detail`
--
ALTER TABLE `tbl_diagnosa_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_aturan`
--
ALTER TABLE `tbl_aturan`
  ADD CONSTRAINT `tbl_aturan_ibfk_1` FOREIGN KEY (`kode_penyakit`) REFERENCES `tbl_penyakit` (`kode_penyakit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_aturan_ibfk_2` FOREIGN KEY (`kode_gejala`) REFERENCES `tbl_gejala` (`kode_gejala`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_diagnosa`
--
ALTER TABLE `tbl_diagnosa`
  ADD CONSTRAINT `tbl_diagnosa_ibfk_2` FOREIGN KEY (`hasil_penyakit`) REFERENCES `tbl_penyakit` (`kode_penyakit`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_diagnosa_detail`
--
ALTER TABLE `tbl_diagnosa_detail`
  ADD CONSTRAINT `tbl_diagnosa_detail_ibfk_2` FOREIGN KEY (`kode_gejala`) REFERENCES `tbl_gejala` (`kode_gejala`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
