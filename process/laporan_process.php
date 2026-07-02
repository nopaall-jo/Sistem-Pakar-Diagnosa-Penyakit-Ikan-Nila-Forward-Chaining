<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    
    if (!empty($start_date) && !empty($end_date)) {
        $stmt = $pdo->prepare("SELECT d.tanggal_diagnosa, d.kode_sampel, p.nama_penyakit, p.kode_penyakit, d.confidence 
                              FROM tbl_diagnosa d 
                              LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                              WHERE DATE(d.tanggal_diagnosa) BETWEEN ? AND ?
                              ORDER BY d.tanggal_diagnosa DESC");
        $stmt->execute([$start_date, $end_date]);
    } else {
        $stmt = $pdo->query("SELECT d.tanggal_diagnosa, d.kode_sampel, p.nama_penyakit, p.kode_penyakit, d.confidence 
                             FROM tbl_diagnosa d 
                             LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                             ORDER BY d.tanggal_diagnosa DESC");
    }
    $laporan = $stmt->fetchAll();
    
    $periode_text = (!empty($start_date) && !empty($end_date)) 
        ? 'Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date))
        : 'Periode: Semua Riwayat';
    
    if ($_GET['action'] == 'export_excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Diagnosa_Ikan_Nila_' . date('Ymd') . '.xls"');
        
        echo '<html><head><style>
              table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
              th, td { border: 1px solid #000; padding: 8px; text-align: left; }
              th { background-color: #e2efd9; font-weight: bold; text-align: center; }
              </style></head><body>';
        
        echo '<table border="0" style="width: 100%; border: none;">
                <tr>
                    <td colspan="5" style="text-align: center; border: none;">
                        <h3 style="margin: 0;">SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA</h3>
                        <p style="margin: 0;">Metode Forward Chaining</p>
                        <p style="margin: 0;">Kecamatan Bojong Gede, Kabupaten Bogor, Jawa Barat</p>
                    </td>
                </tr>
                <tr><td colspan="5" style="border-bottom: 2px solid #000; border-top: none; border-left: none; border-right: none;"></td></tr>
                <tr><td colspan="5" style="border: none; height: 10px;"></td></tr>
                <tr>
                    <td colspan="5" style="text-align: center; border: none;">
                        <h2>LAPORAN HASIL DIAGNOSA BERDASARKAN KODE SAMPEL</h2>
                        <p>' . $periode_text . '</p>
                    </td>
                </tr>
              </table>';
        
        echo '<table border="1">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="30%">Kode Sampel</th>
                    <th width="30%">Hasil Penyakit</th>
                    <th width="20%">Tingkat Akurasi</th>
                </tr>';
        
        $no = 1;
        if (count($laporan) > 0) {
            foreach ($laporan as $row) {
                $akurasi = round(($row['confidence'] * 100), 1) . '%';
                $nama_penyakit = $row['nama_penyakit'] ? htmlspecialchars($row['nama_penyakit']) : 'Tidak Terdeteksi';
                
                echo "<tr>
                        <td style='text-align: center;'>" . $no++ . "</td>
                        <td style='text-align: center;'>" . date('d/m/Y H:i', strtotime($row['tanggal_diagnosa'])) . "</td>
                        <td>" . htmlspecialchars($row['kode_sampel']) . "</td>
                        <td>" . $nama_penyakit . "</td>
                        <td style='text-align: center;'>" . $akurasi . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align: center;'>Tidak ada data diagnosa pada periode ini</td></tr>";
        }
        echo "</table>";
        
        echo '<table border="0" style="width: 100%; border: none; margin-top: 30px;">
                <tr>
                    <td colspan="3" style="border: none;"></td>
                    <td colspan="2" style="text-align: center; border: none;">
                        <p>' . getTanggalTtdIndo() . '</p>
                        <p>Admin Sistem / Pakar</p>
                        <br><br><br><br>
                        <p><strong>(__________________________)</strong></p>
                    </td>
                </tr>
              </table>';
        echo '</body></html>';
        exit();
    } 
    
    elseif ($_GET['action'] == 'export_pdf') {
        require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistem Pakar Ikan Nila');
        $pdf->SetTitle('Laporan Diagnosa Penyakit');
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false); 
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', '', 10);
        
        $path_logoKiri = realpath(__DIR__ . '/../assets/img/Logo2.png');
        $path_logoKanan = realpath(__DIR__ . '/../assets/img/logo3.png');
        
        $img_kiri = file_exists($path_logoKiri) ? '<img src="' . $path_logoKiri . '" width="70">' : '';
        $img_kanan = file_exists($path_logoKanan) ? '<img src="' . $path_logoKanan . '" width="70">' : '';
        
        $kop_surat = '
        <table border="0" cellpadding="2" cellspacing="0" width="100%">
            <tr>
                <td width="12%" align="center">
                    ' . $img_kiri . '
                </td>
                <td width="76%" align="center">
                    <h2 style="margin: 0; font-weight: bold; line-height: 1.1; color: #002d27; font-size: 15pt;">DZAWIL GARDEN OFFICE FARM</h2>
                    <h4 style="margin: 0; font-weight: bold; line-height: 1.2; font-size: 11pt;">SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA</h4>
                    <h5 style="margin: 0; font-weight: bold; line-height: 1.2; color: #555555; font-size: 9pt;">METODE FORWARD CHAINING</h5>
                    <p style="margin: 0; font-size: 8pt; color: #444444; line-height: 1.2; margin-top: 4px;">
                        Jl. H. Sena, Ragajaya Citayam, Kecamatan Bojonggede, Kabupaten Bogor, Jawa Barat 16920<br>
                        Telp: 0852-1010-0139
                    </p>
                </td>
                <td width="12%" align="center">
                    ' . $img_kanan . '
                </td>
            </tr>
        </table>
        <hr style="border-top: 2px solid #000; margin: 0; padding: 0; height: 1px;">
        <hr style="border-top: 1px solid #000; margin: 0; padding: 0; height: 1px; margin-top: 2px;">
        <br>
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin-bottom: 0;">LAPORAN HASIL DIAGNOSA</h3>
            <p style="margin-top: 5px;">' . $periode_text . '</p>
        </div>';
        $pdf->writeHTML($kop_surat, true, false, false, false, '');
        
        $tbl = '
        <table border="0.5" bordercolor="#dddddd" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr style="background-color:#f8f9fa; font-weight:bold; text-align:center; color:#333333;">
                    <th width="6%">No</th>
                    <th width="20%">Tanggal & Jam</th>
                    <th width="24%">Kode Sampel</th>
                    <th width="28%">Hasil Penyakit</th>
                    <th width="10%">Kode</th>
                    <th width="12%">Akurasi</th>
                </tr>
            </thead>
            <tbody>';
        
        $no = 1;
        if (count($laporan) > 0) {
            foreach ($laporan as $row) {
                $confidence = round(($row['confidence'] * 100), 1);
                $nama_penyakit = $row['nama_penyakit'] ? htmlspecialchars($row['nama_penyakit']) : 'Tidak Terdeteksi';
                $kode_penyakit = $row['kode_penyakit'] ? $row['kode_penyakit'] : '-';
                
                $tbl .= '
                    <tr>
                        <td width="6%" align="center">' . $no++ . '</td>
                        <td width="20%" align="center">' . date('d/m/Y H:i', strtotime($row['tanggal_diagnosa'])) . '</td>
                        <td width="24%">' . htmlspecialchars($row['kode_sampel']) . '</td>
                        <td width="28%">' . $nama_penyakit . '</td>
                        <td width="10%" align="center">' . $kode_penyakit . '</td>
                        <td width="12%" align="center"><strong>' . $confidence . '%</strong></td>
                    </tr>';
            }
        } else {
            $tbl .= '<tr><td colspan="6" align="center"><i>Tidak ada riwayat diagnosa pada periode tanggal yang dipilih.</i></td></tr>';
        }
        $tbl .= '</tbody></table>';
        
        $pdf->writeHTML($tbl, true, false, false, false, '');
        
        $ttd = '
        <br><br>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="70%"></td>
                <td width="30%" align="center">
                    <p>' . getTanggalTtdIndo() . '</p>
                    <p>Pakar / Admin Sistem</p>
                    <br><br><br>
                    <p><strong>(________________________)</strong></p>
                </td>
            </tr>
        </table>';
        $pdf->writeHTML($ttd, true, false, false, false, '');
        
        // Tampilkan PDF di Browser (buka tab baru)
        $pdf->Output('Laporan_Ikan_Nila_' . date('Ymd_His') . '.pdf', 'I');
        exit();
    }
}

header("Location: ../pages/admin/laporan.php");
exit();
?>