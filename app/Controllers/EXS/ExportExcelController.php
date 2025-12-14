<?php

namespace App\Controllers\EXS;

use App\Controllers\BaseController;
use App\Models\Exstenso\PengukuranEksModel;
use App\Models\Exstenso\PembacaanEx1Model;
use App\Models\Exstenso\PembacaanEx2Model;
use App\Models\Exstenso\PembacaanEx3Model;
use App\Models\Exstenso\PembacaanEx4Model;
use App\Models\Exstenso\DeformasiEx1Model;
use App\Models\Exstenso\DeformasiEx2Model;
use App\Models\Exstenso\DeformasiEx3Model;
use App\Models\Exstenso\DeformasiEx4Model;
use App\Models\Exstenso\ReadingsEx1Model;
use App\Models\Exstenso\ReadingsEx2Model;
use App\Models\Exstenso\ReadingsEx3Model;
use App\Models\Exstenso\ReadingsEx4Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;

class ExportExcelController extends BaseController
{
    protected $pengukuranModel;
    protected $pembacaanEx1Model;
    protected $pembacaanEx2Model;
    protected $pembacaanEx3Model;
    protected $pembacaanEx4Model;
    protected $deformasiEx1Model;
    protected $deformasiEx2Model;
    protected $deformasiEx3Model;
    protected $deformasiEx4Model;
    protected $readingsEx1Model;
    protected $readingsEx2Model;
    protected $readingsEx3Model;
    protected $readingsEx4Model;

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
            $this->pengukuranModel = new PengukuranEksModel();
            $this->pembacaanEx1Model = new PembacaanEx1Model();
            $this->pembacaanEx2Model = new PembacaanEx2Model();
            $this->pembacaanEx3Model = new PembacaanEx3Model();
            $this->pembacaanEx4Model = new PembacaanEx4Model();
            $this->deformasiEx1Model = new DeformasiEx1Model();
            $this->deformasiEx2Model = new DeformasiEx2Model();
            $this->deformasiEx3Model = new DeformasiEx3Model();
            $this->deformasiEx4Model = new DeformasiEx4Model();
            $this->readingsEx1Model = new ReadingsEx1Model();
            $this->readingsEx2Model = new ReadingsEx2Model();
            $this->readingsEx3Model = new ReadingsEx3Model();
            $this->readingsEx4Model = new ReadingsEx4Model();
            
        } catch (\Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
            die("Error loading models: " . $e->getMessage());
        }
    }

    /**
     * Export Excel dengan header yang sama seperti di view Extenso
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
                                           ->orderBy('periode', 'ASC')
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
            
            // ===== SETUP DEFAULT STYLE =====
            $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setName('Calibri')
                ->setSize(10);
            
            // ===== SHEET 1: DATA EXTENSOMETER KONSOLIDASI =====
            $consolidatedSheet = $spreadsheet->getActiveSheet();
            $consolidatedSheet->setTitle('Data Extensometer');
            $this->createConsolidatedSheet($consolidatedSheet, $pengukuran, $tahun, $periode, $dma);
            
            // ===== SETUP PAGE MARGINS =====
            $consolidatedSheet->getPageMargins()
                ->setTop(0.75)
                ->setRight(0.25)
                ->setLeft(0.25)
                ->setBottom(0.75);
            
            // ===== SETUP HEADER & FOOTER =====
            $this->setupExcelHeaderFooter($consolidatedSheet, $tahun, $periode, $dma);
            
            // ===== SAVE FILE =====
            $writer = new Xlsx($spreadsheet);
            $filename = 'Extensometer_Data_Export_' . date('Ymd_His') . '.xlsx';
            
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
     * Setup Excel Header & Footer yang seragam dengan HDM
     */
    private function setupExcelHeaderFooter($sheet, $tahun, $periode, $dma)
    {
        $headerFooter = $sheet->getHeaderFooter();
        
        // Build header text dengan format seragam HDM
        $headerFooter->setOddHeader(
            '&L&"Calibri,Bold"&14PT INDONESIA POWER' . 
            '&C&"Calibri"&12DATA EXTENSOMETER MONITORING SYSTEM' .
            '&R&"Calibri"&8' . date('d/m/Y H:i')
        );
        
        // Build filter info untuk footer
        $filterInfo = [];
        if (!empty($tahun)) $filterInfo[] = "Tahun: $tahun";
        if (!empty($periode)) $filterInfo[] = "Periode: $periode";
        if (!empty($dma)) $filterInfo[] = "DMA: $dma";
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        // Set Footer dengan format yang lebih baik
        $headerFooter->setOddFooter(
            '&L&"Calibri"&8Filter: ' . $filterText .
            '&C&"Calibri"&8Halaman &P dari &N' .
            '&R&"Calibri"&8© ' . date('Y') . ' - Sistem Monitoring Extensometer'
        );
    }
    
    /**
     * Create consolidated sheet dengan struktur header yang sama seperti view
     */
    private function createConsolidatedSheet($sheet, $pengukuran, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== HEADER UTAMA DAN SUBHEADER =====
        $lastCol = 'AN'; // Total 42 kolom (A-AN) setelah hapus kolom AKSI
        
        // Row 1: Judul Utama dengan informasi perusahaan (SERAGAM DENGAN HDM)
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'DATA EXTENSOMETER MONITORING SYSTEM - PT INDONESIA POWER');
        $this->applyCompanyHeaderStyle($sheet, 'A1:' . $lastCol . '1');
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA EXTENSOMETER');
        $this->applyMainTitleStyle($sheet, 'A2:' . $lastCol . '2');
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Row 3: Informasi Ekspor dan Filter
        $sheet->mergeCells('A3:' . $lastCol . '3');
        
        $filterInfo = [];
        if (!empty($tahunFilter)) $filterInfo[] = "Tahun: $tahunFilter";
        if (!empty($periodeFilter)) $filterInfo[] = "Periode: $periodeFilter";
        if (!empty($dmaFilter)) $filterInfo[] = "DMA: $dmaFilter";
        
        if (!empty($filterInfo)) {
            $filterText = 'Filter: ' . implode(', ', $filterInfo);
            $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s') . ' | ' . $filterText);
        } else {
            $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s') . ' | Filter: Semua Data');
        }
        
        $this->applySubtitleStyle($sheet, 'A3:' . $lastCol . '3');
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // ===== HEADER TABEL (SAMA SEPERTI DI VIEW) =====
        // Row 4-6: Table Headers (3 baris seperti di view)
        $this->createExtensoTableHeaders($sheet);
        
        // ===== ISI DATA =====
        $row = 7; // Mulai dari row 7 (setelah header tabel)
        
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
        $totalRecords = 0;
        
        foreach ($groupedData as $tahun => $itemsInYear) {
            $rowCount = count($itemsInYear);
            $totalRecords += $rowCount;
            
            // Urutkan berdasarkan tanggal
            usort($itemsInYear, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            // Simpan row pertama untuk grup tahun ini
            $firstRowInGroup = $row;
            
            foreach ($itemsInYear as $index => $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil semua data untuk setiap extensometer
                $pembacaanEx1 = $this->pembacaanEx1Model->where('id_pengukuran', $pid)->first();
                $pembacaanEx2 = $this->pembacaanEx2Model->where('id_pengukuran', $pid)->first();
                $pembacaanEx3 = $this->pembacaanEx3Model->where('id_pengukuran', $pid)->first();
                $pembacaanEx4 = $this->pembacaanEx4Model->where('id_pengukuran', $pid)->first();
                
                $deformasiEx1 = $this->deformasiEx1Model->where('id_pengukuran', $pid)->first();
                $deformasiEx2 = $this->deformasiEx2Model->where('id_pengukuran', $pid)->first();
                $deformasiEx3 = $this->deformasiEx3Model->where('id_pengukuran', $pid)->first();
                $deformasiEx4 = $this->deformasiEx4Model->where('id_pengukuran', $pid)->first();
                
                $readingsEx1 = $this->readingsEx1Model->where('id_pengukuran', $pid)->first();
                $readingsEx2 = $this->readingsEx2Model->where('id_pengukuran', $pid)->first();
                $readingsEx3 = $this->readingsEx3Model->where('id_pengukuran', $pid)->first();
                $readingsEx4 = $this->readingsEx4Model->where('id_pengukuran', $pid)->first();
                
                // Format nilai dengan 4 digit di belakang koma untuk pembacaan, deformasi, readings
                $formatNumber = function($value, $decimals = 4) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    if (is_numeric($value)) {
                        return number_format((float)$value, $decimals, '.', '');
                    }
                    return $value;
                };
                
                // Format DMA dengan 0 desimal (bilangan bulat)
                $formatDMANumber = function($value) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    if (is_numeric($value)) {
                        return number_format((float)$value, 0, '.', '');
                    }
                    return $value;
                };
                
                // ===== PERBAIKAN UTAMA DI SINI =====
                // Set nilai untuk TAHUN, PERIODE, TANGGAL, DMA untuk setiap baris
                
                // TAHUN - hanya di row pertama grup (rowspan)
                if ($index === 0) {
                    $sheet->setCellValue('A' . $row, $tahun);
                }
                
                // PERIODE - untuk SETIAP baris
                $sheet->setCellValue('B' . $row, $p['periode'] ?? '-');
                
                // TANGGAL - untuk SETIAP baris (FIX: sebelumnya hanya baris pertama)
                $tanggal = !empty($p['tanggal']) ? date('d-m-Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('C' . $row, $tanggal);
                
                // DMA - untuk SETIAP baris
                $sheet->setCellValue('D' . $row, $formatDMANumber($p['dma'] ?? '-'));
                
                // PEMBACAAN DATA (EX1-EX4)
                // EX-1
                $sheet->setCellValue('E' . $row, $formatNumber($pembacaanEx1['pembacaan_10'] ?? '-'));
                $sheet->setCellValue('F' . $row, $formatNumber($pembacaanEx1['pembacaan_20'] ?? '-'));
                $sheet->setCellValue('G' . $row, $formatNumber($pembacaanEx1['pembacaan_30'] ?? '-'));
                // EX-2
                $sheet->setCellValue('H' . $row, $formatNumber($pembacaanEx2['pembacaan_10'] ?? '-'));
                $sheet->setCellValue('I' . $row, $formatNumber($pembacaanEx2['pembacaan_20'] ?? '-'));
                $sheet->setCellValue('J' . $row, $formatNumber($pembacaanEx2['pembacaan_30'] ?? '-'));
                // EX-3
                $sheet->setCellValue('K' . $row, $formatNumber($pembacaanEx3['pembacaan_10'] ?? '-'));
                $sheet->setCellValue('L' . $row, $formatNumber($pembacaanEx3['pembacaan_20'] ?? '-'));
                $sheet->setCellValue('M' . $row, $formatNumber($pembacaanEx3['pembacaan_30'] ?? '-'));
                // EX-4
                $sheet->setCellValue('N' . $row, $formatNumber($pembacaanEx4['pembacaan_10'] ?? '-'));
                $sheet->setCellValue('O' . $row, $formatNumber($pembacaanEx4['pembacaan_20'] ?? '-'));
                $sheet->setCellValue('P' . $row, $formatNumber($pembacaanEx4['pembacaan_30'] ?? '-'));
                
                // DEFORMASI DATA (EX1-EX4)
                // EX-1
                $sheet->setCellValue('Q' . $row, $formatNumber($deformasiEx1['deformasi_10'] ?? '-'));
                $sheet->setCellValue('R' . $row, $formatNumber($deformasiEx1['deformasi_20'] ?? '-'));
                $sheet->setCellValue('S' . $row, $formatNumber($deformasiEx1['deformasi_30'] ?? '-'));
                // EX-2
                $sheet->setCellValue('T' . $row, $formatNumber($deformasiEx2['deformasi_10'] ?? '-'));
                $sheet->setCellValue('U' . $row, $formatNumber($deformasiEx2['deformasi_20'] ?? '-'));
                $sheet->setCellValue('V' . $row, $formatNumber($deformasiEx2['deformasi_30'] ?? '-'));
                // EX-3
                $sheet->setCellValue('W' . $row, $formatNumber($deformasiEx3['deformasi_10'] ?? '-'));
                $sheet->setCellValue('X' . $row, $formatNumber($deformasiEx3['deformasi_20'] ?? '-'));
                $sheet->setCellValue('Y' . $row, $formatNumber($deformasiEx3['deformasi_30'] ?? '-'));
                // EX-4
                $sheet->setCellValue('Z' . $row, $formatNumber($deformasiEx4['deformasi_10'] ?? '-'));
                $sheet->setCellValue('AA' . $row, $formatNumber($deformasiEx4['deformasi_20'] ?? '-'));
                $sheet->setCellValue('AB' . $row, $formatNumber($deformasiEx4['deformasi_30'] ?? '-'));
                
                // INITIAL READINGS DATA (EX1-EX4)
                // EX-1
                $sheet->setCellValue('AC' . $row, $formatNumber($readingsEx1['reading_10'] ?? '-'));
                $sheet->setCellValue('AD' . $row, $formatNumber($readingsEx1['reading_20'] ?? '-'));
                $sheet->setCellValue('AE' . $row, $formatNumber($readingsEx1['reading_30'] ?? '-'));
                // EX-2
                $sheet->setCellValue('AF' . $row, $formatNumber($readingsEx2['reading_10'] ?? '-'));
                $sheet->setCellValue('AG' . $row, $formatNumber($readingsEx2['reading_20'] ?? '-'));
                $sheet->setCellValue('AH' . $row, $formatNumber($readingsEx2['reading_30'] ?? '-'));
                // EX-3
                $sheet->setCellValue('AI' . $row, $formatNumber($readingsEx3['reading_10'] ?? '-'));
                $sheet->setCellValue('AJ' . $row, $formatNumber($readingsEx3['reading_20'] ?? '-'));
                $sheet->setCellValue('AK' . $row, $formatNumber($readingsEx3['reading_30'] ?? '-'));
                // EX-4
                $sheet->setCellValue('AL' . $row, $formatNumber($readingsEx4['reading_10'] ?? '-'));
                $sheet->setCellValue('AM' . $row, $formatNumber($readingsEx4['reading_20'] ?? '-'));
                $sheet->setCellValue('AN' . $row, $formatNumber($readingsEx4['reading_30'] ?? '-'));
                
                // Apply warna latar belakang sesuai dengan kategori
                // PEMBACAAN (EX1-EX4) - E-P: biru muda
                $sheet->getStyle('E' . $row . ':P' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE8F4FD');
                
                // DEFORMASI (EX1-EX4) - Q-AB: hijau muda
                $sheet->getStyle('Q' . $row . ':AB' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFF0F9EB');
                
                // INITIAL READINGS (EX1-EX4) - AC-AN: kuning muda
                $sheet->getStyle('AC' . $row . ':AN' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFFF2CC');
                
                // Apply borders dan alignment untuk semua cell kecuali A (TAHUN) yang akan di-merge
                $sheet->getStyle('B' . $row . ':AN' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Atur alignment khusus untuk kolom numerik (E-AN) ke kanan
                $sheet->getStyle('E' . $row . ':AN' . $row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Atur alignment untuk kolom B, C, D (PERIODE, TANGGAL, DMA) ke center
                $sheet->getStyle('B' . $row . ':D' . $row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $row++;
                $rowCounter++;
            }
            
            // Setelah selesai loop untuk grup tahun ini, lakukan merge cells hanya untuk kolom A (TAHUN)
            $lastRowInGroup = $row - 1;
            
            // Merge cells hanya untuk kolom A (TAHUN)
            if ($rowCount > 1) {
                $sheet->mergeCells('A' . $firstRowInGroup . ':A' . $lastRowInGroup);
                
                // Apply style untuk merged cells kolom A (TAHUN)
                $mergeStyle = [
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]
                    ],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']]
                ];
                
                $sheet->getStyle('A' . $firstRowInGroup . ':A' . $lastRowInGroup)->applyFromArray($mergeStyle);
                
                // Kolom B, C, D TIDAK di-merge karena memiliki nilai yang berbeda per baris
                // Beri style untuk kolom B, C, D di setiap baris dalam grup
                for ($i = $firstRowInGroup; $i <= $lastRowInGroup; $i++) {
                    // PERIODE - Kolom B
                    $sheet->getStyle('B' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]
                        ],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']]
                    ]);
                    
                    // TANGGAL - Kolom C
                    $sheet->getStyle('C' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]
                        ],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']]
                    ]);
                    
                    // DMA - Kolom D
                    $sheet->getStyle('D' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]
                        ],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF5E6FF']]
                    ]);
                }
            } else {
                // Jika hanya 1 row, beri style untuk semua kolom A-D
                $singleRowStyle = [
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]
                    ]
                ];
                
                $sheet->getStyle('A' . $firstRowInGroup)->applyFromArray(array_merge($singleRowStyle, [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']]
                ]));
                $sheet->getStyle('B' . $firstRowInGroup)->applyFromArray(array_merge($singleRowStyle, [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']]
                ]));
                $sheet->getStyle('C' . $firstRowInGroup)->applyFromArray(array_merge($singleRowStyle, [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']]
                ]));
                $sheet->getStyle('D' . $firstRowInGroup)->applyFromArray(array_merge($singleRowStyle, [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF5E6FF']]
                ]));
            }
        }
        
        // ===== STYLING KOLOM =====
        $this->applyExtensoColumnStyling($sheet);
        
        // ===== FORMAT ANGKA =====
        if ($row > 7) {
            // Format kolom E-AN dengan 4 desimal
            $range4Decimal = 'E7:AN' . ($row - 1);
            $sheet->getStyle($range4Decimal)
                ->getNumberFormat()
                ->setFormatCode('#,##0.0000');
            
            // Format kolom D dengan 0 desimal (bilangan bulat)
            $range0Decimal = 'D7:D' . ($row - 1);
            $sheet->getStyle($range0Decimal)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // ===== PERBAIKAN FOOTER SEPERTI BTM =====
        if ($row > 7) {
            // FOOTER dengan format seperti BTM
            $footerRow = $row;
            $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
            $sheet->setCellValue('A' . $footerRow, 'TOTAL REKORD: ' . $totalRecords . ' | Extensometer Monitoring System - PT Indonesia Power');
            
            // Tambahkan informasi filter di footer
            $filterInfo = [];
            if (!empty($tahunFilter)) $filterInfo[] = "Tahun: $tahunFilter";
            if (!empty($periodeFilter)) $filterInfo[] = "Periode: $periodeFilter";
            if (!empty($dmaFilter)) $filterInfo[] = "DMA: $dmaFilter";
            
            if (!empty($filterInfo)) {
                $filterText = 'Filter: ' . implode(', ', $filterInfo);
                $sheet->setCellValue('A' . $footerRow, 'TOTAL REKORD: ' . $totalRecords . ' | ' . $filterText . ' | Extensometer Monitoring System - PT Indonesia Power');
            } else {
                $sheet->setCellValue('A' . $footerRow, 'TOTAL REKORD: ' . $totalRecords . ' | Filter: Semua Data | Extensometer Monitoring System - PT Indonesia Power');
            }
            
            $sheet->getStyle('A' . $footerRow . ':' . $lastCol . $footerRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['argb' => 'FF666666'],
                    'italic' => true
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
        
        // ===== FREEZE PANES =====
        // Freeze kolom A-D (Tahun, Periode, Tanggal, DMA) dan baris 1-6 (header)
        $sheet->freezePane('E7');
        
        // ===== SET PRINT AREA =====
        if ($row > 7) {
            $sheet->getPageSetup()->setPrintArea('A1:AN' . ($row - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:AN6');
        }
        
        // ===== AUTO SIZE KOLOM =====
        foreach (range('A', 'AN') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Create Extenso table headers (3 baris) sesuai dengan view
     */
    private function createExtensoTableHeaders($sheet)
    {
        $row = 4;
        
        // Warna untuk setiap section (sesuai dengan CSS di view)
        $blueLight = 'FFE8F4FD'; // Biru muda untuk PEMBACAAN
        $greenLight = 'FFF0F9EB'; // Hijau muda untuk DEFORMASI
        $yellowLight = 'FFFFF2CC'; // Kuning muda untuk INITIAL READINGS
        $purpleLight = 'FFF5E6FF'; // Ungu muda untuk DMA
        
        // Row 4: Main Header (baris pertama dari header tabel)
        $mainHeaders = [
            ['label' => 'TAHUN', 'colspan' => 1, 'rowspan' => 3, 'col' => 'A', 'color' => $blueLight],
            ['label' => 'PERIODE', 'colspan' => 1, 'rowspan' => 3, 'col' => 'B', 'color' => $blueLight],
            ['label' => 'TANGGAL', 'colspan' => 1, 'rowspan' => 3, 'col' => 'C', 'color' => $blueLight],
            ['label' => 'DMA', 'colspan' => 1, 'rowspan' => 3, 'col' => 'D', 'color' => $purpleLight],
            ['label' => 'PEMBACAAN', 'colspan' => 12, 'rowspan' => 1, 'col' => 'E', 'color' => $blueLight],
            ['label' => 'DEFORMASI', 'colspan' => 12, 'rowspan' => 1, 'col' => 'Q', 'color' => $greenLight],
            ['label' => 'INITIAL READINGS', 'colspan' => 12, 'rowspan' => 1, 'col' => 'AC', 'color' => $yellowLight]
            // Kolom AKSI dihapus
        ];
        
        foreach ($mainHeaders as $header) {
            if ($header['colspan'] == 1 && $header['rowspan'] == 3) {
                // Header dengan rowspan 3
                $cell = $header['col'] . $row;
                $sheet->setCellValue($cell, $header['label']);
                $this->applyHeaderStyleRowspan3($sheet, $cell . ':' . $header['col'] . ($row + 2), $header['color']);
            } else {
                // Header dengan colspan
                $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
                $range = $header['col'] . $row . ':' . $endCol . $row;
                $sheet->mergeCells($range);
                $sheet->setCellValue($header['col'] . $row, $header['label']);
                
                $this->applyMainHeaderStyle($sheet, $range, $header['color']);
            }
        }
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Row 5: Sub Headers (baris kedua - EX-1 sampai EX-4)
        $row = 5;
        $subHeaders = [
            // PEMBACAAN - EX-1 sampai EX-4
            ['label' => 'EX-1', 'colspan' => 3, 'col' => 'E', 'color' => $blueLight],
            ['label' => 'EX-2', 'colspan' => 3, 'col' => 'H', 'color' => $blueLight],
            ['label' => 'EX-3', 'colspan' => 3, 'col' => 'K', 'color' => $blueLight],
            ['label' => 'EX-4', 'colspan' => 3, 'col' => 'N', 'color' => $blueLight],
            
            // DEFORMASI - EX-1 sampai EX-4
            ['label' => 'EX-1', 'colspan' => 3, 'col' => 'Q', 'color' => $greenLight],
            ['label' => 'EX-2', 'colspan' => 3, 'col' => 'T', 'color' => $greenLight],
            ['label' => 'EX-3', 'colspan' => 3, 'col' => 'W', 'color' => $greenLight],
            ['label' => 'EX-4', 'colspan' => 3, 'col' => 'Z', 'color' => $greenLight],
            
            // INITIAL READINGS - EX-1 sampai EX-4
            ['label' => 'EX-1', 'colspan' => 3, 'col' => 'AC', 'color' => $yellowLight],
            ['label' => 'EX-2', 'colspan' => 3, 'col' => 'AF', 'color' => $yellowLight],
            ['label' => 'EX-3', 'colspan' => 3, 'col' => 'AI', 'color' => $yellowLight],
            ['label' => 'EX-4', 'colspan' => 3, 'col' => 'AL', 'color' => $yellowLight]
        ];
        
        foreach ($subHeaders as $header) {
            $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
            $range = $header['col'] . $row . ':' . $endCol . $row;
            $sheet->mergeCells($range);
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $this->applySubHeaderStyle($sheet, $range, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(22);
        
        // Row 6: Detail Headers (baris ketiga - 10 m, 20 m, 30 m untuk setiap EX)
        $row = 6;
        $detailHeaders = [];
        
        // Kolom A-D sudah rowspan 3, jadi kosong
        
        // PEMBACAAN untuk EX-1 sampai EX-4
        for ($ex = 1; $ex <= 4; $ex++) {
            $startCol = $ex == 1 ? 'E' : ($ex == 2 ? 'H' : ($ex == 3 ? 'K' : 'N'));
            $detailHeaders[] = ['label' => '10 m', 'col' => $startCol, 'color' => $blueLight];
            $detailHeaders[] = ['label' => '20 m', 'col' => $this->nextColumn($startCol, 1), 'color' => $blueLight];
            $detailHeaders[] = ['label' => '30 m', 'col' => $this->nextColumn($startCol, 2), 'color' => $blueLight];
        }
        
        // DEFORMASI untuk EX-1 sampai EX-4
        for ($ex = 1; $ex <= 4; $ex++) {
            $startCol = $ex == 1 ? 'Q' : ($ex == 2 ? 'T' : ($ex == 3 ? 'W' : 'Z'));
            $detailHeaders[] = ['label' => '10 m', 'col' => $startCol, 'color' => $greenLight];
            $detailHeaders[] = ['label' => '20 m', 'col' => $this->nextColumn($startCol, 1), 'color' => $greenLight];
            $detailHeaders[] = ['label' => '30 m', 'col' => $this->nextColumn($startCol, 2), 'color' => $greenLight];
        }
        
        // INITIAL READINGS untuk EX-1 sampai EX-4
        for ($ex = 1; $ex <= 4; $ex++) {
            $startCol = $ex == 1 ? 'AC' : ($ex == 2 ? 'AF' : ($ex == 3 ? 'AI' : 'AL'));
            $detailHeaders[] = ['label' => '10 m', 'col' => $startCol, 'color' => $yellowLight];
            $detailHeaders[] = ['label' => '20 m', 'col' => $this->nextColumn($startCol, 1), 'color' => $yellowLight];
            $detailHeaders[] = ['label' => '30 m', 'col' => $this->nextColumn($startCol, 2), 'color' => $yellowLight];
        }
        
        foreach ($detailHeaders as $header) {
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            $this->applyDetailHeaderStyle($sheet, $header['col'] . $row, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(18);
    }
    
    /**
     * Apply company header style (SERAGAM DENGAN HDM)
     */
    private function applyCompanyHeaderStyle($sheet, $range)
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
                'startColor' => ['argb' => 'FF2F75B5'] // Biru sama seperti HDM
            ]
        ]);
    }
    
    /**
     * Apply main title style
     */
    private function applyMainTitleStyle($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => 'FF2C3E50'],
                'name' => 'Calibri'
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE8F4FD']
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF2F75B5']
                ]
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
     * Apply main header style
     */
    private function applyMainHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['argb' => 'FF2C3E50']
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
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);
    }
    
    /**
     * Apply header style with rowspan 3 (TANPA BORDER di tengah untuk rowspan)
     */
    private function applyHeaderStyleRowspan3($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => 'FF2C3E50']
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
                    'color' => ['argb' => 'FFCCCCCC']
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
                'size' => 9,
                'color' => ['argb' => 'FF2C3E50']
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
                    'color' => ['argb' => 'FFCCCCCC']
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
                'size' => 8,
                'color' => ['argb' => 'FF2C3E50']
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
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);
    }
    
    /**
     * Apply column styling untuk sheet extensometer
     */
    private function applyExtensoColumnStyling($sheet)
    {
        // Set column widths
        $columnWidths = [
            'A' => 8,   // TAHUN
            'B' => 10,  // PERIODE
            'C' => 12,  // TANGGAL
            'D' => 8,   // DMA
            
            // PEMBACAAN (EX1-EX4) - 12 kolom
            'E' => 10, 'F' => 10, 'G' => 10,
            'H' => 10, 'I' => 10, 'J' => 10,
            'K' => 10, 'L' => 10, 'M' => 10,
            'N' => 10, 'O' => 10, 'P' => 10,
            
            // DEFORMASI (EX1-EX4) - 12 kolom
            'Q' => 10, 'R' => 10, 'S' => 10,
            'T' => 10, 'U' => 10, 'V' => 10,
            'W' => 10, 'X' => 10, 'Y' => 10,
            'Z' => 10, 'AA' => 10, 'AB' => 10,
            
            // INITIAL READINGS (EX1-EX4) - 12 kolom
            'AC' => 10, 'AD' => 10, 'AE' => 10,
            'AF' => 10, 'AG' => 10, 'AH' => 10,
            'AI' => 10, 'AJ' => 10, 'AK' => 10,
            'AL' => 10, 'AM' => 10, 'AN' => 10,
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
            $url = base_url('extenso/export-excel/export');
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