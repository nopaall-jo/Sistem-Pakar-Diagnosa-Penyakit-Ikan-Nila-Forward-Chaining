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

// Set dokumen meta data
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Pakar Ikan Nila');
$pdf->SetTitle('Laporan Hasil Diagnosa - ' . ($diagnosa['kode_sampel'] ?? 'Sampel'));
$pdf->SetSubject('Hasil Diagnosa Penyakit Ikan Nila');

// Set margin
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('times', '', 11);
$pdf->AddPage();

// ==========================================
// KOP SURAT 2 LOGO (KIRI & KANAN)
// ==========================================
$path_logoKiri = realpath(__DIR__ . '/../assets/img/logo4.png');
$path_logoKanan = realpath(__DIR__ . '/../assets/img/logo3.png');

$img_kiri = file_exists($path_logoKiri) ? '<img src="' . $path_logoKiri . '" width="75">' : '';
$img_kanan = file_exists($path_logoKanan) ? '<img src="' . $path_logoKanan . '" width="75">' : '';

$kop = '
<table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td width="15%" align="center">' . $img_kiri . '</td>
        <td width="70%" align="center">
            <h3 style="margin: 0; font-weight: bold; line-height: 1.5;">SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA</h3>
            <h3 style="margin: 0; font-weight: bold; line-height: 1.5;">METODE FORWARD CHAINING</h3>
            <p style="margin: 0; font-size: 11pt; font-weight: bold;">KECAMATAN BOJONG GEDE</p>
            <p style="margin: 0; font-size: 10pt;">Kabupaten Bogor, Provinsi Jawa Barat</p>
        </td>
        <td width="15%" align="center">' . $img_kanan . '</td>
    </tr>
</table>
<hr style="border-top: 3px solid #000; margin: 0;">
<hr style="border-top: 1px solid #000; margin-top: 2px;">
<br>';
$pdf->writeHTML($kop, true, false, true, false, '');

// Judul laporan
$pdf->SetFont('times', 'B', 14);
$pdf->Cell(0, 8, 'LAPORAN HASIL DIAGNOSA PENYAKIT IKAN NILA', 0, 1, 'C');
$pdf->SetFont('times', '', 10);
$pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB', 0, 1, 'C');
$pdf->Ln(8);

// ==========================================
// INFORMASI DIAGNOSA & PENGGUNA
// ==========================================
$pdf->SetFont('times', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(40, 167, 69); // Warna header hijau
$pdf->Cell(0, 8, ' A. INFORMASI DIAGNOSA & HASIL', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('times', '', 11);

// Cek apakah ada persentase kecocokan di database (jika tidak ada, set 100%)
$kecocokan = isset($diagnosa['confidence']) ? round(($diagnosa['confidence'] * 100), 2) : 100;

$infoDiagnosa = '<table border="1" cellpadding="6">
    <tr>
        <td width="30%" style="background-color:#f8f9fa;"><strong>ID Diagnosa</strong></td>
        <td width="70%">#DIAG-' . str_pad($diagnosa['id_diagnosa'], 4, '0', STR_PAD_LEFT) . '</td>
    </tr>
    <tr>
        <td style="background-color:#f8f9fa;"><strong>Tanggal Diagnosa</strong></td>
        <td>' . date('d F Y - H:i', strtotime($diagnosa['tanggal_diagnosa'])) . ' WIB</td>
    </tr>
    <tr>
        <td style="background-color:#f8f9fa;"><strong>Kode Sampel</strong></td>
        <td>' . htmlspecialchars($diagnosa['kode_sampel'] ?? '-') . '</td>
    </tr>
    <tr>
        <td style="background-color:#e2efd9;"><strong>Hasil Diagnosa</strong></td>
        <td style="background-color:#e2efd9;"><strong>' . mb_strtoupper($diagnosa['nama_penyakit'] ?? 'Penyakit Tidak Terdeteksi') . '</strong></td>
    </tr>
    <tr>
        <td style="background-color:#f8f9fa;"><strong>Tingkat Kecocokan</strong></td>
        <td>' . $kecocokan . '%</td>
    </tr>
</table>';
$pdf->writeHTML($infoDiagnosa, true, false, false, false, '');
$pdf->Ln(5);

// ==========================================
// GEJALA YANG DIALAMI
// ==========================================
$pdf->SetFont('times', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(40, 167, 69);
$pdf->Cell(0, 8, ' B. GEJALA YANG DIALAMI (DIPILIH)', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('times', '', 11);

$gejalaHTML = '<ul>';
if (count($gejala) > 0) {
    foreach ($gejala as $g) {
        $gejalaHTML .= '<li style="line-height: 1.5;"><strong>[' . $g['kode_gejala'] . ']</strong> - ' . htmlspecialchars($g['nama_gejala']) . '</li>';
    }
} else {
    $gejalaHTML .= '<li>Tidak ada data gejala yang dipilih.</li>';
}
$gejalaHTML .= '</ul>';
$pdf->writeHTML($gejalaHTML, true, false, false, false, '');
$pdf->Ln(5);

// ==========================================
// KETERANGAN & SOLUSI
// ==========================================
if (!empty($diagnosa['kode_penyakit'])) {
    $pdf->SetFont('times', 'B', 12);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(40, 167, 69);
    $pdf->Cell(0, 8, ' C. KETERANGAN & SOLUSI PENANGANAN', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('times', '', 11);
    $penyakitHTML = "
    <div style='text-align: justify; line-height: 1.5;'>
        <br>
        <strong>1. Deskripsi Penyakit:</strong><br>
        " . nl2br(htmlspecialchars($diagnosa['deskripsi'] ?? 'Deskripsi tidak tersedia.')) . "<br><br>
        
        <strong>2. Tindakan Pengobatan (Solusi):</strong><br>
        " . nl2br(htmlspecialchars($diagnosa['solusi'] ?? 'Solusi tidak tersedia.')) . "<br><br>
        
        <strong>3. Langkah Pencegahan:</strong><br>
        " . nl2br(htmlspecialchars($diagnosa['pencegahan'] ?? 'Data pencegahan tidak tersedia.')) . "
    </div>";

    $pdf->writeHTML($penyakitHTML, true, false, false, false, '');
}

// ==========================================
// TANDA TANGAN PDF
// ==========================================
$pdf->Ln(15);
$pdf->SetFont('times', '', 11);
$pdf->Cell(0, 5, getTanggalTtdIndo(), 0, 1, 'R');
$pdf->Cell(0, 5, 'Pakar / Admin Sistem', 0, 1, 'R');
$pdf->Ln(20);
$pdf->Cell(0, 5, '(__________________________)', 0, 1, 'R');

// ==========================================
// TAMBAH FOOTER OTOMATIS
// ==========================================
$pdf->SetY(-15);
$pdf->SetFont('times', 'I', 9);
$pdf->Cell(0, 10, 'Dicetak oleh Sistem Pakar Diagnosis Penyakit Ikan Nila - Halaman ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

// Output PDF ke browser
$pdf->Output('Laporan_Diagnosa_' . ($diagnosa['kode_sampel'] ?? str_pad($diagnosa['id_diagnosa'], 4, '0', STR_PAD_LEFT)) . '.pdf', 'I');
