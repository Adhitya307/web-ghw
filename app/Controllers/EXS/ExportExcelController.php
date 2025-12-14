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
            
            // ===== SHEET 2: GRAFIK AMBANG BATAS =====
            $grafikSheet = $spreadsheet->createSheet();
            $grafikSheet->setTitle('Grafik Ambang');
            $this->createGrafikAmbangSheet($grafikSheet, $pengukuran, $tahun, $periode, $dma);
            
            // ===== SETUP PAGE MARGINS =====
            $consolidatedSheet->getPageMargins()
                ->setTop(0.75)
                ->setRight(0.25)
                ->setLeft(0.25)
                ->setBottom(0.75);
            
            $grafikSheet->getPageMargins()
                ->setTop(0.75)
                ->setRight(0.25)
                ->setLeft(0.25)
                ->setBottom(0.75);
            
            // ===== SETUP HEADER & FOOTER =====
            $this->setupExcelHeaderFooter($consolidatedSheet, $tahun, $periode, $dma);
            $this->setupExcelHeaderFooter($grafikSheet, $tahun, $periode, $dma);
            
            // Set sheet utama ke Data Extensometer
            $spreadsheet->setActiveSheetIndex(0);
            
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
     * Create Grafik Ambang sheet
     */
    private function createGrafikAmbangSheet($sheet, $pengukuran, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // Ambang batas data
        $ambangBatas = [
            'ex1' => ['hijau' => 80.10, 'kuning' => 104.00, 'merah' => 110.90],
            'ex2' => ['hijau' => 46.00, 'kuning' => 80.00, 'merah' => 81.00],
            'ex3' => ['hijau' => 80.10, 'kuning' => 104.00, 'merah' => 110.90],
            'ex4' => ['hijau' => 46.00, 'kuning' => 80.00, 'merah' => 81.00]
        ];
        
        // Pembacaan awal data
        $pembacaanAwal = [
            'ex1' => ['10m' => 35.00, '20m' => 40.95, '30m' => 29.80],
            'ex2' => ['10m' => 22.60, '20m' => 23.70, '30m' => 30.75],
            'ex3' => ['10m' => 37.75, '20m' => 39.15, '30m' => 41.40],
            'ex4' => ['10m' => 33.80, '20m' => 29.30, '30m' => 48.95]
        ];
        
        // WARNA 
        $headerBlue = 'FF0D6EFD';       // Biru untuk header utama
        $headerLightGray = 'FFCED4DA';  // ABU MUDA untuk baris informasi ekspor (diperbaiki dari abu gelap)
        $bacaanColor1 = 'FFFFFFFF';     // PUTIH untuk bacaan kolom pertama
        $bacaanColor2 = 'FFF0F8FF';     // Biru soft untuk selang-seling
        $amanColor = 'FF4CAF50';        // HIJAU CERAH untuk AMAN
        $peringatanColor = 'FFFFC107';  // KUNING CERAH untuk PERINGATAN
        $bahayaColor = 'FFF44336';      // MERAH CERAH untuk BAHAYA
        
        // ===== HEADER UTAMA DAN SUBHEADER =====
        $lastCol = 'BI';
        
        // Row 1: Judul Utama - BIRU
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'GRAFIK & AMBANG BATAS EXTENSOMETER - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Row 2: Laporan Data - BIRU
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN GRAFIK & AMBANG BATAS EXTENSOMETER');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0D6EFD']]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Row 3: Informasi Ekspor dan Filter - ABU MUDA (diperbaiki dari abu gelap)
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]], // WARNA ABU MUDA
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0D6EFD']]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // ===== HEADER TABEL (SEMUA BIRU) =====
        $row = 4;
        
        // Row 4: Main Header - DIPERBAIKI: EX-3 dan EX-4 sudah benar dari AF sampai BJ
        $sheet->setCellValue('A' . $row, 'Rod Extensometer No.');
        
        // EX-1: B-P (15 kolom)
        $sheet->mergeCells('B' . $row . ':P' . $row);
        $sheet->setCellValue('B' . $row, 'EX-1');
        
        // EX-2: Q-AE (15 kolom)
        $sheet->mergeCells('Q' . $row . ':AE' . $row);
        $sheet->setCellValue('Q' . $row, 'EX-2');
        
        // EX-3: AF-AT (15 kolom) - DIPERBAIKI
        $sheet->mergeCells('AF' . $row . ':AT' . $row);
        $sheet->setCellValue('AF' . $row, 'EX-3');
        
        // EX-4: AU-BJ (15 kolom) - DIPERBAIKI
        $sheet->mergeCells('AU' . $row . ':BI' . $row);
        $sheet->setCellValue('AU' . $row, 'EX-4');
        
        // Apply style untuk Row 4 - BIRU SEMUA
        $sheet->getStyle('A' . $row . ':BI' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;
        
        // Row 5: Zona - BIRU
        $sheet->setCellValue('A' . $row, 'Zona');
        $sheet->mergeCells('B' . $row . ':P' . $row);
        $sheet->setCellValue('B' . $row, 'SPILLWAY');
        $sheet->mergeCells('Q' . $row . ':AE' . $row);
        $sheet->setCellValue('Q' . $row, 'SPILLWAY');
        $sheet->mergeCells('AF' . $row . ':AT' . $row);
        $sheet->setCellValue('AF' . $row, 'SPILLWAY');
        $sheet->mergeCells('AU' . $row . ':BI' . $row);
        $sheet->setCellValue('AU' . $row, 'SPILLWAY');
        
        $sheet->getStyle('A' . $row . ':BI' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;
        
        // Row 6: Kedalaman (m) - BIRU - DIPERBAIKI untuk EX-3 dan EX-4
        $sheet->setCellValue('A' . $row, 'Kedalaman (m)');
        
        // EX-1 - 10m (B-F), 20m (G-K), 30m (L-P)
        $sheet->mergeCells('B' . $row . ':F' . $row);
        $sheet->setCellValue('B' . $row, '10');
        $sheet->mergeCells('G' . $row . ':K' . $row);
        $sheet->setCellValue('G' . $row, '20');
        $sheet->mergeCells('L' . $row . ':P' . $row);
        $sheet->setCellValue('L' . $row, '30');
        
        // EX-2 - 10m (Q-U), 20m (V-Z), 30m (AA-AE)
        $sheet->mergeCells('Q' . $row . ':U' . $row);
        $sheet->setCellValue('Q' . $row, '10');
        $sheet->mergeCells('V' . $row . ':Z' . $row);
        $sheet->setCellValue('V' . $row, '20');
        $sheet->mergeCells('AA' . $row . ':AE' . $row);
        $sheet->setCellValue('AA' . $row, '30');
        
        // EX-3 - 10m (AF-AJ), 20m (AK-AO), 30m (AP-AT) - DIPERBAIKI
        $sheet->mergeCells('AF' . $row . ':AJ' . $row);
        $sheet->setCellValue('AF' . $row, '10');
        $sheet->mergeCells('AK' . $row . ':AO' . $row);
        $sheet->setCellValue('AK' . $row, '20');
        $sheet->mergeCells('AP' . $row . ':AT' . $row);
        $sheet->setCellValue('AP' . $row, '30');
        
        // EX-4 - 10m (AU-AY), 20m (AZ-BD), 30m (BE-BI) - DIPERBAIKI
        $sheet->mergeCells('AU' . $row . ':AY' . $row);
        $sheet->setCellValue('AU' . $row, '10');
        $sheet->mergeCells('AZ' . $row . ':BD' . $row);
        $sheet->setCellValue('AZ' . $row, '20');
        $sheet->mergeCells('BE' . $row . ':BI' . $row);
        $sheet->setCellValue('BE' . $row, '30');
        
        // Tambahkan kolom BJ untuk konsistensi
        $sheet->getStyle('BI' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        
        $sheet->getStyle('A' . $row . ':BI' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;
        
        // Row 7: Pemb.Awal (mm) - BIRU - DIPERBAIKI untuk EX-3 dan EX-4
        $sheet->setCellValue('A' . $row, 'Pemb.Awal (mm)');
        
        // Fungsi untuk membuat sel pembacaan awal
        $createPembacaanAwalCell = function($col, $value) use ($row, $sheet, $headerBlue) {
            $sheet->setCellValue($col . $row, $value);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
            ]);
        };
        
        // Fungsi untuk membuat sel ambang batas
        $createAmbangCell = function($col, $label) use ($row, $sheet, $headerBlue) {
            $sheet->setCellValue($col . $row, $label);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
            ]);
        };
        
        // EX-1
        $createPembacaanAwalCell('B', $pembacaanAwal['ex1']['10m']);
        $sheet->mergeCells('C' . $row . ':F' . $row);
        $createAmbangCell('C', 'Ambang Batas');
        
        $createPembacaanAwalCell('G', $pembacaanAwal['ex1']['20m']);
        $sheet->mergeCells('H' . $row . ':K' . $row);
        $createAmbangCell('H', 'Ambang Batas');
        
        $createPembacaanAwalCell('L', $pembacaanAwal['ex1']['30m']);
        $sheet->mergeCells('M' . $row . ':P' . $row);
        $createAmbangCell('M', 'Ambang Batas');
        
        // EX-2
        $createPembacaanAwalCell('Q', $pembacaanAwal['ex2']['10m']);
        $sheet->mergeCells('R' . $row . ':U' . $row);
        $createAmbangCell('R', 'Ambang Batas');
        
        $createPembacaanAwalCell('V', $pembacaanAwal['ex2']['20m']);
        $sheet->mergeCells('W' . $row . ':Z' . $row);
        $createAmbangCell('W', 'Ambang Batas');
        
        $createPembacaanAwalCell('AA', $pembacaanAwal['ex2']['30m']);
        $sheet->mergeCells('AB' . $row . ':AE' . $row);
        $createAmbangCell('AB', 'Ambang Batas');
        
        // EX-3 - DIPERBAIKI
        $createPembacaanAwalCell('AF', $pembacaanAwal['ex3']['10m']);
        $sheet->mergeCells('AG' . $row . ':AJ' . $row);
        $createAmbangCell('AG', 'Ambang Batas');
        
        $createPembacaanAwalCell('AK', $pembacaanAwal['ex3']['20m']);
        $sheet->mergeCells('AL' . $row . ':AO' . $row);
        $createAmbangCell('AL', 'Ambang Batas');
        
        $createPembacaanAwalCell('AP', $pembacaanAwal['ex3']['30m']);
        $sheet->mergeCells('AQ' . $row . ':AT' . $row);
        $createAmbangCell('AQ', 'Ambang Batas');
        
        // EX-4 - DIPERBAIKI
        $createPembacaanAwalCell('AU', $pembacaanAwal['ex4']['10m']);
        $sheet->mergeCells('AV' . $row . ':AY' . $row);
        $createAmbangCell('AV', 'Ambang Batas');
        
        $createPembacaanAwalCell('AZ', $pembacaanAwal['ex4']['20m']);
        $sheet->mergeCells('BA' . $row . ':BD' . $row);
        $createAmbangCell('BA', 'Ambang Batas');
        
        $createPembacaanAwalCell('BE', $pembacaanAwal['ex4']['30m']);
        $sheet->mergeCells('BF' . $row . ':BI' . $row);
        $createAmbangCell('BF', 'Ambang Batas');
        
        // Style untuk kolom A dan BJ
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        
        $sheet->getStyle('BI' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;
        
        // Row 8: Koordinat - BIRU
        $sheet->setCellValue('A' . $row, 'Koordinat');
        
        // Isi "-" untuk semua sel koordinat
        $coordinateCols = $this->getAllColumns('B', 'BI');
        
        foreach ($coordinateCols as $col) {
            $sheet->setCellValue($col . $row, '-');
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
            ]);
        }
        
        // Style untuk kolom A
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
        ]);
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;
        
        // Row 9: Header Bacaan & Ambang Batas DETAIL - DIPERBAIKI untuk EX-3 dan EX-4
        $sheet->setCellValue('A' . $row, 'Tanggal');
        
        // Fungsi untuk membuat header detail untuk satu kedalaman (5 kolom)
        $createDetailHeader = function($startCol, $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor) {
            // Bacaan (mm) - BIRU
            $sheet->setCellValue($startCol . $row, 'Bacaan (mm)');
            $sheet->getStyle($startCol . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            
            // Hijau (mm) - HIJAU CERAH
            $sheet->setCellValue($this->nextColumn($startCol, 1) . $row, 'Hijau (mm)');
            $sheet->getStyle($this->nextColumn($startCol, 1) . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $amanColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            
            // Kuning (mm) - KUNING CERAH
            $sheet->setCellValue($this->nextColumn($startCol, 2) . $row, 'Kuning (mm)');
            $sheet->getStyle($this->nextColumn($startCol, 2) . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $peringatanColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            
            // Merah (mm) - MERAH CERAH
            $sheet->setCellValue($this->nextColumn($startCol, 3) . $row, 'Merah (mm)');
            $sheet->getStyle($this->nextColumn($startCol, 3) . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bahayaColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            
            // Bacaan (mm) akhir - BIRU
            $sheet->setCellValue($this->nextColumn($startCol, 4) . $row, 'Bacaan (mm)');
            $sheet->getStyle($this->nextColumn($startCol, 4) . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
        };
        
        // EX-1: 10m (B), 20m (G), 30m (L)
        $createDetailHeader('B', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('G', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('L', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        
        // EX-2: 10m (Q), 20m (V), 30m (AA)
        $createDetailHeader('Q', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('V', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('AA', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        
        // EX-3: 10m (AF), 20m (AK), 30m (AP) - DIPERBAIKI
        $createDetailHeader('AF', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('AK', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('AP', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        
        // EX-4: 10m (AU), 20m (AZ), 30m (BE) - DIPERBAIKI
        $createDetailHeader('AU', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('AZ', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        $createDetailHeader('BE', $sheet, $row, $headerBlue, $amanColor, $peringatanColor, $bahayaColor);
        
        // Kolom BJ untuk konsistensi
        $sheet->getStyle('BI' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
        ]);
        
        // Style untuk kolom A (Tanggal) - BIRU
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
        ]);
        
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;
        
        // ===== ISI DATA =====
        $startDataRow = $row;
        
        if (empty($pengukuran)) {
            // Jika tidak ada data
            $sheet->mergeCells('A' . $row . ':BI' . $row);
            $sheet->setCellValue('A' . $row, 'Tidak ada data monitoring yang tersedia');
            $sheet->getStyle('A' . $row . ':BI' . $row)->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            $sheet->getRowDimension($row)->setRowHeight(40);
            $row++;
        } else {
            // Urutkan data berdasarkan tanggal ASC
            usort($pengukuran, function($a, $b) {
                $dateA = strtotime($a['tanggal'] ?? '1970-01-01');
                $dateB = strtotime($b['tanggal'] ?? '1970-01-01');
                return $dateA - $dateB;
            });
            
            $rowIndex = 0;
            foreach ($pengukuran as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data pembacaan untuk setiap extensometer
                $pembacaanEx1 = $this->pembacaanEx1Model->where('id_pengukuran', $pid)->first();
                $pembacaanEx2 = $this->pembacaanEx2Model->where('id_pengukuran', $pid)->first();
                $pembacaanEx3 = $this->pembacaanEx3Model->where('id_pengukuran', $pid)->first();
                $pembacaanEx4 = $this->pembacaanEx4Model->where('id_pengukuran', $pid)->first();
                
                // Format tanggal
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $row, $tanggal);
                
                // Tentukan warna baris untuk efek selang-seling
                $isEvenRow = ($rowIndex % 2 == 0);
                
                // Fungsi untuk menentukan status dengan warna SOFT
                $getStatusColorSoft = function($bacaan, $ambang) {
                    if ($bacaan === null || $bacaan === '' || $bacaan === '-') {
                        return 'FFE8F5E9'; // Hijau soft default
                    }
                    $bacaan = (float)$bacaan;
                    
                    // Warna SOFT
                    if ($bacaan <= $ambang['hijau']) {
                        return 'FFE8F5E9'; // HIJAU SOFT
                    } elseif ($bacaan <= $ambang['kuning']) {
                        return 'FFFFF8E1'; // KUNING SOFT
                    } else {
                        return 'FFFCE4EC'; // MERAH SOFT
                    }
                };
                
                // Fungsi untuk menentukan warna teks berdasarkan background SOFT
                $getTextColorSoft = function($bgColor) {
                    return 'FF000000'; // Selalu teks hitam untuk warna soft
                };
                
                // Fungsi format number
                $formatNumber = function($value) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    return number_format((float)$value, 2, '.', '');
                };
                
                // Fungsi untuk membuat data baris untuk satu kedalaman (5 kolom)
                $createDataRow = function($col, $bacaan, $ambang, $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2) {
                    // Bacaan (mm) - PUTIH atau BIRU SOFT selang-seling
                    $bacaanBgColor = $isEvenRow ? $bacaanColor1 : $bacaanColor2;
                    $sheet->setCellValue($col . $row, $formatNumber($bacaan));
                    $sheet->getStyle($col . $row)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bacaanBgColor]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                    
                    // Hijau (mm) - WARNA SOFT untuk nilai ambang
                    $hijauBgColor = 'FFE8F5E9'; // Hijau soft
                    $sheet->setCellValue($this->nextColumn($col, 1) . $row, $formatNumber($ambang['hijau']));
                    $sheet->getStyle($this->nextColumn($col, 1) . $row)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['color' => ['argb' => 'FF000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $hijauBgColor]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                    
                    // Kuning (mm) - WARNA SOFT untuk nilai ambang
                    $kuningBgColor = 'FFFFF8E1'; // Kuning soft
                    $sheet->setCellValue($this->nextColumn($col, 2) . $row, $formatNumber($ambang['kuning']));
                    $sheet->getStyle($this->nextColumn($col, 2) . $row)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['color' => ['argb' => 'FF000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $kuningBgColor]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                    
                    // Merah (mm) - WARNA SOFT untuk nilai ambang
                    $merahBgColor = 'FFFCE4EC'; // Merah soft
                    $sheet->setCellValue($this->nextColumn($col, 3) . $row, $formatNumber($ambang['merah']));
                    $sheet->getStyle($this->nextColumn($col, 3) . $row)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['color' => ['argb' => 'FF000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $merahBgColor]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                    
                    // Bacaan (mm) dengan status warna SOFT
                    $statusBgColor = $getStatusColorSoft($bacaan, $ambang);
                    $statusTextColor = $getTextColorSoft($statusBgColor);
                    $sheet->setCellValue($this->nextColumn($col, 4) . $row, $formatNumber($bacaan));
                    $sheet->getStyle($this->nextColumn($col, 4) . $row)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusTextColor]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $statusBgColor]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                    ]);
                };
                
                // EX-1: 10m (B), 20m (G), 30m (L)
                $createDataRow('B', $pembacaanEx1['pembacaan_10'] ?? null, $ambangBatas['ex1'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('G', $pembacaanEx1['pembacaan_20'] ?? null, $ambangBatas['ex1'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('L', $pembacaanEx1['pembacaan_30'] ?? null, $ambangBatas['ex1'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                
                // EX-2: 10m (Q), 20m (V), 30m (AA)
                $createDataRow('Q', $pembacaanEx2['pembacaan_10'] ?? null, $ambangBatas['ex2'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('V', $pembacaanEx2['pembacaan_20'] ?? null, $ambangBatas['ex2'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('AA', $pembacaanEx2['pembacaan_30'] ?? null, $ambangBatas['ex2'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                
                // EX-3: 10m (AF), 20m (AK), 30m (AP) - DIPERBAIKI
                $createDataRow('AF', $pembacaanEx3['pembacaan_10'] ?? null, $ambangBatas['ex3'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('AK', $pembacaanEx3['pembacaan_20'] ?? null, $ambangBatas['ex3'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('AP', $pembacaanEx3['pembacaan_30'] ?? null, $ambangBatas['ex3'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                
                // EX-4: 10m (AU), 20m (AZ), 30m (BE) - DIPERBAIKI
                $createDataRow('AU', $pembacaanEx4['pembacaan_10'] ?? null, $ambangBatas['ex4'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('AZ', $pembacaanEx4['pembacaan_20'] ?? null, $ambangBatas['ex4'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                $createDataRow('BE', $pembacaanEx4['pembacaan_30'] ?? null, $ambangBatas['ex4'], $sheet, $row, $formatNumber, $getStatusColorSoft, $getTextColorSoft, $isEvenRow, $bacaanColor1, $bacaanColor2);
                
                // Kolom BJ untuk konsistensi
                $sheet->getStyle('BI' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $isEvenRow ? $bacaanColor1 : $bacaanColor2]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                ]);
                
                // Style untuk kolom A (Tanggal) - BIRU untuk header
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                ]);
                
                $row++;
                $rowIndex++;
            }
        }
        
        // ===== FREEZE PANES (DIPERBAIKI: Kolom A akan tetap terlihat saat di-scroll horizontal) =====
        // Freeze kolom A (Tanggal) dan baris 1-9 (header)
        $sheet->freezePane('B10');
        
        // ===== FOOTER =====
        if ($row > $startDataRow) {
            $footerRow = $row;
            $sheet->mergeCells('A' . $footerRow . ':BI' . $footerRow);
            
            $filterInfo = [];
            if (!empty($tahunFilter)) $filterInfo[] = "Tahun: $tahunFilter";
            if (!empty($periodeFilter)) $filterInfo[] = "Periode: $periodeFilter";
            if (!empty($dmaFilter)) $filterInfo[] = "DMA: $dmaFilter";
            
            $totalRecords = count($pengukuran);
            $filterText = !empty($filterInfo) ? implode(', ', $filterInfo) : 'Semua Data';
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . ' | Filter: ' . $filterText . 
                ' | Grafik & Ambang Batas Extensometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':BI' . $footerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF666666'], 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(30);
        }
        
        // ===== FORMAT ANGKA =====
        if ($row > $startDataRow) {
            // Format kolom numerik dengan 2 desimal
            $numericRange = 'B' . $startDataRow . ':BI' . ($row - 1); // Hingga BI karena BJ hanya untuk padding
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // Set font size dan alignment
            $sheet->getStyle($numericRange)
                ->getFont()
                ->setSize(10);
            
            $sheet->getStyle($numericRange)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
        
        // ===== SET COLUMN WIDTHS =====
        $sheet->getColumnDimension('A')->setWidth(18); // Tanggal
        $sheet->getColumnDimension('BI')->setWidth(14); // Kolom terakhir untuk padding
        
        // Set width untuk semua kolom data
        for ($col = 'B'; $col <= 'BI'; $col++) {
            $sheet->getColumnDimension($col)->setWidth(14);
        }
        
        // ===== SET ROW HEIGHTS =====
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(35);
        $sheet->getRowDimension(3)->setRowHeight(28);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(28);
        $sheet->getRowDimension(6)->setRowHeight(28);
        $sheet->getRowDimension(7)->setRowHeight(28);
        $sheet->getRowDimension(8)->setRowHeight(28);
        $sheet->getRowDimension(9)->setRowHeight(25);
        
        // Atur tinggi baris data
        if (!empty($pengukuran)) {
            for ($i = 10; $i <= $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(22);
            }
        }
        
        // ===== SET PRINT AREA =====
        if ($row > $startDataRow) {
            $sheet->getPageSetup()->setPrintArea('A1:BI' . ($row - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:BI' . $row);
        }
        
        // Set wrap text untuk semua header
        $sheet->getStyle('A4:BI9')->getAlignment()->setWrapText(true);
    }
    
    /**
     * Setup Excel Header & Footer
     */
    private function setupExcelHeaderFooter($sheet, $tahun, $periode, $dma)
    {
        $headerFooter = $sheet->getHeaderFooter();
        
        // Build header text
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
        
        // Set Footer
        $headerFooter->setOddFooter(
            '&L&"Calibri"&8Filter: ' . $filterText .
            '&C&"Calibri"&8Halaman &P dari &N' .
            '&R&"Calibri"&8© ' . date('Y') . ' - Sistem Monitoring Extensometer'
        );
    }
    
    /**
     * Create consolidated sheet
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
        $lastCol = 'AN';
        
        // WARNA UNTUK SHEET 1
        $headerBlue = 'FF0D6EFD';       // Biru untuk semua header utama
        $headerLightGray = 'FFCED4DA';  // ABU MUDA untuk baris informasi ekspor (diperbaiki dari abu gelap)
        $dataWhite = 'FFFFFFFF';        // PUTIH untuk nilai data baris ganjil
        $dataBlueSoft = 'FFF0F8FF';     // Biru soft untuk nilai data baris genap
        
        // Row 1: Judul Utama - BIRU SEMUA
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'DATA EXTENSOMETER MONITORING SYSTEM - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Row 2: Laporan Data - BIRU SEMUA
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA EXTENSOMETER');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0D6EFD']]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Row 3: Informasi Ekspor dan Filter - ABU MUDA (diperbaiki dari abu gelap)
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]], // WARNA ABU MUDA
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0D6EFD']]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // ===== HEADER TABEL (SEMUA BIRU) =====
        // Row 4-6: Table Headers (3 baris seperti di view)
        $row = 4;
        
        // Row 4: Main Header - SEMUA BIRU
        $mainHeaders = [
            ['label' => 'TAHUN', 'colspan' => 1, 'rowspan' => 3, 'col' => 'A', 'color' => $headerBlue],
            ['label' => 'PERIODE', 'colspan' => 1, 'rowspan' => 3, 'col' => 'B', 'color' => $headerBlue],
            ['label' => 'TANGGAL', 'colspan' => 1, 'rowspan' => 3, 'col' => 'C', 'color' => $headerBlue],
            ['label' => 'DMA', 'colspan' => 1, 'rowspan' => 3, 'col' => 'D', 'color' => $headerBlue],
            ['label' => 'PEMBACAAN', 'colspan' => 12, 'rowspan' => 1, 'col' => 'E', 'color' => $headerBlue],
            ['label' => 'DEFORMASI', 'colspan' => 12, 'rowspan' => 1, 'col' => 'Q', 'color' => $headerBlue],
            ['label' => 'INITIAL READINGS', 'colspan' => 12, 'rowspan' => 1, 'col' => 'AC', 'color' => $headerBlue]
        ];
        
        foreach ($mainHeaders as $header) {
            if ($header['colspan'] == 1 && $header['rowspan'] == 3) {
                // Header dengan rowspan 3 - BIRU
                $cell = $header['col'] . $row;
                $sheet->setCellValue($cell, $header['label']);
                
                $sheet->getStyle($cell . ':' . $header['col'] . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $header['color']]],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
                ]);
            } else {
                // Header dengan colspan - BIRU
                $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
                $range = $header['col'] . $row . ':' . $endCol . $row;
                $sheet->mergeCells($range);
                $sheet->setCellValue($header['col'] . $row, $header['label']);
                
                $sheet->getStyle($range)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $header['color']]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
                ]);
            }
        }
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Row 5: Sub Headers - SEMUA BIRU
        $row = 5;
        $subHeaders = [
            // PEMBACAAN - EX-1 sampai EX-4
            ['label' => 'EX-1', 'colspan' => 3, 'col' => 'E', 'color' => $headerBlue],
            ['label' => 'EX-2', 'colspan' => 3, 'col' => 'H', 'color' => $headerBlue],
            ['label' => 'EX-3', 'colspan' => 3, 'col' => 'K', 'color' => $headerBlue],
            ['label' => 'EX-4', 'colspan' => 3, 'col' => 'N', 'color' => $headerBlue],
            
            // DEFORMASI - EX-1 sampai EX-4
            ['label' => 'EX-1', 'colspan' => 3, 'col' => 'Q', 'color' => $headerBlue],
            ['label' => 'EX-2', 'colspan' => 3, 'col' => 'T', 'color' => $headerBlue],
            ['label' => 'EX-3', 'colspan' => 3, 'col' => 'W', 'color' => $headerBlue],
            ['label' => 'EX-4', 'colspan' => 3, 'col' => 'Z', 'color' => $headerBlue],
            
            // INITIAL READINGS - EX-1 sampai EX-4
            ['label' => 'EX-1', 'colspan' => 3, 'col' => 'AC', 'color' => $headerBlue],
            ['label' => 'EX-2', 'colspan' => 3, 'col' => 'AF', 'color' => $headerBlue],
            ['label' => 'EX-3', 'colspan' => 3, 'col' => 'AI', 'color' => $headerBlue],
            ['label' => 'EX-4', 'colspan' => 3, 'col' => 'AL', 'color' => $headerBlue]
        ];
        
        foreach ($subHeaders as $header) {
            $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
            $range = $header['col'] . $row . ':' . $endCol . $row;
            $sheet->mergeCells($range);
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $header['color']]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
            ]);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(22);
        
        // Row 6: Detail Headers - SEMUA BIRU
        $row = 6;
        $detailHeaders = [];
        
        // PEMBACAAN untuk EX-1 sampai EX-4
        for ($ex = 1; $ex <= 4; $ex++) {
            $startCol = $ex == 1 ? 'E' : ($ex == 2 ? 'H' : ($ex == 3 ? 'K' : 'N'));
            $detailHeaders[] = ['label' => '10 m', 'col' => $startCol, 'color' => $headerBlue];
            $detailHeaders[] = ['label' => '20 m', 'col' => $this->nextColumn($startCol, 1), 'color' => $headerBlue];
            $detailHeaders[] = ['label' => '30 m', 'col' => $this->nextColumn($startCol, 2), 'color' => $headerBlue];
        }
        
        // DEFORMASI untuk EX-1 sampai EX-4
        for ($ex = 1; $ex <= 4; $ex++) {
            $startCol = $ex == 1 ? 'Q' : ($ex == 2 ? 'T' : ($ex == 3 ? 'W' : 'Z'));
            $detailHeaders[] = ['label' => '10 m', 'col' => $startCol, 'color' => $headerBlue];
            $detailHeaders[] = ['label' => '20 m', 'col' => $this->nextColumn($startCol, 1), 'color' => $headerBlue];
            $detailHeaders[] = ['label' => '30 m', 'col' => $this->nextColumn($startCol, 2), 'color' => $headerBlue];
        }
        
        // INITIAL READINGS untuk EX-1 sampai EX-4
        for ($ex = 1; $ex <= 4; $ex++) {
            $startCol = $ex == 1 ? 'AC' : ($ex == 2 ? 'AF' : ($ex == 3 ? 'AI' : 'AL'));
            $detailHeaders[] = ['label' => '10 m', 'col' => $startCol, 'color' => $headerBlue];
            $detailHeaders[] = ['label' => '20 m', 'col' => $this->nextColumn($startCol, 1), 'color' => $headerBlue];
            $detailHeaders[] = ['label' => '30 m', 'col' => $this->nextColumn($startCol, 2), 'color' => $headerBlue];
        }
        
        foreach ($detailHeaders as $header) {
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $sheet->getStyle($header['col'] . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $header['color']]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]]
            ]);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(18);
        
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
        $globalRowIndex = 0;
        
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
                
                // Format nilai dengan 4 digit di belakang koma
                $formatNumber = function($value, $decimals = 4) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    if (is_numeric($value)) {
                        return number_format((float)$value, $decimals, '.', '');
                    }
                    return $value;
                };
                
                // Format DMA dengan 0 desimal
                $formatDMANumber = function($value) {
                    if ($value === null || $value === '' || $value === '-') {
                        return '-';
                    }
                    if (is_numeric($value)) {
                        return number_format((float)$value, 0, '.', '');
                    }
                    return $value;
                };
                
                // TAHUN - hanya di row pertama grup
                if ($index === 0) {
                    $sheet->setCellValue('A' . $row, $tahun);
                }
                
                // PERIODE
                $sheet->setCellValue('B' . $row, $p['periode'] ?? '-');
                
                // TANGGAL
                $tanggal = !empty($p['tanggal']) ? date('d-m-Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('C' . $row, $tanggal);
                
                // DMA
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
                
                // ===== WARNA NILAI DATA (PUTIH dan BIRU SOFT selang-seling) =====
                $isEvenGlobalRow = ($globalRowIndex % 2 == 0);
                $dataBgColor = $isEvenGlobalRow ? $dataWhite : $dataBlueSoft;
                $textColor = 'FF000000'; // Teks hitam untuk kontras
                
                // Apply warna latar belakang untuk semua kolom data (E-AN)
                $sheet->getStyle('E' . $row . ':AN' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $dataBgColor]],
                    'font' => ['color' => ['argb' => $textColor]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                ]);
                
                // Apply alignment untuk kolom numerik (E-AN) ke kanan
                $sheet->getStyle('E' . $row . ':AN' . $row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                
                // Apply borders untuk kolom B-D
                $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
                ]);
                
                // Atur alignment untuk kolom B, C, D ke center
                $sheet->getStyle('B' . $row . ':D' . $row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                
                $row++;
                $rowCounter++;
                $globalRowIndex++;
            }
            
            // Merge cells untuk kolom A (TAHUN) saja
            $lastRowInGroup = $row - 1;
            
            if ($rowCount > 1) {
                $sheet->mergeCells('A' . $firstRowInGroup . ':A' . $lastRowInGroup);
                
                // Apply style untuk merged cells kolom A (TAHUN) - BIRU
                $sheet->getStyle('A' . $firstRowInGroup . ':A' . $lastRowInGroup)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
                ]);
                
                // Kolom B, C, D TIDAK di-merge
                for ($i = $firstRowInGroup; $i <= $lastRowInGroup; $i++) {
                    // PERIODE - Kolom B (BIRU)
                    $sheet->getStyle('B' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
                    ]);
                    
                    // TANGGAL - Kolom C (BIRU)
                    $sheet->getStyle('C' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
                    ]);
                    
                    // DMA - Kolom D (BIRU)
                    $sheet->getStyle('D' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
                    ]);
                }
            } else {
                // Jika hanya 1 row
                $singleRowStyle = [
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
                ];
                
                $sheet->getStyle('A' . $firstRowInGroup . ':D' . $firstRowInGroup)->applyFromArray($singleRowStyle);
            }
        }
        
        // ===== FREEZE PANES =====
        // Freeze kolom A-D dan baris 1-6 (header)
        $sheet->freezePane('E7');
        
        // ===== FORMAT ANGKA =====
        if ($row > 7) {
            // Format kolom E-AN dengan 4 desimal
            $range4Decimal = 'E7:AN' . ($row - 1);
            $sheet->getStyle($range4Decimal)
                ->getNumberFormat()
                ->setFormatCode('#,##0.0000');
            
            // Format kolom D dengan 0 desimal
            $range0Decimal = 'D7:D' . ($row - 1);
            $sheet->getStyle($range0Decimal)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
                
            // Set font size untuk data numerik
            $sheet->getStyle('E7:AN' . ($row - 1))
                ->getFont()
                ->setSize(10);
        }
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 15,   // TAHUN
            'B' => 15,   // PERIODE
            'C' => 20,   // TANGGAL
            'D' => 15,   // DMA
            
            // PEMBACAAN (EX1-EX4) - 12 kolom
            'E' => 18, 'F' => 18, 'G' => 18,
            'H' => 18, 'I' => 18, 'J' => 18,
            'K' => 18, 'L' => 18, 'M' => 18,
            'N' => 18, 'O' => 18, 'P' => 18,
            
            // DEFORMASI (EX1-EX4) - 12 kolom
            'Q' => 18, 'R' => 18, 'S' => 18,
            'T' => 18, 'U' => 18, 'V' => 18,
            'W' => 18, 'X' => 18, 'Y' => 18,
            'Z' => 18, 'AA' => 18, 'AB' => 18,
            
            // INITIAL READINGS (EX1-EX4) - 12 kolom
            'AC' => 18, 'AD' => 18, 'AE' => 18,
            'AF' => 18, 'AG' => 18, 'AH' => 18,
            'AI' => 18, 'AJ' => 18, 'AK' => 18,
            'AL' => 18, 'AM' => 18, 'AN' => 18,
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        $sheet->getRowDimension(1)->setRowHeight(35);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(25);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(25);
        $sheet->getRowDimension(6)->setRowHeight(22);
        
        // Atur tinggi baris data
        for ($i = 7; $i <= $row; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }
        
        // ===== FOOTER =====
        if ($row > 7) {
            $footerRow = $row;
            $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
            
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
                ],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(30);
        }
        
        // ===== SET PRINT AREA =====
        if ($row > 7) {
            $sheet->getPageSetup()->setPrintArea('A1:AN' . ($row - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:AN6');
        }
        
        // Set wrap text untuk semua header
        $sheet->getStyle('A4:AN6')->getAlignment()->setWrapText(true);
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
     * Helper function to get all columns between start and end
     */
    private function getAllColumns($start, $end)
    {
        $columns = [];
        $current = $start;
        
        while ($current !== $end) {
            $columns[] = $current;
            $current = $this->nextColumn($current, 1);
        }
        $columns[] = $end;
        
        return $columns;
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