<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../config/database.php';
require_once '../vendor/autoload.php';

// Set header untuk cache control
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Mengambil parameter dari form
$format = $_POST['format'] ?? (isset($_GET['format']) ? $_GET['format'] : 'pdf');
$jenis_laporan = $_POST['jenis_laporan'] ?? (isset($_GET['jenis_laporan']) ? $_GET['jenis_laporan'] : 'semua');
$kode_gejala = $_POST['kode_gejala'] ?? [];

// 1. PERBAIKAN QUERY: Ganti 'gejala' menjadi 'tbl_gejala' dan tambahkan ORDER BY
if ($jenis_laporan === 'terpilih' && !empty($kode_gejala)) {
    $placeholders = implode(',', array_fill(0, count($kode_gejala), '?'));
    // Sinkronisasi nama tabel menjadi tbl_gejala
    $query = "SELECT * FROM tbl_gejala WHERE kode_gejala IN ($placeholders) ORDER BY kode_gejala ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($kode_gejala);
} else {
    // Sinkronisasi nama tabel menjadi tbl_gejala
    $query = "SELECT * FROM tbl_gejala ORDER BY kode_gejala ASC";
    $stmt = $pdo->query($query);
}

$gejala = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'pdf') {
    // Cetak PDF menggunakan TCPDF
    require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
    
    // Buat objek PDF baru
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // 2. PERBAIKAN META DATA: Disesuaikan dengan topik Ikan Nila
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Pakar Ikan Nila');
    $pdf->SetTitle('Laporan Data Gejala');
    $pdf->SetSubject('Data Gejala Penyakit Ikan Nila');
    
    // Set margin
    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 25);
    
    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // Set font
    $pdf->SetFont('times', '', 10);
    
    // Add a page
    $pdf->AddPage();
    
    // Kop surat, Persiapkan path logo (Gunakan realpath agar TCPDF bisa membaca gambar lokal)
    $path_logoKiri = realpath(__DIR__ . '/../assets/img/logo4.png');
    $path_logoKanan = realpath(__DIR__ . '/../assets/img/logo3.png');

    // Validasi file logo (Mencegah error 'Image not found' pada TCPDF jika file terhapus/salah folder)
    $img_kiri = file_exists($path_logoKiri) ? '<img src="' . $path_logoKiri . '" width="75">' : '';
    $img_kanan = file_exists($path_logoKanan) ? '<img src="' . $path_logoKanan . '" width="75">' : '';

    $kop = '
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr>
            <td width="15%" align="center">
                ' . $img_kiri . '
            </td>
            
            <td width="70%" align="center">
                <h3 style="margin: 0; font-weight: bold; line-height: 1.5;">SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA</h3>
                <h3 style="margin: 0; font-weight: bold; line-height: 1.5;">METODE FORWARD CHAINING</h3>
                <p style="margin: 0; font-size: 11pt; font-weight: bold;">KECAMATAN BOJONG GEDE</p>
                <p style="margin: 0; font-size: 10pt;">Kabupaten Bogor, Provinsi Jawa Barat</p>
            </td>
            
            <td width="15%" align="center">
                ' . $img_kanan . '
            </td>
        </tr>
    </table>
    
    <br>
    <hr style="border-top: 3px solid #000; margin: 0;">
    <br>
    ';

    
    $pdf->writeHTML($kop, true, false, true, false, '');
    
    // Judul laporan
    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(0, 10, 'LAPORAN DATA GEJALA IKAN NILA', 0, 1, 'C');
    $pdf->SetFont('times', '', 10);
    
    // Jenis laporan
    $jenisText = ($jenis_laporan === 'terpilih') ? 'Data Terpilih' : 'Semua Data';
    $pdf->Cell(0, 5, 'Jenis Laporan: ' . $jenisText, 0, 1);
    
    // Tanggal cetak
    $pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB', 0, 1);
    $pdf->Ln(5);
    
    // Buat tabel (Warna Header Hijau Muda khas Perikanan)
    $html = '<table border="1" cellpadding="6">
        <thead>
            <tr style="background-color:#e2efd9; font-weight:bold; text-align:center;">
                <th width="10%">No</th>
                <th width="20%">Kode Gejala</th>
                <th width="70%">Nama Gejala</th>
            </tr>
        </thead>
        <tbody>';
    
    if (count($gejala) > 0) {
        foreach ($gejala as $key => $g) {
            $html .= '<tr>
                <td width="10%" align="center">'.($key+1).'</td>
                <td width="20%" align="center">'.htmlspecialchars($g['kode_gejala']).'</td>
                <td width="70%">'.htmlspecialchars($g['nama_gejala']).'</td>
            </tr>';
        }
    } else {
        $html .= '<tr><td colspan="3" align="center">Tidak ada data gejala.</td></tr>';
    }
    
    $html .= '</tbody></table>';
    
    // Output HTML ke PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Tanda tangan (Disesuaikan dengan lokasi Bojong Gede)
    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'Bojong Gede, ' . date('d F Y'), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Pakar / Admin Sistem', 0, 1, 'R');
    $pdf->Ln(20);
    $pdf->Cell(0, 5, '(__________________________)', 0, 1, 'R');
    
    // Cetak ke browser (I = Inline/Tampil di browser, D = Download)
    $pdf->Output('Laporan_Gejala_Ikan_Nila_'.date('Ymd_His').'.pdf', 'I');
    
} elseif ($format === 'excel') {
    // Cetak Excel menggunakan PHPOffice/PhpSpreadsheet
    require_once '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
    require_once '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php';
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // [PERBAIKAN KOP SURAT EXCEL]
    $sheet->setCellValue('A1', 'SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA');
    $sheet->mergeCells('A1:C1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A2', 'Metode Forward Chaining');
    $sheet->mergeCells('A2:C2');
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A3', 'Kecamatan Bojong Gede, Kabupaten Bogor, Jawa Barat');
    $sheet->mergeCells('A3:C3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
    
    // Judul Laporan
    $sheet->setCellValue('A5', 'LAPORAN DATA GEJALA IKAN NILA');
    $sheet->mergeCells('A5:C5');
    $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A6', 'Jenis Laporan: ' . ($jenis_laporan === 'terpilih' ? 'Data Terpilih' : 'Semua Data'));
    $sheet->mergeCells('A6:C6');
    $sheet->getStyle('A6')->getAlignment()->setHorizontal('center'); // Tambahan: Rata tengah
    
    $sheet->setCellValue('A7', 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB');
    $sheet->mergeCells('A7:C7');
    $sheet->getStyle('A7')->getAlignment()->setHorizontal('center'); // Tambahan: Rata tengah
    
    // Header tabel
    $sheet->setCellValue('A9', 'No');
    $sheet->setCellValue('B9', 'Kode Gejala');
    $sheet->setCellValue('C9', 'Nama Gejala');
    
    // Style header (Diubah jadi warna hijau muda e2efd9)
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFD9']]
    ];
    $sheet->getStyle('A9:C9')->applyFromArray($headerStyle);
    
    // Isi data
    $row = 10;
    foreach ($gejala as $key => $g) {
        $sheet->setCellValue('A'.$row, $key+1);
        $sheet->setCellValue('B'.$row, $g['kode_gejala']);
        $sheet->setCellValue('C'.$row, $g['nama_gejala']);
        
        // Tengah-kan kolom No dan Kode Gejala
        $sheet->getStyle('A'.$row.':B'.$row)->getAlignment()->setHorizontal('center');
        $row++;
    }
    
    // Auto size columns
    foreach (range('A', 'C') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Set borders for data
    $dataStyle = [
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'alignment' => ['wrapText' => true]
    ];
    $sheet->getStyle('A9:C'.($row-1))->applyFromArray($dataStyle);
    
    // Tanda tangan (Disesuaikan dengan lokasi Bojong Gede)
    $sheet->mergeCells('A'.($row+2).':C'.($row+2));
    $sheet->setCellValue('A'.($row+2), 'Bojong Gede, ' . date('d F Y'));
    $sheet->getStyle('A'.($row+2))->getAlignment()->setHorizontal('right');
    
    $sheet->mergeCells('A'.($row+3).':C'.($row+3));
    $sheet->setCellValue('A'.($row+3), 'Pakar / Admin Sistem');
    $sheet->getStyle('A'.($row+3))->getAlignment()->setHorizontal('right');
    
    $sheet->mergeCells('A'.($row+7).':C'.($row+7));
    $sheet->setCellValue('A'.($row+7), '(__________________________)');
    $sheet->getStyle('A'.($row+7))->getAlignment()->setHorizontal('right');
    
    // Output ke browser
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Laporan_Gejala_Ikan_Nila_'.date('Ymd_His').'.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit();
    
} else {
    // Format tidak valid (Fallback yang lebih aman menggunakan alert)
    echo "<script>alert('Format laporan tidak valid!'); window.history.back();</script>";
    exit();
}