<?php

namespace App\Controllers\LeftPiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\MetrikModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
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
    protected $pembacaanModel;
    protected $metrikModel;
    protected $ireadingA;
    protected $ireadingB;
    protected $perhitunganModel;

    public function __construct()
    {
        // Initialize models
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
        $this->pembacaanModel = new TPembacaanLeftPiezModel();
        $this->metrikModel = new MetrikModel();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->perhitunganModel = new PerhitunganLeftPiezModel();
    }

    /**
     * Export Excel dengan header yang sama persis seperti di view
     */
    public function export()
{
    // Cek login dari session
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('/auth/login');
    }

    try {
        // ===== OPTIMASI MEMORY =====
        ini_set('memory_limit', '1024M'); // Tingkatkan memory limit
        ini_set('max_execution_time', 300); // Tingkatkan waktu eksekusi
        gc_enable(); // Aktifkan garbage collection
        
        // Get filter parameters
        $tahun = $this->request->getGet('tahun');
        $periode = $this->request->getGet('periode');
        $dma = $this->request->getGet('dma');
        
        // ===== OPTIMASI QUERY =====
        // Batasi data jika terlalu banyak
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
        
        // Batasi data untuk menghindari memory overflow
        $query->limit(1000); // Maksimal 1000 record
        
        $pengukuranData = $query->findAll();
        
        // Jika data terlalu banyak, beri peringatan
        $totalRecords = count($pengukuranData);
        if ($totalRecords > 1000) {
            log_message('warning', 'Data pengukuran melebihi 1000 record, hanya menampilkan 1000 record pertama');
        }
        
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        
        // ===== OPTIMASI MEMORY PHPSPREADSHEET =====
        $spreadsheet->getProperties()
            ->setCreator("PT Indonesia Power")
            ->setLastModifiedBy("Piezometer Monitoring System")
            ->setTitle("Laporan Piezometer Left Bank")
            ->setSubject("Data Piezometer")
            ->setDescription("Laporan ekspor data piezometer Left Bank")
            ->setKeywords("piezometer left bank indonesia power");
            
        // ===== SETUP DEFAULT STYLE =====
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Calibri')
            ->setSize(9);
        
        // ===== SHEET 1: DATA PIEZOMETER UTAMA =====
        $mainSheet = $spreadsheet->getActiveSheet();
        $mainSheet->setTitle('Data Piezometer');
        $this->createMainSheet($mainSheet, $pengukuranData, $tahun, $periode, $dma);
        
        // ===== SHEET 2: GRAFIK HISTORY L1-L3 =====
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Grafik History L1-L3');
        $this->createGrafikHistoryL1L3Sheet($sheet2, $pengukuranData, $tahun, $periode, $dma);
        
        // ===== SHEET 3: GRAFIK HISTORY L4-L6 =====
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Grafik History L4-L6');
        $this->createGrafikHistoryL4L6Sheet($sheet3, $pengukuranData, $tahun, $periode, $dma);
        
        // ===== SHEET 4: GRAFIK HISTORY L7-L9 =====
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Grafik History L7-L9');
        $this->createGrafikHistoryL7L9Sheet($sheet4, $pengukuranData, $tahun, $periode, $dma);
        
        // ===== SHEET 5: GRAFIK HISTORY L10-SPZ02 =====
        $sheet5 = $spreadsheet->createSheet();
        $sheet5->setTitle('Grafik History L10-SPZ02');
        $this->createGrafikHistoryL10SPZ02Sheet($sheet5, $pengukuranData, $tahun, $periode, $dma);
        
        // Set sheet utama kembali
        $spreadsheet->setActiveSheetIndex(0);
        
        // ===== SETUP PAGE MARGINS =====
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheet->getPageMargins()
                ->setTop(0.75)
                ->setRight(0.25)
                ->setLeft(0.25)
                ->setBottom(0.75);
        }
        
        // ===== OPTIMASI MEMORY SEBELUM OUTPUT =====
        gc_collect_cycles(); // Force garbage collection
        
        // ===== SAVE FILE =====
        $writer = new Xlsx($spreadsheet);
        $filename = 'Piezometer_Left_Bank_Export_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        $writer->save('php://output');
        
        // ===== CLEANUP MEMORY =====
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $writer);
        gc_collect_cycles();
        
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Error exporting Excel: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
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
 * Helper function untuk konversi yang aman
 */
private function safeConvert($value, $conversionFactor = 0.3048)
{
    if ($value === null || $value === '' || $value === '-') {
        return 0;
    }
    
    // Pastikan $value adalah numeric
    if (!is_numeric($value)) {
        // Coba bersihkan string
        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9.-]/', '', $value);
        
        if (!is_numeric($value)) {
            return 0;
        }
    }
    
    return (float)$value * $conversionFactor;
}

/**
 * Helper function untuk format angka yang aman
 */
