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
            $dma = $this->request->getGet('dma');
            
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
            
            if (!empty($dma)) {
                $query->where('dma', $dma);
            }
            
            $pengukuran = $query->findAll();

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data HDM');
            
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
                
                // Format nilai numerik
                $formatNumber = function($value) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    
                    if (is_numeric($value)) {
                        return number_format((float)$value, 2);
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
            
            // ===== BUAT SHEET KEDUA UNTUK TAMPILAN ALTERNATIF =====
            $this->createAlternativeSheet($spreadsheet, $pengukuran);
            
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
     * Create HDM table headers (3 baris)
     */
    private function createHDMTableHeaders($sheet)
    {
        $row = 4;
        
        // Warna untuk setiap section
        $blueLight = 'FF5B9BD5'; // Biru terang untuk PEMBACAAN HDM
        $blue = 'FF0D6EFD';     // Biru untuk DEPTH
        $green = 'FF198754';    // Hijau untuk READINGS (DIPERBAIKI)
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
                    $color = $blue; // Biru
                } elseif ($header['label'] == 'READINGS (S)') {
                    $color = $green; // HIJAU (DIPERBAIKI)
                } elseif ($header['label'] == 'PERGERAKAN (CM)') {
                    $color = $yellow; // Kuning
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
            
            // READINGS (S) - ELV.625 dan ELV.600 (SAMA-SAMA HIJAU)
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
            
            // Gunakan warna yang sudah ditentukan untuk setiap header
            $this->applySubHeaderStyle($sheet, $range, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Row 6: Measurement Headers
        $row = 6;
        $measurementHeaders = [
            // Kolom A-D sudah rowspan 3, jadi kosong
            
            // PEMBACAAN HDM - ELV 625 (E-G) - BIRU TERANG
            ['label' => 'HV-1', 'col' => 'E', 'color' => $blueLight],
            ['label' => 'HV-2', 'col' => 'F', 'color' => $blueLight],
            ['label' => 'HV-3', 'col' => 'G', 'color' => $blueLight],
            
            // PEMBACAAN HDM - ELV 600 (H-L) - BIRU TERANG
            ['label' => 'HV-1', 'col' => 'H', 'color' => $blueLight],
            ['label' => 'HV-2', 'col' => 'I', 'color' => $blueLight],
            ['label' => 'HV-3', 'col' => 'J', 'color' => $blueLight],
            ['label' => 'HV-4', 'col' => 'K', 'color' => $blueLight],
            ['label' => 'HV-5', 'col' => 'L', 'color' => $blueLight],
            
            // DEPTH (S) - ELV 625 (M-O) - BIRU
            ['label' => 'HV-1', 'col' => 'M', 'color' => $blue],
            ['label' => 'HV-2', 'col' => 'N', 'color' => $blue],
            ['label' => 'HV-3', 'col' => 'O', 'color' => $blue],
            
            // DEPTH (S) - ELV 600 (P-T) - BIRU
            ['label' => 'HV-1', 'col' => 'P', 'color' => $blue],
            ['label' => 'HV-2', 'col' => 'Q', 'color' => $blue],
            ['label' => 'HV-3', 'col' => 'R', 'color' => $blue],
            ['label' => 'HV-4', 'col' => 'S', 'color' => $blue],
            ['label' => 'HV-5', 'col' => 'T', 'color' => $blue],
            
            // READINGS (S) - ELV 625 (U-W) - HIJAU
            ['label' => 'HV-1', 'col' => 'U', 'color' => $green],
            ['label' => 'HV-2', 'col' => 'V', 'color' => $green],
            ['label' => 'HV-3', 'col' => 'W', 'color' => $green],
            
            // READINGS (S) - ELV 600 (X-AB) - HIJAU
            ['label' => 'HV-1', 'col' => 'X', 'color' => $green],
            ['label' => 'HV-2', 'col' => 'Y', 'color' => $green],
            ['label' => 'HV-3', 'col' => 'Z', 'color' => $green],
            ['label' => 'HV-4', 'col' => 'AA', 'color' => $green],
            ['label' => 'HV-5', 'col' => 'AB', 'color' => $green],
            
            // PERGERAKAN (CM) - ELV 625 (AC-AE) - KUNING
            ['label' => 'HV-1', 'col' => 'AC', 'color' => $yellow],
            ['label' => 'HV-2', 'col' => 'AD', 'color' => $yellow],
            ['label' => 'HV-3', 'col' => 'AE', 'color' => $yellow],
            
            // PERGERAKAN (CM) - ELV 600 (AF-AJ) - KUNING
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
                'startColor' => ['argb' => 'FF2F75B5'] // Biru tua untuk judul
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
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ],
                'right' => [
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
                'color' => ['argb' => 'FF000000'] // Teks hitam untuk kuning, putih untuk lainnya
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
     * Apply column styling
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
        
        // Format angka untuk kolom numerik
        if ($lastRow > 7) { // Pastikan ada data
            try {
                // Format semua kolom numerik (E-AJ) dengan 2 desimal
                $sheet->getStyle('E7:AJ' . ($lastRow - 1))
                    ->getNumberFormat()
                    ->setFormatCode('0.00');
                    
                // Kolom dasar (A-D) center alignment
                $sheet->getStyle('A7:D' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                // Kolom numerik (E-AJ) right alignment
                $sheet->getStyle('E7:AJ' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    
            } catch (\Exception $e) {
                // Skip error
                log_message('error', 'Error applying column styling: ' . $e->getMessage());
            }
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
     * Create alternative sheet with frozen columns
     */
    private function createAlternativeSheet($spreadsheet, $pengukuran)
    {
        try {
            $altSheet = $spreadsheet->createSheet();
            $altSheet->setTitle('Data HDM (Frozen View)');
            
            // ===== SET PAGE SETUP =====
            $altSheet->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                ->setPaperSize(PageSetup::PAPERSIZE_A3)
                ->setFitToWidth(1)
                ->setFitToHeight(0);
            
            // ===== HEADER UTAMA DAN SUBHEADER =====
            $lastCol = 'AJ';
            
            // Judul Utama
            $altSheet->mergeCells('A1:' . $lastCol . '1');
            $altSheet->setCellValue('A1', 'DATA HORIZONTAL DISPLACEMENT METER - PT INDONESIA POWER');
            $this->applyTitleStyle($altSheet, 'A1:' . $lastCol . '1');
            $altSheet->getRowDimension(1)->setRowHeight(35);
            
            // Sub Judul
            $altSheet->mergeCells('A2:' . $lastCol . '2');
            $altSheet->setCellValue('A2', 'LAPORAN DATA HORIZONTAL DISPLACEMENT METER (FROZEN COLUMNS VIEW)');
            $this->applyTitleStyle($altSheet, 'A2:' . $lastCol . '2');
            $altSheet->getRowDimension(2)->setRowHeight(30);
            
            // Informasi Tanggal Ekspor
            $altSheet->mergeCells('A3:' . $lastCol . '3');
            $altSheet->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s') . ' | Kolom A-D dibekukan');
            $this->applySubtitleStyle($altSheet, 'A3:' . $lastCol . '3');
            $altSheet->getRowDimension(3)->setRowHeight(25);
            
            // ===== HEADER TABEL =====
            $this->createHDMTableHeaders($altSheet);
            
            // ===== ISI DATA =====
            $row = 7;
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
                
                // Format nilai numerik
                $formatNumber = function($value) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    
                    if (is_numeric($value)) {
                        return number_format((float)$value, 2);
                    }
                    
                    return $value;
                };
                
                $rowData = [
                    // Basic Info (A-D) - akan dibekukan
                    $p['tahun'] ?? '-',
                    $p['periode'] ?? '-',
                    $displayDate,
                    $formatNumber($p['dma'] ?? '-'),
                    
                    // Data lainnya...
                    $formatNumber($pembacaanElv625['hv_1'] ?? '-'),
                    $formatNumber($pembacaanElv625['hv_2'] ?? '-'),
                    $formatNumber($pembacaanElv625['hv_3'] ?? '-'),
                    $formatNumber($pembacaanElv600['hv_1'] ?? '-'),
                    $formatNumber($pembacaanElv600['hv_2'] ?? '-'),
                    $formatNumber($pembacaanElv600['hv_3'] ?? '-'),
                    $formatNumber($pembacaanElv600['hv_4'] ?? '-'),
                    $formatNumber($pembacaanElv600['hv_5'] ?? '-'),
                    $formatNumber($depthElv625['hv_1'] ?? '-'),
                    $formatNumber($depthElv625['hv_2'] ?? '-'),
                    $formatNumber($depthElv625['hv_3'] ?? '-'),
                    $formatNumber($depthElv600['hv_1'] ?? '-'),
                    $formatNumber($depthElv600['hv_2'] ?? '-'),
                    $formatNumber($depthElv600['hv_3'] ?? '-'),
                    $formatNumber($depthElv600['hv_4'] ?? '-'),
                    $formatNumber($depthElv600['hv_5'] ?? '-'),
                    $formatNumber($initialReadingElv625['hv_1'] ?? '-'),
                    $formatNumber($initialReadingElv625['hv_2'] ?? '-'),
                    $formatNumber($initialReadingElv625['hv_3'] ?? '-'),
                    $formatNumber($initialReadingElv600['hv_1'] ?? '-'),
                    $formatNumber($initialReadingElv600['hv_2'] ?? '-'),
                    $formatNumber($initialReadingElv600['hv_3'] ?? '-'),
                    $formatNumber($initialReadingElv600['hv_4'] ?? '-'),
                    $formatNumber($initialReadingElv600['hv_5'] ?? '-'),
                    $formatNumber($pergerakanElv625['hv_1'] ?? '-'),
                    $formatNumber($pergerakanElv625['hv_2'] ?? '-'),
                    $formatNumber($pergerakanElv625['hv_3'] ?? '-'),
                    $formatNumber($pergerakanElv600['hv_1'] ?? '-'),
                    $formatNumber($pergerakanElv600['hv_2'] ?? '-'),
                    $formatNumber($pergerakanElv600['hv_3'] ?? '-'),
                    $formatNumber($pergerakanElv600['hv_4'] ?? '-'),
                    $formatNumber($pergerakanElv600['hv_5'] ?? '-')
                ];
                
                // Set data ke row
                $col = 'A';
                foreach ($rowData as $value) {
                    $altSheet->setCellValue($col . $row, $value);
                    $col++;
                }
                
                // Alternating row color
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
                
                $altSheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($rowStyle);
                
                if ($index % 2 == 0) {
                    $altSheet->getStyle('A' . $row . ':' . $lastCol . $row)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFF8F9FA');
                }
                
                $altSheet->getRowDimension($row)->setRowHeight(25);
                $row++;
            }
            
            // ===== STYLING KOLOM =====
            $this->applyColumnStyling($altSheet, $row);
            
            // ===== FREEZE PANES YANG LEBIH EFEKTIF =====
            // Freeze kolom A-D dan baris 1-6
            $altSheet->freezePane('E7'); // Mulai dari kolom E, baris 7
            
            // Tambahkan border tebal untuk memisahkan kolom beku
            $altSheet->getStyle('D1:D' . ($row - 1))
                ->getBorders()
                ->getRight()
                ->setBorderStyle(Border::BORDER_MEDIUM)
                ->setColor(new Color('FF000000'));
            
            // ===== FOOTER =====
            $footerRow = $row + 1;
            $altSheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
            $altSheet->setCellValue('A' . $footerRow, 'Sistem Monitoring Horizontal Displacement Meter - PT Indonesia Power | Kolom Tahun, Periode, Tanggal, DAM dibekukan');
            $altSheet->getStyle('A' . $footerRow)->applyFromArray([
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
            $altSheet->getRowDimension($footerRow)->setRowHeight(30);
            
        } catch (\Exception $e) {
            log_message('error', 'Error creating alternative sheet: ' . $e->getMessage());
        }
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
            
            if (!empty($filterData['dma'])) {
                $params[] = 'dma=' . urlencode($filterData['dma']);
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