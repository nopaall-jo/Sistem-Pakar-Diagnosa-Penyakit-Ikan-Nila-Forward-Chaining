<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../config/database.php';
require_once '../vendor/autoload.php';

// Set header untuk cache control
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// PERBAIKAN: Mendukung metode GET & POST untuk ID
$diagnosa_id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$diagnosa_id) {
    die("ID Diagnosa tidak valid");
}

// PERBAIKAN: Format selalu ditangkap, dibersihkan, dan dijadikan huruf kecil
$format = strtolower(trim($_POST['format'] ?? $_GET['format'] ?? 'pdf'));

// PERBAIKAN QUERY: Tanpa JOIN ke tabel user, karena ini sistem 1 aktor.
$stmt = $pdo->prepare("SELECT d.*, p.* FROM tbl_diagnosa d 
                       LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                       WHERE d.id_diagnosa = ?");
$stmt->execute([$diagnosa_id]);
$diagnosa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$diagnosa) {
    die("Data diagnosa tidak ditemukan di database.");
}

// Ambil gejala yang dipilih
// PERBAIKAN QUERY: Sinkronisasi tabel (tbl_diagnosa_detail, tbl_gejala)
$stmt = $pdo->prepare("SELECT g.kode_gejala, g.nama_gejala 
                       FROM tbl_diagnosa_detail dd 
                       JOIN tbl_gejala g ON dd.kode_gejala = g.kode_gejala 
                       WHERE dd.id_diagnosa = ?");
$stmt->execute([$diagnosa_id]);
$gejala = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat PDF menggunakan TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);

// Set dokumen meta data
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Pakar Ikan Nila');
$pdf->SetTitle('Laporan Hasil Diagnosa - ' . ($diagnosa['kode_sampel'] ?? 'Sampel'));
$pdf->SetSubject('Hasil Diagnosa Penyakit Ikan Nila');

// Set margin
$pdf->SetMargins(15, 12, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 12);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('times', '', 11);
$pdf->AddPage();

// ==========================================
// KOP SURAT 2 LOGO (KIRI & KANAN)
// ==========================================
$path_logoKiri = realpath(__DIR__ . '/../assets/img/Logo2.png');
$path_logoKanan = realpath(__DIR__ . '/../assets/img/logo3.png');
 
$img_kiri = file_exists($path_logoKiri) ? '<img src="' . $path_logoKiri . '" width="85">' : '';
$img_kanan = file_exists($path_logoKanan) ? '<img src="' . $path_logoKanan . '" width="85">' : '';
 
$kop = '
<table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td width="15%" align="center">' . $img_kiri . '</td>
        <td width="70%" align="center">
            <h2 style="margin: 0; font-weight: bold; line-height: 1.1; color: #002d27; font-size: 14pt;">DZAWIL GARDEN OFFICE FARM</h2>
            <h4 style="margin: 0; font-weight: bold; line-height: 1.2; font-size: 11pt;">SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA</h4>
            <h5 style="margin: 0; font-weight: bold; line-height: 1.2; color: #555555; font-size: 9pt;">METODE FORWARD CHAINING</h5>
            <p style="margin: 0; font-size: 8pt; color: #444444; line-height: 1.2; margin-top: 5px;">
                Jl. H. Sena, Ragajaya Citayam, Kecamatan Bojonggede, Kabupaten Bogor, Jawa Barat 16920<br>
                Telp: 0852-1010-0139
            </p>
        </td>
        <td width="15%" align="center">' . $img_kanan . '</td>
    </tr>
</table>
<hr style="border-top: 2px solid #000; margin: 0; padding: 0; height: 1px;">
<hr style="border-top: 1px solid #000; margin: 0; padding: 0; height: 1px; margin-top: 2px;">
<br>';
$pdf->writeHTML($kop, true, false, true, false, '');

// Judul laporan
$pdf->SetFont('times', 'B', 14);
$pdf->Cell(0, 8, 'LAPORAN HASIL DIAGNOSA PENYAKIT IKAN NILA', 0, 1, 'C');
$pdf->SetFont('times', '', 10);
$pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB', 0, 1, 'C');
$pdf->Ln(4);

// ==========================================
// RENDER KONTEN (TAMPILAN MODERN CETAK BIASA)
// ==========================================
$kecocokan = isset($diagnosa['confidence']) ? round(($diagnosa['confidence'] * 100), 2) : 100;
 
