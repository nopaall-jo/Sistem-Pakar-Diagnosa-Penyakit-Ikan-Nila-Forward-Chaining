<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../config/database.php';
require_once '../vendor/autoload.php';

// Set header untuk cache control
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// PERBAIKAN: Tangkap data, bersihkan spasi, dan paksa jadi huruf kecil (mencegah bug redirect)
$format = strtolower(trim($_POST['format'] ?? $_GET['format'] ?? 'pdf'));
$jenis_laporan = strtolower(trim($_POST['jenis_laporan'] ?? $_GET['jenis_laporan'] ?? 'semua'));

// Ambil data penyakit berdasarkan jenis laporan
$query = "SELECT * FROM tbl_penyakit ORDER BY kode_penyakit ASC";
$stmt = $pdo->query($query);
$penyakit = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'pdf') {
    // Cetak PDF menggunakan TCPDF
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
    
    // Buat objek PDF baru
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // PERBAIKAN: Set dokumen meta data disesuaikan dengan Ikan Nila
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Pakar Ikan Nila');
    $pdf->SetTitle('Laporan Data Penyakit');
    $pdf->SetSubject('Data Penyakit Ikan Nila');
    
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
    <hr style="border-top: 3px solid #000; margin-top: 2px;">
    <br>
    ';
    
    $pdf->writeHTML($kop, true, false, true, false, '');
    
    // Judul laporan
    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(0, 10, 'LAPORAN DATA PENYAKIT IKAN NILA', 0, 1, 'C');
    $pdf->SetFont('times', '', 10);
    $jenisText = ($jenis_laporan === 'lengkap') ? 'Laporan Lengkap (Termasuk Deskripsi)' : 'Laporan Standar';
    $pdf->Cell(0, 5, 'Jenis Laporan: ' . $jenisText, 0, 1, 'C');
    $pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB', 0, 1, 'C');
    $pdf->Ln(5);
    
// ==========================================
    // PERBAIKAN 1: LEBAR KOLOM DINAMIS (TOTAL 100%)
    // ==========================================
    if ($jenis_laporan === 'lengkap') {
        $w_no = "5%"; $w_kode = "15%"; $w_nama = "40%"; $w_desc = "40%";
    } else {
        $w_no = "10%"; $w_kode = "20%"; $w_nama = "70%";
    }

    // Buat tabel PDF (Warna hijau muda e2efd9)
    $html = '<table border="1" cellpadding="6">
        <thead>
            <tr style="background-color:#e2efd9; font-weight:bold; text-align:center;">
                <th width="'.$w_no.'">No</th>
                <th width="'.$w_kode.'">Kode</th>
                <th width="'.$w_nama.'">Nama Penyakit</th>';
    
    if ($jenis_laporan === 'lengkap') {
        $html .= '<th width="'.$w_desc.'">Deskripsi</th>';
    }
    
    $html .= '</tr>
        </thead>
        <tbody>';
    
    if (count($penyakit) > 0) {
        foreach ($penyakit as $key => $p) {
            $html .= '<tr>
                <td width="'.$w_no.'" align="center">'.($key+1).'</td>
                <td width="'.$w_kode.'" align="center">'.htmlspecialchars($p['kode_penyakit']).'</td>
                <td width="'.$w_nama.'">'.htmlspecialchars($p['nama_penyakit']).'</td>';
            
            if ($jenis_laporan === 'lengkap') {
                $html .= '<td width="'.$w_desc.'">'.nl2br(htmlspecialchars($p['deskripsi'] ?? '-')).'</td>';
            }
            
            $html .= '</tr>';
        }
    } else {
        $colspan = ($jenis_laporan === 'lengkap') ? 4 : 3;
        $html .= '<tr><td colspan="'.$colspan.'" align="center">Tidak ada data penyakit.</td></tr>';
    }
    
    $html .= '</tbody></table>';
    
    // Output HTML ke PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Tanda tangan (Disesuaikan dengan lokasi skripsi)
    $pdf->Ln(10);
    $pdf->Cell(0, 5, getTanggalTtdIndo(), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Pakar / Admin Sistem', 0, 1, 'R');
    $pdf->Ln(20);
    $pdf->Cell(0, 5, '(__________________________)', 0, 1, 'R');
    
    // Cetak ke browser
    $pdf->Output('Laporan_Penyakit_Ikan_Nila_'.date('Ymd_His').'.pdf', 'I');
    
} elseif ($format === 'excel') {
    // Cetak Excel menggunakan PHPOffice/PhpSpreadsheet
    require_once '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
    require_once '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php';
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $lastCol = ($jenis_laporan === 'lengkap') ? 'D' : 'C';

    // ==========================================
    // KOP SURAT EXCEL & JUDUL
    // ==========================================
    $sheet->setCellValue('A1', 'SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA');
    $sheet->mergeCells('A1:' . $lastCol . '1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A2', 'Metode Forward Chaining');
    $sheet->mergeCells('A2:' . $lastCol . '2');
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A3', 'Kecamatan Bojong Gede, Kabupaten Bogor, Jawa Barat');
    $sheet->mergeCells('A3:' . $lastCol . '3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A5', 'LAPORAN DATA PENYAKIT IKAN NILA');
    $sheet->mergeCells('A5:' . $lastCol . '5');
    $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
    
    $jenisTextExcel = ($jenis_laporan === 'lengkap') ? 'Laporan Lengkap (Termasuk Deskripsi)' : 'Laporan Standar';
    $sheet->setCellValue('A6', 'Jenis Laporan: ' . $jenisTextExcel);
    $sheet->mergeCells('A6:' . $lastCol . '6');
    $sheet->getStyle('A6')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A7', 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB');
    $sheet->mergeCells('A7:' . $lastCol . '7');
    $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');
    
    // ==========================================
    // HEADER TABEL EXCEL
    // ==========================================
    $sheet->setCellValue('A9', 'No');
    $sheet->setCellValue('B9', 'Kode Penyakit');
    $sheet->setCellValue('C9', 'Nama Penyakit');
    
    if ($jenis_laporan === 'lengkap') {
        $sheet->setCellValue('D9', 'Deskripsi');
    }
    
    // Style header (Warna hijau muda e2efd9)
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFD9']]
    ];
    // Terapkan style hanya sampai kolom terakhir ($lastCol)
    $sheet->getStyle('A9:' . $lastCol . '9')->applyFromArray($headerStyle);
    
    // ==========================================
    // ISI DATA EXCEL
    // ==========================================
    $row = 10;
    foreach ($penyakit as $key => $p) {
        $sheet->setCellValue('A'.$row, $key+1);
        $sheet->setCellValue('B'.$row, $p['kode_penyakit']);
        $sheet->setCellValue('C'.$row, $p['nama_penyakit']);
        
        if ($jenis_laporan === 'lengkap') {
            $sheet->setCellValue('D'.$row, $p['deskripsi'] ?? '-');
        }
        
        // Tengah-kan kolom No dan Kode Penyakit
        $sheet->getStyle('A'.$row.':B'.$row)->getAlignment()->setHorizontal('center');
        
        $row++;
    }
    
    // ==========================================
    // AUTO SIZE & BORDERS EXCEL
    // ==========================================
    // Auto size columns (Dari A sampai lastCol)
    foreach (range('A', $lastCol) as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Set borders for data
    $dataStyle = [
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'alignment' => ['wrapText' => true]
    ];
    $sheet->getStyle('A9:' . $lastCol . ($row-1))->applyFromArray($dataStyle);
    
    // ==========================================
    // TANDA TANGAN EXCEL (Bojong Gede)
    // ==========================================
    $sheet->mergeCells('A'.($row+2).':' . $lastCol . ($row+2));
    $sheet->setCellValue('A'.($row+2), getTanggalTtdIndo());
    $sheet->getStyle('A'.($row+2))->getAlignment()->setHorizontal('right');
    
    $sheet->mergeCells('A'.($row+3).':' . $lastCol . ($row+3));
    $sheet->setCellValue('A'.($row+3), 'Pakar / Admin Sistem');
    $sheet->getStyle('A'.($row+3))->getAlignment()->setHorizontal('right');
    
    $sheet->mergeCells('A'.($row+7).':' . $lastCol . ($row+7));
    $sheet->setCellValue('A'.($row+7), '(__________________________)');
    $sheet->getStyle('A'.($row+7))->getAlignment()->setHorizontal('right');
    
    // ==========================================
    // OUTPUT KE BROWSER
    // ==========================================
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Laporan_Penyakit_Ikan_Nila_'.date('Ymd_His').'.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit();
    
} else {
    // Jika format tidak dikenali
    echo "<script>alert('Format laporan tidak valid!'); window.history.back();</script>";
    exit();
}