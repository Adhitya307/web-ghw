<?php

namespace App\Controllers\HDM;

use App\Controllers\BaseController;
use App\Models\HDM\MPengukuranHdm;
use App\Models\HDM\MPembacaanElv625;
use App\Models\HDM\MPembacaanElv600;
use App\Models\HDM\MPergerakanElv625;
use App\Models\HDM\MPergerakanElv600;
use App\Models\HDM\MInitialReadingElv625;
use App\Models\HDM\MInitialReadingElv600;
use App\Models\HDM\DepthElv625Model;
use App\Models\HDM\DepthElv600Model;
use App\Models\HDM\AmbangBatas600H1Model;
use App\Models\HDM\AmbangBatas600H2Model;
use App\Models\HDM\AmbangBatas600H3Model;
use App\Models\HDM\AmbangBatas600H4Model;
use App\Models\HDM\AmbangBatas600H5Model;
use App\Models\HDM\AmbangBatas625H1Model;
use App\Models\HDM\AmbangBatas625H2Model;
use App\Models\HDM\AmbangBatas625H3Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ExportExcelController extends BaseController
{
    protected $pengukuranModel;
    protected $pembacaanElv625Model;
    protected $pembacaanElv600Model;
    protected $pergerakanElv625Model;
    protected $pergerakanElv600Model;
    protected $initialReadingElv625Model;
    protected $initialReadingElv600Model;
    protected $depthElv625Model;
    protected $depthElv600Model;
    
    // Models untuk ambang batas
    protected $ambangBatas600H1Model;
    protected $ambangBatas600H2Model;
    protected $ambangBatas600H3Model;
    protected $ambangBatas600H4Model;
    protected $ambangBatas600H5Model;
    protected $ambangBatas625H1Model;
    protected $ambangBatas625H2Model;
    protected $ambangBatas625H3Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Cek login
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        try {
            // Initialize models
            $this->pengukuranModel = new MPengukuranHdm();
            $this->pembacaanElv625Model = new MPembacaanElv625();
            $this->pembacaanElv600Model = new MPembacaanElv600();
            $this->pergerakanElv625Model = new MPergerakanElv625();
            $this->pergerakanElv600Model = new MPergerakanElv600();
            $this->initialReadingElv625Model = new MInitialReadingElv625();
            $this->initialReadingElv600Model = new MInitialReadingElv600();
            $this->depthElv625Model = new DepthElv625Model();
            $this->depthElv600Model = new DepthElv600Model();
            
            // Initialize ambang batas models
            $this->ambangBatas600H1Model = new AmbangBatas600H1Model();
            $this->ambangBatas600H2Model = new AmbangBatas600H2Model();
            $this->ambangBatas600H3Model = new AmbangBatas600H3Model();
            $this->ambangBatas600H4Model = new AmbangBatas600H4Model();
            $this->ambangBatas600H5Model = new AmbangBatas600H5Model();
            $this->ambangBatas625H1Model = new AmbangBatas625H1Model();
            $this->ambangBatas625H2Model = new AmbangBatas625H2Model();
            $this->ambangBatas625H3Model = new AmbangBatas625H3Model();
            
        } catch (\Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
            die("Error loading models: " . $e->getMessage());
        }
    }

    /**
     * Export Excel dengan header yang sama seperti di view
     */
    public function export()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        try {
            // Get filter parameters
            $tahun = $this->request->getGet('tahun');
            $periode = $this->request->getGet('periode');
            
            // Build query dengan filter
            $query = $this->pengukuranModel->orderBy('tahun', 'ASC')
                                           ->orderBy('tanggal', 'ASC');
            
            // Apply filters if provided
            if (!empty($tahun)) {
                $query->where('tahun', $tahun);
            }
            
            if (!empty($periode)) {
                $query->where('periode', $periode);
            }
            
            $pengukuran = $query->findAll();

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            
            // ===== SHEET 1: DATA HDM KONSOLIDASI =====
            $consolidatedSheet = $spreadsheet->getActiveSheet();
            $consolidatedSheet->setTitle('Data HDM');
            $this->createConsolidatedSheet($consolidatedSheet, $pengukuran);
            
            // ===== SHEET 2: HDM 600 (TAMPILAN SEPERTI VIEW) =====
            $hdm600Sheet = $spreadsheet->createSheet();
            $hdm600Sheet->setTitle('HDM 600');
            $this->createHdm600Sheet($hdm600Sheet, $pengukuran);
            
            // ===== SHEET 3: HDM 625 (TAMPILAN SEPERTI VIEW) =====
            $hdm625Sheet = $spreadsheet->createSheet();
            $hdm625Sheet->setTitle('HDM 625');
            $this->createHdm625Sheet($hdm625Sheet, $pengukuran);
            
            // ===== SAVE FILE =====
            $writer = new Xlsx($spreadsheet);
            $filename = 'HDM_Data_Export_' . date('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting Excel: ' . $e->getMessage());
            
            // Jika AJAX request, kembalikan JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengexport: ' . $e->getMessage()
                ]);
            }
            
            // Jika bukan AJAX, redirect dengan flashdata
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengexport: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    /**
     * Create consolidated sheet
     */
    private function createConsolidatedSheet($sheet, $pengukuran)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // ===== HEADER UTAMA DAN SUBHEADER =====
        $lastCol = 'AJ'; // Total 36 kolom (A-AJ)
        
        // Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'DATA HORIZONTAL DISPLACEMENT METER - PT INDONESIA POWER');
        $this->applyTitleStyle($sheet, 'A1:' . $lastCol . '1');
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Sub Judul
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA HORIZONTAL DISPLACEMENT METER');
        $this->applyTitleStyle($sheet, 'A2:' . $lastCol . '2');
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Informasi Tanggal Ekspor
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s'));
        $this->applySubtitleStyle($sheet, 'A3:' . $lastCol . '3');
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // ===== HEADER TABEL (SAMA SEPERTI DI VIEW) =====
        // Row 4-6: Table Headers (3 baris)
        $this->createHDMTableHeaders($sheet);
        
        // ===== ISI DATA =====
        $row = 7; // Mulai dari row 7
        foreach ($pengukuran as $index => $p) {
            $pid = $p['id_pengukuran'];
            
            $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $pid)->first();
            $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $pid)->first();
            $pergerakanElv600 = $this->pergerakanElv600Model->where('id_pengukuran', $pid)->first();
            $pergerakanElv625 = $this->pergerakanElv625Model->where('id_pengukuran', $pid)->first();
            $initialReadingElv600 = $this->initialReadingElv600Model->where('id_pengukuran', $pid)->first();
            $initialReadingElv625 = $this->initialReadingElv625Model->where('id_pengukuran', $pid)->first();
            $depthElv600 = $this->depthElv600Model->where('id_pengukuran', $pid)->first();
            $depthElv625 = $this->depthElv625Model->where('id_pengukuran', $pid)->first();
            
            // Format tanggal
            $displayDate = $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
            
            // Format nilai numerik dengan 2 digit di belakang koma
            $formatNumber = function($value) {
                if ($value === null || $value === '' || $value === '-') {
                    return '-';
                }
                
                if (is_numeric($value)) {
                    return number_format((float)$value, 2, '.', '');
                }
                
                return $value;
            };
            
            $rowData = [
                // Basic Info
                $p['tahun'] ?? '-',
                $p['periode'] ?? '-',
                $displayDate,
                $formatNumber($p['dma'] ?? '-'),
                
                // PEMBACAAN HDM - ELV 625
                $formatNumber($pembacaanElv625['hv_1'] ?? '-'),
                $formatNumber($pembacaanElv625['hv_2'] ?? '-'),
                $formatNumber($pembacaanElv625['hv_3'] ?? '-'),
                
                // PEMBACAAN HDM - ELV 600
                $formatNumber($pembacaanElv600['hv_1'] ?? '-'),
                $formatNumber($pembacaanElv600['hv_2'] ?? '-'),
                $formatNumber($pembacaanElv600['hv_3'] ?? '-'),
                $formatNumber($pembacaanElv600['hv_4'] ?? '-'),
                $formatNumber($pembacaanElv600['hv_5'] ?? '-'),
                
                // DEPTH (S) - ELV 625
                $formatNumber($depthElv625['hv_1'] ?? '-'),
                $formatNumber($depthElv625['hv_2'] ?? '-'),
                $formatNumber($depthElv625['hv_3'] ?? '-'),
                
                // DEPTH (S) - ELV 600
                $formatNumber($depthElv600['hv_1'] ?? '-'),
                $formatNumber($depthElv600['hv_2'] ?? '-'),
                $formatNumber($depthElv600['hv_3'] ?? '-'),
                $formatNumber($depthElv600['hv_4'] ?? '-'),
                $formatNumber($depthElv600['hv_5'] ?? '-'),
                
                // READINGS (S) - ELV 625
                $formatNumber($initialReadingElv625['hv_1'] ?? '-'),
                $formatNumber($initialReadingElv625['hv_2'] ?? '-'),
                $formatNumber($initialReadingElv625['hv_3'] ?? '-'),
                
                // READINGS (S) - ELV 600
                $formatNumber($initialReadingElv600['hv_1'] ?? '-'),
                $formatNumber($initialReadingElv600['hv_2'] ?? '-'),
                $formatNumber($initialReadingElv600['hv_3'] ?? '-'),
                $formatNumber($initialReadingElv600['hv_4'] ?? '-'),
                $formatNumber($initialReadingElv600['hv_5'] ?? '-'),
                
                // PERGERAKAN (CM) - ELV 625
                $formatNumber($pergerakanElv625['hv_1'] ?? '-'),
                $formatNumber($pergerakanElv625['hv_2'] ?? '-'),
                $formatNumber($pergerakanElv625['hv_3'] ?? '-'),
                
                // PERGERAKAN (CM) - ELV 600
                $formatNumber($pergerakanElv600['hv_1'] ?? '-'),
                $formatNumber($pergerakanElv600['hv_2'] ?? '-'),
                $formatNumber($pergerakanElv600['hv_3'] ?? '-'),
                $formatNumber($pergerakanElv600['hv_4'] ?? '-'),
                $formatNumber($pergerakanElv600['hv_5'] ?? '-')
            ];
            
            // Set data ke row
            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            
            // Alternating row color untuk readability
            $rowStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];
            
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($rowStyle);
            
            // Row warna alternating (zebra stripes)
            if ($index % 2 == 0) {
                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFF8F9FA');
            }
            
            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        
        // ===== STYLING KOLOM =====
        $this->applyColumnStyling($sheet, $row);
        
        // ===== FORMAT ANGKA 2 DESIMAL =====
        if ($row > 7) {
            // Format semua kolom numerik (D sampai AJ) dengan 2 desimal
            $numericRange = 'D7:AJ' . ($row - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // Atur alignment untuk kolom numerik
            $sheet->getStyle('D7:AJ' . ($row - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        
        // ===== FREEZE PANES =====
        // Freeze kolom A-D (Tahun, Periode, Tanggal, DAM) dan baris 1-6 (header)
        $sheet->freezePane('E7'); // Kolom E adalah kolom pertama setelah DAM (kolom D)
        
        // ===== FOOTER =====
        $footerRow = $row + 1;
        $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 'Sistem Monitoring Horizontal Displacement Meter - PT Indonesia Power');
        $sheet->getStyle('A' . $footerRow)->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['argb' => 'FF666666']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2']
            ]
        ]);
        $sheet->getRowDimension($footerRow)->setRowHeight(30);
    }
    
    /**
     * Create HDM 600 Sheet (Tampilan seperti view hdm_600.php)
     */
    private function createHdm600Sheet($sheet, $pengukuran)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // ===== HEADER UTAMA =====
        $sheet->mergeCells('A1:AH1');
        $sheet->setCellValue('A1', 'HORIZONTAL DISPLACEMENT METER - ELV 600');
        $this->applyTitleStyle($sheet, 'A1:AH1');
        $sheet->getRowDimension(1)->setRowHeight(40);
        
        // ===== HEADER TABEL (SAMA SEPERTI VIEW hdm_600.php) =====
        // Baris 2-6: Header tabel (5 baris header seperti di view)
        $this->createHdm600TableHeaders($sheet);
        
        // ===== ISI DATA =====
        $row = 7; // Mulai dari row 7 (setelah 6 baris header)
        
        // Group data by tahun
        $groupedData = [];
        foreach ($pengukuran as $item) {
            $tahun = $item['tahun'];
            if (!isset($groupedData[$tahun])) {
                $groupedData[$tahun] = [];
            }
            $groupedData[$tahun][] = $item;
        }
        
        // Urutkan tahun secara ascending
        ksort($groupedData);
        
        $rowCounter = 0;
        foreach ($groupedData as $tahun => $itemsInYear) {
            $rowCount = count($itemsInYear);
            $firstRowInYear = $row;
            
            // Urutkan berdasarkan tanggal
            usort($itemsInYear, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($itemsInYear as $index => $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil semua data yang dibutuhkan
                $pembacaan = $this->pembacaanElv600Model->where('id_pengukuran', $pid)->first();
                $pergerakan = $this->pergerakanElv600Model->where('id_pengukuran', $pid)->first();
                $depth = $this->depthElv600Model->where('id_pengukuran', $pid)->first();
                $initialReading = $this->initialReadingElv600Model->where('id_pengukuran', $pid)->first();
                
                // Ambil ambang batas
                $ambangH1 = $this->ambangBatas600H1Model->getByPengukuran($pid);
                $ambangH2 = $this->ambangBatas600H2Model->getByPengukuran($pid);
                $ambangH3 = $this->ambangBatas600H3Model->getByPengukuran($pid);
                $ambangH4 = $this->ambangBatas600H4Model->getByPengukuran($pid);
                $ambangH5 = $this->ambangBatas600H5Model->getByPengukuran($pid);
                
                // Hitung pergerakan untuk ambang batas (sudah dikali 10)
                $pergerakan_hv1 = $pergerakan['hv_1'] ?? 0;
                $pergerakan_hv2 = $pergerakan['hv_2'] ?? 0;
                $pergerakan_hv3 = $pergerakan['hv_3'] ?? 0;
                $pergerakan_hv4 = $pergerakan['hv_4'] ?? 0;
                $pergerakan_hv5 = $pergerakan['hv_5'] ?? 0;
                
                $pergerakan_ambang_hv1 = $ambangH1['pergerakan'] ?? ($pergerakan_hv1 * 10);
                $pergerakan_ambang_hv2 = $ambangH2['pergerakan'] ?? ($pergerakan_hv2 * 10);
                $pergerakan_ambang_hv3 = $ambangH3['pergerakan'] ?? ($pergerakan_hv3 * 10);
                $pergerakan_ambang_hv4 = $ambangH4['pergerakan'] ?? ($pergerakan_hv4 * 10);
                $pergerakan_ambang_hv5 = $ambangH5['pergerakan'] ?? ($pergerakan_hv5 * 10);
                
                // Format nilai dengan 2 digit di belakang koma
                $formatNumber = function($value, $decimals = 2) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    if (is_numeric($value)) {
                        return number_format((float)$value, $decimals, '.', '');
                    }
                    return $value;
                };
                
                // Helper function untuk menentukan warna status seperti di view
                $getStatusColor = function($pergerakanValue, $depthType) {
                    if ($pergerakanValue === null || $pergerakanValue === '' || $pergerakanValue === '-') {
                        return 'FFFFFFFF'; // Warna default
                    }
                    
                    $pergerakanValue = floatval($pergerakanValue);
                    
                    switch($depthType) {
                        case 'H1':
                            $aman = -44.29;
                            $peringatan = -51.11;
                            $bahaya = -60.40;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA'; // Hijau muda untuk Aman
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA'; // Merah muda untuk Bahaya
                            } else {
                                return 'FFFFF3CD'; // Kuning muda untuk Peringatan
                            }
                            
                        case 'H2':
                            $aman = -39.75;
                            $peringatan = -45.86;
                            $bahaya = -54.20;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA';
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA';
                            } else {
                                return 'FFFFF3CD';
                            }
                            
                        case 'H3':
                            $aman = -40.63;
                            $peringatan = -46.88;
                            $bahaya = -55.40;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA';
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA';
                            } else {
                                return 'FFFFF3CD';
                            }
                            
                        case 'H4':
                            $aman = -24.86;
                            $peringatan = -28.68;
                            $bahaya = -33.90;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA';
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA';
                            } else {
                                return 'FFFFF3CD';
                            }
                            
                        case 'H5':
                            $aman = -11.22;
                            $peringatan = -12.95;
                            $bahaya = -15.30;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA';
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA';
                            } else {
                                return 'FFFFF3CD';
                            }
                            
                        default:
                            return 'FFFFFFFF'; // Putih
                    }
                };
                
                // Set nilai default jika ambang batas tidak ada
                $defaultAmbangH1 = ['aman' => -44.29, 'peringatan' => -51.11, 'bahaya' => -60.40];
                $defaultAmbangH2 = ['aman' => -39.75, 'peringatan' => -45.86, 'bahaya' => -54.20];
                $defaultAmbangH3 = ['aman' => -40.63, 'peringatan' => -46.88, 'bahaya' => -55.40];
                $defaultAmbangH4 = ['aman' => -24.86, 'peringatan' => -28.68, 'bahaya' => -33.90];
                $defaultAmbangH5 = ['aman' => -11.22, 'peringatan' => -12.95, 'bahaya' => -15.30];
                
                // TAHUN (Rowspan jika pertama dalam grup)
                if ($index === 0) {
                    $sheet->setCellValue('A' . $row, $tahun);
                    if ($rowCount > 1) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + $rowCount - 1));
                    }
                    // Apply style untuk tahun
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                }
                
                // PERIODE dan TANGGAL
                $sheet->setCellValue('B' . $row, $p['periode'] ?? '-');
                $sheet->setCellValue('C' . $row, date('d-m-Y', strtotime($p['tanggal'])));
                
                // HV1 Data
                $sheet->setCellValue('D' . $row, $formatNumber($pembacaan['hv_1'] ?? '-'));
                $sheet->setCellValue('E' . $row, $formatNumber($pergerakan_ambang_hv1));
                $sheet->setCellValue('F' . $row, $formatNumber($ambangH1['aman'] ?? $defaultAmbangH1['aman']));
                $sheet->setCellValue('G' . $row, $formatNumber($ambangH1['peringatan'] ?? $defaultAmbangH1['peringatan']));
                $sheet->setCellValue('H' . $row, $formatNumber($ambangH1['bahaya'] ?? $defaultAmbangH1['bahaya']));
                $sheet->setCellValue('I' . $row, $formatNumber($pergerakan_ambang_hv1));
                
                // HV2 Data
                $sheet->setCellValue('J' . $row, $formatNumber($pembacaan['hv_2'] ?? '-'));
                $sheet->setCellValue('K' . $row, $formatNumber($pergerakan_ambang_hv2));
                $sheet->setCellValue('L' . $row, $formatNumber($ambangH2['aman'] ?? $defaultAmbangH2['aman']));
                $sheet->setCellValue('M' . $row, $formatNumber($ambangH2['peringatan'] ?? $defaultAmbangH2['peringatan']));
                $sheet->setCellValue('N' . $row, $formatNumber($ambangH2['bahaya'] ?? $defaultAmbangH2['bahaya']));
                $sheet->setCellValue('O' . $row, $formatNumber($pergerakan_ambang_hv2));
                
                // HV3 Data
                $sheet->setCellValue('P' . $row, $formatNumber($pembacaan['hv_3'] ?? '-'));
                $sheet->setCellValue('Q' . $row, $formatNumber($pergerakan_ambang_hv3));
                $sheet->setCellValue('R' . $row, $formatNumber($ambangH3['aman'] ?? $defaultAmbangH3['aman']));
                $sheet->setCellValue('S' . $row, $formatNumber($ambangH3['peringatan'] ?? $defaultAmbangH3['peringatan']));
                $sheet->setCellValue('T' . $row, $formatNumber($ambangH3['bahaya'] ?? $defaultAmbangH3['bahaya']));
                $sheet->setCellValue('U' . $row, $formatNumber($pergerakan_ambang_hv3));
                
                // HV4 Data
                $sheet->setCellValue('V' . $row, $formatNumber($pembacaan['hv_4'] ?? '-'));
                $sheet->setCellValue('W' . $row, $formatNumber($pergerakan_ambang_hv4));
                $sheet->setCellValue('X' . $row, $formatNumber($ambangH4['aman'] ?? $defaultAmbangH4['aman']));
                $sheet->setCellValue('Y' . $row, $formatNumber($ambangH4['peringatan'] ?? $defaultAmbangH4['peringatan']));
                $sheet->setCellValue('Z' . $row, $formatNumber($ambangH4['bahaya'] ?? $defaultAmbangH4['bahaya']));
                $sheet->setCellValue('AA' . $row, $formatNumber($pergerakan_ambang_hv4));
                
                // HV5 Data
                $sheet->setCellValue('AB' . $row, $formatNumber($pembacaan['hv_5'] ?? '-'));
                $sheet->setCellValue('AC' . $row, $formatNumber($pergerakan_ambang_hv5));
                $sheet->setCellValue('AD' . $row, $formatNumber($ambangH5['aman'] ?? $defaultAmbangH5['aman']));
                $sheet->setCellValue('AE' . $row, $formatNumber($ambangH5['peringatan'] ?? $defaultAmbangH5['peringatan']));
                $sheet->setCellValue('AF' . $row, $formatNumber($ambangH5['bahaya'] ?? $defaultAmbangH5['bahaya']));
                $sheet->setCellValue('AG' . $row, $formatNumber($pergerakan_ambang_hv5));
                
                // MA.Waduk
                $sheet->setCellValue('AH' . $row, $formatNumber($p['dma'] ?? '-'));
                
                // Apply warna untuk kolom pembacaan dan pergerakan (abu-abu muda)
                $grayCells = ['D', 'E', 'J', 'K', 'P', 'Q', 'V', 'W', 'AB', 'AC'];
                foreach ($grayCells as $col) {
                    $sheet->getStyle($col . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFF8F9FA');
                }
                
                // Apply warna untuk kolom ambang batas berdasarkan nilainya
                $this->applyAmbangBatasColor($sheet, 'F' . $row, $ambangH1['aman'] ?? $defaultAmbangH1['aman'], $defaultAmbangH1);
                $this->applyAmbangBatasColor($sheet, 'G' . $row, $ambangH1['peringatan'] ?? $defaultAmbangH1['peringatan'], $defaultAmbangH1);
                $this->applyAmbangBatasColor($sheet, 'H' . $row, $ambangH1['bahaya'] ?? $defaultAmbangH1['bahaya'], $defaultAmbangH1);
                
                $this->applyAmbangBatasColor($sheet, 'L' . $row, $ambangH2['aman'] ?? $defaultAmbangH2['aman'], $defaultAmbangH2);
                $this->applyAmbangBatasColor($sheet, 'M' . $row, $ambangH2['peringatan'] ?? $defaultAmbangH2['peringatan'], $defaultAmbangH2);
                $this->applyAmbangBatasColor($sheet, 'N' . $row, $ambangH2['bahaya'] ?? $defaultAmbangH2['bahaya'], $defaultAmbangH2);
                
                $this->applyAmbangBatasColor($sheet, 'R' . $row, $ambangH3['aman'] ?? $defaultAmbangH3['aman'], $defaultAmbangH3);
                $this->applyAmbangBatasColor($sheet, 'S' . $row, $ambangH3['peringatan'] ?? $defaultAmbangH3['peringatan'], $defaultAmbangH3);
                $this->applyAmbangBatasColor($sheet, 'T' . $row, $ambangH3['bahaya'] ?? $defaultAmbangH3['bahaya'], $defaultAmbangH3);
                
                $this->applyAmbangBatasColor($sheet, 'X' . $row, $ambangH4['aman'] ?? $defaultAmbangH4['aman'], $defaultAmbangH4);
                $this->applyAmbangBatasColor($sheet, 'Y' . $row, $ambangH4['peringatan'] ?? $defaultAmbangH4['peringatan'], $defaultAmbangH4);
                $this->applyAmbangBatasColor($sheet, 'Z' . $row, $ambangH4['bahaya'] ?? $defaultAmbangH4['bahaya'], $defaultAmbangH4);
                
                $this->applyAmbangBatasColor($sheet, 'AD' . $row, $ambangH5['aman'] ?? $defaultAmbangH5['aman'], $defaultAmbangH5);
                $this->applyAmbangBatasColor($sheet, 'AE' . $row, $ambangH5['peringatan'] ?? $defaultAmbangH5['peringatan'], $defaultAmbangH5);
                $this->applyAmbangBatasColor($sheet, 'AF' . $row, $ambangH5['bahaya'] ?? $defaultAmbangH5['bahaya'], $defaultAmbangH5);
                
                // Apply status colors untuk kolom pergerakan terakhir
                $colorH1 = $getStatusColor($pergerakan_ambang_hv1, 'H1');
                $colorH2 = $getStatusColor($pergerakan_ambang_hv2, 'H2');
                $colorH3 = $getStatusColor($pergerakan_ambang_hv3, 'H3');
                $colorH4 = $getStatusColor($pergerakan_ambang_hv4, 'H4');
                $colorH5 = $getStatusColor($pergerakan_ambang_hv5, 'H5');
                
                $sheet->getStyle('I' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH1);
                $sheet->getStyle('O' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH2);
                $sheet->getStyle('U' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH3);
                $sheet->getStyle('AA' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH4);
                $sheet->getStyle('AG' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH5);
                
                // Apply warna untuk MA.Waduk (hijau muda)
                $sheet->getStyle('AH' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE8F5E8');
                
                // Apply borders dan alignment untuk semua cell
                $sheet->getStyle('A' . $row . ':AH' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                $row++;
                $rowCounter++;
            }
        }
        
        // ===== STYLING KOLOM =====
        $this->applyHdm600ColumnStyling($sheet);
        
        // ===== FORMAT ANGKA 2 DESIMAL =====
        if ($row > 7) {
            // Format semua kolom numerik (D sampai AH) dengan 2 desimal
            $numericRange = 'D7:AH' . ($row - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // Atur alignment untuk kolom numerik
            $sheet->getStyle('D7:AH' . ($row - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Kolom A-C tetap center
            $sheet->getStyle('A7:C' . ($row - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('D7'); // Freeze kolom A-C dan baris 1-6
        
        // ===== FOOTER =====
        $footerRow = $row + 1;
        $sheet->mergeCells('A' . $footerRow . ':AH' . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 'HDM 600 - Sistem Monitoring Horizontal Displacement Meter - PT Indonesia Power');
        $sheet->getStyle('A' . $footerRow)->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['argb' => 'FF666666']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2']
            ]
        ]);
        $sheet->getRowDimension($footerRow)->setRowHeight(30);
    }
    
    /**
     * Create HDM 625 Sheet (Tampilan seperti view hdm_625.php)
     */
    private function createHdm625Sheet($sheet, $pengukuran)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // ===== HEADER UTAMA =====
        $sheet->mergeCells('A1:V1');
        $sheet->setCellValue('A1', 'HORIZONTAL DISPLACEMENT METER - ELV 625');
        $this->applyTitleStyle($sheet, 'A1:V1');
        $sheet->getRowDimension(1)->setRowHeight(40);
        
        // ===== HEADER TABEL (SAMA SEPERTI VIEW hdm_625.php) =====
        // Baris 2-6: Header tabel (5 baris header seperti di view)
        $this->createHdm625TableHeaders($sheet);
        
        // ===== ISI DATA =====
        $row = 7; // Mulai dari row 7 (setelah 6 baris header)
        
        // Group data by tahun
        $groupedData = [];
        foreach ($pengukuran as $item) {
            $tahun = $item['tahun'];
            if (!isset($groupedData[$tahun])) {
                $groupedData[$tahun] = [];
            }
            $groupedData[$tahun][] = $item;
        }
        
        // Urutkan tahun secara ascending
        ksort($groupedData);
        
        $rowCounter = 0;
        foreach ($groupedData as $tahun => $itemsInYear) {
            $rowCount = count($itemsInYear);
            $firstRowInYear = $row;
            
            // Urutkan berdasarkan tanggal
            usort($itemsInYear, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($itemsInYear as $index => $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil semua data yang dibutuhkan
                $pembacaan = $this->pembacaanElv625Model->where('id_pengukuran', $pid)->first();
                $pergerakan = $this->pergerakanElv625Model->where('id_pengukuran', $pid)->first();
                $depth = $this->depthElv625Model->where('id_pengukuran', $pid)->first();
                $initialReading = $this->initialReadingElv625Model->where('id_pengukuran', $pid)->first();
                
                // Ambil ambang batas
                $ambangH1 = $this->ambangBatas625H1Model->getByPengukuran($pid);
                $ambangH2 = $this->ambangBatas625H2Model->getByPengukuran($pid);
                $ambangH3 = $this->ambangBatas625H3Model->getByPengukuran($pid);
                
                // Hitung pergerakan untuk ambang batas (sudah dikali 10)
                $pergerakan_hv1 = $pergerakan['hv_1'] ?? 0;
                $pergerakan_hv2 = $pergerakan['hv_2'] ?? 0;
                $pergerakan_hv3 = $pergerakan['hv_3'] ?? 0;
                
                $pergerakan_ambang_hv1 = $ambangH1['pergerakan'] ?? ($pergerakan_hv1 * 10);
                $pergerakan_ambang_hv2 = $ambangH2['pergerakan'] ?? ($pergerakan_hv2 * 10);
                $pergerakan_ambang_hv3 = $ambangH3['pergerakan'] ?? ($pergerakan_hv3 * 10);
                
                // Format nilai dengan 2 digit di belakang koma
                $formatNumber = function($value, $decimals = 2) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    if (is_numeric($value)) {
                        return number_format((float)$value, $decimals, '.', '');
                    }
                    return $value;
                };
                
                // Helper function untuk menentukan warna status seperti di view
                $getStatusColor = function($pergerakanValue, $depthType) {
                    if ($pergerakanValue === null || $pergerakanValue === '' || $pergerakanValue === '-') {
                        return 'FFFFFFFF'; // Warna default
                    }
                    
                    $pergerakanValue = floatval($pergerakanValue);
                    
                    switch($depthType) {
                        case 'H1':
                            $aman = -18.77;
                            $peringatan = -21.66;
                            $bahaya = -25.60;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA'; // Hijau muda untuk Aman
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA'; // Merah muda untuk Bahaya
                            } else {
                                return 'FFFFF3CD'; // Kuning muda untuk Peringatan
                            }
                            
                        case 'H2':
                            $aman = -9.02;
                            $peringatan = -10.41;
                            $bahaya = -12.30;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA';
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA';
                            } else {
                                return 'FFFFF3CD';
                            }
                            
                        case 'H3':
                            $aman = -5.94;
                            $peringatan = -6.85;
                            $bahaya = -8.10;
                            
                            if ($pergerakanValue > $peringatan) {
                                return 'FFD4EDDA';
                            } elseif ($pergerakanValue <= $bahaya) {
                                return 'FFF8D7DA';
                            } else {
                                return 'FFFFF3CD';
                            }
                            
                        default:
                            return 'FFFFFFFF'; // Putih
                    }
                };
                
                // Set nilai default jika ambang batas tidak ada
                $defaultAmbangH1 = ['aman' => -18.77, 'peringatan' => -21.66, 'bahaya' => -25.60];
                $defaultAmbangH2 = ['aman' => -9.02, 'peringatan' => -10.41, 'bahaya' => -12.30];
                $defaultAmbangH3 = ['aman' => -5.94, 'peringatan' => -6.85, 'bahaya' => -8.10];
                
                // TAHUN (Rowspan jika pertama dalam grup)
                if ($index === 0) {
                    $sheet->setCellValue('A' . $row, $tahun);
                    if ($rowCount > 1) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + $rowCount - 1));
                    }
                    // Apply style untuk tahun
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                }
                
                // PERIODE dan TANGGAL
                $sheet->setCellValue('B' . $row, $p['periode'] ?? '-');
                $sheet->setCellValue('C' . $row, date('d-m-Y', strtotime($p['tanggal'])));
                
                // HV1 Data
                $sheet->setCellValue('D' . $row, $formatNumber($pembacaan['hv_1'] ?? '-'));
                $sheet->setCellValue('E' . $row, $formatNumber($pergerakan_ambang_hv1));
                $sheet->setCellValue('F' . $row, $formatNumber($ambangH1['aman'] ?? $defaultAmbangH1['aman']));
                $sheet->setCellValue('G' . $row, $formatNumber($ambangH1['peringatan'] ?? $defaultAmbangH1['peringatan']));
                $sheet->setCellValue('H' . $row, $formatNumber($ambangH1['bahaya'] ?? $defaultAmbangH1['bahaya']));
                $sheet->setCellValue('I' . $row, $formatNumber($pergerakan_ambang_hv1));
                
                // HV2 Data
                $sheet->setCellValue('J' . $row, $formatNumber($pembacaan['hv_2'] ?? '-'));
                $sheet->setCellValue('K' . $row, $formatNumber($pergerakan_ambang_hv2));
                $sheet->setCellValue('L' . $row, $formatNumber($ambangH2['aman'] ?? $defaultAmbangH2['aman']));
                $sheet->setCellValue('M' . $row, $formatNumber($ambangH2['peringatan'] ?? $defaultAmbangH2['peringatan']));
                $sheet->setCellValue('N' . $row, $formatNumber($ambangH2['bahaya'] ?? $defaultAmbangH2['bahaya']));
                $sheet->setCellValue('O' . $row, $formatNumber($pergerakan_ambang_hv2));
                
                // HV3 Data
                $sheet->setCellValue('P' . $row, $formatNumber($pembacaan['hv_3'] ?? '-'));
                $sheet->setCellValue('Q' . $row, $formatNumber($pergerakan_ambang_hv3));
                $sheet->setCellValue('R' . $row, $formatNumber($ambangH3['aman'] ?? $defaultAmbangH3['aman']));
                $sheet->setCellValue('S' . $row, $formatNumber($ambangH3['peringatan'] ?? $defaultAmbangH3['peringatan']));
                $sheet->setCellValue('T' . $row, $formatNumber($ambangH3['bahaya'] ?? $defaultAmbangH3['bahaya']));
                $sheet->setCellValue('U' . $row, $formatNumber($pergerakan_ambang_hv3));
                
                // MA.Waduk
                $sheet->setCellValue('V' . $row, $formatNumber($p['dma'] ?? '-'));
                
                // Apply warna untuk kolom pembacaan dan pergerakan (abu-abu muda)
                $grayCells = ['D', 'E', 'J', 'K', 'P', 'Q'];
                foreach ($grayCells as $col) {
                    $sheet->getStyle($col . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFF8F9FA');
                }
                
                // Apply warna untuk kolom ambang batas berdasarkan nilainya
                $this->applyAmbangBatasColor($sheet, 'F' . $row, $ambangH1['aman'] ?? $defaultAmbangH1['aman'], $defaultAmbangH1);
                $this->applyAmbangBatasColor($sheet, 'G' . $row, $ambangH1['peringatan'] ?? $defaultAmbangH1['peringatan'], $defaultAmbangH1);
                $this->applyAmbangBatasColor($sheet, 'H' . $row, $ambangH1['bahaya'] ?? $defaultAmbangH1['bahaya'], $defaultAmbangH1);
                
                $this->applyAmbangBatasColor($sheet, 'L' . $row, $ambangH2['aman'] ?? $defaultAmbangH2['aman'], $defaultAmbangH2);
                $this->applyAmbangBatasColor($sheet, 'M' . $row, $ambangH2['peringatan'] ?? $defaultAmbangH2['peringatan'], $defaultAmbangH2);
                $this->applyAmbangBatasColor($sheet, 'N' . $row, $ambangH2['bahaya'] ?? $defaultAmbangH2['bahaya'], $defaultAmbangH2);
                
                $this->applyAmbangBatasColor($sheet, 'R' . $row, $ambangH3['aman'] ?? $defaultAmbangH3['aman'], $defaultAmbangH3);
                $this->applyAmbangBatasColor($sheet, 'S' . $row, $ambangH3['peringatan'] ?? $defaultAmbangH3['peringatan'], $defaultAmbangH3);
                $this->applyAmbangBatasColor($sheet, 'T' . $row, $ambangH3['bahaya'] ?? $defaultAmbangH3['bahaya'], $defaultAmbangH3);
                
                // Apply status colors untuk kolom pergerakan terakhir
                $colorH1 = $getStatusColor($pergerakan_ambang_hv1, 'H1');
                $colorH2 = $getStatusColor($pergerakan_ambang_hv2, 'H2');
                $colorH3 = $getStatusColor($pergerakan_ambang_hv3, 'H3');
                
                $sheet->getStyle('I' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH1);
                $sheet->getStyle('O' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH2);
                $sheet->getStyle('U' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($colorH3);
                
                // Apply warna untuk MA.Waduk (hijau muda)
                $sheet->getStyle('V' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE8F5E8');
                
                // Apply borders dan alignment untuk semua cell
                $sheet->getStyle('A' . $row . ':V' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                $row++;
                $rowCounter++;
            }
        }
        
        // ===== STYLING KOLOM =====
        $this->applyHdm625ColumnStyling($sheet);
        
        // ===== FORMAT ANGKA 2 DESIMAL =====
        if ($row > 7) {
            // Format semua kolom numerik (D sampai V) dengan 2 desimal
            $numericRange = 'D7:V' . ($row - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // Atur alignment untuk kolom numerik
            $sheet->getStyle('D7:V' . ($row - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Kolom A-C tetap center
            $sheet->getStyle('A7:C' . ($row - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('D7'); // Freeze kolom A-C dan baris 1-6
        
        // ===== FOOTER =====
        $footerRow = $row + 1;
        $sheet->mergeCells('A' . $footerRow . ':V' . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 'HDM 625 - Sistem Monitoring Horizontal Displacement Meter - PT Indonesia Power');
        $sheet->getStyle('A' . $footerRow)->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['argb' => 'FF666666']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2']
            ]
        ]);
        $sheet->getRowDimension($footerRow)->setRowHeight(30);
    }
    
    /**
     * Apply warna untuk ambang batas berdasarkan nilainya
     */
    private function applyAmbangBatasColor($sheet, $cell, $value, $ambang)
    {
        if ($value === null || $value === '' || $value === '-') {
            return;
        }
        
        $floatVal = floatval($value);
        $peringatan = $ambang['peringatan'];
        $bahaya = $ambang['bahaya'];
        
        if ($floatVal > $peringatan) {
            $color = 'FFD4EDDA'; // Hijau muda untuk Aman
        } elseif ($floatVal <= $bahaya) {
            $color = 'FFF8D7DA'; // Merah muda untuk Bahaya
        } else {
            $color = 'FFFFF3CD'; // Kuning muda untuk Peringatan
        }
        
        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
    }
    
    /**
     * Create HDM 600 Table Headers (sama seperti view)
     */
    private function createHdm600TableHeaders($sheet)
    {
        // Row 2: Header utama (Hor. Displ. Meter No. dan H.1-H.5)
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'Hor. Displ. Meter No.');
        $sheet->mergeCells('D2:I2');
        $sheet->setCellValue('D2', 'H.1');
        $sheet->mergeCells('J2:O2');
        $sheet->setCellValue('J2', 'H.2');
        $sheet->mergeCells('P2:U2');
        $sheet->setCellValue('P2', 'H.3');
        $sheet->mergeCells('V2:AA2');
        $sheet->setCellValue('V2', 'H.4');
        $sheet->mergeCells('AB2:AG2');
        $sheet->setCellValue('AB2', 'H.5');
        $sheet->mergeCells('AH2:AH2');
        $sheet->setCellValue('AH2', 'MA.Waduk');
        
        $styleRow2 = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']]
        ];
        $sheet->getStyle('A2:AH2')->applyFromArray($styleRow2);
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Row 3: Elevasi dan Ambang Batas
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Elevasi (EL. m)');
        $sheet->mergeCells('D3:E3');
        $sheet->setCellValue('D3', '600');
        $sheet->mergeCells('F3:I3');
        $sheet->setCellValue('F3', 'Ambang Batas');
        $sheet->mergeCells('J3:K3');
        $sheet->setCellValue('J3', '600');
        $sheet->mergeCells('L3:O3');
        $sheet->setCellValue('L3', 'Ambang Batas');
        $sheet->mergeCells('P3:Q3');
        $sheet->setCellValue('P3', '600');
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'Ambang Batas');
        $sheet->mergeCells('V3:W3');
        $sheet->setCellValue('V3', '600');
        $sheet->mergeCells('X3:AA3');
        $sheet->setCellValue('X3', 'Ambang Batas');
        $sheet->mergeCells('AB3:AC3');
        $sheet->setCellValue('AB3', '600');
        $sheet->mergeCells('AD3:AG3');
        $sheet->setCellValue('AD3', 'Ambang Batas');
        
        $styleRow3 = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ];
        $sheet->getStyle('A3:AH3')->applyFromArray($styleRow3);
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // Row 4: Kedalaman
        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'Kedalaman (m)');
        $sheet->mergeCells('D4:E4');
        $sheet->setCellValue('D4', '10.00');
        $sheet->mergeCells('J4:K4');
        $sheet->setCellValue('J4', '30.00');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '50.00');
        $sheet->mergeCells('V4:W4');
        $sheet->setCellValue('V4', '70.00');
        $sheet->mergeCells('AB4:AC4');
        $sheet->setCellValue('AB4', '84.50');
        
        $styleRow4 = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ];
        $sheet->getStyle('A4:AH4')->applyFromArray($styleRow4);
        $sheet->getRowDimension(4)->setRowHeight(25);
        
        // Row 5: Bacaan Awal
        $sheet->mergeCells('A5:C5');
        $sheet->setCellValue('A5', 'Bacaan Awal /Initial Reading (cm)');
        $sheet->mergeCells('D5:E5');
        $sheet->setCellValue('D5', '26.60');
        $sheet->mergeCells('J5:K5');
        $sheet->setCellValue('J5', '25.50');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', '24.50');
        $sheet->mergeCells('V5:W5');
        $sheet->setCellValue('V5', '23.40');
        $sheet->mergeCells('AB5:AC5');
        $sheet->setCellValue('AB5', '23.60');
        
        $styleRow5 = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ];
        $sheet->getStyle('A5:AH5')->applyFromArray($styleRow5);
        $sheet->getRowDimension(5)->setRowHeight(25);
        
        // Row 6: Header detail kolom
        $headersRow6 = [
            'A' => 'TAHUN', 'B' => 'PERIODE', 'C' => 'TANGGAL',
            'D' => 'PEMBACAAN (cm)', 'E' => 'PERGERAKAN (mm)',
            'F' => 'Aman', 'G' => 'Peringatan', 'H' => 'Bahaya', 'I' => 'Pergerakan',
            'J' => 'PEMBACAAN (cm)', 'K' => 'PERGERAKAN (mm)',
            'L' => 'Aman', 'M' => 'Peringatan', 'N' => 'Bahaya', 'O' => 'Pergerakan',
            'P' => 'PEMBACAAN (cm)', 'Q' => 'PERGERAKAN (mm)',
            'R' => 'Aman', 'S' => 'Peringatan', 'T' => 'Bahaya', 'U' => 'Pergerakan',
            'V' => 'PEMBACAAN (cm)', 'W' => 'PERGERAKAN (mm)',
            'X' => 'Aman', 'Y' => 'Peringatan', 'Z' => 'Bahaya', 'AA' => 'Pergerakan',
            'AB' => 'PEMBACAAN (cm)', 'AC' => 'PERGERAKAN (mm)',
            'AD' => 'Aman', 'AE' => 'Peringatan', 'AF' => 'Bahaya', 'AG' => 'Pergerakan',
            'AH' => 'Elevasi (EL. m)'
        ];
        
        foreach ($headersRow6 as $col => $value) {
            $sheet->setCellValue($col . '6', $value);
            
            // Tentukan warna berdasarkan tipe kolom
            if (in_array($col, ['A', 'B', 'C'])) {
                $color = 'FFF8F9FA'; // Light gray untuk header info
                $textColor = 'FF000000';
            } elseif (in_array($col, ['D', 'E', 'J', 'K', 'P', 'Q', 'V', 'W', 'AB', 'AC'])) {
                $color = 'FFF8F9FA'; // Light gray untuk pembacaan dan pergerakan
                $textColor = 'FF000000';
            } elseif (in_array($col, ['F', 'L', 'R', 'X', 'AD'])) {
                $color = 'FF4CAF50'; // Hijau cerah untuk Aman
                $textColor = 'FFFFFFFF';
            } elseif (in_array($col, ['G', 'M', 'S', 'Y', 'AE'])) {
                $color = 'FFFFC107'; // Kuning cerah untuk Peringatan
                $textColor = 'FF000000';
            } elseif (in_array($col, ['H', 'N', 'T', 'Z', 'AF'])) {
                $color = 'FFF44336'; // Merah cerah untuk Bahaya
                $textColor = 'FFFFFFFF';
            } elseif (in_array($col, ['I', 'O', 'U', 'AA', 'AG'])) {
                $color = 'FFF8F9FA'; // Light gray untuk kolom pergerakan
                $textColor = 'FF000000';
            } elseif ($col == 'AH') {
                $color = 'FF198754'; // Hijau untuk MA.Waduk
                $textColor = 'FFFFFFFF';
            } else {
                $color = 'FFFFFFFF';
                $textColor = 'FF000000';
            }
            
            $sheet->getStyle($col . '6')->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => $textColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
        }
        $sheet->getRowDimension(6)->setRowHeight(25);
    }
    
    /**
     * Create HDM 625 Table Headers (sama seperti view)
     */
    private function createHdm625TableHeaders($sheet)
    {
        // Row 2: Header utama (Hor. Displ. Meter No. dan H.1-H.3)
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'Hor. Displ. Meter No.');
        $sheet->mergeCells('D2:I2');
        $sheet->setCellValue('D2', 'H.1');
        $sheet->mergeCells('J2:O2');
        $sheet->setCellValue('J2', 'H.2');
        $sheet->mergeCells('P2:U2');
        $sheet->setCellValue('P2', 'H.3');
        $sheet->mergeCells('V2:V2');
        $sheet->setCellValue('V2', 'MA.Waduk');
        
        $styleRow2 = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']]
        ];
        $sheet->getStyle('A2:V2')->applyFromArray($styleRow2);
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Row 3: Elevasi dan Ambang Batas
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Elevasi (EL. m)');
        $sheet->mergeCells('D3:E3');
        $sheet->setCellValue('D3', '625');
        $sheet->mergeCells('F3:I3');
        $sheet->setCellValue('F3', 'Ambang Batas');
        $sheet->mergeCells('J3:K3');
        $sheet->setCellValue('J3', '625');
        $sheet->mergeCells('L3:O3');
        $sheet->setCellValue('L3', 'Ambang Batas');
        $sheet->mergeCells('P3:Q3');
        $sheet->setCellValue('P3', '625');
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'Ambang Batas');
        
        $styleRow3 = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ];
        $sheet->getStyle('A3:V3')->applyFromArray($styleRow3);
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // Row 4: Kedalaman
        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'Kedalaman (m)');
        $sheet->mergeCells('D4:E4');
        $sheet->setCellValue('D4', '20.00');
        $sheet->mergeCells('J4:K4');
        $sheet->setCellValue('J4', '40.00');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '50.00');
        
        $styleRow4 = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ];
        $sheet->getStyle('A4:V4')->applyFromArray($styleRow4);
        $sheet->getRowDimension(4)->setRowHeight(25);
        
        // Row 5: Bacaan Awal
        $sheet->mergeCells('A5:C5');
        $sheet->setCellValue('A5', 'Bacaan Awal /Initial Reading (cm)');
        $sheet->mergeCells('D5:E5');
        $sheet->setCellValue('D5', '36.00');
        $sheet->mergeCells('J5:K5');
        $sheet->setCellValue('J5', '35.50');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', '35.00');
        
        $styleRow5 = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0DCAF0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ];
        $sheet->getStyle('A5:V5')->applyFromArray($styleRow5);
        $sheet->getRowDimension(5)->setRowHeight(25);
        
        // Row 6: Header detail kolom
        $headersRow6 = [
            'A' => 'TAHUN', 'B' => 'PERIODE', 'C' => 'TANGGAL',
            'D' => 'PEMBACAAN (cm)', 'E' => 'PERGERAKAN (mm)',
            'F' => 'Aman', 'G' => 'Peringatan', 'H' => 'Bahaya', 'I' => 'Pergerakan',
            'J' => 'PEMBACAAN (cm)', 'K' => 'PERGERAKAN (mm)',
            'L' => 'Aman', 'M' => 'Peringatan', 'N' => 'Bahaya', 'O' => 'Pergerakan',
            'P' => 'PEMBACAAN (cm)', 'Q' => 'PERGERAKAN (mm)',
            'R' => 'Aman', 'S' => 'Peringatan', 'T' => 'Bahaya', 'U' => 'Pergerakan',
            'V' => 'Elevasi (EL. m)'
        ];
        
        foreach ($headersRow6 as $col => $value) {
            $sheet->setCellValue($col . '6', $value);
            
            // Tentukan warna berdasarkan tipe kolom
            if (in_array($col, ['A', 'B', 'C'])) {
                $color = 'FFF8F9FA'; // Light gray untuk header info
                $textColor = 'FF000000';
            } elseif (in_array($col, ['D', 'E', 'J', 'K', 'P', 'Q'])) {
                $color = 'FFF8F9FA'; // Light gray untuk pembacaan dan pergerakan
                $textColor = 'FF000000';
            } elseif (in_array($col, ['F', 'L', 'R'])) {
                $color = 'FF4CAF50'; // Hijau cerah untuk Aman
                $textColor = 'FFFFFFFF';
            } elseif (in_array($col, ['G', 'M', 'S'])) {
                $color = 'FFFFC107'; // Kuning cerah untuk Peringatan
                $textColor = 'FF000000';
            } elseif (in_array($col, ['H', 'N', 'T'])) {
                $color = 'FFF44336'; // Merah cerah untuk Bahaya
                $textColor = 'FFFFFFFF';
            } elseif (in_array($col, ['I', 'O', 'U'])) {
                $color = 'FFF8F9FA'; // Light gray untuk kolom pergerakan
                $textColor = 'FF000000';
            } elseif ($col == 'V') {
                $color = 'FF198754'; // Hijau untuk MA.Waduk
                $textColor = 'FFFFFFFF';
            } else {
                $color = 'FFFFFFFF';
                $textColor = 'FF000000';
            }
            
            $sheet->getStyle($col . '6')->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => $textColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
        }
        $sheet->getRowDimension(6)->setRowHeight(25);
    }
    
    /**
     * Apply column styling for HDM 600
     */
    private function applyHdm600ColumnStyling($sheet)
    {
        $columnWidths = [
            'A' => 10,  // TAHUN
            'B' => 12,  // PERIODE
            'C' => 15,  // TANGGAL
            // HV1
            'D' => 14, 'E' => 16, 'F' => 12, 'G' => 14, 'H' => 12, 'I' => 14,
            // HV2
            'J' => 14, 'K' => 16, 'L' => 12, 'M' => 14, 'N' => 12, 'O' => 14,
            // HV3
            'P' => 14, 'Q' => 16, 'R' => 12, 'S' => 14, 'T' => 12, 'U' => 14,
            // HV4
            'V' => 14, 'W' => 16, 'X' => 12, 'Y' => 14, 'Z' => 12, 'AA' => 14,
            // HV5
            'AB' => 14, 'AC' => 16, 'AD' => 12, 'AE' => 14, 'AF' => 12, 'AG' => 14,
            // MA.Waduk
            'AH' => 14
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
    
    /**
     * Apply column styling for HDM 625
     */
    private function applyHdm625ColumnStyling($sheet)
    {
        $columnWidths = [
            'A' => 10,  // TAHUN
            'B' => 12,  // PERIODE
            'C' => 15,  // TANGGAL
            // HV1
            'D' => 14, 'E' => 16, 'F' => 12, 'G' => 14, 'H' => 12, 'I' => 14,
            // HV2
            'J' => 14, 'K' => 16, 'L' => 12, 'M' => 14, 'N' => 12, 'O' => 14,
            // HV3
            'P' => 14, 'Q' => 16, 'R' => 12, 'S' => 14, 'T' => 12, 'U' => 14,
            // MA.Waduk
            'V' => 14
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
    
    /**
     * Create HDM table headers (3 baris) untuk sheet konsolidasi
     */
    private function createHDMTableHeaders($sheet)
    {
        $row = 4;
        
        // Warna untuk setiap section
        $blueLight = 'FF5B9BD5'; // Biru terang untuk PEMBACAAN HDM
        $blue = 'FF0D6EFD';     // Biru untuk DEPTH
        $green = 'FF198754';    // Hijau untuk READINGS
        $yellow = 'FFFFC107';   // Kuning untuk PERGERAKAN
        
        // Row 4: Main Header
        $mainHeaders = [
            ['label' => 'TAHUN', 'colspan' => 1, 'rowspan' => 3, 'col' => 'A'],
            ['label' => 'PERIODE', 'colspan' => 1, 'rowspan' => 3, 'col' => 'B'],
            ['label' => 'TANGGAL', 'colspan' => 1, 'rowspan' => 3, 'col' => 'C'],
            ['label' => 'DAM', 'colspan' => 1, 'rowspan' => 3, 'col' => 'D'],
            ['label' => 'PEMBACAAN HDM', 'colspan' => 8, 'rowspan' => 1, 'col' => 'E'],
            ['label' => 'DEPTH (S)', 'colspan' => 8, 'rowspan' => 1, 'col' => 'M'],
            ['label' => 'READINGS (S)', 'colspan' => 8, 'rowspan' => 1, 'col' => 'U'],
            ['label' => 'PERGERAKAN (CM)', 'colspan' => 8, 'rowspan' => 1, 'col' => 'AC']
        ];
        
        foreach ($mainHeaders as $header) {
            if ($header['colspan'] == 1 && $header['rowspan'] == 3) {
                // Header dengan rowspan 3
                $cell = $header['col'] . $row;
                $sheet->setCellValue($cell, $header['label']);
                $this->applyHeaderStyleRowspan3($sheet, $cell . ':' . $header['col'] . ($row + 2), $blueLight);
            } else {
                // Header dengan colspan
                $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
                $range = $header['col'] . $row . ':' . $endCol . $row;
                $sheet->mergeCells($range);
                $sheet->setCellValue($header['col'] . $row, $header['label']);
                
                // Tentukan warna berdasarkan section
                $color = $blueLight; // Default (PEMBACAAN HDM)
                if ($header['label'] == 'DEPTH (S)') {
                    $color = $blue;
                } elseif ($header['label'] == 'READINGS (S)') {
                    $color = $green;
                } elseif ($header['label'] == 'PERGERAKAN (CM)') {
                    $color = $yellow;
                }
                
                $this->applyHeaderStyle($sheet, $range, $color);
            }
        }
        
        $sheet->getRowDimension($row)->setRowHeight(30);
        
        // Row 5: Sub Headers
        $row = 5;
        $subHeaders = [
            // PEMBACAAN HDM - ELV.625 dan ELV.600
            ['label' => 'ELV.625', 'colspan' => 3, 'col' => 'E', 'color' => $blueLight],
            ['label' => 'ELV.600', 'colspan' => 5, 'col' => 'H', 'color' => $blueLight],
            
            // DEPTH (S) - ELV.625 dan ELV.600
            ['label' => 'ELV.625', 'colspan' => 3, 'col' => 'M', 'color' => $blue],
            ['label' => 'ELV.600', 'colspan' => 5, 'col' => 'P', 'color' => $blue],
            
            // READINGS (S) - ELV.625 dan ELV.600
            ['label' => 'ELV.625', 'colspan' => 3, 'col' => 'U', 'color' => $green],
            ['label' => 'ELV.600', 'colspan' => 5, 'col' => 'X', 'color' => $green],
            
            // PERGERAKAN (CM) - ELV.625 dan ELV.600
            ['label' => 'ELV.625', 'colspan' => 3, 'col' => 'AC', 'color' => $yellow],
            ['label' => 'ELV.600', 'colspan' => 5, 'col' => 'AF', 'color' => $yellow]
        ];
        
        foreach ($subHeaders as $header) {
            $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
            $range = $header['col'] . $row . ':' . $endCol . $row;
            $sheet->mergeCells($range);
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $this->applySubHeaderStyle($sheet, $range, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Row 6: Measurement Headers
        $row = 6;
        $measurementHeaders = [
            // Kolom A-D sudah rowspan 3, jadi kosong
            
            // PEMBACAAN HDM - ELV 625 (E-G)
            ['label' => 'HV-1', 'col' => 'E', 'color' => $blueLight],
            ['label' => 'HV-2', 'col' => 'F', 'color' => $blueLight],
            ['label' => 'HV-3', 'col' => 'G', 'color' => $blueLight],
            
            // PEMBACAAN HDM - ELV 600 (H-L)
            ['label' => 'HV-1', 'col' => 'H', 'color' => $blueLight],
            ['label' => 'HV-2', 'col' => 'I', 'color' => $blueLight],
            ['label' => 'HV-3', 'col' => 'J', 'color' => $blueLight],
            ['label' => 'HV-4', 'col' => 'K', 'color' => $blueLight],
            ['label' => 'HV-5', 'col' => 'L', 'color' => $blueLight],
            
            // DEPTH (S) - ELV 625 (M-O)
            ['label' => 'HV-1', 'col' => 'M', 'color' => $blue],
            ['label' => 'HV-2', 'col' => 'N', 'color' => $blue],
            ['label' => 'HV-3', 'col' => 'O', 'color' => $blue],
            
            // DEPTH (S) - ELV 600 (P-T)
            ['label' => 'HV-1', 'col' => 'P', 'color' => $blue],
            ['label' => 'HV-2', 'col' => 'Q', 'color' => $blue],
            ['label' => 'HV-3', 'col' => 'R', 'color' => $blue],
            ['label' => 'HV-4', 'col' => 'S', 'color' => $blue],
            ['label' => 'HV-5', 'col' => 'T', 'color' => $blue],
            
            // READINGS (S) - ELV 625 (U-W)
            ['label' => 'HV-1', 'col' => 'U', 'color' => $green],
            ['label' => 'HV-2', 'col' => 'V', 'color' => $green],
            ['label' => 'HV-3', 'col' => 'W', 'color' => $green],
            
            // READINGS (S) - ELV 600 (X-AB)
            ['label' => 'HV-1', 'col' => 'X', 'color' => $green],
            ['label' => 'HV-2', 'col' => 'Y', 'color' => $green],
            ['label' => 'HV-3', 'col' => 'Z', 'color' => $green],
            ['label' => 'HV-4', 'col' => 'AA', 'color' => $green],
            ['label' => 'HV-5', 'col' => 'AB', 'color' => $green],
            
            // PERGERAKAN (CM) - ELV 625 (AC-AE)
            ['label' => 'HV-1', 'col' => 'AC', 'color' => $yellow],
            ['label' => 'HV-2', 'col' => 'AD', 'color' => $yellow],
            ['label' => 'HV-3', 'col' => 'AE', 'color' => $yellow],
            
            // PERGERAKAN (CM) - ELV 600 (AF-AJ)
            ['label' => 'HV-1', 'col' => 'AF', 'color' => $yellow],
            ['label' => 'HV-2', 'col' => 'AG', 'color' => $yellow],
            ['label' => 'HV-3', 'col' => 'AH', 'color' => $yellow],
            ['label' => 'HV-4', 'col' => 'AI', 'color' => $yellow],
            ['label' => 'HV-5', 'col' => 'AJ', 'color' => $yellow]
        ];
        
        foreach ($measurementHeaders as $header) {
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            $this->applyDetailHeaderStyle($sheet, $header['col'] . $row, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(20);
    }
    
    /**
     * Apply title style
     */
    private function applyTitleStyle($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => Color::COLOR_WHITE],
                'name' => 'Calibri'
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2F75B5']
            ]
        ]);
    }
    
    /**
     * Apply subtitle style
     */
    private function applySubtitleStyle($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['argb' => 'FF666666']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2']
            ]
        ]);
    }
    
    /**
     * Apply header style
     */
    private function applyHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['argb' => Color::COLOR_WHITE]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $color]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ]
        ]);
    }
    
    /**
     * Apply header style with rowspan 3
     */
    private function applyHeaderStyleRowspan3($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['argb' => Color::COLOR_WHITE]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $color]
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ]
        ]);
    }
    
    /**
     * Apply sub header style
     */
    private function applySubHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => Color::COLOR_WHITE]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $color]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ]
        ]);
    }
    
    /**
     * Apply detail header style
     */
    private function applyDetailHeaderStyle($sheet, $cell, $color)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['argb' => 'FF000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $color]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ]
        ]);
        
        // Jika warna kuning, ubah teks jadi hitam, jika hijau atau biru tetap putih
        if ($color == 'FFFFC107') {
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF000000');
        } else {
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }
    }
    
    /**
     * Apply column styling untuk sheet konsolidasi
     */
    private function applyColumnStyling($sheet, $lastRow)
    {
        // Set column widths
        $columnWidths = [
            'A' => 8,   // TAHUN
            'B' => 10,  // PERIODE
            'C' => 12,  // TANGGAL
            'D' => 8,   // DAM
            // PEMBACAAN HDM - ELV 625 (E-G)
            'E' => 12, 'F' => 12, 'G' => 12,
            // PEMBACAAN HDM - ELV 600 (H-L)
            'H' => 12, 'I' => 12, 'J' => 12, 'K' => 12, 'L' => 12,
            // DEPTH (S) - ELV 625 (M-O)
            'M' => 12, 'N' => 12, 'O' => 12,
            // DEPTH (S) - ELV 600 (P-T)
            'P' => 12, 'Q' => 12, 'R' => 12, 'S' => 12, 'T' => 12,
            // READINGS (S) - ELV 625 (U-W)
            'U' => 12, 'V' => 12, 'W' => 12,
            // READINGS (S) - ELV 600 (X-AB)
            'X' => 12, 'Y' => 12, 'Z' => 12, 'AA' => 12, 'AB' => 12,
            // PERGERAKAN (CM) - ELV 625 (AC-AE)
            'AC' => 14, 'AD' => 14, 'AE' => 14,
            // PERGERAKAN (CM) - ELV 600 (AF-AJ)
            'AF' => 14, 'AG' => 14, 'AH' => 14, 'AI' => 14, 'AJ' => 14
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
    
    /**
     * Helper function to get next column
     */
    private function nextColumn($column, $steps = 1)
    {
        $column = strtoupper($column);
        for ($i = 0; $i < $steps; $i++) {
            $column++;
        }
        return $column;
    }
    
    /**
     * Export filtered data (untuk AJAX dari frontend)
     */
    public function exportFiltered()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }
        
        try {
            $filterData = $this->request->getJSON(true);
            
            // Kembalikan URL untuk download dengan parameter filter
            $url = base_url('hdm/export-excel/export');
            $params = [];
            
            if (!empty($filterData['tahun'])) {
                $params[] = 'tahun=' . urlencode($filterData['tahun']);
            }
            
            if (!empty($filterData['periode'])) {
                $params[] = 'periode=' . urlencode($filterData['periode']);
            }
            
            if (!empty($params)) {
                $url .= '?' . implode('&', $params);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data siap diexport',
                'download_url' => $url
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting filtered data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}