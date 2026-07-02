-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jul 2026 pada 07.58
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
(1, 'admin', '$2y$10$bVsvqz8.0cy2QMGJL.Q2jekjf9nAp94uqA4XhUN7ICgXKmBSZ1Gtq', 'Aditya Rahman'),
(4, 'nopaall', '$2y$10$TWn/raWZIq4aG0WGosKjS.Q.H4ziBwJZrwVC2IoGop0KT1CsSV7.C', 'Naufal Rafif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_aturan`
--

CREATE TABLE `tbl_aturan` (
  `id_aturan` int(11) NOT NULL,
  `kode_aturan` varchar(10) DEFAULT NULL,
  `kode_penyakit` varchar(10) NOT NULL,
  `kode_gejala` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_aturan`
--

INSERT INTO `tbl_aturan` (`id_aturan`, `kode_aturan`, `kode_penyakit`, `kode_gejala`) VALUES
(1, 'R01', 'P01', 'G04'),
(2, 'R01', 'P01', 'G09'),
(3, 'R01', 'P01', 'G21'),
(4, 'R01', 'P01', 'G25'),
(5, 'R02', 'P01', 'G02'),
(6, 'R02', 'P01', 'G04'),
(7, 'R02', 'P01', 'G21'),
(8, 'R02', 'P01', 'G26'),
(9, 'R03', 'P02', 'G01'),
(10, 'R03', 'P02', 'G02'),
(11, 'R03', 'P02', 'G19'),
(12, 'R03', 'P02', 'G20'),
(13, 'R04', 'P02', 'G02'),
(14, 'R04', 'P02', 'G19'),
(15, 'R04', 'P02', 'G20'),
(16, 'R04', 'P02', 'G24'),
(17, 'R05', 'P03', 'G02'),
(18, 'R05', 'P03', 'G05'),
(19, 'R05', 'P03', 'G20'),
(20, 'R05', 'P03', 'G21'),
(21, 'R06', 'P03', 'G02'),
(22, 'R06', 'P03', 'G04'),
(23, 'R06', 'P03', 'G20'),
(24, 'R06', 'P03', 'G21'),
(25, 'R07', 'P04', 'G04'),
(26, 'R07', 'P04', 'G16'),
(27, 'R07', 'P04', 'G25'),
(28, 'R08', 'P04', 'G04'),
(29, 'R08', 'P04', 'G16'),
(30, 'R08', 'P04', 'G28'),
(31, 'R09', 'P05', 'G07'),
(32, 'R09', 'P05', 'G17'),
(33, 'R09', 'P05', 'G24'),
(34, 'R09', 'P05', 'G27'),
(35, 'R10', 'P05', 'G07'),
(36, 'R10', 'P05', 'G17'),
(37, 'R10', 'P05', 'G22'),
(38, 'R10', 'P05', 'G23'),
(39, 'R11', 'P06', 'G13'),
(40, 'R11', 'P06', 'G14'),
(41, 'R11', 'P06', 'G18'),
(42, 'R12', 'P06', 'G13'),
(43, 'R12', 'P06', 'G14'),
(44, 'R12', 'P06', 'G28'),
(45, 'R13', 'P07', 'G04'),
(46, 'R13', 'P07', 'G15'),
(47, 'R13', 'P07', 'G16'),
(48, 'R14', 'P07', 'G04'),
(49, 'R14', 'P07', 'G15'),
(50, 'R14', 'P07', 'G25'),
(51, 'R15', 'P08', 'G01'),
(52, 'R15', 'P08', 'G06'),
(53, 'R15', 'P08', 'G24'),
(54, 'R15', 'P08', 'G26'),
(55, 'R15', 'P08', 'G27'),
(56, 'R16', 'P08', 'G03'),
(57, 'R16', 'P08', 'G08'),
(58, 'R16', 'P08', 'G24'),
(59, 'R16', 'P08', 'G27'),
(60, 'R17', 'P09', 'G15'),
(61, 'R17', 'P09', 'G18'),
(62, 'R17', 'P09', 'G30'),
(63, 'R18', 'P09', 'G12'),
(64, 'R18', 'P09', 'G15'),
(65, 'R18', 'P09', 'G31'),
(66, 'R19', 'P10', 'G04'),
(67, 'R19', 'P10', 'G06'),
(68, 'R19', 'P10', 'G10'),
(69, 'R20', 'P10', 'G04'),
(70, 'R20', 'P10', 'G08'),
(71, 'R20', 'P10', 'G10'),
(72, 'R21', 'P11', 'G11'),
(73, 'R21', 'P11', 'G12'),
(74, 'R21', 'P11', 'G17'),
(75, 'R21', 'P11', 'G23'),
(76, 'R22', 'P11', 'G11'),
(77, 'R22', 'P11', 'G12'),
(78, 'R22', 'P11', 'G24'),
(79, 'R22', 'P11', 'G29'),
(80, 'R23', 'P12', 'G11'),
(81, 'R23', 'P12', 'G12'),
(82, 'R23', 'P12', 'G18'),
(83, 'R23', 'P12', 'G29'),
(84, 'R24', 'P12', 'G08'),
(85, 'R24', 'P12', 'G12'),
(86, 'R24', 'P12', 'G29'),
(87, 'R24', 'P12', 'G34'),
(88, 'R25', 'P13', 'G25'),
(89, 'R25', 'P13', 'G28'),
(90, 'R25', 'P13', 'G32'),
(91, 'R26', 'P13', 'G25'),
(92, 'R26', 'P13', 'G32'),
(93, 'R26', 'P13', 'G33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_diagnosa`
--

CREATE TABLE `tbl_diagnosa` (
  `id_diagnosa` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `kode_sampel` varchar(50) DEFAULT NULL,
  `nama_pembudidaya` varchar(100) DEFAULT NULL,
  `tanggal_diagnosa` timestamp NOT NULL DEFAULT current_timestamp(),
  `hasil_penyakit` varchar(10) DEFAULT NULL,
  `confidence` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_diagnosa`
--

INSERT INTO `tbl_diagnosa` (`id_diagnosa`, `id_admin`, `kode_sampel`, `nama_pembudidaya`, `tanggal_diagnosa`, `hasil_penyakit`, `confidence`) VALUES
(1, 4, 'SPL-001', NULL, '2026-07-02 03:17:00', 'P01', 1),
(2, 4, 'SPL-002', NULL, '2026-07-02 03:17:14', 'P02', 1),
(3, 4, 'SPL-003', NULL, '2026-07-02 03:17:29', 'P03', 1),
(4, 4, 'SPL-004', NULL, '2026-07-02 03:23:12', 'P04', 0.6),
(5, 4, 'SPL-005', NULL, '2026-07-02 03:24:12', 'P02', 0.666667);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_diagnosa_detail`
--

CREATE TABLE `tbl_diagnosa_detail` (
  `id_detail` int(11) NOT NULL,
  `id_diagnosa` int(11) NOT NULL,
  `kode_gejala` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_diagnosa_detail`
--

INSERT INTO `tbl_diagnosa_detail` (`id_detail`, `id_diagnosa`, `kode_gejala`) VALUES
(1, 1, 'G04'),
(2, 1, 'G09'),
(3, 1, 'G21'),
(4, 1, 'G25'),
(5, 2, 'G01'),
(6, 2, 'G02'),
(7, 2, 'G19'),
(8, 2, 'G20'),
(9, 3, 'G02'),
(10, 3, 'G05'),
(11, 3, 'G20'),
(12, 3, 'G21'),
(13, 4, 'G04'),
(14, 4, 'G16'),
(15, 4, 'G21'),
(16, 4, 'G25'),
(17, 4, 'G28'),
(18, 5, 'G01'),
(19, 5, 'G02'),
(20, 5, 'G19'),
(21, 5, 'G20'),
(22, 5, 'G24'),
(23, 5, 'G26');

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
('G01', 'Aktivitas berenang ikan menurun dan gerakannya terlihat lebih lambat dari kondisi normal'),
('G02', 'Ikan sering muncul ke permukaan sambil membuka mulut seperti kekurangan oksigen'),
('G03', 'Pola renang ikan tidak normal, sering meloncat atau bergerak secara tidak beraturan'),
('G04', 'Ikan sering menggesekkan atau menabrakkan tubuhnya pada dinding kolam, dasar kolam, maupun benda di sekitarnya akibat iritasi.'),
('G05', 'Ikan cenderung berkumpul di sekitar saluran masuk air atau area dengan kandungan oksigen lebih tinggi'),
('G06', 'Nafsu makan ikan mengalami penurunan secara signifikan'),
('G07', 'Ikan berenang berputar, kehilangan arah, atau mengalami gangguan koordinasi gerak'),
('G08', 'Kondisi tubuh ikan terlihat lemah dan respons terhadap lingkungan menurun'),
('G09', 'Warna tubuh ikan berubah menjadi lebih pucat atau lebih gelap dari biasanya'),
('G10', 'Muncul bintik-bintik putih pada tubuh, sirip, atau insang ikan'),
('G11', 'Terdapat luka pada kulit yang berkembang menjadi borok atau ulser'),
('G12', 'Muncul pendarahan pada kulit, sirip, atau bagian tutup insang'),
('G13', 'Terlihat benang-benang halus berwarna putih menyerupai kapas pada permukaan tubuh ikan'),
('G14', 'Terdapat pertumbuhan hifa atau miselium berwarna putih hingga kecokelatan di sekitar luka'),
('G15', 'Sirip tampak rusak, geripis, menguncup, atau mengalami pembusukan'),
('G16', 'Area tempat menempelnya parasit menunjukkan warna kemerahan atau peradangan'),
('G17', 'Salah satu atau kedua mata ikan tampak menonjol'),
('G18', 'Permukaan kulit mengalami kerusakan jaringan atau nekrosis'),
('G19', 'Insang tampak kemerahan atau mengalami peradangan'),
('G20', 'Insang terlihat pucat atau membengkak sehingga mengganggu pernapasan'),
('G21', 'Produksi lendir pada insang meningkat secara berlebihan sehingga sebagian permukaan insang tampak tertutup lendir'),
('G22', 'Kornea mata mengalami kekeruhan atau terdapat infeksi di sekitar mata'),
('G23', 'Perut ikan membesar secara tidak normal dan pada beberapa kasus disertai penumpukan cairan (dropsy).'),
('G24', 'Terjadi peningkatan jumlah kematian ikan dalam waktu relatif singkat'),
('G25', 'Produksi lendir pada permukaan tubuh meningkat secara berlebihan'),
('G26', 'Ikan cenderung memisahkan diri dari kelompok dan lebih sering diam di dasar kolam'),
('G27', 'Ikan mengalami kehilangan keseimbangan saat berenang'),
('G28', 'Permukaan kulit tampak kasar, kusam, atau kehilangan kilap alaminya'),
('G29', 'Terjadi pendarahan pada pangkal sirip atau di sekitar anus'),
('G30', 'Mulut ikan tampak memutih atau mengalami pembusukan'),
('G31', 'Sirip atau bagian tubuh tampak ditutupi lapisan putih keabu-abuan'),
('G32', 'Tubuh tampak kusam, kehilangan warna cerah alaminya, dan sering disertai lapisan lendir berlebih'),
('G33', 'Terdapat bercak putih keabu-abuan pada kulit atau insang'),
('G34', 'Organ dalam (hati atau limpa) mengalami pembengkakan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_penyakit`
--

CREATE TABLE `tbl_penyakit` (
  `kode_penyakit` varchar(10) NOT NULL,
  `nama_penyakit` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `solusi` text DEFAULT NULL,
  `pencegahan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_penyakit`
--

INSERT INTO `tbl_penyakit` (`kode_penyakit`, `nama_penyakit`, `deskripsi`, `solusi`, `pencegahan`) VALUES
('P01', 'Penyakit Trichodiniasis', 'Infeksi protozoa Trichodina sp. pada insang dan permukaan tubuh ikan nila yang menyebabkan iritasi kulit, peningkatan produksi lendir, dan gangguan pernapasan.', 'Lakukan pergantian air secara berkala, kurangi kepadatan tebar, serta lakukan perendaman menggunakan larutan garam atau formalin sesuai dosis dan anjuran pakar.', 'Menjaga kualitas air kolam, melakukan pergantian air secara berkala, mengurangi kepadatan tebar, serta menghindari penumpukan bahan organik pada dasar kolam.'),
('P02', 'Branchiomycosis (Busuk Insang)', 'Infeksi jamur Branchiomyces sp. pada jaringan insang yang menyebabkan kerusakan insang, gangguan pernapasan, dan penurunan kondisi ikan secara umum.', 'Perbaiki kualitas air kolam, tingkatkan aerasi, dan lakukan penanganan menggunakan bahan antijamur sesuai dosis dan rekomendasi pakar.', 'Memastikan sirkulasi dan aerasi air berjalan baik, menjaga kebersihan kolam, serta menghindari penurunan kadar oksigen terlarut.'),
('P03', 'Cacing Insang Dactylogyriasis', 'Infestasi cacing monogenea Dactylogyrus sp. pada insang yang menyebabkan kerusakan jaringan insang, produksi lendir berlebih, gangguan pernapasan, dan penurunan kondisi ikan secara umum.', 'Lakukan perendaman menggunakan larutan garam atau formalin sesuai dosis yang dianjurkan, serta kurangi penumpukan lumpur dan bahan organik pada kolam.', 'Menjaga kebersihan kolam, mengurangi kepadatan ikan, dan melakukan karantina terhadap benih baru sebelum ditebar.'),
('P04', 'Cacing Kulit Gyrodactylosis', 'Infestasi parasit Gyrodactylus sp. pada permukaan kulit ikan yang menyebabkan iritasi, peningkatan produksi lendir, luka, dan peradangan pada kulit.', 'Tingkatkan kualitas air dan kadar oksigen terlarut, serta lakukan perendaman menggunakan larutan garam atau bahan antiparasit sesuai anjuran pakar.', 'Menjaga kualitas air dan kadar oksigen terlarut, menghindari kepadatan tebar berlebihan, serta melakukan pemeriksaan kesehatan ikan secara berkala.'),
('P05', 'Penyakit Streptococcus', 'Infeksi bakteri Streptococcus sp. yang menyerang sistem saraf dan organ dalam ikan nila sehingga dapat menyebabkan gangguan keseimbangan berenang dan tingkat mortalitas yang tinggi.', 'Pisahkan ikan yang sakit, jaga kualitas air dan suhu kolam tetap stabil, serta lakukan pengobatan menggunakan antibiotik sesuai rekomendasi tenaga ahli atau pakar perikanan.', 'Menjaga kualitas air dan kadar oksigen terlarut tetap stabil, menghindari stres pada ikan, memberikan pakan berkualitas, serta meminimalkan fluktuasi suhu yang ekstrem.'),
('P06', 'Jamur Kapas (Saprolegniasis)', 'Infeksi jamur Saprolegnia sp. pada luka terbuka atau jaringan yang mengalami stres, ditandai dengan pertumbuhan miselium menyerupai kapas pada tubuh ikan.', 'Buang ikan yang mati, bersihkan kolam secara rutin, dan lakukan perendaman menggunakan larutan garam atau bahan antijamur sesuai dosis yang dianjurkan.', 'Menghindari luka pada tubuh ikan, segera memisahkan ikan yang sakit, menjaga kebersihan kolam dan peralatan budidaya, serta menerapkan biosekuriti.'),
('P07', 'Cacing Jangkar (Lerneosis)', 'Infestasi parasit Lernaea cyprinacea yang menancapkan diri pada permukaan tubuh ikan sehingga menimbulkan luka, peradangan, dan infeksi sekunder.', 'Parasit yang menempel dapat diangkat secara hati-hati, kemudian dilakukan perendaman menggunakan bahan desinfektan atau larutan garam sesuai rekomendasi pakar.', 'Melakukan karantina ikan baru, menjaga kualitas air, serta membersihkan kolam dan peralatan secara rutin.'),
('P08', 'Virus Tilapia Lake (TiLV)', 'Infeksi Tilapia Lake Virus (TiLV) yang menyebabkan penurunan kondisi tubuh, gangguan pertumbuhan, dan kematian massal pada ikan nila, serta hingga saat ini belum tersedia pengobatan spesifik.', 'Hingga saat ini belum tersedia pengobatan spesifik. Penanganan dilakukan melalui karantina ikan sakit, peningkatan biosekuriti kolam, pengurangan stres, dan pembuangan ikan mati secara cepat untuk mencegah penyebaran penyakit.', 'Menerapkan biosekuriti, menggunakan benih sehat, menghindari perpindahan ikan tanpa karantina, serta segera membuang ikan yang mati.'),
('P09', 'Penyakit Columnaris', 'Infeksi bakteri Flavobacterium columnare yang menyerang kulit, insang, dan sirip ikan serta dapat menyebabkan erosi jaringan dan kerusakan progresif pada tubuh ikan.', 'Tingkatkan kualitas air, kurangi kepadatan tebar, dan lakukan pengobatan sesuai anjuran pakar untuk menghambat perkembangan infeksi bakteri.', 'Menjaga kualitas air dan kadar oksigen terlarut, mencegah luka fisik pada ikan, serta menghindari kepadatan tebar yang terlalu tinggi.'),
('P10', 'Bintik Putih (Ichthyophthiriasis)', 'Infeksi protozoa Ichthyophthirius multifiliis yang sangat menular dan ditandai dengan munculnya bintik-bintik putih pada tubuh, sirip, dan insang ikan.', 'Lakukan perendaman menggunakan formalin atau bahan antiparasit sesuai dosis yang dianjurkan serta jaga kualitas air dan suhu kolam agar tetap stabil.', 'Menjaga kualitas air dan suhu kolam tetap stabil, menghindari stres, serta melakukan karantina ikan baru.'),
('P11', 'Luka Merah (Aeromoniasis)', 'Infeksi bakteri Aeromonas hydrophila yang menyebabkan pendarahan, peradangan, dan terbentuknya luka borok pada permukaan tubuh ikan nila.', 'Jaga kebersihan kolam, lakukan pergantian air secara rutin, serta lakukan pengobatan menggunakan antibiotik sesuai rekomendasi pakar dan ketentuan yang berlaku.', 'Menjaga kebersihan kolam, menghindari penumpukan sisa pakan, melakukan pergantian air secara rutin, serta meminimalkan stres pada ikan.'),
('P12', 'Penyakit Pseudomonas', 'Infeksi bakteri Pseudomonas sp. yang dapat menyebabkan nekrosis jaringan, pendarahan pada tubuh, serta penurunan kondisi kesehatan ikan secara umum.', 'Pisahkan ikan yang terinfeksi, perbaiki kualitas air, dan lakukan pengobatan sesuai rekomendasi pakar atau tenaga kesehatan perikanan.', 'Menjaga kualitas air tetap baik, menghindari luka pada tubuh ikan, serta melakukan sanitasi kolam dan peralatan budidaya secara rutin.'),
('P13', 'Penyakit Epistylis', 'Infestasi protozoa Epistylis sp. yang menyerang permukaan tubuh ikan dan ditandai dengan peningkatan produksi lendir, iritasi kulit, serta warna tubuh yang tampak kusam.', 'Lakukan pergantian air, kurangi bahan organik yang berlebihan, dan lakukan perendaman menggunakan bahan antiparasit sesuai rekomendasi pakar.', 'Menjaga kebersihan kolam, mengurangi kepadatan tebar, serta menjaga kualitas air agar tetap stabil dan tidak tercemar bahan organik berlebih.');

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_aturan`
--
ALTER TABLE `tbl_aturan`
  MODIFY `id_aturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT untuk tabel `tbl_diagnosa`
--
ALTER TABLE `tbl_diagnosa`
  MODIFY `id_diagnosa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
