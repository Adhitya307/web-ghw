<?php

namespace App\Controllers\Rightpiezo;

use App\Controllers\BaseController;
use App\Models\Rightpiezo\T_pengukuran_rightpiez;
use App\Models\Rightpiezo\T_pembacaan;
use App\Models\Rightpiezo\B_piezo_metrik;
use App\Models\Rightpiezo\I_reading_atas;
use App\Models\Rightpiezo\Perhitungan_t_psmetrik;
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
    protected $pembacaanModel;
    protected $metrikModel;
    protected $ireadingModel;
    protected $perhitunganModel;

    public function __construct()
    {
        // Initialize models
        $this->pengukuranModel = new T_pengukuran_rightpiez();
        $this->pembacaanModel = new T_pembacaan();
        $this->metrikModel = new B_piezo_metrik();
        $this->ireadingModel = new I_reading_atas();
        $this->perhitunganModel = new Perhitungan_t_psmetrik();
    }

    /**
     * Export Excel dengan header yang sama persis seperti di view Right Piezo
     */
    public function export()
    {
        // Cek login dari session
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        try {
            // Get filter parameters
            $tahun = $this->request->getGet('tahun');
            $periode = $this->request->getGet('periode');
            $tma = $this->request->getGet('tma');
            
            // Build query dengan filter
            $query = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                           ->orderBy('tanggal', 'DESC');
            
            // Apply filters if provided
            if (!empty($tahun)) {
                $query->where('tahun', $tahun);
            }
            
            if (!empty($periode)) {
                $query->where('periode', $periode);
            }
            
            if (!empty($tma)) {
                $query->where('tma', $tma);
            }
            
            $pengukuranData = $query->findAll();

            // Jika tidak ada data
            if (empty($pengukuranData)) {
                return $this->exportEmptyTemplate();
            }

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            
            // ===== SETUP DEFAULT STYLE =====
            $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setName('Calibri')
                ->setSize(9);
            
            // ===== SHEET 1: DATA PIEZOMETER UTAMA (RIGHT BANK) =====
            $mainSheet = $spreadsheet->getActiveSheet();
            $mainSheet->setTitle('Data Piezometer Right');
            $this->createMainSheet($mainSheet, $pengukuranData, $tahun, $periode, $tma);
            
            // Set sheet utama
            $spreadsheet->setActiveSheetIndex(0);
            
            // ===== SETUP PAGE MARGINS =====
            $mainSheet->getPageMargins()
                ->setTop(0.75)
                ->setRight(0.25)
                ->setLeft(0.25)
                ->setBottom(0.75);
            
            // ===== SAVE FILE =====
            $writer = new Xlsx($spreadsheet);
            $filename = 'Piezometer_Right_Bank_' . date('Ymd_His') . '.xlsx';
            
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
            log_message('error', 'Error exporting Excel (Right Piezo): ' . $e->getMessage());
            
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
     * Export template kosong jika tidak ada data
     */
    private function exportEmptyTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Piezometer Right');
        
        // Setup default style
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Calibri')
            ->setSize(9);
        
        // Buat header seperti di view
        $this->createEmptyTemplate($sheet);
        
        // Setup page margins
        $sheet->getPageMargins()
            ->setTop(0.75)
            ->setRight(0.25)
            ->setLeft(0.25)
            ->setBottom(0.75);
        
        // Save file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Piezometer_Right_Bank_Empty_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
    
    /**
     * Create Main Sheet dengan struktur header sama persis seperti view Right Piezo
     */
    private function createMainSheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $tmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA SESUAI DENGAN VIEW RIGHT PIEZO =====
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $bgReading = 'FFE8F4FD';           // Biru muda untuk BACAAN (bg-reading)
        $bgCalculation = 'FFF0F9EB';       // Hijau muda untuk PERHITUNGAN (bg-calculation)
        $bgInitial = 'FFE6FFED';           // Hijau muda untuk INITIAL READINGS (bg-initial)
        $bgMetrik = 'FFFFF2CC';            // Kuning muda untuk BACAAN METRIK (bg-metrik)
        $bgInfoColumn = 'FFE7F1FF';        // Biru muda untuk kolom info (bg-info-column)
        $headerLightGray = 'FFCED4DA';     // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';           // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';       // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'BL'; // Kolom terakhir berdasarkan struktur: A sampai BL
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - RIGHT BANK - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA PIEZOMETER RIGHT BANK');
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
        if (!empty($tmaFilter)) $filterInfo[] = "TMA: $tmaFilter";
        
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
        
        // ===== HEADER TABEL (SAMA PERSIS DENGAN VIEW) =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 3; // 4 baris header (row 4-7)
        
        // ===== ROW 4: Main Header =====
        // KOLOM INFORMASI (5 kolom pertama)
        $sheet->setCellValue('A' . $currentRow, 'TAHUN');
        $sheet->mergeCells('A' . $currentRow . ':A' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('B' . $currentRow, 'PERIODE');
        $sheet->mergeCells('B' . $currentRow . ':B' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'B' . $currentRow, 'B' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('C' . $currentRow, 'TANGGAL');
        $sheet->mergeCells('C' . $currentRow . ':C' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'C' . $currentRow, 'C' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('D' . $currentRow, 'TMA');
        $sheet->mergeCells('D' . $currentRow . ':D' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'D' . $currentRow, 'D' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('E' . $currentRow, 'CH HUJAN');
        $sheet->mergeCells('E' . $currentRow . ':E' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'E' . $currentRow, 'E' . $headerEndRow, $bgInfoColumn);
        
        // BACAAN PIEZOMETER (28 kolom) - F sampai AG - Rowspan 2 baris (baris 4-5)
        $sheet->setCellValue('F' . $currentRow, 'BACAAN PIEZOMETER');
        $sheet->mergeCells('F' . $currentRow . ':AG' . ($currentRow + 1)); // Rowspan 2 baris
        $this->applyMergedRowspanStyle($sheet, 'F' . $currentRow, 'AG' . ($currentRow + 1), $bgMetrik);
        
        // KONVERSI (2 kolom) - AH sampai AI - Rowspan 2 baris (baris 4-5)
        $sheet->setCellValue('AH' . $currentRow, 'KONVERSI');
        $sheet->mergeCells('AH' . $currentRow . ':AI' . ($currentRow + 1)); // Rowspan 2 baris
        $this->applyMergedRowspanStyle($sheet, 'AH' . $currentRow, 'AI' . ($currentRow + 1), $bgCalculation);
        
        // BACAAN PIEZOMETER (14 kolom) - AJ sampai AW - Rowspan 2 baris (baris 4-5)
        $sheet->setCellValue('AJ' . $currentRow, 'BACAAN PIEZOMETER');
        $sheet->mergeCells('AJ' . $currentRow . ':AW' . ($currentRow + 1)); // Rowspan 2 baris
        $this->applyMergedRowspanStyle($sheet, 'AJ' . $currentRow, 'AW' . ($currentRow + 1), $bgReading);
        
        // INITIAL READINGS ATAS (15 kolom) - AX sampai BL
        $sheet->setCellValue('AX' . $currentRow, 'INITIAL READINGS ATAS');
        $sheet->mergeCells('AX' . $currentRow . ':BL' . $currentRow);
        $this->applyColspanStyle($sheet, 'AX' . $currentRow, 'BL' . $currentRow, $bgInitial);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers =====
        // INITIAL READINGS Sub Headers (15 kolom) - AX sampai BL
        $initialHeaders = ['Elev.Piez', 'R-01', 'R-02', 'R-03', 'R-04', 'R-05', 'R-06', 'R-07', 'R-08', 'R-09', 'R-10', 'R-11', 'R-12', 'IPZ-01', 'PZ-04'];
        $currentCol = 'AX';
        
        foreach ($initialHeaders as $header) {
            $sheet->setCellValue($currentCol . $currentRow, $header);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 6: Column Headers =====
        // BACAAN METRIK Headers untuk setiap titik (14 titik × 2 kolom = 28 kolom)
        $titikList = ['R-01', 'R-02', 'R-03', 'R-04', 'R-05', 'R-06', 'R-07', 'R-08', 'R-09', 'R-10', 'R-11', 'R-12', 'IPZ-01', 'PZ-04'];
        $currentCol = 'F';
        
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, $titik);
            $nextCol = $this->nextColumn($currentCol, 1);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $nextCol . $currentRow);
            $this->applyMergedCellStyle($sheet, $currentCol . $currentRow, $nextCol . $currentRow, $bgMetrik, false);
            $currentCol = $this->nextColumn($nextCol, 1);
        }
        
        // KONVERSI Sub Headers - FEET → M dan INCH → M
        $sheet->setCellValue('AH' . $currentRow, 'FEET → M');
        $sheet->mergeCells('AH' . $currentRow . ':AH' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AH' . $currentRow, 'AH' . $headerEndRow, $bgCalculation);
        
        $sheet->setCellValue('AI' . $currentRow, 'INCH → M');
        $sheet->mergeCells('AI' . $currentRow . ':AI' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AI' . $currentRow, 'AI' . $headerEndRow, $bgCalculation);
        
        // BACAAN PIEZOMETER Sub Headers (14 titik)
        $currentCol = 'AJ';
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, $titik);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgReading);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // INITIAL READINGS Headers - Elev.Piez dan nilai tetap
        $initialValues = ['Elev.Piez', '651.48', '647.22', '606.43', '586.41', '655.30', '661.03', '649.06', '671.51', '656.48', '677.35', '644.90', '630.49', '649.90', '651.39'];
        $currentCol = 'AX';
        
        foreach ($initialValues as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 7: Column Headers =====
        // BACAAN METRIK Headers (Feet & Inch untuk setiap titik)
        $currentCol = 'F';
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, 'Feet');
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgMetrik);
            
            $currentCol = $this->nextColumn($currentCol, 1);
            $sheet->setCellValue($currentCol . $currentRow, 'Inch');
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgMetrik);
            
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // PERHITUNGAN Headers - Kedalaman
        $kedalamanValues = ['Kedalaman', '50.00', '60.00', '50.00', '51.00', '50.27', '60.00', '50.00', '40.00', '42.00', '-', '57.00', '42.00', '-', '73.50'];
        $currentCol = 'AX';
        
        foreach ($kedalamanValues as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':BL' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':BL' . $headerEndRow;
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
        
        // Urutkan data berdasarkan tahun dan tanggal descending seperti di view
        usort($pengukuranData, function($a, $b) {
            $tahunA = $a['tahun'] ?? 0;
            $tahunB = $b['tahun'] ?? 0;
            
            if ($tahunA != $tahunB) {
                return $tahunB - $tahunA; // Urutkan tahun descending
            }
            
            $dateA = strtotime($a['tanggal'] ?? '1970-01-01');
            $dateB = strtotime($b['tanggal'] ?? '1970-01-01');
            
            return $dateB - $dateA; // Urutkan tanggal descending
        });
        
        $lastYear = null;
        $rowGroups = []; // Untuk menyimpan rentang row per grup tahun
        
        foreach ($pengukuranData as $index => $p) {
            $pid = $p['id_pengukuran'];
            
            // Ambil semua data terkait
            $pembacaanData = $this->getPembacaanFormatted($pid);
            $metrikData = $this->metrikModel->find($pid) ?? [];
            $initialData = $this->getInitialFormatted($pid);
            $perhitunganData = $this->perhitunganModel->find($pid) ?? [];
            
            // Format angka seperti di view (tidak ada format tambahan)
            $formatNumber = function($value) {
                if ($value === null || $value === '' || $value === '-') {
                    return '-';
                }
                // Jika string KERING, biarkan sebagai string
                if (is_string($value) && strtoupper(trim($value)) === 'KERING') {
                    return 'KERING';
                }
                // Jika numeric, kembalikan sebagai string tanpa format
                if (is_numeric($value)) {
                    return (string)$value;
                }
                return $value;
            };
            
            // Cek apakah tahun baru
            $currentYear = $p['tahun'] ?? null;
            $isNewYearGroup = ($currentYear !== $lastYear);
            
            if ($isNewYearGroup && $lastYear !== null) {
                // Simpan row akhir untuk grup tahun sebelumnya
                $rowGroups[$lastYear]['end'] = $currentRow - 1;
            }
            
            if ($isNewYearGroup) {
                // Mulai grup baru untuk tahun ini
                $rowGroups[$currentYear] = ['start' => $currentRow];
                $lastYear = $currentYear;
            }
            
            // ===== KOLOM INFORMASI =====
            // TAHUN - hanya diisi di row pertama grup
            if ($isNewYearGroup) {
                $sheet->setCellValue('A' . $currentRow, $formatNumber($currentYear));
            }
            
            // PERIODE
            $sheet->setCellValue('B' . $currentRow, $p['periode'] ?? '-');
            
            // TANGGAL - format d/m/Y seperti di view
            $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
            $sheet->setCellValue('C' . $currentRow, $tanggal);
            
            // TMA - Ambil nilai TMA dari database
            $sheet->setCellValue('D' . $currentRow, $formatNumber($p['tma'] ?? '-'));
            
            // CH HUJAN
            $sheet->setCellValue('E' . $currentRow, $formatNumber($p['ch_hujan'] ?? '-'));
            
            // ===== BACAAN METRIK (Feet & Inch) =====
            $currentCol = 'F';
            foreach ($titikList as $titik) {
                $bacaan = $pembacaanData[$titik] ?? null;
                $feet = $bacaan['feet'] ?? null;
                $inch = $bacaan['inch'] ?? null;
                
                $sheet->setCellValue($currentCol . $currentRow, $formatNumber($feet));
                $currentCol = $this->nextColumn($currentCol, 1);
                $sheet->setCellValue($currentCol . $currentRow, $formatNumber($inch));
                $currentCol = $this->nextColumn($currentCol, 1);
            }
            
            // ===== KONVERSI =====
            $sheet->setCellValue('AH' . $currentRow, '0.3048');
            $sheet->setCellValue('AI' . $currentRow, '0.0254');
            
            // ===== BACAAN PIEZOMETER METRIK =====
            $currentCol = 'AJ';
            foreach ($titikList as $titik) {
                $value = $metrikData[$titik] ?? null;
                $sheet->setCellValue($currentCol . $currentRow, $formatNumber($value));
                $currentCol = $this->nextColumn($currentCol, 1);
            }
            
            // ===== INITIAL READINGS =====
            $currentCol = 'AX';
            
            // Elev.Piez - sesuai dengan view (kolom pertama di initial readings)
            $sheet->setCellValue($currentCol . $currentRow, '-');
            $currentCol = $this->nextColumn($currentCol, 1);
            
            // Nilai Elv_Piez untuk setiap titik
            foreach ($titikList as $titik) {
                $initialItem = $initialData[$titik] ?? [];
                $elvPiez = $initialItem['Elv_Piez'] ?? null;
                $sheet->setCellValue($currentCol . $currentRow, $formatNumber($elvPiez));
                $currentCol = $this->nextColumn($currentCol, 1);
            }
            
            $currentRow++;
        }
        
        // Simpan row akhir untuk grup tahun terakhir
        if ($lastYear !== null && isset($rowGroups[$lastYear])) {
            $rowGroups[$lastYear]['end'] = $currentRow - 1;
        }
        
        // Merge cells untuk kolom TAHUN dalam setiap grup
        foreach ($rowGroups as $year => $range) {
            if ($range['start'] < $range['end']) {
                $sheet->mergeCells('A' . $range['start'] . ':A' . $range['end']);
            }
        }
        
        // ===== APPLY STYLES UNTUK SELURUH AREA DATA =====
        if ($currentRow > $startDataRow) {
            $dataAreaStart = $startDataRow;
            $dataAreaEnd = $currentRow - 1;
            $dataAreaRange = 'A' . $dataAreaStart . ':BL' . $dataAreaEnd;
            
            // 1. Apply warna latar belakang untuk semua data (selang-seling)
            for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                $rowRange = 'A' . $row . ':BL' . $row;
                
                $sheet->getStyle($rowRange)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                ]);
            }
            
            // 2. Override warna untuk kolom khusus
            // Kolom info (A-E) tetap warna biru muda
            $infoColRange = 'A' . $dataAreaStart . ':E' . $dataAreaEnd;
            $sheet->getStyle($infoColRange)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgInfoColumn]]
            ]);
            
            // 3. Apply border untuk semua sel data
            $sheet->getStyle($dataAreaRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC']
                    ]
                ]
            ]);
            
            // 4. Alignment untuk kolom numerik (kanan) dan teks (tengah)
            // Kolom dengan angka: D, E, F-AG, AH-AI, AJ-AW, AX-BL
            $numericRanges = [
                'D' . $dataAreaStart . ':E' . $dataAreaEnd,
                'F' . $dataAreaStart . ':AG' . $dataAreaEnd,
                'AH' . $dataAreaStart . ':AI' . $dataAreaEnd,
                'AJ' . $dataAreaStart . ':AW' . $dataAreaEnd,
                'AX' . $dataAreaStart . ':BL' . $dataAreaEnd
            ];
            
            foreach ($numericRanges as $range) {
                $sheet->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
            
            // Kolom teks: A, B, C
            $textRanges = [
                'A' . $dataAreaStart . ':C' . $dataAreaEnd
            ];
            
            foreach ($textRanges as $range) {
                $sheet->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
            
            // 5. Alignment untuk kolom TAHUN yang merged
            foreach ($rowGroups as $year => $range) {
                $sheet->getStyle('A' . $range['start'] . ':A' . $range['end'])
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
            
            // 6. Format angka untuk kolom numerik (4 desimal)
            $sheet->getStyle('D' . $dataAreaStart . ':BL' . $dataAreaEnd)
                ->getNumberFormat()
                ->setFormatCode('0.0000');
            
            // 7. Format khusus untuk kolom konversi (tanpa desimal berlebihan)
            $sheet->getStyle('AH' . $dataAreaStart . ':AI' . $dataAreaEnd)
                ->getNumberFormat()
                ->setFormatCode('0.0000');
        }
        
        // ===== SET COLUMN WIDTHS =====
        // Set width untuk semua kolom sesuai dengan view
        $columnWidths = [
            'A' => 8,   // TAHUN
            'B' => 10,  // PERIODE
            'C' => 12,  // TANGGAL
            'D' => 12,   // TMA
            'E' => 12,  // CH HUJAN
        ];
        
        // Set width untuk kolom data (F sampai BL)
        for ($col = 'F'; $col <= 'BL'; $col = $this->nextColumn($col, 1)) {
            $columnWidths[$col] = 8;
        }
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        // Atur tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // Atur tinggi baris header tabel (row 4-7)
        for ($i = 4; $i <= 7; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        // Atur tinggi baris data
        if ($currentRow > $startDataRow) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        if ($currentRow > $startDataRow) {
            $footerRow = $currentRow;
            $sheet->mergeCells('A' . $footerRow . ':BL' . $footerRow);
            
            $filterInfo = [];
            if (!empty($tahunFilter)) $filterInfo[] = "Tahun: $tahunFilter";
            if (!empty($periodeFilter)) $filterInfo[] = "Periode: $periodeFilter";
            if (!empty($tmaFilter)) $filterInfo[] = "TMA: $tmaFilter";
            
            $totalRecords = count($pengukuranData);
            $filterText = !empty($filterInfo) ? implode(', ', $filterInfo) : 'Semua Data';
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . ' | Filter: ' . $filterText . 
                ' | Diekspor pada: ' . date('d F Y H:i:s') . 
                ' | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':BL' . $footerRow)->applyFromArray([
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
            
            // Set print area termasuk footer
            $sheet->getPageSetup()->setPrintArea('A1:BL' . $footerRow);
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:BL7');
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $tmaFilter);
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('F' . ($headerEndRow + 1)); // Freeze header rows dan 5 kolom pertama
    }
    
    /**
     * Create empty template
     */
    private function createEmptyTemplate($sheet)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA SESUAI DENGAN VIEW RIGHT PIEZO =====
        $headerBlue = 'FF0D6EFD';          // Biru untuk header utama
        $bgReading = 'FFE8F4FD';           // Biru muda untuk BACAAN
        $bgCalculation = 'FFF0F9EB';       // Hijau muda untuk PERHITUNGAN
        $bgInitial = 'FFE6FFED';           // Hijau muda untuk INITIAL READINGS
        $bgMetrik = 'FFFFF2CC';            // Kuning muda untuk BACAAN METRIK
        $bgInfoColumn = 'FFE7F1FF';        // Biru muda untuk kolom info
        $headerLightGray = 'FFCED4DA';     // Abu muda untuk informasi
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'BL'; // Kolom terakhir berdasarkan struktur: A sampai BL
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'PIEZOMETER - RIGHT BANK - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Laporan Data
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA PIEZOMETER RIGHT BANK');
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
        // Row 3: Informasi Ekspor
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s') . ' | Filter: Semua Data');
        $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        // ===== HEADER TABEL (SAMA PERSIS DENGAN VIEW) =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 3; // 4 baris header (row 4-7)
        
        // ===== ROW 4: Main Header =====
        // KOLOM INFORMASI (5 kolom pertama)
        $sheet->setCellValue('A' . $currentRow, 'TAHUN');
        $sheet->mergeCells('A' . $currentRow . ':A' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('B' . $currentRow, 'PERIODE');
        $sheet->mergeCells('B' . $currentRow . ':B' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'B' . $currentRow, 'B' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('C' . $currentRow, 'TANGGAL');
        $sheet->mergeCells('C' . $currentRow . ':C' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'C' . $currentRow, 'C' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('D' . $currentRow, 'TMA');
        $sheet->mergeCells('D' . $currentRow . ':D' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'D' . $currentRow, 'D' . $headerEndRow, $bgInfoColumn);
        
        $sheet->setCellValue('E' . $currentRow, 'CH HUJAN');
        $sheet->mergeCells('E' . $currentRow . ':E' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'E' . $currentRow, 'E' . $headerEndRow, $bgInfoColumn);
        
        // BACAAN PIEZOMETER (28 kolom) - F sampai AG - Rowspan 2 baris (baris 4-5)
        $sheet->setCellValue('F' . $currentRow, 'BACAAN PIEZOMETER');
        $sheet->mergeCells('F' . $currentRow . ':AG' . ($currentRow + 1)); // Rowspan 2 baris
        $this->applyMergedRowspanStyle($sheet, 'F' . $currentRow, 'AG' . ($currentRow + 1), $bgMetrik);
        
        // KONVERSI (2 kolom) - AH sampai AI - Rowspan 2 baris (baris 4-5)
        $sheet->setCellValue('AH' . $currentRow, 'KONVERSI');
        $sheet->mergeCells('AH' . $currentRow . ':AI' . ($currentRow + 1)); // Rowspan 2 baris
        $this->applyMergedRowspanStyle($sheet, 'AH' . $currentRow, 'AI' . ($currentRow + 1), $bgCalculation);
        
        // BACAAN PIEZOMETER (14 kolom) - AJ sampai AW - Rowspan 2 baris (baris 4-5)
        $sheet->setCellValue('AJ' . $currentRow, 'BACAAN PIEZOMETER');
        $sheet->mergeCells('AJ' . $currentRow . ':AW' . ($currentRow + 1)); // Rowspan 2 baris
        $this->applyMergedRowspanStyle($sheet, 'AJ' . $currentRow, 'AW' . ($currentRow + 1), $bgReading);
        
        // INITIAL READINGS ATAS (15 kolom) - AX sampai BL
        $sheet->setCellValue('AX' . $currentRow, 'INITIAL READINGS ATAS');
        $sheet->mergeCells('AX' . $currentRow . ':BL' . $currentRow);
        $this->applyColspanStyle($sheet, 'AX' . $currentRow, 'BL' . $currentRow, $bgInitial);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers =====
        // INITIAL READINGS Sub Headers (15 kolom) - AX sampai BL
        $initialHeaders = ['Elev.Piez', 'R-01', 'R-02', 'R-03', 'R-04', 'R-05', 'R-06', 'R-07', 'R-08', 'R-09', 'R-10', 'R-11', 'R-12', 'IPZ-01', 'PZ-04'];
        $currentCol = 'AX';
        
        foreach ($initialHeaders as $header) {
            $sheet->setCellValue($currentCol . $currentRow, $header);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 6: Column Headers =====
        // BACAAN METRIK Headers untuk setiap titik (14 titik × 2 kolom = 28 kolom)
        $titikList = ['R-01', 'R-02', 'R-03', 'R-04', 'R-05', 'R-06', 'R-07', 'R-08', 'R-09', 'R-10', 'R-11', 'R-12', 'IPZ-01', 'PZ-04'];
        $currentCol = 'F';
        
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, $titik);
            $nextCol = $this->nextColumn($currentCol, 1);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $nextCol . $currentRow);
            $this->applyMergedCellStyle($sheet, $currentCol . $currentRow, $nextCol . $currentRow, $bgMetrik, false);
            $currentCol = $this->nextColumn($nextCol, 1);
        }
        
        // KONVERSI Sub Headers - FEET → M dan INCH → M
        $sheet->setCellValue('AH' . $currentRow, 'FEET → M');
        $sheet->mergeCells('AH' . $currentRow . ':AH' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AH' . $currentRow, 'AH' . $headerEndRow, $bgCalculation);
        
        $sheet->setCellValue('AI' . $currentRow, 'INCH → M');
        $sheet->mergeCells('AI' . $currentRow . ':AI' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'AI' . $currentRow, 'AI' . $headerEndRow, $bgCalculation);
        
        // BACAAN PIEZOMETER Sub Headers (14 titik)
        $currentCol = 'AJ';
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, $titik);
            $sheet->mergeCells($currentCol . $currentRow . ':' . $currentCol . $headerEndRow);
            $this->applyRowspanStyle($sheet, $currentCol . $currentRow, $currentCol . $headerEndRow, $bgReading);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // INITIAL READINGS Headers - Elev.Piez dan nilai tetap
        $initialValues = ['Elev.Piez', '651.48', '647.22', '606.43', '586.41', '655.30', '661.03', '649.06', '671.51', '656.48', '677.35', '644.90', '630.49', '649.90', '651.39'];
        $currentCol = 'AX';
        
        foreach ($initialValues as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgInitial);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        $currentRow++;
        
        // ===== ROW 7: Column Headers =====
        // BACAAN METRIK Headers (Feet & Inch untuk setiap titik)
        $currentCol = 'F';
        foreach ($titikList as $titik) {
            $sheet->setCellValue($currentCol . $currentRow, 'Feet');
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgMetrik);
            
            $currentCol = $this->nextColumn($currentCol, 1);
            $sheet->setCellValue($currentCol . $currentRow, 'Inch');
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgMetrik);
            
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // PERHITUNGAN Headers - Kedalaman
        $kedalamanValues = ['Kedalaman', '50.00', '60.00', '50.00', '51.00', '50.27', '60.00', '50.00', '40.00', '42.00', '-', '57.00', '42.00', '-', '73.50'];
        $currentCol = 'AX';
        
        foreach ($kedalamanValues as $value) {
            $sheet->setCellValue($currentCol . $currentRow, $value);
            $this->applySingleCellStyle($sheet, $currentCol . $currentRow, $bgCalculation);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':BL' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':BL' . $headerEndRow;
        $sheet->getStyle($headerBottomRange)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // Row untuk "Tidak ada data"
        $currentRow = $headerEndRow + 1;
        $sheet->mergeCells('A' . $currentRow . ':BL' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Tidak ada data piezometer yang tersedia');
        $sheet->getStyle('A' . $currentRow . ':BL' . $currentRow)->applyFromArray([
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
        
        // Footer
        $footerRow = $currentRow + 1;
        $sheet->mergeCells('A' . $footerRow . ':BL' . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 
            'TOTAL REKORD: 0 | Filter: Semua Data | ' .
            'Diekspor pada: ' . date('d F Y H:i:s') . 
            ' | Piezometer Monitoring System - PT Indonesia Power'
        );
        
        $sheet->getStyle('A' . $footerRow . ':BL' . $footerRow)->applyFromArray([
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
        
        // Set print area
        $sheet->getPageSetup()->setPrintArea('A1:BL' . $footerRow);
        
        // Setup header & footer
        $this->setupExcelHeaderFooter($sheet, null, null, null);
        
        // Set column widths
        $columnWidths = [
            'A' => 8, 'B' => 10, 'C' => 12, 'D' => 8, 'E' => 12,
        ];
        
        for ($col = 'F'; $col <= 'BL'; $col = $this->nextColumn($col, 1)) {
            $columnWidths[$col] = 8;
        }
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);
        
        for ($i = 4; $i <= 7; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        // Freeze panes
        $sheet->freezePane('F' . ($headerEndRow + 1));
    }
    
    /**
     * Get formatted pembacaan data
     */
    private function getPembacaanFormatted($id_pengukuran)
    {
        $pembacaanData = $this->pembacaanModel->where('id_pengukuran', $id_pengukuran)->findAll();
        $formatted = [];
        
        foreach ($pembacaanData as $bacaan) {
            $formatted[$bacaan['lokasi']] = [
                'feet' => $bacaan['feet'],
                'inch' => $bacaan['inch']
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Get formatted initial data
     */
    private function getInitialFormatted($id_pengukuran)
    {
        $initialData = $this->ireadingModel->where('id_pengukuran', $id_pengukuran)->findAll();
        $formatted = [];
        
        foreach ($initialData as $init) {
            $formatted[$init['titik_piezometer']] = [
                'Elv_Piez' => $init['Elv_Piez'],
                'kedalaman' => $init['kedalaman']
            ];
        }
        
        return $formatted;
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
     * Apply style untuk sel merged dengan rowspan (vertical merge)
     */
    private function applyMergedRowspanStyle($sheet, $startCell, $endCell, $bgColor)
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
     * Apply style untuk sel merged (2 kolom horizontal)
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
    private function setupExcelHeaderFooter($sheet, $tahun, $periode, $tma)
    {
        $headerFooter = $sheet->getHeaderFooter();
        
        // Build filter info untuk footer
        $filterInfo = [];
        if (!empty($tahun)) $filterInfo[] = "Tahun: $tahun";
        if (!empty($periode)) $filterInfo[] = "Periode: $periode";
        if (!empty($tma)) $filterInfo[] = "TMA: $tma";
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        // Set Header
        $headerFooter->setOddHeader(
            '&L&"Calibri,Bold"&12PT INDONESIA POWER' . 
            '&C&"Calibri"&10Right Bank Piezometer' .
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
}