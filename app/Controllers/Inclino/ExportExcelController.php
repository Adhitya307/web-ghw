<?php

namespace App\Controllers\Inclino;

use App\Controllers\BaseController;
use App\Models\Inclino\PembacaanInclinoModel;
use App\Models\Inclino\Ireadingmodel;
use App\Models\Inclino\ProfilAModel;
use App\Models\Inclino\ProfilBModel;
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
    protected $pembacaanModel;
    protected $ireadingModel;
    protected $profilAModel;
    protected $profilBModel;

    public function __construct()
    {
        // Initialize models
        $this->pembacaanModel = new PembacaanInclinoModel();
        $this->ireadingModel = new Ireadingmodel();
        $this->profilAModel = new ProfilAModel();
        $this->profilBModel = new ProfilBModel();
    }

    /**
     * Export Excel untuk InclinoMeter dengan struktur sama persis seperti di view
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
            $year = $this->request->getGet('year');
            $month = $this->request->getGet('month');
            $day = $this->request->getGet('day');
            $borehole = $this->request->getGet('borehole');
            
            // Get filtered data (menggunakan metode yang sama seperti di InclinoController)
            $filteredData = $this->getFilteredDataForExport($year, $month, $day, $borehole);
            
            // Jika tidak ada data
            if (empty($filteredData['data'])) {
                return $this->exportEmptyTemplate($year, $month, $day, $borehole);
            }

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            
            // ===== SETUP DEFAULT STYLE =====
            $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setName('Calibri')
                ->setSize(9);
            
            // ===== SHEET 1: DATA INCLINOMETER UTAMA =====
            $mainSheet = $spreadsheet->getActiveSheet();
            $mainSheet->setTitle('Data InclinoMeter');
            $this->createMainSheet($mainSheet, $filteredData, $year, $month, $day, $borehole);
            
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
            
            // Generate filename berdasarkan filter
            $filename = 'InclinoMeter_Data';
            if (!empty($borehole)) {
                $filename .= '_' . str_replace(' ', '_', $borehole);
            }
            if (!empty($year)) {
                $filename .= '_' . $year;
            }
            if (!empty($month)) {
                $filename .= '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
            }
            if (!empty($day)) {
                $filename .= '_' . str_pad($day, 2, '0', STR_PAD_LEFT);
            }
            $filename .= '_' . date('Ymd_His') . '.xlsx';
            
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
            log_message('error', 'Error exporting Excel (InclinoMeter): ' . $e->getMessage());
            
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
     * Get filtered data untuk export (sesuai dengan view)
     */
    private function getFilteredDataForExport($year, $month, $day, $borehole)
    {
        try {
            $builder = $this->pembacaanModel->db->table('inclinometer_readings ir');
            
            $builder->select("
                ir.id_pengukuran,
                ir.depth,
                ir.face_a_plus,
                ir.face_a_minus,
                ir.face_b_plus,
                ir.face_b_minus,
                ir.reading_date,
                ir.borehole_name,
                ir.probe_serial,
                ir.reel_serial,
                ir.operator,
                ia.face_a as mean_deviation_a,
                ia.face_b as mean_deviation_b,
                ia.mean_cum_deviation_a,
                ia.mean_cum_deviation_b,
                ia.basereading_a,
                ia.basereading_b,
                ia.displace_profile_a,
                ia.displace_profile_b,
                pa.nilai_profil_a,
                pb.nilai_profil_b
            ")
            ->join('initial_reading ia', 'ir.id_pengukuran = ia.id_pengukuran AND ir.depth = ia.depth', 'left')
            ->join('profil_a pa', 'ir.id_pengukuran = pa.id_pengukuran AND ir.depth = pa.depth', 'left')
            ->join('profil_b pb', 'ir.id_pengukuran = pb.id_pengukuran AND ir.depth = pb.depth', 'left');
        
            // Apply filters - SAMA PERSIS DENGAN DI INCLINOCONTROLLER
            if (!empty($year)) {
                $builder->where('YEAR(ir.reading_date)', $year);
            }
            
            if (!empty($month)) {
                $builder->where('MONTH(ir.reading_date)', $month);
            }
            
            if (!empty($day)) {
                $builder->where('DAY(ir.reading_date)', $day);
            }
            
            if (!empty($borehole)) {
                $builder->where('ir.borehole_name', $borehole);
            }
            
            // Urutkan depth DESCENDING seperti di view
            $builder->orderBy('ir.depth', 'DESC');
            
            $rawData = $builder->get()->getResultArray();
            
            if (empty($rawData)) {
                return [
                    'header' => [],
                    'data' => [],
                    'metadata' => []
                ];
            }
            
            // Process data untuk table - GUNAKAN METHOD YANG SAMA DENGAN INCLINOCONTROLLER
            $processedData = $this->processTableDataForExport($rawData);
            
            return [
                'header' => $this->getTableHeaderForExport(),
                'data' => $processedData,
                'metadata' => $this->getMetadataForExport($rawData)
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'getFilteredDataForExport Error: ' . $e->getMessage());
            return [
                'header' => [],
                'data' => [],
                'metadata' => []
            ];
        }
    }

    /**
     * Process data untuk export (sama seperti di InclinoController)
     */
    private function processTableDataForExport($rawData)
    {
        $processedData = [];
        $counter = 1;
        
        foreach ($rawData as $row) {
            $face_a_avg = isset($row['mean_deviation_a']) ? $row['mean_deviation_a'] : (($row['face_a_plus'] + $row['face_a_minus']) / 2);
            $face_b_avg = isset($row['mean_deviation_b']) ? $row['mean_deviation_b'] : (($row['face_b_plus'] + $row['face_b_minus']) / 2);
            
            $baseReadingA = isset($row['basereading_a']) ? $row['basereading_a'] : $this->getBaseReadingA($row['depth']);
            $baseReadingB = isset($row['basereading_b']) ? $row['basereading_b'] : $this->getBaseReadingB($row['depth']);
            
            $diff_a = $face_a_avg - $baseReadingA;
            $diff_b = $face_b_avg - $baseReadingB;
            
            $mean_cum_dev_a = isset($row['mean_cum_deviation_a']) ? $row['mean_cum_deviation_a'] : ((500 * $diff_a) / 0.5);
            $mean_cum_dev_b = isset($row['mean_cum_deviation_b']) ? $row['mean_cum_deviation_b'] : ((500 * $diff_b) / 0.5);
            
            $processedData[] = [
                'no' => $counter++,
                'depth' => number_format($row['depth'], 1),
                'face_a_plus' => number_format($row['face_a_plus'], 6),
                'face_a_minus' => number_format($row['face_a_minus'], 6),
                'face_a_avg' => number_format($face_a_avg, 6),
                'basereading_a' => number_format($baseReadingA, 6),
                'diff_a' => number_format($diff_a, 6),
                'mean_cum_deviation_a' => number_format($mean_cum_dev_a, 6),
                'displace_profile_a' => isset($row['displace_profile_a']) ? number_format($row['displace_profile_a'], 6) : '0.000000',
                'face_b_plus' => number_format($row['face_b_plus'], 6),
                'face_b_minus' => number_format($row['face_b_minus'], 6),
                'face_b_avg' => number_format($face_b_avg, 6),
                'basereading_b' => number_format($baseReadingB, 6),
                'diff_b' => number_format($diff_b, 6),
                'mean_cum_deviation_b' => number_format($mean_cum_dev_b, 6),
                'displace_profile_b' => isset($row['displace_profile_b']) ? number_format($row['displace_profile_b'], 6) : '0.000000',
                'profil_a' => isset($row['nilai_profil_a']) ? number_format($row['nilai_profil_a'], 6) : '0.000000',
                'profil_b' => isset($row['nilai_profil_b']) ? number_format($row['nilai_profil_b'], 6) : '0.000000',
                'id_pengukuran' => $row['id_pengukuran']
            ];
        }
        
        return $processedData;
    }
    
    /**
     * Get base reading A (sama seperti di InclinoController)
     */
    private function getBaseReadingA($depth)
    {
        $key = number_format($depth, 1, '.', '');
        
        $defaultValues = [
            '-0.5' => 0.003, '-1.0' => 0.007, '-1.5' => 0.010, '-2.0' => 0.009,
            '-2.5' => 0.006, '-3.0' => 0.005, '-3.5' => 0.003, '-4.0' => 0.002,
            '-4.5' => 0.000, '-5.0' => -0.001
        ];
        
        return isset($defaultValues[$key]) ? $defaultValues[$key] : 0.000000;
    }
    
    /**
     * Get base reading B (sama seperti di InclinoController)
     */
    private function getBaseReadingB($depth)
    {
        $key = number_format($depth, 1, '.', '');
        
        $defaultValues = [
            '-0.5' => 0.006, '-1.0' => 0.005, '-1.5' => 0.005, '-2.0' => 0.005,
            '-2.5' => 0.005, '-3.0' => 0.004, '-3.5' => 0.004, '-4.0' => 0.004,
            '-4.5' => 0.003, '-5.0' => 0.001
        ];
        
        return isset($defaultValues[$key]) ? $defaultValues[$key] : 0.000000;
    }
    
    /**
     * Get metadata untuk export (sama seperti di InclinoController)
     */
    private function getMetadataForExport($rawData)
    {
        if (empty($rawData)) {
            return null;
        }
        
        $firstRow = $rawData[0];
        
        $readingDate = isset($firstRow['reading_date']) ? date('d-m-Y', strtotime($firstRow['reading_date'])) : 'Unknown';
        
        return [
            'borehole_name' => $firstRow['borehole_name'] ?? 'Unknown',
            'reading_date' => $readingDate,
            'probe_serial' => $firstRow['probe_serial'] ?? 'Unknown',
            'reel_serial' => $firstRow['reel_serial'] ?? 'Unknown',
            'operator' => $firstRow['operator'] ?? 'Unknown',
            'total_records' => count($rawData),
            'min_depth' => min(array_column($rawData, 'depth')),
            'max_depth' => max(array_column($rawData, 'depth'))
        ];
    }
    
    /**
     * Get table header structure untuk export (sesuai dengan view inclino)
     */
    private function getTableHeaderForExport()
    {
        return [
            'row1' => [
                ['text' => 'No', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-info-column'],
                ['text' => 'Depth (m)', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-info-column'],
                ['text' => 'Reading Date', 'colspan' => 7, 'rowspan' => 1, 'class' => 'point-header'],
                ['text' => '', 'colspan' => 7, 'rowspan' => 1, 'class' => 'point-header'],
                ['text' => 'Profil A', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-reading'],
                ['text' => 'Profil B', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-reading'],
                ['text' => 'Aksi', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-action']
            ],
            'row2' => [
                ['text' => 'Pembacaan', 'colspan' => 2, 'rowspan' => 1, 'class' => 'initial-header'],
                ['text' => 'Rata-rata (Face A)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Base Reading', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-initial'],
                ['text' => 'Selisih (A-AR)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Mean Cum Dev', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Displace Profil', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-result'],
                ['text' => 'Pembacaan', 'colspan' => 2, 'rowspan' => 1, 'class' => 'initial-header'],
                ['text' => 'Rata-rata (Face B)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Base Reading', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-initial'],
                ['text' => 'Selisih (B-BR)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Mean Cum Dev', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Displace Profil', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-result']
            ],
            'row3' => [
                ['text' => 'Face A+', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => 'Face A-', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => 'Face B+', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => 'Face B-', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
            ],
            'row4' => [
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik']
            ]
        ];
    }
    
    /**
     * Create Main Sheet dengan struktur sama persis seperti view Inclino
     */
    private function createMainSheet($sheet, $filteredData, $yearFilter, $monthFilter, $dayFilter, $boreholeFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA SESUAI DENGAN VIEW INCLINO =====
        $headerBlue = 'FF2C3E50';          // Biru gelap untuk header utama
        $bgCalculation = 'FFF0F9EB';       // Hijau muda untuk PERHITUNGAN (bg-calculation)
        $bgInitial = 'FFE6FFED';           // Hijau muda untuk INITIAL (bg-initial)
        $bgInfoColumn = 'FFE7F1FF';        // Biru muda untuk kolom info (bg-info-column)
        $readingDateHeader = 'FF2C3E50';   // Biru gelap untuk READING DATE header
        $bgMetrik = 'FFFFF2CC';            // Kuning muda untuk METRIK (bg-metrik)
        $headerLightGray = 'FFCED4DA';     // Abu muda untuk informasi
        $dataWhite = 'FFFFFFFF';           // Putih untuk data baris ganjil
        $dataLightGray = 'FFF8F9FA';       // Abu muda untuk data baris genap
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'M'; // Kolom terakhir: Depth + 12 kolom (sesuai dengan view inclino)
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'INCLINOMETER MONITORING DATA - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Informasi Metadata
        $sheet->mergeCells('A2:' . $lastCol . '2');
        
        // Build filter info
        $filterInfo = [];
        if (!empty($yearFilter)) $filterInfo[] = "Tahun: $yearFilter";
        if (!empty($monthFilter)) {
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $monthName = $monthNames[(int)$monthFilter] ?? $monthFilter;
            $filterInfo[] = "Bulan: $monthName";
        }
        if (!empty($dayFilter)) $filterInfo[] = "Hari: $dayFilter";
        if (!empty($boreholeFilter)) $filterInfo[] = "Lokasi: $boreholeFilter";
        
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        $sheet->setCellValue('A2', 'Diekspor pada: ' . date('d F Y H:i:s') . ' | ' . $filterText);
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        // Row 3: Metadata jika ada
        $currentRow = 3;
        if (!empty($filteredData['metadata'])) {
            $metadata = $filteredData['metadata'];
            
            $sheet->setCellValue('A' . $currentRow, 'Lokasi: ' . ($metadata['borehole_name'] ?? '-'));
            $sheet->setCellValue('F' . $currentRow, 'Tanggal: ' . ($metadata['reading_date'] ?? '-'));
            
            $sheet->setCellValue('A' . ($currentRow + 1), 'Serial Probe: ' . ($metadata['probe_serial'] ?? '-'));
            $sheet->setCellValue('F' . ($currentRow + 1), 'Serial Reel: ' . ($metadata['reel_serial'] ?? '-'));
            
            $sheet->setCellValue('A' . ($currentRow + 2), 'Operator: ' . ($metadata['operator'] ?? '-'));
            $sheet->setCellValue('F' . ($currentRow + 2), 'Total Data: ' . ($metadata['total_records'] ?? '0'));
            
            $sheet->getStyle('A' . $currentRow . ':M' . ($currentRow + 2))->applyFromArray([
                'font' => ['size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
            
            $currentRow += 4; // Skip 3 rows untuk metadata + 1 row kosong
        } else {
            $currentRow += 1; // Skip 1 row kosong
        }
        
        // ===== HEADER TABEL (SAMA PERSIS DENGAN VIEW) =====
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 3; // 4 baris header
        
        // ===== ROW 1: Main Header =====
        // Kolom Depth (sticky)
        $sheet->setCellValue('A' . $currentRow, 'Depth (m)');
        $sheet->mergeCells('A' . $currentRow . ':A' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . $headerEndRow, $bgInfoColumn, true);
        
        // READING DATE 1
        $sheet->setCellValue('B' . $currentRow, 'READING DATE 1');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyMergedCellStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $readingDateHeader);
        
        // READING DATE 2
        $sheet->setCellValue('H' . $currentRow, 'READING DATE 2');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyMergedCellStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $readingDateHeader);
        
        $currentRow++;
        
        // ===== ROW 2: Column Headers =====
        // Kolom-kolom untuk READING DATE 1
        $sheet->setCellValue('B' . $currentRow, 'Deviation face A+');
        $this->applySingleCellStyle($sheet, 'B' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('C' . $currentRow, 'Deviation face A-');
        $this->applySingleCellStyle($sheet, 'C' . $currentRow, $bgInitial);
        
        $sheet->setCellValue('D' . $currentRow, 'Mean Deviation A');
        $this->applySingleCellStyle($sheet, 'D' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('E' . $currentRow, 'Deviation face B+');
        $this->applySingleCellStyle($sheet, 'E' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('F' . $currentRow, 'Deviation face B-');
        $this->applySingleCellStyle($sheet, 'F' . $currentRow, $bgInitial);
        
        $sheet->setCellValue('G' . $currentRow, 'Mean Deviation B');
        $this->applySingleCellStyle($sheet, 'G' . $currentRow, $bgCalculation);
        
        // Kolom-kolom untuk READING DATE 2
        $sheet->setCellValue('H' . $currentRow, 'Mean Deviation 500*((4)-(9))/0,5');
        $this->applySingleCellStyle($sheet, 'H' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('I' . $currentRow, 'Base Reading A');
        $this->applySingleCellStyle($sheet, 'I' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('J' . $currentRow, 'Displace Profile A');
        $this->applySingleCellStyle($sheet, 'J' . $currentRow, $bgInitial);
        
        $sheet->setCellValue('K' . $currentRow, 'Mean Deviation 500*((7)-(12))/0,5');
        $this->applySingleCellStyle($sheet, 'K' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('L' . $currentRow, 'Base Reading B');
        $this->applySingleCellStyle($sheet, 'L' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('M' . $currentRow, 'Displace Profile B');
        $this->applySingleCellStyle($sheet, 'M' . $currentRow, $bgInitial);
        
        $currentRow++;
        
        // ===== ROW 3: Unit Headers =====
        $unitHeaders = [
            'A' => '',     // Depth - no unit (already says (m) in title)
            'B' => '(m)',  // Deviation face A+
            'C' => '(m)',  // Deviation face A-
            'D' => '(m)',  // Mean Deviation A
            'E' => '(m)',  // Deviation face B+
            'F' => '(m)',  // Deviation face B-
            'G' => '(m)',  // Mean Deviation B
            'H' => '(mm)', // Mean Deviation 500*((4)-(9))/0,5
            'I' => '(mm)', // Base Reading A
            'J' => '(mm)', // Displace Profile A
            'K' => '(mm)', // Mean Deviation 500*((7)-(12))/0,5
            'L' => '(mm)', // Base Reading B
            'M' => '(mm)'  // Displace Profile B
        ];
        
        foreach ($unitHeaders as $col => $unit) {
            $cell = $col . $currentRow;
            $sheet->setCellValue($cell, $unit);
            
            // Determine background color based on column
            $bgColor = $bgMetrik; // Default untuk unit
            
            if ($col === 'A') {
                $bgColor = $bgInfoColumn;
            } elseif (in_array($col, ['B', 'D', 'E', 'G', 'H', 'I', 'K', 'L'])) {
                $bgColor = $bgCalculation;
            } elseif (in_array($col, ['C', 'F', 'J', 'M'])) {
                $bgColor = $bgInitial;
            }
            
            $this->applySingleCellStyle($sheet, $cell, $bgColor);
        }
        
        $currentRow++;
        
        // ===== ROW 4: Column Numbers (2-13) =====
        $colNumbers = [
            'A' => '',
            'B' => '(2)',
            'C' => '(3)', 
            'D' => '(4)',
            'E' => '(5)',
            'F' => '(6)',
            'G' => '(7)',
            'H' => '(8)',
            'I' => '(9)',
            'J' => '(10)',
            'K' => '(11)',
            'L' => '(12)',
            'M' => '(13)'
        ];
        
        foreach ($colNumbers as $col => $number) {
            $cell = $col . $currentRow;
            $sheet->setCellValue($cell, $number);
            
            // Determine background color based on column
            $bgColor = $bgMetrik; // Default untuk unit
            
            if ($col === 'A') {
                $bgColor = $bgInfoColumn;
            } elseif (in_array($col, ['B', 'D', 'E', 'G', 'H', 'I', 'K', 'L'])) {
                $bgColor = $bgCalculation;
            } elseif (in_array($col, ['C', 'F', 'J', 'M'])) {
                $bgColor = $bgInitial;
            }
            
            $this->applySingleCellStyle($sheet, $cell, $bgColor);
        }
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':M' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':M' . $headerEndRow;
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
        
        $data = $filteredData['data'] ?? [];
        
        foreach ($data as $index => $row) {
            // Kolom Depth (m) - kolom A
            $sheet->setCellValue('A' . $currentRow, $row['depth']);
            
            // Kolom sesuai dengan view - MENAMPILKAN NILAI PERSIS DARI DATABASE
            // Kolom 2: Deviation face A+
            $sheet->setCellValue('B' . $currentRow, $row['face_a_plus']);
            
            // Kolom 3: Deviation face A-
            $sheet->setCellValue('C' . $currentRow, $row['face_a_minus']);
            
            // Kolom 4: Mean Deviation A
            $sheet->setCellValue('D' . $currentRow, $row['face_a_avg']);
            
            // Kolom 5: Deviation face B+
            $sheet->setCellValue('E' . $currentRow, $row['face_b_plus']);
            
            // Kolom 6: Deviation face B-
            $sheet->setCellValue('F' . $currentRow, $row['face_b_minus']);
            
            // Kolom 7: Mean Deviation B
            $sheet->setCellValue('G' . $currentRow, $row['face_b_avg']);
            
            // Kolom 8: Mean Deviation 500*((4)-(9))/0,5
            $sheet->setCellValue('H' . $currentRow, $row['mean_cum_deviation_a']);
            
            // Kolom 9: Base Reading A
            $sheet->setCellValue('I' . $currentRow, $row['basereading_a']);
            
            // Kolom 10: Displace Profile A
            $sheet->setCellValue('J' . $currentRow, $row['displace_profile_a']);
            
            // Kolom 11: Mean Deviation 500*((7)-(12))/0,5
            $sheet->setCellValue('K' . $currentRow, $row['mean_cum_deviation_b']);
            
            // Kolom 12: Base Reading B
            $sheet->setCellValue('L' . $currentRow, $row['basereading_b']);
            
            // Kolom 13: Displace Profile B
            $sheet->setCellValue('M' . $currentRow, $row['displace_profile_b']);
            
            $currentRow++;
        }
        
        // ===== APPLY STYLES UNTUK SELURUH AREA DATA =====
        if ($currentRow > $startDataRow) {
            $dataAreaStart = $startDataRow;
            $dataAreaEnd = $currentRow - 1;
            $dataAreaRange = 'A' . $dataAreaStart . ':M' . $dataAreaEnd;
            
            // 1. Apply warna latar belakang untuk semua data (selang-seling)
            for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                $rowRange = 'A' . $row . ':M' . $row;
                
                $sheet->getStyle($rowRange)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                ]);
            }
            
            // 2. Override warna untuk kolom Depth (A) dengan warna khusus
            $depthColRange = 'A' . $dataAreaStart . ':A' . $dataAreaEnd;
            $sheet->getStyle($depthColRange)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgInfoColumn]],
                'font' => ['bold' => true]
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
            // Kolom dengan angka: semua kecuali kolom A (depth)
            $numericRanges = [
                'B' . $dataAreaStart . ':M' . $dataAreaEnd
            ];
            
            foreach ($numericRanges as $range) {
                $sheet->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
            
            // Kolom teks: A (Depth)
            $sheet->getStyle('A' . $dataAreaStart . ':A' . $dataAreaEnd)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            
            // 5. Format angka untuk kolom numerik (6 desimal)
            $sheet->getStyle('B' . $dataAreaStart . ':M' . $dataAreaEnd)
                ->getNumberFormat()
                ->setFormatCode('0.000000');
                
            // Format untuk depth (1 desimal)
            $sheet->getStyle('A' . $dataAreaStart . ':A' . $dataAreaEnd)
                ->getNumberFormat()
                ->setFormatCode('0.0');
        }
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 10,  // Depth (m)
            'B' => 15,  // Deviation face A+
            'C' => 15,  // Deviation face A-
            'D' => 15,  // Mean Deviation A
            'E' => 15,  // Deviation face B+
            'F' => 15,  // Deviation face B-
            'G' => 15,  // Mean Deviation B
            'H' => 25,  // Mean Deviation 500*((4)-(9))/0,5
            'I' => 15,  // Base Reading A
            'J' => 15,  // Displace Profile A
            'K' => 25,  // Mean Deviation 500*((7)-(12))/0,5
            'L' => 15,  // Base Reading B
            'M' => 15   // Displace Profile B
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ===== SET ROW HEIGHTS =====
        // Atur tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        // Atur tinggi baris header tabel
        for ($i = $headerStartRow; $i <= $headerEndRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        // Atur tinggi baris data
        if ($currentRow > $startDataRow) {
            for ($i = $startDataRow; $i < $currentRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(16);
            }
        }
        
        // ===== FOOTER =====
        $footerRow = $currentRow;
        $sheet->mergeCells('A' . $footerRow . ':M' . $footerRow);
        
        $totalRecords = count($data);
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        $footerText = 'TOTAL REKORD: ' . $totalRecords . ' | Filter: ' . $filterText . 
                     ' | Diekspor pada: ' . date('d F Y H:i:s') . 
                     ' | InclinoMeter Monitoring System - PT Indonesia Power';
        
        $sheet->setCellValue('A' . $footerRow, $footerText);
        
        $sheet->getStyle('A' . $footerRow . ':M' . $footerRow)->applyFromArray([
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
        $sheet->getPageSetup()->setPrintArea('A1:M' . $footerRow);
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $yearFilter, $monthFilter, $dayFilter, $boreholeFilter);
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('A' . ($headerEndRow + 1)); // Freeze header rows
    }
    
    /**
     * Export template kosong jika tidak ada data
     */
    private function exportEmptyTemplate($year, $month, $day, $borehole)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data InclinoMeter');
        
        // Setup default style
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Calibri')
            ->setSize(9);
        
        // Buat header seperti di view
        $this->createEmptyTemplate($sheet, $year, $month, $day, $borehole);
        
        // Setup page margins
        $sheet->getPageMargins()
            ->setTop(0.75)
            ->setRight(0.25)
            ->setLeft(0.25)
            ->setBottom(0.75);
        
        // Save file
        $writer = new Xlsx($spreadsheet);
        $filename = 'InclinoMeter_Data_Empty_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
    
    /**
     * Create empty template
     */
    private function createEmptyTemplate($sheet, $year, $month, $day, $borehole)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA SESUAI DENGAN VIEW INCLINO =====
        $headerBlue = 'FF2C3E50';          // Biru gelap untuk header utama
        $bgCalculation = 'FFF0F9EB';       // Hijau muda untuk PERHITUNGAN (bg-calculation)
        $bgInitial = 'FFE6FFED';           // Hijau muda untuk INITIAL (bg-initial)
        $bgInfoColumn = 'FFE7F1FF';        // Biru muda untuk kolom info (bg-info-column)
        $readingDateHeader = 'FF2C3E50';   // Biru gelap untuk READING DATE header
        $bgMetrik = 'FFFFF2CC';            // Kuning muda untuk METRIK (bg-metrik)
        $headerLightGray = 'FFCED4DA';     // Abu muda untuk informasi
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'M'; // Kolom terakhir: Depth + 12 kolom
        
        // Row 1: Judul Utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'INCLINOMETER MONITORING DATA - PT INDONESIA POWER');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Row 2: Informasi Ekspor
        $sheet->mergeCells('A2:' . $lastCol . '2');
        
        // Build filter info
        $filterInfo = [];
        if (!empty($year)) $filterInfo[] = "Tahun: $year";
        if (!empty($month)) {
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $monthName = $monthNames[(int)$month] ?? $month;
            $filterInfo[] = "Bulan: $monthName";
        }
        if (!empty($day)) $filterInfo[] = "Hari: $day";
        if (!empty($borehole)) $filterInfo[] = "Lokasi: $borehole";
        
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        $sheet->setCellValue('A2', 'Diekspor pada: ' . date('d F Y H:i:s') . ' | ' . $filterText);
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerLightGray]]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        // ===== HEADER TABEL (SAMA PERSIS DENGAN VIEW) =====
        $currentRow = 3;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 3; // 4 baris header
        
        // ===== ROW 1: Main Header =====
        // Kolom Depth (sticky)
        $sheet->setCellValue('A' . $currentRow, 'Depth (m)');
        $sheet->mergeCells('A' . $currentRow . ':A' . $headerEndRow);
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . $headerEndRow, $bgInfoColumn, true);
        
        // READING DATE 1
        $sheet->setCellValue('B' . $currentRow, 'READING DATE 1');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyMergedCellStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $readingDateHeader);
        
        // READING DATE 2
        $sheet->setCellValue('H' . $currentRow, 'READING DATE 2');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyMergedCellStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $readingDateHeader);
        
        $currentRow++;
        
        // ===== ROW 2: Column Headers =====
        // Kolom-kolom untuk READING DATE 1
        $sheet->setCellValue('B' . $currentRow, 'Deviation face A+');
        $this->applySingleCellStyle($sheet, 'B' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('C' . $currentRow, 'Deviation face A-');
        $this->applySingleCellStyle($sheet, 'C' . $currentRow, $bgInitial);
        
        $sheet->setCellValue('D' . $currentRow, 'Mean Deviation A');
        $this->applySingleCellStyle($sheet, 'D' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('E' . $currentRow, 'Deviation face B+');
        $this->applySingleCellStyle($sheet, 'E' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('F' . $currentRow, 'Deviation face B-');
        $this->applySingleCellStyle($sheet, 'F' . $currentRow, $bgInitial);
        
        $sheet->setCellValue('G' . $currentRow, 'Mean Deviation B');
        $this->applySingleCellStyle($sheet, 'G' . $currentRow, $bgCalculation);
        
        // Kolom-kolom untuk READING DATE 2
        $sheet->setCellValue('H' . $currentRow, 'Mean Deviation 500*((4)-(9))/0,5');
        $this->applySingleCellStyle($sheet, 'H' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('I' . $currentRow, 'Base Reading A');
        $this->applySingleCellStyle($sheet, 'I' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('J' . $currentRow, 'Displace Profile A');
        $this->applySingleCellStyle($sheet, 'J' . $currentRow, $bgInitial);
        
        $sheet->setCellValue('K' . $currentRow, 'Mean Deviation 500*((7)-(12))/0,5');
        $this->applySingleCellStyle($sheet, 'K' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('L' . $currentRow, 'Base Reading B');
        $this->applySingleCellStyle($sheet, 'L' . $currentRow, $bgCalculation);
        
        $sheet->setCellValue('M' . $currentRow, 'Displace Profile B');
        $this->applySingleCellStyle($sheet, 'M' . $currentRow, $bgInitial);
        
        $currentRow++;
        
        // ===== ROW 3: Unit Headers =====
        $unitHeaders = [
            'A' => '',     // Depth - no unit (already says (m) in title)
            'B' => '(m)',  // Deviation face A+
            'C' => '(m)',  // Deviation face A-
            'D' => '(m)',  // Mean Deviation A
            'E' => '(m)',  // Deviation face B+
            'F' => '(m)',  // Deviation face B-
            'G' => '(m)',  // Mean Deviation B
            'H' => '(mm)', // Mean Deviation 500*((4)-(9))/0,5
            'I' => '(mm)', // Base Reading A
            'J' => '(mm)', // Displace Profile A
            'K' => '(mm)', // Mean Deviation 500*((7)-(12))/0,5
            'L' => '(mm)', // Base Reading B
            'M' => '(mm)'  // Displace Profile B
        ];
        
        foreach ($unitHeaders as $col => $unit) {
            $cell = $col . $currentRow;
            $sheet->setCellValue($cell, $unit);
            
            // Determine background color based on column
            $bgColor = $bgMetrik; // Default untuk unit
            
            if ($col === 'A') {
                $bgColor = $bgInfoColumn;
            } elseif (in_array($col, ['B', 'D', 'E', 'G', 'H', 'I', 'K', 'L'])) {
                $bgColor = $bgCalculation;
            } elseif (in_array($col, ['C', 'F', 'J', 'M'])) {
                $bgColor = $bgInitial;
            }
            
            $this->applySingleCellStyle($sheet, $cell, $bgColor);
        }
        
        $currentRow++;
        
        // ===== ROW 4: Column Numbers (2-13) =====
        $colNumbers = [
            'A' => '',
            'B' => '(2)',
            'C' => '(3)', 
            'D' => '(4)',
            'E' => '(5)',
            'F' => '(6)',
            'G' => '(7)',
            'H' => '(8)',
            'I' => '(9)',
            'J' => '(10)',
            'K' => '(11)',
            'L' => '(12)',
            'M' => '(13)'
        ];
        
        foreach ($colNumbers as $col => $number) {
            $cell = $col . $currentRow;
            $sheet->setCellValue($cell, $number);
            
            // Determine background color based on column
            $bgColor = $bgMetrik; // Default untuk unit
            
            if ($col === 'A') {
                $bgColor = $bgInfoColumn;
            } elseif (in_array($col, ['B', 'D', 'E', 'G', 'H', 'I', 'K', 'L'])) {
                $bgColor = $bgCalculation;
            } elseif (in_array($col, ['C', 'F', 'J', 'M'])) {
                $bgColor = $bgInitial;
            }
            
            $this->applySingleCellStyle($sheet, $cell, $bgColor);
        }
        
        // ===== APPLY BORDER UNTUK HEADER AREA =====
        $headerOuterRange = 'A' . $headerStartRow . ':M' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        // ===== GARIS PEMISAH ANTARA HEADER DAN DATA =====
        $headerBottomRange = 'A' . $headerEndRow . ':M' . $headerEndRow;
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
        $sheet->mergeCells('A' . $currentRow . ':M' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Tidak ada data inclinometer yang tersedia');
        $sheet->getStyle('A' . $currentRow . ':M' . $currentRow)->applyFromArray([
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
        $sheet->mergeCells('A' . $footerRow . ':M' . $footerRow);
        
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        $footerText = 'TOTAL REKORD: 0 | Filter: ' . $filterText . 
                     ' | Diekspor pada: ' . date('d F Y H:i:s') . 
                     ' | InclinoMeter Monitoring System - PT Indonesia Power';
        
        $sheet->setCellValue('A' . $footerRow, $footerText);
        
        $sheet->getStyle('A' . $footerRow . ':M' . $footerRow)->applyFromArray([
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
        $sheet->getPageSetup()->setPrintArea('A1:M' . $footerRow);
        
        // Setup header & footer
        $this->setupExcelHeaderFooter($sheet, $year, $month, $day, $borehole);
        
        // Set column widths
        $columnWidths = [
            'A' => 10,  // Depth (m)
            'B' => 15,  // Deviation face A+
            'C' => 15,  // Deviation face A-
            'D' => 15,  // Mean Deviation A
            'E' => 15,  // Deviation face B+
            'F' => 15,  // Deviation face B-
            'G' => 15,  // Mean Deviation B
            'H' => 25,  // Mean Deviation 500*((4)-(9))/0,5
            'I' => 15,  // Base Reading A
            'J' => 15,  // Displace Profile A
            'K' => 25,  // Mean Deviation 500*((7)-(12))/0,5
            'L' => 15,  // Base Reading B
            'M' => 15   // Displace Profile B
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        for ($i = $headerStartRow; $i <= $headerEndRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(16);
        }
        
        // Freeze panes
        $sheet->freezePane('A' . ($headerEndRow + 1));
    }
    
    /**
     * Apply style untuk sel dengan rowspan
     */
    private function applyRowspanStyle($sheet, $startCell, $endCell, $bgColor, $sticky = false)
    {
        $styleArray = [
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ];
        
        // Jika sticky, tambahkan border right yang lebih tebal
        if ($sticky) {
            $styleArray['borders']['right'] = [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000']
            ];
        }
        
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray($styleArray);
    }
    
    /**
     * Apply style untuk sel merged (horizontal)
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
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
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
    private function setupExcelHeaderFooter($sheet, $year, $month, $day, $borehole)
    {
        $headerFooter = $sheet->getHeaderFooter();
        
        // Build filter info untuk footer
        $filterInfo = [];
        if (!empty($year)) $filterInfo[] = "Tahun: $year";
        if (!empty($month)) {
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];
            $monthName = $monthNames[(int)$month] ?? $month;
            $filterInfo[] = "Bulan: $monthName";
        }
        if (!empty($day)) $filterInfo[] = "Hari: $day";
        if (!empty($borehole)) $filterInfo[] = "Lokasi: $borehole";
        $filterText = !empty($filterInfo) ? implode(' | ', $filterInfo) : 'Semua Data';
        
        // Set Header
        $headerFooter->setOddHeader(
            '&L&"Calibri,Bold"&12PT INDONESIA POWER' . 
            '&C&"Calibri"&10InclinoMeter Monitoring' .
            '&R&"Calibri"&8' . date('d/m/Y H:i')
        );
        
        // Set Footer
        $headerFooter->setOddFooter(
            '&L&"Calibri"&8Filter: ' . $filterText .
            '&C&"Calibri"&8Halaman &P dari &N' .
            '&R&"Calibri"&8© ' . date('Y') . ' - Sistem Monitoring InclinoMeter'
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