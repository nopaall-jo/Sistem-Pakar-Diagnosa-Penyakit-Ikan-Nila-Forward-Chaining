<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    $start_date = $_GET['start_date'] ?? date('Y-m-01');
    $end_date = $_GET['end_date'] ?? date('Y-m-t');
    
    // [1] SINKRONISASI KUERI UTAMA: Berlaku untuk Excel maupun PDF
    // Menggunakan tbl_diagnosa, nama_pembudidaya, hasil_penyakit
    $stmt = $pdo->prepare("SELECT d.tanggal_diagnosa, d.nama_pembudidaya, p.nama_penyakit, p.kode_penyakit, d.confidence 
                          FROM tbl_diagnosa d 
                          LEFT JOIN tbl_penyakit p ON d.hasil_penyakit = p.kode_penyakit 
                          WHERE DATE(d.tanggal_diagnosa) BETWEEN ? AND ?
                          ORDER BY d.tanggal_diagnosa DESC");
    $stmt->execute([$start_date, $end_date]);
    $laporan = $stmt->fetchAll();
    
    // ==========================================
    // [2] LOGIKA EXPORT EXCEL
    // ==========================================
    if ($_GET['action'] == 'export_excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Diagnosa_Ikan_Nila_' . date('Ymd') . '.xls"');
        
        echo '<html><head><style>
              table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
              th, td { border: 1px solid #000; padding: 8px; text-align: left; }
              th { background-color: #e2efd9; font-weight: bold; text-align: center; }
              </style></head><body>';
        
        // Kop Surat Excel
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
                        <h2>LAPORAN HASIL DIAGNOSA</h2>
                        <p>Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)) . '</p>
                    </td>
                </tr>
              </table>';
        
        // Tabel Data Excel
        echo '<table border="1">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="30%">Nama Pembudidaya</th>
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
                        <td>" . htmlspecialchars($row['nama_pembudidaya']) . "</td>
                        <td>" . $nama_penyakit . "</td>
                        <td style='text-align: center;'>" . $akurasi . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align: center;'>Tidak ada data diagnosa pada periode ini</td></tr>";
        }
        echo "</table>";
        
        // Tanda Tangan Excel
        echo '<table border="0" style="width: 100%; border: none; margin-top: 30px;">
                <tr>
                    <td colspan="3" style="border: none;"></td>
                    <td colspan="2" style="text-align: center; border: none;">
                        <p>Bojong Gede, ' . date('d F Y') . '</p>
                        <p>Admin Sistem / Pakar</p>
                        <br><br><br><br>
                        <p><strong>(__________________________)</strong></p>
                    </td>
                </tr>
              </table>';
        echo '</body></html>';
        exit();
    } 
    
    // ==========================================
    // [3] LOGIKA EXPORT PDF (Menggunakan TCPDF)
    // ==========================================
    elseif ($_GET['action'] == 'export_pdf') {
        // Pastikan path ke tcpdf.php ini sudah benar sesuai struktur folder kamu!
        require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Inisialisasi TCPDF (Landscape, A4)
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
        
        // Kop Surat PDF
        $kop_surat = '
        <table border="0" cellpadding="2" cellspacing="0" width="100%">
            <tr>
                <td width="100%" align="center">
                    <h2 style="margin: 0; padding: 0; font-weight: bold;">SISTEM PAKAR DIAGNOSIS PENYAKIT IKAN NILA</h2>
                    <h4 style="margin: 0; padding: 0; font-weight: bold;">Metode Forward Chaining</h4>
                    <p style="margin: 0; padding: 0;">Kecamatan Bojong Gede, Kabupaten Bogor, Jawa Barat</p>
                </td>
            </tr>
        </table>
        <hr style="border-top: 2px solid #000;"><br>
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin-bottom: 0;">LAPORAN HASIL DIAGNOSA</h3>
            <p style="margin-top: 5px;">Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)) . '</p>
        </div>';
        $pdf->writeHTML($kop_surat, true, false, false, false, '');
        
        // Tabel Data PDF
        $tbl = '
        <table border="1" cellpadding="6" width="100%">
            <thead>
                <tr style="background-color:#e2efd9; font-weight:bold; text-align:center;">
                    <th width="5%">No</th>
                    <th width="20%">Tanggal & Jam</th>
                    <th width="25%">Nama Pembudidaya</th>
                    <th width="25%">Hasil Penyakit</th>
                    <th width="10%">Kode</th>
                    <th width="15%">Akurasi</th>
                </tr>
            </thead>
            <tbody>';
        
        $no = 1;
        if (count($laporan) > 0) {
            foreach ($laporan as $row) {
                $confidence = round(($row['confidence'] * 100), 1);
                
                $row_color = '';
                if ($confidence >= 75) { $row_color = 'background-color: #d4edda;'; } 
                elseif ($confidence >= 50) { $row_color = 'background-color: #fff3cd;'; } 
                else { $row_color = 'background-color: #f8d7da;'; }
                
                $nama_penyakit = $row['nama_penyakit'] ? htmlspecialchars($row['nama_penyakit']) : 'Tidak Terdeteksi';
                $kode_penyakit = $row['kode_penyakit'] ? $row['kode_penyakit'] : '-';
                
                $tbl .= '
                    <tr style="' . $row_color . '">
                        <td width="5%" align="center">' . $no++ . '</td>
                        <td width="20%" align="center">' . date('d/m/Y H:i', strtotime($row['tanggal_diagnosa'])) . '</td>
                        <td width="25%">' . htmlspecialchars($row['nama_pembudidaya']) . '</td>
                        <td width="25%">' . $nama_penyakit . '</td>
                        <td width="10%" align="center">' . $kode_penyakit . '</td>
                        <td width="15%" align="center"><strong>' . $confidence . '%</strong></td>
                    </tr>';
            }
        } else {
            $tbl .= '<tr><td colspan="6" align="center"><i>Tidak ada riwayat diagnosa pada periode tanggal yang dipilih.</i></td></tr>';
        }
        $tbl .= '</tbody></table>';
        
        $pdf->writeHTML($tbl, true, false, false, false, '');
        
        // Tanda Tangan PDF
        $ttd = '
        <br><br><br>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="65%"></td>
                <td width="35%" align="center">
                    <p>Bojong Gede, ' . date('d F Y') . '</p>
                    <p>Pakar / Admin Sistem</p>
                    <br><br><br><br>
                    <p><strong>(________________________)</strong></p>
                </td>
            </tr>
        </table>';
        $pdf->writeHTML($ttd, true, false, false, false, '');
        
        // Unduh PDF
        $pdf->Output('Laporan_Ikan_Nila_' . date('Ymd_His') . '.pdf', 'D');
        exit();
    }
}

// Jika diakses secara langsung tanpa action, tendang balik ke halaman laporan
header("Location: ../pages/admin/laporan.php");
exit();
?>