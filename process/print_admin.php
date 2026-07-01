<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../config/database.php';
require_once '../vendor/autoload.php';

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$format = strtolower(trim($_POST['format'] ?? $_GET['format'] ?? 'pdf'));

$stmt = $pdo->prepare("SELECT * FROM tbl_admin ORDER BY nama_admin ASC");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'pdf') {
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Pakar Ikan Nila');
    $pdf->SetTitle('Laporan Data Administrator');
    $pdf->SetSubject('Data Administrator Sistem');
    
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(15);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    $pdf->SetFont('times', '', 11);
    $pdf->AddPage();
    
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
    
    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(0, 10, 'LAPORAN DATA ADMINISTRATOR SISTEM', 0, 1, 'C');
    $pdf->SetFont('times', '', 10);
    $pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB', 0, 1, 'C');
    $pdf->Ln(5);
    
    $html = '<table border="1" cellpadding="6">
        <thead>
            <tr style="background-color:#e2efd9; font-weight:bold; text-align:center;">
                <th width="10%">No</th>
                <th width="30%">Username</th>
                <th width="60%">Nama Administrator</th>
            </tr>
        </thead>
        <tbody>';
    
    if (count($admins) > 0) {
        foreach ($admins as $key => $a) {
            $html .= '<tr>
                <td width="10%" align="center">'.($key+1).'</td>
                <td width="30%" align="center">'.htmlspecialchars($a['username']).'</td>
                <td width="60%">'.htmlspecialchars($a['nama_admin']).'</td>
            </tr>';
        }
    } else {
        $html .= '<tr><td colspan="3" align="center">Tidak ada data administrator.</td></tr>';
    }
    
    $html .= '</tbody></table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Tanda tangan
    $pdf->Ln(10);
    $pdf->Cell(0, 5, getTanggalTtdIndo(), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Pakar / Kepala Sistem', 0, 1, 'R');
    $pdf->Ln(20);
    $pdf->Cell(0, 5, '(__________________________)', 0, 1, 'R');
    
    $pdf->Output('Laporan_Admin_Sistem_'.date('YmdHis').'.pdf', 'I');
    
} elseif ($format === 'excel') {
    require_once '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
    require_once '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php';
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
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
    
    $sheet->setCellValue('A5', 'LAPORAN DATA ADMINISTRATOR SISTEM');
    $sheet->mergeCells('A5:C5');
    $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A6', 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB');
    $sheet->mergeCells('A6:C6');
    $sheet->getStyle('A6')->getAlignment()->setHorizontal('center');
    
    $sheet->setCellValue('A8', 'No');
    $sheet->setCellValue('B8', 'Username');
    $sheet->setCellValue('C8', 'Nama Administrator');
    
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFD9']]
    ];
    $sheet->getStyle('A8:C8')->applyFromArray($headerStyle);
    
    $row = 9;
    foreach ($admins as $key => $a) {
        $sheet->setCellValue('A'.$row, $key+1);
        $sheet->setCellValue('B'.$row, $a['username']);
        $sheet->setCellValue('C'.$row, $a['nama_admin']);
        
        $sheet->getStyle('A'.$row.':B'.$row)->getAlignment()->setHorizontal('center');
        $row++;
    }
    
    foreach (range('A', 'C') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    $dataStyle = [
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    ];
    $sheet->getStyle('A8:C'.($row-1))->applyFromArray($dataStyle);
    
    $sheet->mergeCells('A'.($row+2).':C'.($row+2));
    $sheet->setCellValue('A'.($row+2), getTanggalTtdIndo());
    $sheet->getStyle('A'.($row+2))->getAlignment()->setHorizontal('right');
    
    $sheet->mergeCells('A'.($row+3).':C'.($row+3));
    $sheet->setCellValue('A'.($row+3), 'Pakar / Kepala Sistem');
    $sheet->getStyle('A'.($row+3))->getAlignment()->setHorizontal('right');
    
    $sheet->mergeCells('A'.($row+7).':C'.($row+7));
    $sheet->setCellValue('A'.($row+7), '(__________________________)');
    $sheet->getStyle('A'.($row+7))->getAlignment()->setHorizontal('right');
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Laporan_Admin_Sistem_'.date('Ymd_His').'.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit();
    
} else {
    echo "<script>alert('Format laporan tidak valid!'); window.history.back();</script>";
    exit();
}