private function safeNumberFormat($value, $decimals = 2)
{
    if ($value === null || $value === '' || $value === '-') {
        return '0.00';
    }
    
    if (!is_numeric($value)) {
        return '0.00';
    }
    
    return number_format((float)$value, $decimals);
}
    /**
     * Create Main Sheet dengan struktur header sama persis seperti view
     */
    private function createMainSheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA =====
        $headerLightBlue = 'FFE3F2FD';      // Biru muda untuk header
        $bgReading = 'FFE8F4FD';           // Biru muda untuk BACAAN
        $bgCalculation = 'FFF0F9EB';       // Hijau muda untuk PERHITUNGAN
        $bgInitial = 'FFE6FFED';           // Hijau muda untuk INITIAL READINGS
        $bgMetrik = 'FFFFF2CC';            // Kuning muda untuk BACAAN METRIK
        $bgInfoColumn = 'FFE7F1FF';        // Biru muda untuk kolom info
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $headerLightGray = 'FFCED4DA';     // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';           // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';       // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'BX'; // Total kolom: A sampai BX (tanpa kolom AKSI)
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - LEFT BANK - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA PIEZOMETER LEFT BANK');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // ===== HEADER TABEL =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 6; // Row 10 adalah baris terakhir header
        
        // ===== ROW 4: Main Header =====
        // Kolom 1-5: Fixed columns dengan rowspan 7
        $sheet->setCellValue('A' . $currentRow, 'TAHUN');
        $sheet->mergeCells('A' . $currentRow . ':A' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . $headerEndRow, $headerLightBlue);
        
        $sheet->setCellValue('B' . $currentRow, 'PERIODE');
        $sheet->mergeCells('B' . $currentRow . ':B' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'B' . $currentRow, 'B' . $headerEndRow, $headerLightBlue);
        
        $sheet->setCellValue('C' . $currentRow, 'TANGGAL');
        $sheet->mergeCells('C' . $currentRow . ':C' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'C' . $currentRow, 'C' . $headerEndRow, $headerLightBlue);
        
        $sheet->setCellValue('D' . $currentRow, 'DMA');
        $sheet->mergeCells('D' . $currentRow . ':D' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'D' . $currentRow, 'D' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('E' . $currentRow, 'CH BULANAN');
        $sheet->mergeCells('E' . $currentRow . ':E' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'E' . $currentRow, 'E' . $headerEndRow, $bgInfoColumn);
        
        // BACAAN METRIK - colspan 22
        $sheet->setCellValue('F' . $currentRow, 'BACAAN METRIK');
        $sheet->mergeCells('F' . $currentRow . ':AA' . $currentRow);
        $this->applyColspanStyle($sheet, 'F' . $currentRow, 'AA' . $currentRow, $bgMetrik);
        
        // KONVERSI - colspan 2
        $sheet->setCellValue('AB' . $currentRow, 'KONVERSI');
        $sheet->mergeCells('AB' . $currentRow . ':AC' . $currentRow);
        $this->applyColspanStyle($sheet, 'AB' . $currentRow, 'AC' . $currentRow, $bgCalculation);
        
        // BACAAN PIEZOMETER METRIK - colspan 11
        $sheet->setCellValue('AD' . $currentRow, 'BACAAN PIEZOMETER METRIK');
        $sheet->mergeCells('AD' . $currentRow . ':AN' . $currentRow);
        $this->applyColspanStyle($sheet, 'AD' . $currentRow, 'AN' . $currentRow, $bgReading);
        
        // PERHITUNGAN - colspan 12
        $sheet->setCellValue('AO' . $currentRow, 'PERHITUNGAN PIEZOMETER');
        $sheet->mergeCells('AO' . $currentRow . ':AZ' . $currentRow);
        $this->applyColspanStyle($sheet, 'AO' . $currentRow, 'AZ' . $currentRow, $bgCalculation);
        
        // INITIAL READINGS A - colspan 12
        $sheet->setCellValue('BA' . $currentRow, 'INITIAL READINGS A');
        $sheet->mergeCells('BA' . $currentRow . ':BL' . $currentRow);
        $this->applyColspanStyle($sheet, 'BA' . $currentRow, 'BL' . $currentRow, $bgInitial);
        
        // INITIAL READINGS B - colspan 12
        $sheet->setCellValue('BM' . $currentRow, 'INITIAL READINGS B');
        $sheet->mergeCells('BM' . $currentRow . ':BX' . $currentRow);
        $this->applyColspanStyle($sheet, 'BM' . $currentRow, 'BX' . $currentRow, $bgInitial);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers =====
        // BACAAN METRIK Sub Headers
        $titikList = ['L-01', 'L-02', 'L-03', 'L-04', 'L-05', 'L-06', 'L-07', 'L-08', 'L-09', 'L-10', 'SPZ-02'];
        $currentCol = 'F';
        
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, $titik);
            $nextCol = $this->nextColumn($currentCol, 1);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $nextCol . $currentRow);
            $this->applyMergedCellStyle($sheet, $currentCol . $currentRow, $nextCol . $currentRow, $bgMetrik, false);
            $currentCol = $this->nextColumn($nextCol, 1);
        }
        
        // KONVERSI Sub Headers - rowspan 6
        $sheet->setCellValue('AB' . $currentRow, 'FEET → M');
        $sheet->mergeCells('AB' . $currentRow . ':AB' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AB' . $currentRow, 'AB' . $headerEndRow, $bgCalculation);
        
        $sheet->setCellValue('AC' . $currentRow, 'INCH → M');
        $sheet->mergeCells('AC' . $currentRow . ':AC' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AC' . $currentRow, 'AC' . $headerEndRow, $bgCalculation);
        
        // BACAAN PIEZOMETER METRIK Sub Headers - rowspan 6
        $currentCol = 'AD';
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, $titik);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgReading);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // PERHITUNGAN Sub Headers
        $perhitunganHeaders = ['No.Titik', 'L-01', 'L-02', 'L-03', 'L-04', 'L-05', 'L-06', 'L-07', 'L-08', 'L-09', 'L-10', 'SPZ-02'];
        $currentCol = 'AO';
        foreach ($perhitunganHeaders as $header) {
            $sheet->setCellValue($currentCol . $currentRow, $header);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // INITIAL READINGS A Sub Headers
        $currentCol = 'BA';
        foreach ($perhitunganHeaders as $header) {
            $sheet->setCellValue($currentCol . $currentRow, $header);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // INITIAL READINGS B Sub Headers
        $currentCol = 'BM';
        foreach ($perhitunganHeaders as $header) {
            $sheet->setCellValue($currentCol . $currentRow, $header);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 6: Column Headers =====
        // BACAAN METRIK Headers (Feet & Inch)
        $currentCol = 'F';
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, 'Feet');
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgMetrik);
            
            $currentCol = $this->nextColumn($currentCol, 1);
            $sheet->setCellValue($currentCol . $currentRow, 'Inch');
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgMetrik);
            
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // PERHITUNGAN Headers
        $perhitunganValues1 = ['Elev.Piez', '650.64', '650.66', '616.55', '580.26', '700.76', '690.09', '653.36', '659.14', '622.45', '580.36', '700.08'];
        $currentCol = 'AO';
        foreach ($perhitunganValues1 as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // INITIAL READINGS A Headers
        $initialAValues = ['Elev.Piez', '650.64', '650.6', '616.55', '580.26', '700.76', '690.09', '653.36', '659.14', '622.45', '580.36', '700.08'];
        $currentCol = 'BA';
        foreach ($initialAValues as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // INITIAL READINGS B Headers
        $initialBValues = ['Elev.Piez', '71.5', '73', '59', '50', '62', '62', '40', '55.5', '57', '51.5', '70'];
        $currentCol = 'BM';
        foreach ($initialBValues as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 7: Column Headers =====
        // PERHITUNGAN Headers - Kedalaman
        $perhitunganValues2 = ['Kedalaman', '71.5', '73', '59', '50', '62', '62', '40', '55.5', '57', '51.5', '70'];
        $currentCol = 'AO';
        foreach ($perhitunganValues2 as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 8: Column Headers =====
        $sheet->setCellValue('AO' . $currentRow, 'Record Max/Min');
        $sheet->mergeCells('AO' . $currentRow . ':AO' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AO' . $currentRow, 'AO' . $headerEndRow, $bgCalculation);
        
        // Nilai-nilai untuk Record Max/Min (baris 8)
        $recordValues1 = ['636.21', '624.41', '603.77', '571.01', '667.89', '635.53', '624.96', '607.32', '582.61', '562.11', '671.18'];
        $currentCol = 'AP';
        foreach ($recordValues1 as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 9: Column Headers =====
        $recordValues2 = ['638.72', '625.37', '609.03', '578.76', '669.80', '658.30', '638.21', '607.70', '585.89', '563.26', '671.18'];
        $currentCol = 'AP';
        foreach ($recordValues2 as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 10: Column Headers =====
        $recordValues3 = ['634.65', '618.29', '602.06', '562.76', '666.86', '628.09', '613.36', '603.64', '565.45', '561.07', '630.08'];
        $currentCol = 'AP';
        foreach ($recordValues3 as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        // Apply border luar untuk seluruh area header (A4:BX10)
        $headerOuterRange = 'A' . $headerStartRow . ':BX' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== APPLY VERTICAL SEPARATOR BORDERS UNTUK HEADER =====
        // Garis vertikal pemisah antar bagian utama - HANYA untuk header
        $verticalSeparators = ['E', 'AA', 'AC', 'AN', 'AZ', 'BL'];
        foreach ($verticalSeparators as $col) {
            for ($row = $headerStartRow; $row <= $headerEndRow; $row++) {
                $sheet->getStyle($col . $row)->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ]);
            }
        }
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA (ROW 10) =====
        // Tambahkan border bawah tebal pada row 10 (LAST HEADER ROW)
        $headerBottomRow = $headerEndRow; // Row 10
        $headerBottomRange = 'A' . $headerBottomRow . ':BX' . $headerBottomRow;
        
        $sheet->getStyle($headerBottomRange)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // Tambahkan juga border bawah tebal untuk kolom pemisah vertikal
        foreach ($verticalSeparators as $col) {
            $sheet->getStyle($col . $headerBottomRow)->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF000000']
                    ]
                ]
            ]);
        }
        
        // Mulai data di row 11
        $currentRow = $headerEndRow + 1;
        
        // ===== ISI DATA =====
        $startDataRow = $currentRow;
        
        if (empty($pengukuranData)) {
            // Jika tidak ada data
            $sheet->mergeCells('A' . $currentRow . ':BX' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Tidak ada data piezometer yang tersedia');
            $sheet->getStyle('A' . $currentRow . ':BX' . $currentRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
            $currentRow++;
        } else {
            // Group data by tahun untuk rowspan
            $groupedData = [];
            foreach ($pengukuranData as $item) {
                $tahun = $item['tahun'];
                if (!isset($groupedData[$tahun])) {
                    $groupedData[$tahun] = [];
                }
                $groupedData[$tahun][] = $item;
            }
            
            // Urutkan tahun secara ascending
            ksort($groupedData);
            
            $globalRowIndex = 0;
            
            foreach ($groupedData as $tahun => $itemsInYear) {
                $rowCount = count($itemsInYear);
                
                // Urutkan berdasarkan tanggal
                usort($itemsInYear, function($a, $b) {
                    $dateA = strtotime($a['tanggal']);
                    $dateB = strtotime($b['tanggal']);
                    return $dateA - $dateB;
                });
                
                // Simpan row pertama untuk grup tahun ini
                $firstRowInGroup = $currentRow;
                
                foreach ($itemsInYear as $index => $p) {
                    $pid = $p['id_pengukuran'];
                    
                    // Ambil semua data terkait
                    $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                    $metrikData = $this->metrikModel->where('id_pengukuran', $pid)->first();
                    $initialAData = $this->ireadingA->getByPengukuran($pid);
                    $initialBData = $this->ireadingB->getByPengukuran($pid);
                    $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                    
                    // Format data
                    $formatNumber = function($value) {
                        if ($value === null || $value === '' || $value === '-') {
                            return '-';
                        }
                        if (is_numeric($value)) {
                            return number_format((float)$value, 4, '.', '');
                        }
                        return $value;
                    };
                    
                    // TAHUN - hanya di row pertama grup
                    if ($index === 0) {
                        $sheet->setCellValue('A' . $currentRow, $tahun);
                    }
                    
                    // PERIODE
                    $sheet->setCellValue('B' . $currentRow, $p['periode'] ?? '-');
                    
                    // TANGGAL
                    $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                    $sheet->setCellValue('C' . $currentRow, $tanggal);
                    
                    // DMA
                    $dmaValue = $p['dma'] ?? '-';
                    if (is_numeric($dmaValue)) {
                        $sheet->setCellValue('D' . $currentRow, (int)$dmaValue);
                    } else {
                        $sheet->setCellValue('D' . $currentRow, $dmaValue);
                    }
                    
                    // CH BULANAN
                    $sheet->setCellValue('E' . $currentRow, $formatNumber($p['temp_id'] ?? '-'));
                    
                    // ===== BACAAN METRIK (Feet & Inch) =====
                    $currentCol = 'F';
                    $titikListDB = ['L01', 'L02', 'L03', 'L04', 'L05', 'L06', 'L07', 'L08', 'L09', 'L10', 'SPZ02'];
                    
                    foreach ($titikListDB as $titik) {
                        $bacaan = $pembacaanData[$titik] ?? null;
                        $feet = $bacaan['feet'] ?? null;
                        $inch = $bacaan['inch'] ?? null;
                        
                        $sheet->setCellValue($currentCol . $currentRow, $formatNumber($feet));
                        $currentCol = $this->nextColumn($currentCol, 1);
                        $sheet->setCellValue($currentCol . $currentRow, $formatNumber($inch));
                        $currentCol = $this->nextColumn($currentCol, 1);
                    }
                    
                    // ===== KONVERSI =====
                    $sheet->setCellValue('AB' . $currentRow, '0.3048');
                    $sheet->setCellValue('AC' . $currentRow, '0.0254');
                    
                    // ===== BACAAN PIEZOMETER METRIK =====
                    $currentCol = 'AD';
                    $metrikFields = ['l_01', 'l_02', 'l_03', 'l_04', 'l_05', 'l_06', 'l_07', 'l_08', 'l_09', 'l_10', 'spz_02'];
                    
                    foreach ($metrikFields as $field) {
                        $value = $metrikData[$field] ?? null;
                        $sheet->setCellValue($currentCol . $currentRow, $formatNumber($value));
                        $currentCol = $this->nextColumn($currentCol, 1);
                    }
                    
                    // ===== PERHITUNGAN PIEZOMETER =====
                    $currentCol = 'AO';
                    
                    // No.Titik - Elev.Piez
                    $sheet->setCellValue($currentCol . $currentRow, 'Elev.Piez');
                    $currentCol = $this->nextColumn($currentCol, 1);
                    
                    // Nilai t_psmetrik untuk setiap titik
                    foreach ($titikListDB as $titik) {
                        $perhitungan = null;
                        foreach ($perhitunganData as $pData) {
                            if ($pData['tipe_piezometer'] == $titik) {
                                $perhitungan = $pData;
                                break;
                            }
                        }
                        
                        $value = $perhitungan['t_psmetrik'] ?? null;
                        $sheet->setCellValue($currentCol . $currentRow, $formatNumber($value));
                        $currentCol = $this->nextColumn($currentCol, 1);
                    }
                    
                    // ===== INITIAL READINGS A =====
                    $currentCol = 'BA';
                    
                    // No.Titik - Elev.Piez
                    $sheet->setCellValue($currentCol . $currentRow, 'Elev.Piez');
                    $currentCol = $this->nextColumn($currentCol, 1);
                    
                    // Nilai untuk setiap titik
                    $titikListView = ['L_01', 'L_02', 'L_03', 'L_04', 'L_05', 'L_06', 'L_07', 'L_08', 'L_09', 'L_10', 'SPZ_02'];
                    foreach ($titikListView as $titik) {
                        $value = null;
                        foreach ($initialAData as $item) {
                            if (strtoupper($item['titik_piezometer']) == $titik) {
                                $value = $item['Elv_Piez'] ?? null;
                                break;
                            }
                        }
                        $sheet->setCellValue($currentCol . $currentRow, $formatNumber($value));
                        $currentCol = $this->nextColumn($currentCol, 1);
                    }
                    
                    // ===== INITIAL READINGS B =====
                    $currentCol = 'BM';
                    
                    // No.Titik - Elev.Piez
                    $sheet->setCellValue($currentCol . $currentRow, 'Elev.Piez');
                    $currentCol = $this->nextColumn($currentCol, 1);
                    
                    // Nilai untuk setiap titik
                    foreach ($titikListView as $titik) {
                        $value = null;
                        foreach ($initialBData as $item) {
                            if (strtoupper($item['titik_piezometer']) == $titik) {
                                $value = $item['Elv_Piez'] ?? null;
                                break;
                            }
                        }
                        $sheet->setCellValue($currentCol . $currentRow, $formatNumber($value));
                        $currentCol = $this->nextColumn($currentCol, 1);
                    }
                    
                    $currentRow++;
                    $globalRowIndex++;
                }
                
                // Merge cells untuk kolom A (TAHUN) jika lebih dari 1 row
                if ($rowCount > 1) {
                    $lastRowInGroup = $currentRow - 1;
                    $sheet->mergeCells('A' . $firstRowInGroup . ':A' . $lastRowInGroup);
                }
            }
            
            // ===== APPLY STYLES UNTUK SELURUH AREA DATA =====
            if ($currentRow > $startDataRow) {
                $dataAreaStart = $startDataRow;
                $dataAreaEnd = $currentRow - 1;
                $dataAreaRange = 'A' . $dataAreaStart . ':BX' . $dataAreaEnd;
                
                // 1. Apply warna latar belakang untuk semua data (selang-seling)
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                    $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                    $rowRange = 'A' . $row . ':BX' . $row;
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border vertikal untuk SETIAP kolom (A sampai BX)
                // Gunakan teknik yang lebih efisien dengan apply border ke seluruh area
                $allDataColumns = [];
                for ($col = 'A'; $col <= 'BX'; $col++) {
                    $allDataColumns[] = $col;
                }
                
                // Apply border kanan tipis untuk semua kolom kecuali kolom terakhir
                foreach ($allDataColumns as $col) {
                    if ($col < 'BX') {
                        $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                        $sheet->getStyle($colRange)->applyFromArray([
                            'borders' => [
                                'right' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => 'FFE0E0E0'] // Abu-abu sangat muda
                                ]
                            ]
                        ]);
                    }
                }
                
                // 3. Apply border horizontal untuk semua baris data
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $rowRange = 'A' . $row . ':BX' . $row;
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'borders' => [
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFE0E0E0']
                            ]
                        ]
                    ]);
                }
                
                // 4. Apply border vertikal tebal untuk pemisah utama
                foreach ($verticalSeparators as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['argb' => 'FF000000']
                            ]
                        ]
                    ]);
                }
                
                // 5. Alignment untuk kolom numerik (kanan)
                $numericColumns = array_merge(
                    $this->getColumnRange('F', 'AA'), // Bacaan Metrik
                    $this->getColumnRange('AB', 'AC'), // Konversi
                    $this->getColumnRange('AD', 'AN'), // Bacaan Piezometer Metrik
                    $this->getColumnRange('AP', 'AZ'), // Perhitungan (kecuali AO)
                    $this->getColumnRange('BB', 'BL'), // Initial A (kecuali BA)
                    $this->getColumnRange('BN', 'BX')  // Initial B (kecuali BM)
                );
                
                foreach ($numericColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // 6. Alignment untuk kolom teks (tengah)
                $textColumns = ['A', 'B', 'C', 'D', 'E', 'AO', 'BA', 'BM'];
                foreach ($textColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // 7. Alignment khusus untuk kolom merged (TAHUN)
                if ($currentRow > $startDataRow) {
                    // Cari semua range merged di kolom A
                    $mergedRanges = $sheet->getMergeCells();
                    foreach ($mergedRanges as $mergedRange) {
                        if (strpos($mergedRange, 'A') === 0) {
                            $sheet->getStyle($mergedRange)
                                ->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                                ->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }
                }
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('F11'); // Freeze header rows (1-10) dan 5 kolom pertama, mulai data di row 11
        
        // ===== FORMAT ANGKA =====
        if ($currentRow > $startDataRow) {
            // Format semua kolom numerik dengan 4 desimal
            $numericRange = 'F' . $startDataRow . ':BX' . ($currentRow - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('0.0000');
            
            // Format DMA (kolom D) tanpa desimal
            $sheet->getStyle('D' . $startDataRow . ':D' . ($currentRow - 1))
                ->getNumberFormat()
                ->setFormatCode('0');
                
            // Format CH BULANAN (kolom E) dengan 4 desimal
            $sheet->getStyle('E' . $startDataRow . ':E' . ($currentRow - 1))
                ->getNumberFormat()
                ->setFormatCode('0.0000');
        }
        
        // ===== SET COLUMN WIDTHS =====
        // Set width untuk semua kolom
        $columnWidths = [
            'A' => 8,   // TAHUN
            'B' => 10,  // PERIODE
            'C' => 12,  // TANGGAL
            'D' => 8,   // DMA
            'E' => 12,  // CH BULANAN
        ];
        
        // Set width untuk kolom data (F sampai BX)
        for ($col = 'F'; $col <= 'BX'; $col++) {
            $columnWidths[$col] = 8;
        }
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        // Atur tinggi baris header
        for ($i = 1; $i <= 3; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }
        
        // Atur tinggi baris header tabel (row 4-10)
        for ($i = 4; $i <= 10; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        // Atur tinggi baris data
        if (!empty($pengukuranData)) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        if ($currentRow > $startDataRow) {
            $footerRow = $currentRow;
            $sheet->mergeCells('A' . $footerRow . ':BX' . $footerRow);
            
            $filterInfo = [];
            if (!empty($tahunFilter)) $filterInfo[] = "Tahun: $tahunFilter";
            if (!empty($periodeFilter)) $filterInfo[] = "Periode: $periodeFilter";
            if (!empty($dmaFilter)) $filterInfo[] = "DMA: $dmaFilter";
            
            $totalRecords = count($pengukuranData);
            $filterText = !empty($filterInfo) ? implode(', ', $filterInfo) : 'Semua Data';
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . ' | Filter: ' . $filterText . 
                ' | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':BX' . $footerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF666666'], 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(25);
        }
        
        // ===== SET PRINT AREA =====
        if ($currentRow > $startDataRow) {
            $sheet->getPageSetup()->setPrintArea('A1:BX' . ($currentRow - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:BX10');
        }
        
        // ===== AUTOFILTER =====
        if ($currentRow > $startDataRow) {
            $sheet->setAutoFilter('A' . ($startDataRow - 1) . ':BX' . ($currentRow - 1));
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter);
    }
    
    /**
     * Create Sheet untuk Grafik History L1-L3 dengan struktur sama persis seperti view
     */
    private function createGrafikHistoryL1L3Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA =====
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $headerLightBlue = 'FFE3F2FD';     // Biru muda untuk header
        $bgAman = 'FFD4EDDA';             // Hijau muda untuk AMAN
        $bgPeringatan = 'FFFFF3CD';       // Kuning muda untuk PERINGATAN
        $bgBahaya = 'FFF8D7DA';           // Merah muda untuk BAHAYA
        $headerAman = 'FF28A745';         // Hijau untuk header AMAN
        $headerPeringatan = 'FFFFC107';   // Kuning untuk header PERINGATAN
        $headerBahaya = 'FFDC3545';       // Merah untuk header BAHAYA
        $headerLightGray = 'FFCED4DA';    // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';          // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';      // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'S'; // Total kolom: A sampai S (18 kolom)
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - GRAFIK HISTORY L1-L3 - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'GRAFIK HISTORY DATA PIEZOMETER L1-L3');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // ===== HEADER TABEL (SAMA SEPERTI DI VIEW) =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 6; // Row 10 adalah baris terakhir header
        
        // ===== ROW 4: Main Headers (Row 1 di view) =====
        // Pisometer No. dengan rowspan 2
        $sheet->setCellValue('A' . $currentRow, 'Pisometer No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . ($currentRow + 1), $headerLightBlue);
        
        // L-1 dengan colspan 6
        $sheet->setCellValue('B' . $currentRow, 'L-1');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerBlue);
        
        // L-2 dengan colspan 6
        $sheet->setCellValue('H' . $currentRow, 'L-2');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerBlue);
        
        // L-3 dengan colspan 6
        $sheet->setCellValue('N' . $currentRow, 'L-3');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, $headerBlue);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers (Row 2 di view) =====
        // Upstream untuk L-1
        $sheet->setCellValue('B' . $currentRow, 'Upstream');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerLightBlue);
        
        // As Bend untuk L-2
        $sheet->setCellValue('H' . $currentRow, 'As Bend');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerLightBlue);
        
        // Downstream untuk L-3
        $sheet->setCellValue('N' . $currentRow, 'Downstream');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 6: Data Headers (Row 3 di view) =====
        // Kolom A: Elev.Piso.Atas(El.m)
        $sheet->setCellValue('A' . $currentRow, 'Elev.Piso.Atas(El.m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-1: 650.64 dengan colspan 2
        $sheet->setCellValue('B' . $currentRow, '650.64');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-1 Ambang Batas dengan colspan 4 dan rowspan 4 (D6:G9)
        $sheet->setCellValue('D' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('D' . $currentRow . ':G' . ($currentRow + 3));
        
        // L-2: 650.66 dengan colspan 2
        $sheet->setCellValue('H' . $currentRow, '650.66');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-2 Ambang Batas dengan colspan 4 dan rowspan 4 (J6:M9)
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':M' . ($currentRow + 3));
        
        // L-3: 616.55 dengan colspan 2
        $sheet->setCellValue('N' . $currentRow, '616.55');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        // L-3 Ambang Batas dengan colspan 4 dan rowspan 4 (P6:S9)
        $sheet->setCellValue('P' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('P' . $currentRow . ':S' . ($currentRow + 3));
        
        $currentRow++;
        
        // ===== ROW 7: Kedalaman (Row 4 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Kedalaman(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-1: 71.50
        $sheet->setCellValue('B' . $currentRow, '71.50');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-2: 73.00
        $sheet->setCellValue('H' . $currentRow, '73.00');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-3: 59.00
        $sheet->setCellValue('N' . $currentRow, '59.00');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 8: Koordinat X (Row 5 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat X(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-1: 6.196,48
        $sheet->setCellValue('B' . $currentRow, '6.196,48');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-2: 6.158,64
        $sheet->setCellValue('H' . $currentRow, '6.158,64');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-3: 6.140,12
        $sheet->setCellValue('N' . $currentRow, '6.140,12');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 9: Koordinat Y (Row 6 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat Y(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-1: (8.988,12)
        $sheet->setCellValue('B' . $currentRow, '(8.988,12)');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-2: (8.901,46)
        $sheet->setCellValue('H' . $currentRow, '(8.901,46)');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-3: (8.792,90)
        $sheet->setCellValue('N' . $currentRow, '(8.792,90)');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 10: Final Headers untuk Ambang Batas dan kolom status (Row 7 di view) =====
        // Kolom A: Tanggal
        $sheet->setCellValue('A' . $currentRow, 'Tanggal');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // ===== L-1 Columns =====
        // Bacaan(m) - Biru (kolom B10)
        $sheet->setCellValue('B' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('B' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // T.Psmetrik(El.m) - Biru (kolom C10)
        $sheet->setCellValue('C' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('C' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // Ambang Batas L-1: Kolom D10-G10 adalah header untuk area ambang batas
        $sheet->setCellValue('D' . $currentRow, 'Aman');
        $sheet->getStyle('D' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('E' . $currentRow, 'Peringatan');
        $sheet->getStyle('E' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('F' . $currentRow, 'Bahaya');
        $sheet->getStyle('F' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('G' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('G' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== L-2 Columns =====
        // Bacaan(m) - Biru (kolom H10)
        $sheet->setCellValue('H' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('H' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // T.Psmetrik(El.m) - Biru (kolom I10)
        $sheet->setCellValue('I' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('I' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // Ambang Batas L-2: Kolom J10-M10
        $sheet->setCellValue('J' . $currentRow, 'Aman');
        $sheet->getStyle('J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('K' . $currentRow, 'Peringatan');
        $sheet->getStyle('K' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('L' . $currentRow, 'Bahaya');
        $sheet->getStyle('L' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('M' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('M' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== L-3 Columns =====
        // Bacaan(m) - Biru (kolom N10)
        $sheet->setCellValue('N' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('N' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // T.Psmetrik(El.m) - Biru (kolom O10)
        $sheet->setCellValue('O' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('O' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // Ambang Batas L-3: Kolom P10-S10
        $sheet->setCellValue('P' . $currentRow, 'Aman');
        $sheet->getStyle('P' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('Q' . $currentRow, 'Peringatan');
        $sheet->getStyle('Q' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('R' . $currentRow, 'Bahaya');
        $sheet->getStyle('R' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('S' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('S' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== ISI DATA AMBANG BATAS DI AREA ROW 6-9 =====
        // Isi data ambang batas untuk L-1 (D6:G9)
        $this->fillAmbangBatasDataL1($sheet, 'D6', 'G9');
        
        // Isi data ambang batas untuk L-2 (J6:M9)
        $this->fillAmbangBatasDataL2($sheet, 'J6', 'M9');
        
        // Isi data ambang batas untuk L-3 (P6:S9)
        $this->fillAmbangBatasDataL3($sheet, 'P6', 'S9');
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerBottomRange)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== ISI DATA =====
        $currentRow = $headerEndRow + 1;
        $startDataRow = $currentRow;
        
        if (empty($pengukuranData)) {
            // Jika tidak ada data
            $sheet->mergeCells('A' . $currentRow . ':S' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Tidak ada data untuk Grafik History L1-L3');
            $sheet->getStyle('A' . $currentRow . ':S' . $currentRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
            $currentRow++;
        } else {
            // Urutkan berdasarkan tanggal
            usort($pengukuranData, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($pengukuranData as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data terkait
                $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                
                // Format tanggal
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $currentRow, $tanggal);
                
                // Fungsi untuk mendapatkan status warna
                $getStatus = function($t_psmetrik, $type) {
                    switch($type) {
                        case 'L01':
                            if ($t_psmetrik <= 648.93) return 'aman';
                            if ($t_psmetrik <= 650.63) return 'peringatan';
                            return 'bahaya';
                        case 'L02':
                            if ($t_psmetrik <= 648.95) return 'aman';
                            if ($t_psmetrik <= 650.65) return 'peringatan';
                            return 'bahaya';
                        case 'L03':
                            if ($t_psmetrik <= 614.84) return 'aman';
                            if ($t_psmetrik <= 616.54) return 'peringatan';
                            return 'bahaya';
                        default:
                            return 'aman';
                    }
                };
                
                // ===== L-1 Data =====
                // Bacaan(m) - konversi dari feet ke meter
                $bacaan_L01 = $pembacaanData['L01']['feet'] ?? 0;
                $bacaan_L01_m = $bacaan_L01 * 0.3048;
                $sheet->setCellValue('B' . $currentRow, number_format($bacaan_L01_m, 2));
                
                // T.Psmetrik(El.m)
                $t_psmetrik_L01 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L01') {
                        $t_psmetrik_L01 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('C' . $currentRow, number_format($t_psmetrik_L01, 2));
                
                // Ambang Batas (nilai tetap untuk setiap baris data)
                $sheet->setCellValue('D' . $currentRow, '647.84');  // Aman
                $sheet->setCellValue('E' . $currentRow, '648.94');  // Peringatan
                $sheet->setCellValue('F' . $currentRow, '650.64');  // Bahaya
                
                // T.Psmetrik(El.m) dengan status warna
                $status_L01 = $getStatus($t_psmetrik_L01, 'L01');
                $sheet->setCellValue('G' . $currentRow, number_format($t_psmetrik_L01, 2));
                
                // ===== L-2 Data =====
                $bacaan_L02 = $pembacaanData['L02']['feet'] ?? 0;
                $bacaan_L02_m = $bacaan_L02 * 0.3048;
                $sheet->setCellValue('H' . $currentRow, number_format($bacaan_L02_m, 2));
                
                $t_psmetrik_L02 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L02') {
                        $t_psmetrik_L02 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('I' . $currentRow, number_format($t_psmetrik_L02, 2));
                
                $sheet->setCellValue('J' . $currentRow, '647.86');  // Aman
                $sheet->setCellValue('K' . $currentRow, '648.96');  // Peringatan
                $sheet->setCellValue('L' . $currentRow, '650.66');  // Bahaya
                
                $status_L02 = $getStatus($t_psmetrik_L02, 'L02');
                $sheet->setCellValue('M' . $currentRow, number_format($t_psmetrik_L02, 2));
                
                // ===== L-3 Data =====
                $bacaan_L03 = $pembacaanData['L03']['feet'] ?? 0;
                $bacaan_L03_m = $bacaan_L03 * 0.3048;
                $sheet->setCellValue('N' . $currentRow, number_format($bacaan_L03_m, 2));
                
                $t_psmetrik_L03 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L03') {
                        $t_psmetrik_L03 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('O' . $currentRow, number_format($t_psmetrik_L03, 2));
                
                $sheet->setCellValue('P' . $currentRow, '613.75');  // Aman
                $sheet->setCellValue('Q' . $currentRow, '614.85');  // Peringatan
                $sheet->setCellValue('R' . $currentRow, '616.55');  // Bahaya
                
                $status_L03 = $getStatus($t_psmetrik_L03, 'L03');
                $sheet->setCellValue('S' . $currentRow, number_format($t_psmetrik_L03, 2));
                
                // Apply warna latar untuk kolom status (background berwarna, teks hitam)
                $this->applyStatusColor($sheet, 'G' . $currentRow, $status_L01);
                $this->applyStatusColor($sheet, 'M' . $currentRow, $status_L02);
                $this->applyStatusColor($sheet, 'S' . $currentRow, $status_L03);
                
                $currentRow++;
            }
            
            // ===== APPLY STYLES UNTUK DATA =====
            if ($currentRow > $startDataRow) {
                $dataAreaStart = $startDataRow;
                $dataAreaEnd = $currentRow - 1;
                $dataAreaRange = 'A' . $dataAreaStart . ':S' . $dataAreaEnd;
                
                // 1. Apply warna latar belakang untuk semua data (selang-seling)
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                    $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                    $rowRange = 'A' . $row . ':S' . $row;
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border untuk semua sel data
                $sheet->getStyle($dataAreaRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ]
                ]);
                
                // 3. Apply warna khusus untuk kolom ambang batas di area data
                $amanColumns = ['D', 'J', 'P'];
                $peringatanColumns = ['E', 'K', 'Q'];
                $bahayaColumns = ['F', 'L', 'R'];
                
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF155724']]
                    ]);
                }
                
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF856404']]
                    ]);
                }
                
                foreach ($bahayaColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF721C24']]
                    ]);
                }
                
                // 4. Alignment untuk kolom numerik (kanan)
                $numericColumns = array_merge(
                    $this->getColumnRange('B', 'S')
                );
                
                foreach ($numericColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // 5. Alignment untuk kolom tanggal (tengah)
                $sheet->getStyle('A' . $dataAreaStart . ':A' . $dataAreaEnd)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('A11'); // Freeze header rows, mulai data di row 11
        
        // ===== FORMAT ANGKA =====
        if ($currentRow > $startDataRow) {
            // Format semua kolom numerik dengan 2 desimal
            $numericRange = 'B' . $startDataRow . ':S' . ($currentRow - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 12,  // Tanggal
            'B' => 10,  // Bacaan L1
            'C' => 14,  // T.Psmetrik L1
            'D' => 8,   // Aman L1
            'E' => 10,  // Peringatan L1
            'F' => 8,   // Bahaya L1
            'G' => 14,  // Status L1
            'H' => 10,  // Bacaan L2
            'I' => 14,  // T.Psmetrik L2
            'J' => 8,   // Aman L2
            'K' => 10,  // Peringatan L2
            'L' => 8,   // Bahaya L2
            'M' => 14,  // Status L2
            'N' => 10,  // Bacaan L3
            'O' => 14,  // T.Psmetrik L3
            'P' => 8,   // Aman L3
            'Q' => 10,  // Peringatan L3
            'R' => 8,   // Bahaya L3
            'S' => 14   // Status L3
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        for ($i = 1; $i <= 3; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }
        
        for ($i = 4; $i <= 10; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        if (!empty($pengukuranData)) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        if ($currentRow > $startDataRow) {
            $footerRow = $currentRow;
            $sheet->mergeCells('A' . $footerRow . ':S' . $footerRow);
            
            $totalRecords = count($pengukuranData);
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . 
                ' | Grafik History L1-L3 | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':S' . $footerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF666666'], 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(25);
        }
        
        // ===== SET PRINT AREA =====
        if ($currentRow > $startDataRow) {
            $sheet->getPageSetup()->setPrintArea('A1:S' . ($currentRow - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:S10');
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter);
    }
    
    /**
     * Create Sheet untuk Grafik History L4-L6 dengan struktur sama persis seperti view
     */
    private function createGrafikHistoryL4L6Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA =====
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $headerLightBlue = 'FFE3F2FD';     // Biru muda untuk header
        $bgAman = 'FFD4EDDA';             // Hijau muda untuk AMAN
        $bgPeringatan = 'FFFFF3CD';       // Kuning muda untuk PERINGATAN
        $bgBahaya = 'FFF8D7DA';           // Merah muda untuk BAHAYA
        $headerAman = 'FF28A745';         // Hijau untuk header AMAN
        $headerPeringatan = 'FFFFC107';   // Kuning untuk header PERINGATAN
        $headerBahaya = 'FFDC3545';       // Merah untuk header BAHAYA
        $headerLightGray = 'FFCED4DA';    // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';          // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';      // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'S'; // Total kolom: A sampai S (18 kolom)
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - GRAFIK HISTORY L4-L6 - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'GRAFIK HISTORY DATA PIEZOMETER L4-L6');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // ===== HEADER TABEL (SAMA SEPERTI DI VIEW L4-L6) =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 6; // Row 10 adalah baris terakhir header
        
        // ===== ROW 4: Main Headers (Row 1 di view) =====
        // Pisometer No. dengan rowspan 2
        $sheet->setCellValue('A' . $currentRow, 'Pisometer No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . ($currentRow + 1), $headerLightBlue);
        
        // L-4 dengan colspan 6
        $sheet->setCellValue('B' . $currentRow, 'L-4');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerBlue);
        
        // L-5 dengan colspan 6
        $sheet->setCellValue('H' . $currentRow, 'L-5');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerBlue);
        
        // L-6 dengan colspan 6
        $sheet->setCellValue('N' . $currentRow, 'L-6');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, $headerBlue);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers (Row 2 di view) =====
        // Upstream untuk L-4
        $sheet->setCellValue('B' . $currentRow, 'Upstream');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerLightBlue);
        
        // As Bend untuk L-5
        $sheet->setCellValue('H' . $currentRow, 'As Bend');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerLightBlue);
        
        // Downstream untuk L-6
        $sheet->setCellValue('N' . $currentRow, 'Downstream');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 6: Data Headers (Row 3 di view) =====
        // Kolom A: Elev.Piso.Atas(El.m)
        $sheet->setCellValue('A' . $currentRow, 'Elev.Piso.Atas(El.m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-4: 580.26 dengan colspan 2
        $sheet->setCellValue('B' . $currentRow, '580.26');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-4 Ambang Batas dengan colspan 4 dan rowspan 4 (D6:G9)
        $sheet->setCellValue('D' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('D' . $currentRow . ':G' . ($currentRow + 3));
        
        // L-5: 700.76 dengan colspan 2
        $sheet->setCellValue('H' . $currentRow, '700.76');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-5 Ambang Batas dengan colspan 4 dan rowspan 4 (J6:M9)
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':M' . ($currentRow + 3));
        
        // L-6: 690.09 dengan colspan 2
        $sheet->setCellValue('N' . $currentRow, '690.09');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        // L-6 Ambang Batas dengan colspan 4 dan rowspan 4 (P6:S9)
        $sheet->setCellValue('P' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('P' . $currentRow . ':S' . ($currentRow + 3));
        
        $currentRow++;
        
        // ===== ROW 7: Kedalaman (Row 4 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Kedalaman(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-4: 50.00
        $sheet->setCellValue('B' . $currentRow, '50.00');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-5: 62.00
        $sheet->setCellValue('H' . $currentRow, '62.00');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-6: 62.00
        $sheet->setCellValue('N' . $currentRow, '62.00');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 8: Koordinat X (Row 5 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat X(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-4: 6.116,59
        $sheet->setCellValue('B' . $currentRow, '6.116,59');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-5: 6.168,84
        $sheet->setCellValue('H' . $currentRow, '6.168,84');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-6: 6.106,56
        $sheet->setCellValue('N' . $currentRow, '6.106,56');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 9: Koordinat Y (Row 6 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat Y(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-4: (8.669,64)
        $sheet->setCellValue('B' . $currentRow, '(8.669,64)');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-5: (9.057,75)
        $sheet->setCellValue('H' . $currentRow, '(9.057,75)');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-6: (8.921,46)
        $sheet->setCellValue('N' . $currentRow, '(8.921,46)');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 10: Final Headers (Row 7 di view) =====
        // Kolom A: Tanggal
        $sheet->setCellValue('A' . $currentRow, 'Tanggal');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // ===== L-4 Columns =====
        // Bacaan(m) - Biru (kolom B10)
        $sheet->setCellValue('B' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('B' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // T.Psmetrik(El.m) - Biru (kolom C10)
        $sheet->setCellValue('C' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('C' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // Ambang Batas L-4: Kolom D10-G10
        $sheet->setCellValue('D' . $currentRow, 'Aman');
        $sheet->getStyle('D' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('E' . $currentRow, 'Peringatan');
        $sheet->getStyle('E' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('F' . $currentRow, 'Bahaya');
        $sheet->getStyle('F' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('G' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('G' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== L-5 Columns =====
        // Bacaan(m) - Biru (kolom H10)
        $sheet->setCellValue('H' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('H' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // T.Psmetrik(El.m) - Biru (kolom I10)
        $sheet->setCellValue('I' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('I' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // Ambang Batas L-5: Kolom J10-M10
        $sheet->setCellValue('J' . $currentRow, 'Aman');
        $sheet->getStyle('J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('K' . $currentRow, 'Peringatan');
        $sheet->getStyle('K' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('L' . $currentRow, 'Bahaya');
        $sheet->getStyle('L' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('M' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('M' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== L-6 Columns =====
        // Bacaan(m) - Biru (kolom N10)
        $sheet->setCellValue('N' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('N' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // T.Psmetrik(El.m) - Biru (kolom O10)
        $sheet->setCellValue('O' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('O' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // Ambang Batas L-6: Kolom P10-S10
        $sheet->setCellValue('P' . $currentRow, 'Aman');
        $sheet->getStyle('P' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('Q' . $currentRow, 'Peringatan');
        $sheet->getStyle('Q' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('R' . $currentRow, 'Bahaya');
        $sheet->getStyle('R' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('S' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('S' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== ISI DATA AMBANG BATAS DI AREA ROW 6-9 =====
        // Isi data ambang batas untuk L-4 (D6:G9)
        $this->fillAmbangBatasDataL4($sheet, 'D6', 'G9');
        
        // Isi data ambang batas untuk L-5 (J6:M9)
        $this->fillAmbangBatasDataL5($sheet, 'J6', 'M9');
        
        // Isi data ambang batas untuk L-6 (P6:S9)
        $this->fillAmbangBatasDataL6($sheet, 'P6', 'S9');
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerBottomRange)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== ISI DATA =====
        $currentRow = $headerEndRow + 1;
        $startDataRow = $currentRow;
        
        if (empty($pengukuranData)) {
            // Jika tidak ada data
            $sheet->mergeCells('A' . $currentRow . ':S' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Tidak ada data untuk Grafik History L4-L6');
            $sheet->getStyle('A' . $currentRow . ':S' . $currentRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
            $currentRow++;
        } else {
            // Urutkan berdasarkan tanggal
            usort($pengukuranData, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($pengukuranData as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data terkait
                $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                
                // Format tanggal
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $currentRow, $tanggal);
                
                // Fungsi untuk mendapatkan status warna sesuai logika L4-L6
                $getStatusL4L6 = function($t_psmetrik, $type) {
                    switch($type) {
                        case 'L04':
                            // L-4: Aman ≤560.86 - 565.35, Peringatan 565.36 - 569.65, Bahaya ≥569.66
                            if ($t_psmetrik <= 565.35) return 'aman';
                            if ($t_psmetrik <= 569.65) return 'peringatan';
                            return 'bahaya';
                        case 'L05':
                            // L-5: Aman ≤691.46 - 692.35, Peringatan 692.36 - 695.35, Bahaya ≥695.36
                            if ($t_psmetrik <= 692.35) return 'aman';
                            if ($t_psmetrik <= 695.35) return 'peringatan';
                            return 'bahaya';
                        case 'L06':
                            // L-6: Aman ≤680.79 - 681.68, Peringatan 681.69 - 684.68, Bahaya ≥684.69
                            if ($t_psmetrik <= 681.68) return 'aman';
                            if ($t_psmetrik <= 684.68) return 'peringatan';
                            return 'bahaya';
                        default:
                            return 'aman';
                    }
                };
                
                // ===== L-4 Data =====
                // Bacaan(m) - konversi dari feet ke meter
                $bacaan_L04 = $pembacaanData['L04']['feet'] ?? 0;
                $bacaan_L04_m = $bacaan_L04 * 0.3048;
                $sheet->setCellValue('B' . $currentRow, number_format($bacaan_L04_m, 2));
                
                // T.Psmetrik(El.m)
                $t_psmetrik_L04 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L04') {
                        $t_psmetrik_L04 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('C' . $currentRow, number_format($t_psmetrik_L04, 2));
                
                // Ambang Batas (nilai tetap)
                $sheet->setCellValue('D' . $currentRow, '560.86');  // Aman L4
                $sheet->setCellValue('E' . $currentRow, '565.36');  // Peringatan L4
                $sheet->setCellValue('F' . $currentRow, '569.66');  // Bahaya L4
                
                // T.Psmetrik(El.m) dengan status warna
                $status_L04 = $getStatusL4L6($t_psmetrik_L04, 'L04');
                $sheet->setCellValue('G' . $currentRow, number_format($t_psmetrik_L04, 2));
                
                // ===== L-5 Data =====
                $bacaan_L05 = $pembacaanData['L05']['feet'] ?? 0;
                $bacaan_L05_m = $bacaan_L05 * 0.3048;
                $sheet->setCellValue('H' . $currentRow, number_format($bacaan_L05_m, 2));
                
                $t_psmetrik_L05 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L05') {
                        $t_psmetrik_L05 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('I' . $currentRow, number_format($t_psmetrik_L05, 2));
                
                $sheet->setCellValue('J' . $currentRow, '691.46');  // Aman L5
                $sheet->setCellValue('K' . $currentRow, '692.36');  // Peringatan L5
                $sheet->setCellValue('L' . $currentRow, '695.36');  // Bahaya L5
                
                $status_L05 = $getStatusL4L6($t_psmetrik_L05, 'L05');
                $sheet->setCellValue('M' . $currentRow, number_format($t_psmetrik_L05, 2));
                
                // ===== L-6 Data =====
                $bacaan_L06 = $pembacaanData['L06']['feet'] ?? 0;
                $bacaan_L06_m = $bacaan_L06 * 0.3048;
                $sheet->setCellValue('N' . $currentRow, number_format($bacaan_L06_m, 2));
                
                $t_psmetrik_L06 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L06') {
                        $t_psmetrik_L06 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('O' . $currentRow, number_format($t_psmetrik_L06, 2));
                
                $sheet->setCellValue('P' . $currentRow, '680.79');  // Aman L6
                $sheet->setCellValue('Q' . $currentRow, '681.69');  // Peringatan L6
                $sheet->setCellValue('R' . $currentRow, '684.69');  // Bahaya L6
                
                $status_L06 = $getStatusL4L6($t_psmetrik_L06, 'L06');
                $sheet->setCellValue('S' . $currentRow, number_format($t_psmetrik_L06, 2));
                
                // Apply warna latar untuk kolom status (background berwarna, teks hitam)
                $this->applyStatusColor($sheet, 'G' . $currentRow, $status_L04);
                $this->applyStatusColor($sheet, 'M' . $currentRow, $status_L05);
                $this->applyStatusColor($sheet, 'S' . $currentRow, $status_L06);
                
                $currentRow++;
            }
            
            // ===== APPLY STYLES UNTUK DATA =====
            if ($currentRow > $startDataRow) {
                $dataAreaStart = $startDataRow;
                $dataAreaEnd = $currentRow - 1;
                $dataAreaRange = 'A' . $dataAreaStart . ':S' . $dataAreaEnd;
                
                // 1. Apply warna latar belakang untuk semua data (selang-seling)
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                    $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                    $rowRange = 'A' . $row . ':S' . $row;
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border untuk semua sel data
                $sheet->getStyle($dataAreaRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ]
                ]);
                
                // 3. Apply warna khusus untuk kolom ambang batas
                $amanColumns = ['D', 'J', 'P'];
                $peringatanColumns = ['E', 'K', 'Q'];
                $bahayaColumns = ['F', 'L', 'R'];
                
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF155724']]
                    ]);
                }
                
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF856404']]
                    ]);
                }
                
                foreach ($bahayaColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF721C24']]
                    ]);
                }
                
                // 4. Alignment untuk kolom numerik (kanan)
                $numericColumns = array_merge(
                    $this->getColumnRange('B', 'S')
                );
                
                foreach ($numericColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // 5. Alignment untuk kolom tanggal (tengah)
                $sheet->getStyle('A' . $dataAreaStart . ':A' . $dataAreaEnd)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('A11'); // Freeze header rows, mulai data di row 11
        
        // ===== FORMAT ANGKA =====
        if ($currentRow > $startDataRow) {
            // Format semua kolom numerik dengan 2 desimal
            $numericRange = 'B' . $startDataRow . ':S' . ($currentRow - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 12,  // Tanggal
            'B' => 10,  // Bacaan L4
            'C' => 14,  // T.Psmetrik L4
            'D' => 8,   // Aman L4
            'E' => 10,  // Peringatan L4
            'F' => 8,   // Bahaya L4
            'G' => 14,  // Status L4
            'H' => 10,  // Bacaan L5
            'I' => 14,  // T.Psmetrik L5
            'J' => 8,   // Aman L5
            'K' => 10,  // Peringatan L5
            'L' => 8,   // Bahaya L5
            'M' => 14,  // Status L5
            'N' => 10,  // Bacaan L6
            'O' => 14,  // T.Psmetrik L6
            'P' => 8,   // Aman L6
            'Q' => 10,  // Peringatan L6
            'R' => 8,   // Bahaya L6
            'S' => 14   // Status L6
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        for ($i = 1; $i <= 3; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }
        
        for ($i = 4; $i <= 10; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        if (!empty($pengukuranData)) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        if ($currentRow > $startDataRow) {
            $footerRow = $currentRow;
            $sheet->mergeCells('A' . $footerRow . ':S' . $footerRow);
            
            $totalRecords = count($pengukuranData);
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . 
                ' | Grafik History L4-L6 | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':S' . $footerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF666666'], 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(25);
        }
        
        // ===== SET PRINT AREA =====
        if ($currentRow > $startDataRow) {
            $sheet->getPageSetup()->setPrintArea('A1:S' . ($currentRow - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:S10');
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter);
    }
    
    /**
     * Create Sheet untuk Grafik History L7-L9 dengan struktur sama persis seperti view
     */
    private function createGrafikHistoryL7L9Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA =====
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $headerLightBlue = 'FFE3F2FD';     // Biru muda untuk header
        $bgAman = 'FFD4EDDA';             // Hijau muda untuk AMAN
        $bgPeringatan = 'FFFFF3CD';       // Kuning muda untuk PERINGATAN
        $bgBahaya = 'FFF8D7DA';           // Merah muda untuk BAHAYA
        $headerAman = 'FF28A745';         // Hijau untuk header AMAN
        $headerPeringatan = 'FFFFC107';   // Kuning untuk header PERINGATAN
        $headerBahaya = 'FFDC3545';       // Merah untuk header BAHAYA
        $headerLightGray = 'FFCED4DA';    // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';          // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';      // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'S'; // Total kolom: A sampai S (18 kolom)
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - GRAFIK HISTORY L7-L9 - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'GRAFIK HISTORY DATA PIEZOMETER L7-L9');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // ===== HEADER TABEL (SAMA SEPERTI DI VIEW L7-L9) =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 6; // Row 10 adalah baris terakhir header
        
        // ===== ROW 4: Main Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Pisometer No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . ($currentRow + 1), $headerLightBlue);
        
        $sheet->setCellValue('B' . $currentRow, 'L-7');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'L-8');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerBlue);
        
        $sheet->setCellValue('N' . $currentRow, 'L-9');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, $headerBlue);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers =====
        $sheet->setCellValue('B' . $currentRow, 'Upstream');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'As Bend');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('N' . $currentRow, 'Downstream');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 6: Data Headers (Row 3 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Elev.Piso.Atas(El.m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-7: 653.36 dengan colspan 2
        $sheet->setCellValue('B' . $currentRow, '653.36');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-7 Ambang Batas dengan colspan 4 dan rowspan 4 (D6:G9)
        $sheet->setCellValue('D' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('D' . $currentRow . ':G' . ($currentRow + 3));
        
        // L-8: 659.14 dengan colspan 2
        $sheet->setCellValue('H' . $currentRow, '659.14');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-8 Ambang Batas dengan colspan 4 dan rowspan 4 (J6:M9)
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':M' . ($currentRow + 3));
        
        // L-9: 622.45 dengan colspan 2
        $sheet->setCellValue('N' . $currentRow, '622.45');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        // L-9 Ambang Batas dengan colspan 4 dan rowspan 4 (P6:S9)
        $sheet->setCellValue('P' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('P' . $currentRow . ':S' . ($currentRow + 3));
        
        $currentRow++;
        
        // ===== ROW 7: Kedalaman (Row 4 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Kedalaman(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-7: 40.00
        $sheet->setCellValue('B' . $currentRow, '40.00');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-8: 55.50
        $sheet->setCellValue('H' . $currentRow, '55.50');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-9: 57.00
        $sheet->setCellValue('N' . $currentRow, '57.00');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 8: Koordinat X (Row 5 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat X(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-7: 6.105,70
        $sheet->setCellValue('B' . $currentRow, '6.105,70');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-8: 6.071,74
        $sheet->setCellValue('H' . $currentRow, '6.071,74');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-9: 6.025,47
        $sheet->setCellValue('N' . $currentRow, '6.025,47');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 9: Koordinat Y (Row 6 di view) =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat Y(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-7: (8.805,36)
        $sheet->setCellValue('B' . $currentRow, '(8.805,36)');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-8: (8.673,34)
        $sheet->setCellValue('H' . $currentRow, '(8.673,34)');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-9: (8.533,16)
        $sheet->setCellValue('N' . $currentRow, '(8.533,16)');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 10: Final Headers (Row 7 di view) =====
        // Kolom A: Tanggal
        $sheet->setCellValue('A' . $currentRow, 'Tanggal');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // ===== L-7 Columns =====
        $sheet->setCellValue('B' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('B' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('C' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('C' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('D' . $currentRow, 'Aman');
        $sheet->getStyle('D' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('E' . $currentRow, 'Peringatan');
        $sheet->getStyle('E' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('F' . $currentRow, 'Bahaya');
        $sheet->getStyle('F' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('G' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('G' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== L-8 Columns =====
        $sheet->setCellValue('H' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('H' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('I' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('I' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('J' . $currentRow, 'Aman');
        $sheet->getStyle('J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('K' . $currentRow, 'Peringatan');
        $sheet->getStyle('K' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('L' . $currentRow, 'Bahaya');
        $sheet->getStyle('L' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('M' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('M' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== L-9 Columns =====
        $sheet->setCellValue('N' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('N' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('O' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('O' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('P' . $currentRow, 'Aman');
        $sheet->getStyle('P' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('Q' . $currentRow, 'Peringatan');
        $sheet->getStyle('Q' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('R' . $currentRow, 'Bahaya');
        $sheet->getStyle('R' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('S' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('S' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== ISI DATA AMBANG BATAS DI AREA ROW 6-9 =====
        // Isi data ambang batas untuk L-7 (D6:G9)
        $this->fillAmbangBatasDataL7($sheet, 'D6', 'G9');
        
        // Isi data ambang batas untuk L-8 (J6:M9)
        $this->fillAmbangBatasDataL8($sheet, 'J6', 'M9');
        
        // Isi data ambang batas untuk L-9 (P6:S9)
        $this->fillAmbangBatasDataL9($sheet, 'P6', 'S9');
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerBottomRange)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== ISI DATA =====
        $currentRow = $headerEndRow + 1;
        $startDataRow = $currentRow;
        
        if (empty($pengukuranData)) {
            // Jika tidak ada data
            $sheet->mergeCells('A' . $currentRow . ':S' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Tidak ada data untuk Grafik History L7-L9');
            $sheet->getStyle('A' . $currentRow . ':S' . $currentRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
            $currentRow++;
        } else {
            // Urutkan berdasarkan tanggal
            usort($pengukuranData, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($pengukuranData as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data terkait
                $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                
                // Format tanggal
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $currentRow, $tanggal);
                
                // Fungsi untuk mendapatkan status warna
                $getStatusL7L9 = function($t_psmetrik, $type) {
                    switch($type) {
                        case 'L07':
                            // L-7: Aman ≤652.06 - 652.95, Peringatan 652.96 - 655.95, Bahaya ≥655.96
                            if ($t_psmetrik <= 652.95) return 'aman';
                            if ($t_psmetrik <= 655.95) return 'peringatan';
                            return 'bahaya';
                        case 'L08':
                            // L-8: Aman ≤657.84 - 658.73, Peringatan 658.74 - 661.73, Bahaya ≥661.74
                            if ($t_psmetrik <= 658.73) return 'aman';
                            if ($t_psmetrik <= 661.73) return 'peringatan';
                            return 'bahaya';
                        case 'L09':
                            // L-9: Aman ≤621.15 - 622.04, Peringatan 622.05 - 625.04, Bahaya ≥625.05
                            if ($t_psmetrik <= 622.04) return 'aman';
                            if ($t_psmetrik <= 625.04) return 'peringatan';
                            return 'bahaya';
                        default:
                            return 'aman';
                    }
                };
                
                // ===== L-7 Data =====
                $bacaan_L07 = $pembacaanData['L07']['feet'] ?? 0;
                $bacaan_L07_m = $bacaan_L07 * 0.3048;
                $sheet->setCellValue('B' . $currentRow, number_format($bacaan_L07_m, 2));
                
                $t_psmetrik_L07 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L07') {
                        $t_psmetrik_L07 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('C' . $currentRow, number_format($t_psmetrik_L07, 2));
                
                $sheet->setCellValue('D' . $currentRow, '652.06');  // Aman
                $sheet->setCellValue('E' . $currentRow, '652.96');  // Peringatan
                $sheet->setCellValue('F' . $currentRow, '655.96');  // Bahaya
                
                $status_L07 = $getStatusL7L9($t_psmetrik_L07, 'L07');
                $sheet->setCellValue('G' . $currentRow, number_format($t_psmetrik_L07, 2));
                
                // ===== L-8 Data =====
                $bacaan_L08 = $pembacaanData['L08']['feet'] ?? 0;
                $bacaan_L08_m = $bacaan_L08 * 0.3048;
                $sheet->setCellValue('H' . $currentRow, number_format($bacaan_L08_m, 2));
                
                $t_psmetrik_L08 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L08') {
                        $t_psmetrik_L08 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('I' . $currentRow, number_format($t_psmetrik_L08, 2));
                
                $sheet->setCellValue('J' . $currentRow, '657.84');  // Aman
                $sheet->setCellValue('K' . $currentRow, '658.74');  // Peringatan
                $sheet->setCellValue('L' . $currentRow, '661.74');  // Bahaya
                
                $status_L08 = $getStatusL7L9($t_psmetrik_L08, 'L08');
                $sheet->setCellValue('M' . $currentRow, number_format($t_psmetrik_L08, 2));
                
                // ===== L-9 Data =====
                $bacaan_L09 = $pembacaanData['L09']['feet'] ?? 0;
                $bacaan_L09_m = $bacaan_L09 * 0.3048;
                $sheet->setCellValue('N' . $currentRow, number_format($bacaan_L09_m, 2));
                
                $t_psmetrik_L09 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L09') {
                        $t_psmetrik_L09 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('O' . $currentRow, number_format($t_psmetrik_L09, 2));
                
                $sheet->setCellValue('P' . $currentRow, '621.15');  // Aman
                $sheet->setCellValue('Q' . $currentRow, '622.05');  // Peringatan
                $sheet->setCellValue('R' . $currentRow, '625.05');  // Bahaya
                
                $status_L09 = $getStatusL7L9($t_psmetrik_L09, 'L09');
                $sheet->setCellValue('S' . $currentRow, number_format($t_psmetrik_L09, 2));
                
                // Apply warna latar untuk kolom status (background berwarna, teks hitam)
                $this->applyStatusColor($sheet, 'G' . $currentRow, $status_L07);
                $this->applyStatusColor($sheet, 'M' . $currentRow, $status_L08);
                $this->applyStatusColor($sheet, 'S' . $currentRow, $status_L09);
                
                $currentRow++;
            }
            
            // ===== APPLY STYLES UNTUK DATA =====
            if ($currentRow > $startDataRow) {
                $dataAreaStart = $startDataRow;
                $dataAreaEnd = $currentRow - 1;
                $dataAreaRange = 'A' . $dataAreaStart . ':S' . $dataAreaEnd;
                
                // 1. Apply warna latar belakang untuk semua data (selang-seling)
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                    $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                    $rowRange = 'A' . $row . ':S' . $row;
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border untuk semua sel data
                $sheet->getStyle($dataAreaRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ]
                ]);
                
                // 3. Apply warna khusus untuk kolom ambang batas
                $amanColumns = ['D', 'J', 'P'];
                $peringatanColumns = ['E', 'K', 'Q'];
                $bahayaColumns = ['F', 'L', 'R'];
                
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF155724']]
                    ]);
                }
                
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF856404']]
                    ]);
                }
                
                foreach ($bahayaColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF721C24']]
                    ]);
                }
                
                // 4. Alignment untuk kolom numerik (kanan)
                $numericColumns = array_merge(
                    $this->getColumnRange('B', 'S')
                );
                
                foreach ($numericColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // 5. Alignment untuk kolom tanggal (tengah)
                $sheet->getStyle('A' . $dataAreaStart . ':A' . $dataAreaEnd)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('A11'); // Freeze header rows, mulai data di row 11
        
        // ===== FORMAT ANGKA =====
        if ($currentRow > $startDataRow) {
            // Format semua kolom numerik dengan 2 desimal
            $numericRange = 'B' . $startDataRow . ':S' . ($currentRow - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 12,  // Tanggal
            'B' => 10,  // Bacaan L7
            'C' => 14,  // T.Psmetrik L7
            'D' => 8,   // Aman L7
            'E' => 10,  // Peringatan L7
            'F' => 8,   // Bahaya L7
            'G' => 14,  // Status L7
            'H' => 10,  // Bacaan L8
            'I' => 14,  // T.Psmetrik L8
            'J' => 8,   // Aman L8
            'K' => 10,  // Peringatan L8
            'L' => 8,   // Bahaya L8
            'M' => 14,  // Status L8
            'N' => 10,  // Bacaan L9
            'O' => 14,  // T.Psmetrik L9
            'P' => 8,   // Aman L9
            'Q' => 10,  // Peringatan L9
            'R' => 8,   // Bahaya L9
            'S' => 14   // Status L9
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        for ($i = 1; $i <= 3; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }
        
        for ($i = 4; $i <= 10; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        if (!empty($pengukuranData)) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        if ($currentRow > $startDataRow) {
            $footerRow = $currentRow;
            $sheet->mergeCells('A' . $footerRow . ':S' . $footerRow);
            
            $totalRecords = count($pengukuranData);
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . 
                ' | Grafik History L7-L9 | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':S' . $footerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF666666'], 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(25);
        }
        
        // ===== SET PRINT AREA =====
        if ($currentRow > $startDataRow) {
            $sheet->getPageSetup()->setPrintArea('A1:S' . ($currentRow - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:S10');
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter);
    }
    
    /**
     * Create Sheet untuk Grafik History L10-SPZ02 dengan struktur sama persis seperti sheet lainnya
     */
    private function createGrafikHistoryL10SPZ02Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA =====
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $headerLightBlue = 'FFE3F2FD';     // Biru muda untuk header
        $bgAman = 'FFD4EDDA';             // Hijau muda untuk AMAN
        $bgPeringatan = 'FFFFF3CD';       // Kuning muda untuk PERINGATAN
        $bgBahaya = 'FFF8D7DA';           // Merah muda untuk BAHAYA
        $headerAman = 'FF28A745';         // Hijau untuk header AMAN
        $headerPeringatan = 'FFFFC107';   // Kuning untuk header PERINGATAN
        $headerBahaya = 'FFDC3545';       // Merah untuk header BAHAYA
        $headerLightGray = 'FFCED4DA';    // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';          // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';      // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'S'; // Total kolom: A sampai S (18 kolom)
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - GRAFIK HISTORY L10-SPZ02 - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'GRAFIK HISTORY DATA PIEZOMETER L10-SPZ02');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
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
        
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // ===== HEADER TABEL =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 6; // Row 10 adalah baris terakhir header
        
        // ===== ROW 4: Main Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Pisometer No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . ($currentRow + 1), $headerLightBlue);
        
        $sheet->setCellValue('B' . $currentRow, 'L-10');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'SPZ-02');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerBlue);
        
        // Kolom N-S tidak digunakan (untuk konsistensi layout)
        $sheet->setCellValue('N' . $currentRow, '');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, 'FFE3F2FD');
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers =====
        $sheet->setCellValue('B' . $currentRow, 'Downstream');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'Downstream');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('N' . $currentRow, '');
        $sheet->mergeCells('N' . $currentRow . ':S' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'S' . $currentRow, 'FFE3F2FD');
        
        $currentRow++;
        
        // ===== ROW 6: Data Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Elev.Piso.Atas(El.m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-10: 580.36 dengan colspan 2
        $sheet->setCellValue('B' . $currentRow, '580.36');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-10 Ambang Batas dengan colspan 4 dan rowspan 4 (D6:G9)
        $sheet->setCellValue('D' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('D' . $currentRow . ':G' . ($currentRow + 3));
        
        // SPZ-02: 700.08 dengan colspan 2
        $sheet->setCellValue('H' . $currentRow, '700.08');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // SPZ-02 Ambang Batas dengan colspan 4 dan rowspan 4 (J6:M9)
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':M' . ($currentRow + 3));
        
        // Kosongkan kolom N-S
        $sheet->setCellValue('N' . $currentRow, '');
        $sheet->mergeCells('N' . $currentRow . ':S' . ($currentRow + 3));
        
        $currentRow++;
        
        // ===== ROW 7: Kedalaman =====
        $sheet->setCellValue('A' . $currentRow, 'Kedalaman(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-10: 51.50
        $sheet->setCellValue('B' . $currentRow, '51.50');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // SPZ-02: 70.00
        $sheet->setCellValue('H' . $currentRow, '70.00');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 8: Koordinat X =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat X(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-10: 5.958,64
        $sheet->setCellValue('B' . $currentRow, '5.958,64');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // SPZ-02: 6.095,66
        $sheet->setCellValue('H' . $currentRow, '6.095,66');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 9: Koordinat Y =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat Y(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // L-10: (8.413,89)
        $sheet->setCellValue('B' . $currentRow, '(8.413,89)');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // SPZ-02: (9.004,50)
        $sheet->setCellValue('H' . $currentRow, '(9.004,50)');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 10: Final Headers =====
        // Kolom A: Tanggal
        $sheet->setCellValue('A' . $currentRow, 'Tanggal');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // ===== L-10 Columns =====
        $sheet->setCellValue('B' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('B' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('C' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('C' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('D' . $currentRow, 'Aman');
        $sheet->getStyle('D' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('E' . $currentRow, 'Peringatan');
        $sheet->getStyle('E' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('F' . $currentRow, 'Bahaya');
        $sheet->getStyle('F' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('G' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('G' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== SPZ-02 Columns =====
        $sheet->setCellValue('H' . $currentRow, 'Bacaan(m)');
        $sheet->getStyle('H' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('I' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('I' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        $sheet->setCellValue('J' . $currentRow, 'Aman');
        $sheet->getStyle('J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerAman]]
        ]);
        
        $sheet->setCellValue('K' . $currentRow, 'Peringatan');
        $sheet->getStyle('K' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerPeringatan]]
        ]);
        
        $sheet->setCellValue('L' . $currentRow, 'Bahaya');
        $sheet->getStyle('L' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBahaya]]
        ]);
        
        $sheet->setCellValue('M' . $currentRow, 'T.Psmetrik(El.m)');
        $sheet->getStyle('M' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']]
        ]);
        
        // ===== Kolom N-S (kosong) =====
        for ($col = 'N'; $col <= 'S'; $col++) {
            $sheet->setCellValue($col . $currentRow, '');
            $sheet->getStyle($col . $currentRow)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE3F2FD']],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000']
                    ]
                ]
            ]);
        }
        
        // ===== ISI DATA AMBANG BATAS DI AREA ROW 6-9 =====
        // Isi data ambang batas untuk L-10 (D6:G9)
        $this->fillAmbangBatasDataL10($sheet, 'D6', 'G9');
        
        // Isi data ambang batas untuk SPZ-02 (J6:M9)
        $this->fillAmbangBatasDataSPZ02($sheet, 'J6', 'M9');
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerBottomRange)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== ISI DATA =====
        $currentRow = $headerEndRow + 1;
        $startDataRow = $currentRow;
        
        if (empty($pengukuranData)) {
            // Jika tidak ada data
            $sheet->mergeCells('A' . $currentRow . ':S' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Tidak ada data untuk Grafik History L10-SPZ02');
            $sheet->getStyle('A' . $currentRow . ':S' . $currentRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FA']],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
            $currentRow++;
        } else {
            // Urutkan berdasarkan tanggal
            usort($pengukuranData, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($pengukuranData as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data terkait
                $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                
                // Format tanggal
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $currentRow, $tanggal);
                
                // Fungsi untuk mendapatkan status warna untuk L10-SPZ02
                $getStatusL10SPZ02 = function($t_psmetrik, $type) {
                    switch($type) {
                        case 'L10':
                            // L-10: Aman ≤560.86 - 565.35, Peringatan 565.36 - 569.65, Bahaya ≥569.66
                            // (sama dengan L-4 karena nilai elevasi sama)
                            if ($t_psmetrik <= 565.35) return 'aman';
                            if ($t_psmetrik <= 569.65) return 'peringatan';
                            return 'bahaya';
                        case 'SPZ02':
                            // SPZ-02: Aman ≤691.46 - 692.35, Peringatan 692.36 - 695.35, Bahaya ≥695.36
                            // (sama dengan L-5 karena nilai elevasi mirip)
                            if ($t_psmetrik <= 692.35) return 'aman';
                            if ($t_psmetrik <= 695.35) return 'peringatan';
                            return 'bahaya';
                        default:
                            return 'aman';
                    }
                };
                
// ===== L-10 Data =====
$bacaan_L10 = $pembacaanData['L10']['feet'] ?? 0;

// Gunakan safeConvert untuk menghindari TypeError
$bacaan_L10_m = $this->safeConvert($bacaan_L10, 0.3048);
$sheet->setCellValue('B' . $currentRow, $this->safeNumberFormat($bacaan_L10_m, 2));

$t_psmetrik_L10 = 0;
foreach ($perhitunganData as $perhitungan) {
    if ($perhitungan['tipe_piezometer'] == 'L10') {
        $t_psmetrik_L10 = $perhitungan['t_psmetrik'] ?? 0;
        break;
    }
}
$sheet->setCellValue('C' . $currentRow, $this->safeNumberFormat($t_psmetrik_L10, 2));

$sheet->setCellValue('D' . $currentRow, '560.86');  // Aman
$sheet->setCellValue('E' . $currentRow, '565.36');  // Peringatan
$sheet->setCellValue('F' . $currentRow, '569.66');  // Bahaya

$status_L10 = $getStatusL10SPZ02($t_psmetrik_L10, 'L10');
$sheet->setCellValue('G' . $currentRow, $this->safeNumberFormat($t_psmetrik_L10, 2));

// ===== SPZ-02 Data =====
$bacaan_SPZ02 = $pembacaanData['SPZ02']['feet'] ?? 0;

// Gunakan safeConvert untuk menghindari TypeError
$bacaan_SPZ02_m = $this->safeConvert($bacaan_SPZ02, 0.3048);
$sheet->setCellValue('H' . $currentRow, $this->safeNumberFormat($bacaan_SPZ02_m, 2));

$t_psmetrik_SPZ02 = 0;
foreach ($perhitunganData as $perhitungan) {
    if ($perhitungan['tipe_piezometer'] == 'SPZ02') {
        $t_psmetrik_SPZ02 = $perhitungan['t_psmetrik'] ?? 0;
        break;
    }
}
$sheet->setCellValue('I' . $currentRow, $this->safeNumberFormat($t_psmetrik_SPZ02, 2));

$sheet->setCellValue('J' . $currentRow, '691.46');  // Aman
$sheet->setCellValue('K' . $currentRow, '692.36');  // Peringatan
$sheet->setCellValue('L' . $currentRow, '695.36');  // Bahaya

$status_SPZ02 = $getStatusL10SPZ02($t_psmetrik_SPZ02, 'SPZ02');
$sheet->setCellValue('M' . $currentRow, $this->safeNumberFormat($t_psmetrik_SPZ02, 2));
                
                // Kolom N-S (kosong)
                for ($col = 'N'; $col <= 'S'; $col++) {
                    $sheet->setCellValue($col . $currentRow, '');
                }
                
                // Apply warna latar untuk kolom status (background berwarna, teks hitam)
                $this->applyStatusColor($sheet, 'G' . $currentRow, $status_L10);
                $this->applyStatusColor($sheet, 'M' . $currentRow, $status_SPZ02);
                
                $currentRow++;
            }
            
            // ===== APPLY STYLES UNTUK DATA =====
            if ($currentRow > $startDataRow) {
                $dataAreaStart = $startDataRow;
                $dataAreaEnd = $currentRow - 1;
                $dataAreaRange = 'A' . $dataAreaStart . ':S' . $dataAreaEnd;
                
                // 1. Apply warna latar belakang untuk semua data (selang-seling)
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                    $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                    $rowRange = 'A' . $row . ':S' . $row;
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border untuk semua sel data
                $sheet->getStyle($dataAreaRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ]
                ]);
                
                // 3. Apply warna khusus untuk kolom ambang batas
                $amanColumns = ['D', 'J'];
                $peringatanColumns = ['E', 'K'];
                $bahayaColumns = ['F', 'L'];
                
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF155724']]
                    ]);
                }
                
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF856404']]
                    ]);
                }
                
                foreach ($bahayaColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF721C24']]
                    ]);
                }
                
                // 4. Apply warna latar untuk kolom kosong N-S
                $emptyRange = 'N' . $dataAreaStart . ':S' . $dataAreaEnd;
                $sheet->getStyle($emptyRange)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE3F2FD']]
                ]);
                
                // 5. Alignment untuk kolom numerik (kanan)
                $numericColumns = array_merge(
                    $this->getColumnRange('B', 'M')
                );
                
                foreach ($numericColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // 6. Alignment untuk kolom tanggal (tengah)
                $sheet->getStyle('A' . $dataAreaStart . ':A' . $dataAreaEnd)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('A11'); // Freeze header rows, mulai data di row 11
        
        // ===== FORMAT ANGKA =====
        if ($currentRow > $startDataRow) {
            // Format semua kolom numerik dengan 2 desimal
            $numericRange = 'B' . $startDataRow . ':M' . ($currentRow - 1);
            $sheet->getStyle($numericRange)
                ->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 12,  // Tanggal
            'B' => 10,  // Bacaan L10
            'C' => 14,  // T.Psmetrik L10
            'D' => 8,   // Aman L10
            'E' => 10,  // Peringatan L10
            'F' => 8,   // Bahaya L10
            'G' => 14,  // Status L10
            'H' => 10,  // Bacaan SPZ02
            'I' => 14,  // T.Psmetrik SPZ02
            'J' => 8,   // Aman SPZ02
            'K' => 10,  // Peringatan SPZ02
            'L' => 8,   // Bahaya SPZ02
            'M' => 14,  // Status SPZ02
            'N' => 8,   // Kosong
            'O' => 8,   // Kosong
            'P' => 8,   // Kosong
            'Q' => 8,   // Kosong
            'R' => 8,   // Kosong
            'S' => 8    // Kosong
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        for ($i = 1; $i <= 3; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }
        
        for ($i = 4; $i <= 10; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        if (!empty($pengukuranData)) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        if ($currentRow > $startDataRow) {
            $footerRow = $currentRow;
            $sheet->mergeCells('A' . $footerRow . ':S' . $footerRow);
            
            $totalRecords = count($pengukuranData);
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . 
                ' | Grafik History L10-SPZ02 | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':S' . $footerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF666666'], 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            $sheet->getRowDimension($footerRow)->setRowHeight(25);
        }
        
        // ===== SET PRINT AREA =====
        if ($currentRow > $startDataRow) {
            $sheet->getPageSetup()->setPrintArea('A1:S' . ($currentRow - 1));
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:S10');
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter);
    }
    
    // ===== HELPER METHODS UNTUK AMBANG BATAS =====
    
    /**
     * Fill Ambang Batas Data untuk L-1
     */
    private function fillAmbangBatasDataL1($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-1
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 647.84 - 648.93', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '648.94 - 650.63', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 650.64', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-2
     */
    private function fillAmbangBatasDataL2($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-2
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 647.86 - 648.95', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '648.96 - 650.65', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 650.66', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-3
     */
    private function fillAmbangBatasDataL3($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-3
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 613.75 - 614.84', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '614.85 - 616.54', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 616.55', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-4
     */
    private function fillAmbangBatasDataL4($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-4
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 560.86 - 565.35', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '565.36 - 569.65', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 569.66', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-5
     */
    private function fillAmbangBatasDataL5($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-5
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 691.46 - 692.35', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '692.36 - 695.35', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 695.36', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-6
     */
    private function fillAmbangBatasDataL6($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-6
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 680.79 - 681.68', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '681.69 - 684.68', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 684.69', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-7
     */
    private function fillAmbangBatasDataL7($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-7
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 652.06 - 652.95', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '652.96 - 655.95', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 655.96', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-8
     */
    private function fillAmbangBatasDataL8($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-8
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 657.84 - 658.73', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '658.74 - 661.73', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 661.74', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-9
     */
    private function fillAmbangBatasDataL9($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-9
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 621.15 - 622.04', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '622.05 - 625.04', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 625.05', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk L-10
     */
    private function fillAmbangBatasDataL10($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk L-10 (sama dengan L-4 karena nilai elevasi sama)
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 560.86 - 565.35', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '565.36 - 569.65', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 569.66', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Fill Ambang Batas Data untuk SPZ-02
     */
    private function fillAmbangBatasDataSPZ02($sheet, $startCell, $endCell)
    {
        list($startCol, $startRow) = $this->cellToCoords($startCell);
        list($endCol, $endRow) = $this->cellToCoords($endCell);
        
        // Data untuk SPZ-02 (sama dengan L-5 karena nilai elevasi mirip)
        $data = [
            ['title' => 'Aman (El.m)', 'range' => '≤ 691.46 - 692.35', 'color' => 'FF28A745'],
            ['title' => 'Peringatan (El.m)', 'range' => '692.36 - 695.35', 'color' => 'FFFFC107'],
            ['title' => 'Bahaya (El.m)', 'range' => '≥ 695.36', 'color' => 'FFDC3545']
        ];
        
        $this->fillAmbangBatasArea($sheet, $startCell, $endCell, $data, 'Ambang Batas');
    }
    
    /**
     * Generic method untuk fill ambang batas area
     */
    /**
 * Generic method untuk fill ambang batas area - OPTIMIZED VERSION
 */
private function fillAmbangBatasArea($sheet, $startCell, $endCell, $data, $title = 'Ambang Batas')
{
    list($startCol, $startRow) = $this->cellToCoords($startCell);
    list($endCol, $endRow) = $this->cellToCoords($endCell);
    
    // HITUNG DIMENSI AREA
    $totalRows = $endRow - $startRow + 1; // Biasanya 4 rows (D6:G9)
    $totalCols = $this->columnToIndex($endCol) - $this->columnToIndex($startCol) + 1;
    
    // SET BACKGROUND UNTUK SELURUH AREA (lebih efisien)
    $bgColor = 'FFE3F2FD'; // Biru muda
    for ($row = $startRow; $row <= $endRow; $row++) {
        for ($colIdx = 0; $colIdx < $totalCols; $colIdx++) {
            $col = $this->indexToColumn($this->columnToIndex($startCol) + $colIdx);
            $cell = $col . $row;
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000']
                    ]
                ]
            ]);
        }
    }
    
    // TULISKAN DATA TANPA MERGE CELLS YANG TERLALU BANYAK
    // Baris 1: Judul - hanya merge horizontal
    $titleRow = $startRow;
    $titleRange = $startCol . $titleRow . ':' . $endCol . $titleRow;
    $sheet->mergeCells($titleRange);
    $sheet->setCellValue($startCol . $titleRow, $title);
    $sheet->getStyle($titleRange)->applyFromArray([
        'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ]);
    
    // Baris 2-4: Data Aman, Peringatan, Bahaya - TIDAK PERLU MERGE VERTIKAL
    // Gunakan sel individual untuk setiap baris data
    for ($i = 0; $i < 3; $i++) {
        $dataRow = $startRow + $i + 1; // +1 karena baris pertama untuk judul
        $dataRange = $startCol . $dataRow . ':' . $endCol . $dataRow;
        
        // Merge hanya horizontal (tidak vertikal)
        $sheet->mergeCells($dataRange);
        
        $cellValue = $data[$i]['title'] . " " . $data[$i]['range'];
        $sheet->setCellValue($startCol . $dataRow, $cellValue);
        $sheet->getStyle($dataRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 7, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $data[$i]['color']]],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }
}

/**
 * Convert column letter to index (0-based)
 */
private function columnToIndex($column)
{
    $column = strtoupper($column);
    $length = strlen($column);
    $index = 0;
    
    for ($i = 0; $i < $length; $i++) {
        $index = $index * 26 + (ord($column[$i]) - ord('A') + 1);
    }
    
    return $index - 1; // 0-based
}

/**
 * Convert index to column letter
 */
private function indexToColumn($index)
{
    $column = '';
    $index++; // Convert to 1-based
    
    while ($index > 0) {
        $modulo = ($index - 1) % 26;
        $column = chr(65 + $modulo) . $column;
        $index = (int)(($index - $modulo) / 26);
    }
    
    return $column;
}
    
    /**
     * Apply warna status untuk kolom T.Psmetrik - background berwarna, teks hitam
     */
    private function applyStatusColor($sheet, $cell, $status)
    {
        $colors = [
            'aman' => ['bg' => 'FFD4EDDA', 'text' => 'FF000000'], // Background hijau, teks hitam
            'peringatan' => ['bg' => 'FFFFF3CD', 'text' => 'FF000000'], // Background kuning, teks hitam
            'bahaya' => ['bg' => 'FFF8D7DA', 'text' => 'FF000000'] // Background merah, teks hitam
        ];
        
        if (isset($colors[$status])) {
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colors[$status]['bg']]],
                'font' => ['bold' => true, 'color' => ['argb' => $colors[$status]['text']]]
            ]);
        }
    }
    
    /**
     * Apply style untuk sel dengan rowspan
     */
    private function applyRowspanStyle($sheet, $startCell, $endCell, $bgColor)
    {
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }
    
    /**
     * Apply style untuk sel dengan colspan
     */
    private function applyColspanStyle($sheet, $startCell, $endCell, $bgColor)
    {
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }
    
    /**
     * Apply style untuk sel merged (2 kolom)
     */
    private function applyMergedCellStyle($sheet, $startCell, $endCell, $bgColor, $innerBorder = false)
    {
        $borderStyle = [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000']
            ]
        ];
        
        if ($innerBorder) {
            $borderStyle['inside'] = [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000']
            ];
        }
        
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'borders' => $borderStyle
        ]);
    }
    
    /**
     * Apply style untuk sel tunggal
     */
    private function applySingleCellStyle($sheet, $cell, $bgColor)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }
    
    /**
     * Setup Excel Header & Footer
     */
    private function setupExcelHeaderFooter($sheet, $tahun, $periode, $dma)
    {
        $headerFooter = $sheet->getHeaderFooter();
        
        // Build filter info untuk footer
        $filterInfo = [];
        if (!empty($tahun)) $filterInfo[] = "Tahun: $tahun";
        if (!empty($periode)) $filterInfo[] = "Periode: $periode";
        if (!empty($dma)) $filterInfo[] = "DMA: $dma";
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        $sheetTitle = $sheet->getTitle();
        
        // Set Header
        $headerFooter->setOddHeader(
            '&L&"Calibri,Bold"&12PT INDONESIA POWER' . 
            '&C&"Calibri"&10' . $sheetTitle .
            '&R&"Calibri"&8' . date('d/m/Y H:i')
        );
        
        // Set Footer
        $headerFooter->setOddFooter(
            '&L&"Calibri"&8Filter: ' . $filterText .
            '&C&"Calibri"&8Halaman &P dari &N' .
            '&R&"Calibri"&8© ' . date('Y') . ' - Sistem Monitoring Piezometer'
        );
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
    private function getColumnRange($start, $end)
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
     * Convert cell reference to coordinates
     */
    private function cellToCoords($cell)
    {
        preg_match('/([A-Z]+)(\d+)/', $cell, $matches);
        return [$matches[1], (int)$matches[2]];
    }
}