// Gejala Rows
$gejalaRows = '';
if (count($gejala) > 0) {
    foreach ($gejala as $g) {
        $gejalaRows .= '
        <tr>
            <td width="15%" align="center" style="font-weight: bold;">' . htmlspecialchars($g['kode_gejala']) . '</td>
            <td width="85%">' . htmlspecialchars($g['nama_gejala']) . '</td>
        </tr>';
    }
} else {
    $gejalaRows .= '<tr><td colspan="2" align="center">Tidak ada data gejala yang dipilih.</td></tr>';
}
 
$html = '
<table border="0" cellpadding="5" cellspacing="0" width="100%" style="background-color: #f9f9f9; border: 1px solid #eeeeee;">
    <tr>
        <td width="18%"><strong>ID Diagnosa:</strong></td>
        <td width="32%">#DIAG-' . str_pad($diagnosa['id_diagnosa'], 4, '0', STR_PAD_LEFT) . '</td>
        <td width="22%"><strong>Tanggal Periksa:</strong></td>
        <td width="28%">' . date('d M Y, H:i', strtotime($diagnosa['tanggal_diagnosa'])) . ' WIB</td>
    </tr>
    <tr>
        <td><strong>Kode Sampel:</strong></td>
        <td>' . htmlspecialchars($diagnosa['kode_sampel'] ?? '-') . '</td>
        <td><strong>Tingkat Kepastian:</strong></td>
        <td>' . $kecocokan . '%</td>
    </tr>
</table>
<br>
 
<h3 style="font-size: 11pt; font-weight: bold; border-bottom: 1px solid #dddddd; color: #333333; padding-bottom: 3px; margin: 0;">
    Kesimpulan Hasil Diagnosa: <span style="color: #dc3545;">' . htmlspecialchars($diagnosa['nama_penyakit'] ?? 'Tidak Dikenali') . '</span>
</h3>
<br>
 
<h3 style="font-size: 11pt; font-weight: bold; border-bottom: 1px solid #dddddd; color: #333333; padding-bottom: 3px; margin: 0;">
    Daftar Gejala Klinis
</h3>
<br>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr style="background-color: #f4fbf9; font-weight: bold;">
            <th width="15%" align="center">Kode</th>
            <th width="85%">Nama Gejala Teramati</th>
        </tr>
    </thead>
    <tbody>
        ' . $gejalaRows . '
    </tbody>
</table>
<br>';
 
if (!empty($diagnosa['kode_penyakit'])) {
    $html .= '
    <h3 style="font-size: 11pt; font-weight: bold; border-bottom: 1px solid #dddddd; color: #333333; padding-bottom: 3px; margin: 0;">
        Rekomendasi Medis & Penanganan
    </h3>
    <br>
    <table border="0" cellpadding="1" cellspacing="0" width="100%">
        <tr>
            <td><strong>Deskripsi Penyakit:</strong></td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                    <tr>
                        <td style="border-left: 3px solid #002d27; background-color: #fafafa; font-size: 9.5pt; text-align: justify; line-height: 1.3;">
                            ' . nl2br(htmlspecialchars($diagnosa['deskripsi'] ?? 'Deskripsi tidak tersedia.')) . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="height: 2px; border: none;"></td></tr>
        <tr>
            <td><strong>Tindakan Pengobatan (Solusi):</strong></td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                    <tr>
                        <td style="border-left: 3px solid #198754; background-color: #fafafa; font-size: 9.5pt; text-align: justify; line-height: 1.3;">
                            ' . nl2br(htmlspecialchars($diagnosa['solusi'] ?? 'Solusi tidak tersedia.')) . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="height: 2px; border: none;"></td></tr>
        <tr>
            <td><strong>Langkah Pencegahan:</strong></td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                    <tr>
                        <td style="border-left: 3px solid #0dcaf0; background-color: #fafafa; font-size: 9.5pt; text-align: justify; line-height: 1.3;">
                            ' . nl2br(htmlspecialchars($diagnosa['pencegahan'] ?? 'Data pencegahan tidak tersedia.')) . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>';
}
 
$pdf->writeHTML($html, true, false, false, false, '');
 
// ==========================================
// TANDA TANGAN PDF
// ==========================================
$pdf->Ln(6);
$pdf->SetFont('times', '', 11);
$pdf->Cell(0, 5, getTanggalTtdIndo(), 0, 1, 'R');
$pdf->Cell(0, 5, 'Pakar / Admin Sistem', 0, 1, 'R');
$pdf->Ln(10);
$pdf->Cell(0, 5, '(__________________________)', 0, 1, 'R');
 
// Output PDF ke browser
$pdf->Output('Laporan_Diagnosa_' . ($diagnosa['kode_sampel'] ?? str_pad($diagnosa['id_diagnosa'], 4, '0', STR_PAD_LEFT)) . '.pdf', 'I');
