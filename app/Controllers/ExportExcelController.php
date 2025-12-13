<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use App\Models\Rembesan\AnalisaLookBurtModel;

class ExportExcelController extends BaseController
{
    private $srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
    private $twHeaders = ['A1 {R}', 'A1 {L}', 'B1', 'B3', 'B5'];
    
    public function exportExcelRapih()
    {
        // Load semua data yang diperlukan untuk sheet pertama
        $data = $this->getAllData();
        
        // Debug: tampilkan jumlah data
        log_message('debug', 'Jumlah data dari database: ' . count($data));
        
        // Filter data unik untuk menghindari duplikat
        $uniqueData = $this->filterUniqueData($data);
        log_message('debug', 'Jumlah data unik setelah filter: ' . count($uniqueData));
        
        // Gunakan data unik untuk export
        $data = $uniqueData;
        
        // Load data untuk Analisis Look Burt
        $analisaLookBurtModel = new AnalisaLookBurtModel();
        $analisaData = $analisaLookBurtModel->getAll();
        log_message('debug', 'Jumlah data Analisis Look Burt: ' . count($analisaData));
        
        $spreadsheet = new Spreadsheet();
        
        // ==============================================
        // SHEET 1: DATA REMBESAN
        // ==============================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Rembesan');
        
        // === SET PAGE SETUP ===
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // === SET JUDUL UTAMA ===
        $lastCol = $this->getLastColumn();
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'DATA INPUT REMBESAN BENDUNGAN - PT INDONESIA POWER');
        $this->applyTitleStyle($sheet, 'A1:' . $lastCol . '1');
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // === SET SUB JUDUL ===
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA GABUNGAN REMBESAN BENDUNGAN');
        $this->applyTitleStyle($sheet, 'A2:' . $lastCol . '2');
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // === INFORMASI TANGGAL EKSPOR ===
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s'));
        $this->applySubtitleStyle($sheet, 'A3:' . $lastCol . '3');
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // === HEADER TABEL UTAMA (3 BARIS) ===
        $this->createTableHeaders($sheet);
        
        // === ISI DATA ===
        $row = 7;
        foreach ($data as $index => $item) {
            $col = 'A';
            
            // 1. Tahun
            $sheet->setCellValue($col++ . $row, $item['tahun'] ?? '-');
            
            // 2. Bulan
            $sheet->setCellValue($col++ . $row, $item['bulan'] ?? '-');
            
            // 3. Periode
            $sheet->setCellValue($col++ . $row, $item['periode'] ?? '-');
            
            // 4. Tanggal
            $sheet->setCellValue($col++ . $row, $item['tanggal'] ?? '-');
            
            // 5. TMA Waduk - tampilkan semua digit
            $tma_waduk = $item['tma_waduk'] ?? null;
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($tma_waduk, 4));
            
            // 6. Curah Hujan - tampilkan semua digit
            $curah_hujan = $item['curah_hujan'] ?? null;
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($curah_hujan, 2));
            
            // 7-11. Thomson Weir (5 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['a1_r'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['a1_l'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['b1'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['b3'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['b5'] ?? null, 2));
            
            // 12-45. SR (34 kolom = 17×2) - Nilai dan Kode - tampilkan semua digit
            foreach ($this->srList as $num) {
                $nilai = $item["sr_{$num}_nilai"] ?? null;
                $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($nilai, 2));
                $sheet->setCellValue($col++ . $row, $item["sr_{$num}_kode"] ?? '-');
            }
            
            // 46-51. Bocoran Baru (6 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['elv_624_t1'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $item['elv_624_t1_kode'] ?? '-');
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['elv_615_t2'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $item['elv_615_t2_kode'] ?? '-');
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['pipa_p1'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $item['pipa_p1_kode'] ?? '-');
            
            // 52-56. Perhitungan Q Thomson Weir (5 kolom) - PERBAIKAN: 2 desimal saja
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['thomson_r'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['thomson_l'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['thomson_b1'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['thomson_b3'] ?? null, 2));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['thomson_b5'] ?? null, 2));
            
            // 57-73. Perhitungan Q SR (17 kolom) - tampilkan semua digit
            foreach ($this->srList as $num) {
                $sr_q = $item["sr_{$num}_q"] ?? null;
                $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($sr_q, 6));
            }
            
            // 74-76. Perhitungan Bocoran Baru (3 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['talang1'] ?? null, 6));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['talang2'] ?? null, 6));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['pipa'] ?? null, 6));
            
            // 77-78. Perhitungan Inti Galery (2 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['a1_inti'] ?? null, 6));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['ambang_a1'] ?? null, 6));
            
            // 79-80. Perhitungan Bawah Bendungan/Spillway (2 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['B3'] ?? null, 6));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['ambang_spillway'] ?? null, 6));
            
            // 81-83. Perhitungan Tebing Kanan (3 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['sr_tebing'] ?? null, 6));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['ambang_tebing'] ?? null, 6));
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['B5_tebing'] ?? $item['B5'] ?? null, 6));
            
            // 84. Total Bocoran (1 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['R1'] ?? null, 6));
            
            // 85. Batasan Maksimal (Tahun) (1 kolom) - tampilkan semua digit
            $sheet->setCellValue($col++ . $row, $this->formatNumberRaw($item['batas_maksimal'] ?? null, 6));
            
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
            
            // Row warna alternating
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
        
        // === STYLING KOLOM ===
        $this->applyColumnStyling($sheet, $row);
        
        // === FREEZE COLUMNS (Kolom A-F akan tetap terlihat saat scroll horizontal) ===
        $sheet->freezePane('G7'); // Freeze kolom A-F (6 kolom pertama)
        
        // === FOOTER ===
        $footerRow = $row + 1;
        $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 'Sistem Monitoring Rembesan Bendungan - PT Indonesia Power');
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
        
        // ==============================================
        // SHEET 2: ANALISIS LOOK BURT
        // ==============================================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Analisis Look Burt');
        
        // === SET PAGE SETUP ===
        $sheet2->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // === SET JUDUL UTAMA ===
        $sheet2->mergeCells('A1:H1');
        $sheet2->setCellValue('A1', 'ANALISIS LOOK BURT 2007 - PT INDONESIA POWER');
        $this->applyTitleStyle($sheet2, 'A1:H1');
        $sheet2->getRowDimension(1)->setRowHeight(35);
        
        // === SET SUB JUDUL ===
        $sheet2->mergeCells('A2:H2');
        $sheet2->setCellValue('A2', 'LAPORAN ANALISIS LOOK BURT');
        $this->applyTitleStyle($sheet2, 'A2:H2');
        $sheet2->getRowDimension(2)->setRowHeight(30);
        
        // === INFORMASI TANGGAL EKSPOR ===
        $sheet2->mergeCells('A3:H3');
        $sheet2->setCellValue('A3', 'Diekspor pada: ' . date('d F Y H:i:s'));
        $this->applySubtitleStyle($sheet2, 'A3:H3');
        $sheet2->getRowDimension(3)->setRowHeight(25);
        
        // === HEADER TABEL ANALISIS LOOK BURT ===
        $headerRow = 5;
        $headers = [
            'Tanggal',
            'TMA Waduk',
            'Rembesan Bendungan (Ltr/mnt)',
            'Panjang Bendungan (M)',
            'Rembesan per M',
            'Ambang OK',
            'Ambang Not OK',
            'Keterangan'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . $headerRow, $header);
            $sheet2->getStyle($col . $headerRow)->applyFromArray([
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
                    'startColor' => ['argb' => 'FF5B9BD5'] // Biru terang
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFFFFFFF']
                    ]
                ]
            ]);
            $col++;
        }
        
        // === ISI DATA ANALISIS LOOK BURT ===
        $row = $headerRow + 1;
        
        foreach ($analisaData as $index => $item) {
            $sheet2->setCellValue('A' . $row, $item['tanggal'] ?? '-');
            $sheet2->setCellValue('B' . $row, $this->formatNumberRaw($item['tma_waduk'] ?? null, 2));
            $sheet2->setCellValue('C' . $row, $this->formatNumberRaw($item['rembesan_bendungan'] ?? null, 2));
            $sheet2->setCellValue('D' . $row, $this->formatNumberRaw($item['panjang_bendungan'] ?? null, 2));
            $sheet2->setCellValue('E' . $row, $this->formatNumberRaw($item['rembesan_per_m'] ?? null, 4));
            $sheet2->setCellValue('F' . $row, $this->formatNumberRaw($item['nilai_ambang_ok'] ?? null, 2));
            $sheet2->setCellValue('G' . $row, $this->formatNumberRaw($item['nilai_ambang_notok'] ?? null, 2));
            $sheet2->setCellValue('H' . $row, $item['keterangan'] ?? '-');
            
            // Styling row
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
            
            $sheet2->getStyle('A' . $row . ':H' . $row)->applyFromArray($rowStyle);
            
            // Row warna alternating
            if ($index % 2 == 0) {
                $sheet2->getStyle('A' . $row . ':H' . $row)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFF8F9FA');
            }
            
            $sheet2->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        
        // === SET COLUMN WIDTH ===
        $sheet2->getColumnDimension('A')->setWidth(12);  // Tanggal
        $sheet2->getColumnDimension('B')->setWidth(12);  // TMA Waduk
        $sheet2->getColumnDimension('C')->setWidth(25);  // Rembesan Bendungan
        $sheet2->getColumnDimension('D')->setWidth(18);  // Panjang Bendungan
        $sheet2->getColumnDimension('E')->setWidth(15);  // Rembesan per M
        $sheet2->getColumnDimension('F')->setWidth(12);  // Ambang OK
        $sheet2->getColumnDimension('G')->setWidth(15);  // Ambang Not OK
        $sheet2->getColumnDimension('H')->setWidth(25);  // Keterangan
        
        // === APPLY NUMBER FORMATTING ===
        if ($row > ($headerRow + 1)) {
            // TMA Waduk - 2 desimal
            $sheet2->getStyle('B' . ($headerRow + 1) . ':B' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('0.00');
                
            // Rembesan Bendungan - 2 desimal
            $sheet2->getStyle('C' . ($headerRow + 1) . ':C' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('0.00');
                
            // Panjang Bendungan - 2 desimal
            $sheet2->getStyle('D' . ($headerRow + 1) . ':D' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('0.00');
                
            // Rembesan per M - 4 desimal
            $sheet2->getStyle('E' . ($headerRow + 1) . ':E' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('0.0000');
                
            // Ambang OK - 2 desimal
            $sheet2->getStyle('F' . ($headerRow + 1) . ':F' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('0.00');
                
            // Ambang Not OK - 2 desimal
            $sheet2->getStyle('G' . ($headerRow + 1) . ':G' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('0.00');
        }
        
        // === FREEZE HEADERS ===
        $sheet2->freezePane('A' . ($headerRow + 1));
        
        // === FOOTER ===
        $footerRow2 = $row + 1;
        $sheet2->mergeCells('A' . $footerRow2 . ':H' . $footerRow2);
        $sheet2->setCellValue('A' . $footerRow2, 'Analisis Look Burt - PT Indonesia Power');
        $sheet2->getStyle('A' . $footerRow2)->applyFromArray([
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
        $sheet2->getRowDimension($footerRow2)->setRowHeight(30);
        
        // ==============================================
        // SET SHEET ORDER (urutan tab di Excel)
        // ==============================================
        $spreadsheet->setActiveSheetIndex(0); // Set sheet pertama sebagai aktif
        
        // ==============================================
        // OUTPUT FILE
        // ==============================================
        $filename = 'Data_Rembesan_Bendungan_' . date('Ymd_His') . '.xlsx';
        
        return $this->downloadExcel($spreadsheet, $filename);
    }
    
    private function filterUniqueData($data)
    {
        $uniqueData = [];
        $uniqueKeys = [];
        
        foreach ($data as $item) {
            // Buat kunci unik berdasarkan kombinasi tanggal dan periode
            $key = sprintf(
                '%s-%s-%s-%s',
                $item['tahun'] ?? '',
                $item['bulan'] ?? '',
                $item['periode'] ?? '',
                $item['tanggal'] ?? ''
            );
            
            // Jika kunci belum ada, tambahkan ke data unik
            if (!isset($uniqueKeys[$key])) {
                $uniqueKeys[$key] = true;
                $uniqueData[] = $item;
            } else {
                // Log duplikat untuk debugging
                log_message('debug', 'Data duplikat dihapus: ' . $key);
            }
        }
        
        return $uniqueData;
    }
    
    private function createTableHeaders($sheet)
    {
        // Warna biru terang untuk semua header
        $blueLight = 'FF5B9BD5'; // Biru terang yang bagus untuk Excel
        
        // ROW 4 (Baris pertama header tabel)
        $row = 4;
        
        // Kolom 1-6: Rowspan 3 - Tanpa garis vertikal terlihat
        $rowspan3Cols = [
            ['label' => 'Tahun', 'col' => 'A'],
            ['label' => 'Bulan', 'col' => 'B'],
            ['label' => 'Periode', 'col' => 'C'],
            ['label' => 'Tanggal', 'col' => 'D'],
            ['label' => 'TMA Waduk', 'col' => 'E'],
            ['label' => 'Curah Hujan', 'col' => 'F'],
        ];
        
        foreach ($rowspan3Cols as $colInfo) {
            $sheet->setCellValue($colInfo['col'] . $row, $colInfo['label']);
            $this->applyHeaderStyleRowspan3($sheet, $colInfo['col'] . $row . ':' . $colInfo['col'] . ($row + 2), $blueLight);
        }
        
        $currentCol = 'G';
        
        // Thomson Weir: rowspan 2, colspan 5
        $thomsonEndCol = $this->nextColumn($currentCol, 4);
        $thomsonRange = $currentCol . $row . ':' . $thomsonEndCol . ($row + 1);
        $sheet->mergeCells($thomsonRange);
        $sheet->setCellValue($currentCol . $row, 'Thomson Weir');
        $this->applyHeaderStyle($sheet, $thomsonRange, $blueLight);
        
        // SR: colspan 34, rowspan 1 (bukan rowspan 2 lagi)
        $currentCol = $this->nextColumn($thomsonEndCol, 1);
        $srColspan = count($this->srList) * 2;
        $srEndCol = $this->nextColumn($currentCol, $srColspan - 1);
        $srRange = $currentCol . $row . ':' . $srEndCol . $row; // Hanya row 4 saja
        $sheet->mergeCells($srRange);
        $sheet->setCellValue($currentCol . $row, 'SR');
        $this->applyHeaderStyle($sheet, $srRange, $blueLight);
        
        // Bocoran Baru: rowspan 2, colspan 6
        $currentCol = $this->nextColumn($srEndCol, 1);
        $bocoranEndCol = $this->nextColumn($currentCol, 5);
        $bocoranRange = $currentCol . $row . ':' . $bocoranEndCol . ($row + 1);
        $sheet->mergeCells($bocoranRange);
        $sheet->setCellValue($currentCol . $row, 'Bocoran Baru');
        $this->applyHeaderStyle($sheet, $bocoranRange, $blueLight);
        
        // PERBAIKAN: Perhitungan Q Thompson Weir (AX-BB): rowspan 2, colspan 5
        $currentCol = $this->nextColumn($bocoranEndCol, 1); // Kolom setelah Bocoran Baru
        $thomsonCalcEndCol = $this->nextColumn($currentCol, 4);
        $thomsonCalcRange = $currentCol . $row . ':' . $thomsonCalcEndCol . ($row + 1);
        $sheet->mergeCells($thomsonCalcRange);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Q Thompson Weir (Liter/Menit)');
        $this->applyHeaderStyle($sheet, $thomsonCalcRange, $blueLight);
        
        // Perhitungan Q SR: rowspan 2, colspan 17
        $currentCol = $this->nextColumn($thomsonCalcEndCol, 1);
        $srCalcEndCol = $this->nextColumn($currentCol, count($this->srList) - 1);
        $srCalcRange = $currentCol . $row . ':' . $srCalcEndCol . ($row + 1);
        $sheet->mergeCells($srCalcRange);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Q SR (Liter/Menit)');
        $this->applyHeaderStyle($sheet, $srCalcRange, $blueLight);
        
        // Perhitungan Bocoran Baru: rowspan 2, colspan 3
        $currentCol = $this->nextColumn($srCalcEndCol, 1);
        $bocoranCalcEndCol = $this->nextColumn($currentCol, 2);
        $bocoranCalcRange = $currentCol . $row . ':' . $bocoranCalcEndCol . ($row + 1);
        $sheet->mergeCells($bocoranCalcRange);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Bocoran Baru');
        $this->applyHeaderStyle($sheet, $bocoranCalcRange, $blueLight);
        
        // Perhitungan Inti Galery: rowspan 2, colspan 2
        $currentCol = $this->nextColumn($bocoranCalcEndCol, 1);
        $intiEndCol = $this->nextColumn($currentCol, 1);
        $intiRange = $currentCol . $row . ':' . $intiEndCol . ($row + 1);
        $sheet->mergeCells($intiRange);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Inti Galery');
        $this->applyHeaderStyle($sheet, $intiRange, $blueLight);
        
        // Perhitungan Bawah Bendungan/Spillway: rowspan 2, colspan 2
        $currentCol = $this->nextColumn($intiEndCol, 1);
        $spillwayEndCol = $this->nextColumn($currentCol, 1);
        $spillwayRange = $currentCol . $row . ':' . $spillwayEndCol . ($row + 1);
        $sheet->mergeCells($spillwayRange);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Bawah Bendungan/Spillway');
        $this->applyHeaderStyle($sheet, $spillwayRange, $blueLight);
        
        // Perhitungan Tebing Kanan (SR dan Ambang): rowspan 2, colspan 2
        $currentCol = $this->nextColumn($spillwayEndCol, 1);
        $tebingEndCol = $this->nextColumn($currentCol, 1);
        $tebingRange1 = $currentCol . $row . ':' . $tebingEndCol . ($row + 1);
        $sheet->mergeCells($tebingRange1);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Tebing Kanan');
        $this->applyHeaderStyle($sheet, $tebingRange1, $blueLight);
        
        // PERBAIKAN: Perhitungan Tebing Kanan B5 (CC): rowspan 2, colspan 1 - tanpa border internal
        $currentCol = $this->nextColumn($tebingEndCol, 1);
        $tebingRange2 = $currentCol . $row . ':' . $currentCol . ($row + 1);
        $sheet->setCellValue($currentCol . $row, 'Perhitungan Tebing Kanan');
        $this->applyHeaderStyleRowspan2NoInternalBorder($sheet, $tebingRange2, $blueLight);
        
        // PERBAIKAN: Total Bocoran (CD): rowspan 2, colspan 1 - tanpa border internal
        $currentCol = $this->nextColumn($currentCol, 1);
        $totalRange = $currentCol . $row . ':' . $currentCol . ($row + 1);
        $sheet->setCellValue($currentCol . $row, 'Total Bocoran');
        $this->applyHeaderStyleRowspan2NoInternalBorder($sheet, $totalRange, $blueLight);
        
        // PERBAIKAN: Batasan Maksimal (CE): rowspan 2, colspan 1 - tanpa border internal
        $currentCol = $this->nextColumn($currentCol, 1);
        $batasRange = $currentCol . $row . ':' . $currentCol . ($row + 1);
        $sheet->setCellValue($currentCol . $row, 'Batasan Maksimal (Tahun)');
        $this->applyHeaderStyleRowspan2NoInternalBorder($sheet, $batasRange, $blueLight);
        
        $sheet->getRowDimension($row)->setRowHeight(30);
        
        // ROW 5 (Baris kedua header tabel)
        $row = 5;
        
        // Kolom A-F sudah rowspan 3, jadi kosong
        
        // Thomson Weir sudah rowspan 2, jadi kosong
        
        // PERBAIKAN: SR detail per SR (row 5): colspan 2 untuk setiap SR mulai dari SR 1
        $currentCol = 'L'; // SR 1 mulai dari kolom L (setelah 6 kolom A-F + 5 kolom Thomson Weir G-K)
        
        foreach ($this->srList as $num) {
            $srDetailRange = $currentCol . $row . ':' . $this->nextColumn($currentCol, 1) . $row;
            $sheet->mergeCells($srDetailRange);
            $sheet->setCellValue($currentCol . $row, 'SR ' . $num);
            $this->applySubHeaderStyle($sheet, $srDetailRange, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 2);
        }
        
        // Bocoran Baru sudah rowspan 2, jadi kosong
        
        // PERBAIKAN: Perhitungan Q Thompson Weir sudah rowspan 2, jadi kosong
        
        // Perhitungan Q SR sudah rowspan 2, jadi kosong
        
        // Perhitungan Bocoran Baru sudah rowspan 2, jadi kosong
        
        // Perhitungan Inti Galery sudah rowspan 2, jadi kosong
        
        // Perhitungan Spillway sudah rowspan 2, jadi kosong
        
        // Perhitungan Tebing Kanan sudah rowspan 2, jadi kosong
        
        // Perhitungan Tebing Kanan B5 sudah rowspan 2, jadi kosong
        
        // Total Bocoran sudah rowspan 2, jadi kosong
        
        // Batasan Maksimal sudah rowspan 2, jadi kosong
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // ROW 6 (Baris ketiga header tabel)
        $row = 6;
        
        // Kolom A-F: Rowspan 3, jadi sudah ada isinya
        
        // Thomson Weir detail (5 kolom)
        $currentCol = 'G';
        foreach ($this->twHeaders as $header) {
            $sheet->setCellValue($currentCol . $row, $header);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // PERBAIKAN: SR detail: Nilai dan Kode untuk setiap SR mulai dari SR 1
        $currentCol = 'L'; // SR 1 mulai dari kolom L
        
        foreach ($this->srList as $num) {
            $sheet->setCellValue($currentCol . $row, 'Nilai');
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
            
            $sheet->setCellValue($currentCol . $row, 'Kode');
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Bocoran Baru detail: ELV 624 T1
        $sheet->setCellValue($currentCol . $row, 'ELV 624 T1');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        $sheet->setCellValue($currentCol . $row, 'Kode');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        // ELV 615 T2
        $sheet->setCellValue($currentCol . $row, 'ELV 615 T2');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        $sheet->setCellValue($currentCol . $row, 'Kode');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        // Pipa P1
        $sheet->setCellValue($currentCol . $row, 'Pipa P1');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        $sheet->setCellValue($currentCol . $row, 'Kode');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        // Perhitungan Q Thomson Weir detail (5 kolom) - AX-BB
        $thomsonCalcDetails = ['R', 'L', 'B-1', 'B-3', 'B-5'];
        foreach ($thomsonCalcDetails as $detail) {
            $sheet->setCellValue($currentCol . $row, $detail);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Perhitungan Q SR detail
        foreach ($this->srList as $num) {
            $sheet->setCellValue($currentCol . $row, 'SR ' . $num);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Perhitungan Bocoran Baru detail
        $bocoranCalcDetails = ['Talang 1', 'Talang 2', 'Pipa'];
        foreach ($bocoranCalcDetails as $detail) {
            $sheet->setCellValue($currentCol . $row, $detail);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Perhitungan Inti Galery detail
        $intiDetails = ['A1', 'Ambang'];
        foreach ($intiDetails as $detail) {
            $sheet->setCellValue($currentCol . $row, $detail);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Perhitungan Spillway detail
        $spillwayDetails = ['B3', 'Ambang'];
        foreach ($spillwayDetails as $detail) {
            $sheet->setCellValue($currentCol . $row, $detail);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Perhitungan Tebing Kanan detail
        $tebingDetails = ['SR', 'Ambang', 'B5'];
        foreach ($tebingDetails as $detail) {
            $sheet->setCellValue($currentCol . $row, $detail);
            $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
            $currentCol = $this->nextColumn($currentCol, 1);
        }
        
        // Total Bocoran detail
        $sheet->setCellValue($currentCol . $row, 'R1');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        $currentCol = $this->nextColumn($currentCol, 1);
        
        // Batasan Maksimal detail
        $sheet->setCellValue($currentCol . $row, '');
        $this->applyDetailHeaderStyle($sheet, $currentCol . $row, $blueLight);
        
        $sheet->getRowDimension($row)->setRowHeight(20);
    }
    
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
    
    private function applyHeaderStyle($sheet, $range, $color, $isRowspan3 = false)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => $isRowspan3 ? 11 : 10,
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
            // Hanya beri border di sisi luar saja, tidak ada border vertikal internal
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
    
    // PERBAIKAN: Method baru untuk rowspan 2 tanpa border internal
    private function applyHeaderStyleRowspan2NoInternalBorder($sheet, $range, $color)
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
            // Hanya beri border di sisi luar saja, tidak ada border internal
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
    
    private function applySubHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['argb' => Color::COLOR_WHITE] // Teks putih untuk biru terang
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
    
    private function applyDetailHeaderStyle($sheet, $cell, $color)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 8,
                'color' => ['argb' => Color::COLOR_WHITE] // Teks putih untuk biru terang
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
    }
    
    private function applyColumnStyling($sheet, $lastRow)
    {
        // Auto size untuk semua kolom
        $lastCol = $this->getLastColumn();
        $lastColNum = $this->columnToNumber($lastCol);
        
        for ($colNum = 1; $colNum <= $lastColNum; $colNum++) {
            $colLetter = $this->getColumnByNumber($colNum);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        
        // Set lebar minimum untuk kolom tertentu
        $minWidths = [
            'A' => 8,   // Tahun
            'B' => 10,  // Bulan
            'C' => 10,  // Periode
            'D' => 12,  // Tanggal
            'E' => 12,  // TMA
            'F' => 15,  // Curah Hujan
        ];
        
        foreach ($minWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        
        // Format angka untuk kolom numerik
        // Set format dengan jumlah desimal yang sesuai
        $numberFormatFull = '0.000000';
        $numberFormatMedium = '0.0000';
        $numberFormatShort = '0.00';
        
        // Tentukan kolom yang berisi angka dan format yang sesuai
        if ($lastRow > 7) { // Pastikan ada data
            try {
                // TMA Waduk - 4 desimal
                $sheet->getStyle('E7:E' . ($lastRow - 1))
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatMedium);
                    
                // Curah Hujan - 2 desimal
                $sheet->getStyle('F7:F' . ($lastRow - 1))
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatShort);
                    
                // Thomson Weir (G-K) - 2 desimal
                for ($col = 'G'; $col <= 'K'; $col++) {
                    $sheet->getStyle($col . '7:' . $col . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatShort);
                }
                
                // SR Nilai (kolom ganjil dari L sampai AQ) - 2 desimal
                $currentCol = 'L';
                for ($i = 0; $i < count($this->srList); $i++) {
                    $sheet->getStyle($currentCol . '7:' . $currentCol . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatShort);
                    $currentCol = $this->nextColumn($currentCol, 2); // Skip kode
                }
                
                // ELV 624 T1, ELV 615 T2, Pipa P1 - 2 desimal
                $afterSR = $this->nextColumn('L', count($this->srList) * 2);
                $bocoranCol = $afterSR;
                
                $sheet->getStyle($bocoranCol . '7:' . $bocoranCol . ($lastRow - 1)) // ELV 624 T1
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatShort);
                $bocoranCol = $this->nextColumn($bocoranCol, 2); // Skip kode
                
                $sheet->getStyle($bocoranCol . '7:' . $bocoranCol . ($lastRow - 1)) // ELV 615 T2
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatShort);
                $bocoranCol = $this->nextColumn($bocoranCol, 2); // Skip kode
                
                $sheet->getStyle($bocoranCol . '7:' . $bocoranCol . ($lastRow - 1)) // Pipa P1
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatShort);
                
                // Semua kolom perhitungan adalah numerik
                $calcStart = $this->nextColumn($afterSR, 6);
                
                // PERBAIKAN: Perhitungan Thomson (5 kolom) - 2 desimal saja
                for ($i = 0; $i < 5; $i++) {
                    $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatShort);
                    $calcStart = $this->nextColumn($calcStart, 1);
                }
                
                // Perhitungan SR (17 kolom) - 6 desimal
                for ($i = 0; $i < 17; $i++) {
                    $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatFull);
                    $calcStart = $this->nextColumn($calcStart, 1);
                }
                
                // Perhitungan Bocoran Baru (3 kolom) - 6 desimal
                for ($i = 0; $i < 3; $i++) {
                    $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatFull);
                    $calcStart = $this->nextColumn($calcStart, 1);
                }
                
                // Perhitungan Inti Galery (2 kolom) - 6 desimal
                for ($i = 0; $i < 2; $i++) {
                    $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatFull);
                    $calcStart = $this->nextColumn($calcStart, 1);
                }
                
                // Perhitungan Spillway (2 kolom) - 6 desimal
                for ($i = 0; $i < 2; $i++) {
                    $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatFull);
                    $calcStart = $this->nextColumn($calcStart, 1);
                }
                
                // Perhitungan Tebing Kanan (3 kolom) - 6 desimal
                for ($i = 0; $i < 3; $i++) {
                    $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatFull);
                    $calcStart = $this->nextColumn($calcStart, 1);
                }
                
                // Total Bocoran (1 kolom) - 6 desimal
                $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatFull);
                $calcStart = $this->nextColumn($calcStart, 1);
                
                // Batasan Maksimal (1 kolom) - 6 desimal
                $sheet->getStyle($calcStart . '7:' . $calcStart . ($lastRow - 1))
                    ->getNumberFormat()
                    ->setFormatCode($numberFormatFull);
                    
            } catch (\Exception $e) {
                // Skip error
                log_message('error', 'Error applying column styling: ' . $e->getMessage());
            }
        }
    }
    
    private function getNumericColumns()
    {
        $numericCols = [];
        
        // Kolom 5-6: TMA Waduk (E), Curah Hujan (F)
        $numericCols[] = 'E';
        $numericCols[] = 'F';
        
        // Kolom 7-11: Thomson Weir (G-K)
        for ($col = 'G'; $col <= 'K'; $col++) {
            $numericCols[] = $col;
        }
        
        // SR Nilai (kolom ganjil dari L sampai AQ)
        $currentCol = 'L';
        for ($i = 0; $i < count($this->srList); $i++) {
            $numericCols[] = $currentCol;
            $currentCol = $this->nextColumn($currentCol, 2); // Skip kode
        }
        
        // Reset untuk Bocoran Baru nilai
        // Setelah SR (34 kolom), Bocoran Baru dimulai
        $afterSR = $this->nextColumn('L', count($this->srList) * 2);
        $bocoranCol = $afterSR;
        
        // ELV 624 T1, ELV 615 T2, Pipa P1 (kolom 1, 3, 5 dari 6 kolom bocoran)
        $numericCols[] = $bocoranCol; // ELV 624 T1
        $bocoranCol = $this->nextColumn($bocoranCol, 2); // Skip kode
        $numericCols[] = $bocoranCol; // ELV 615 T2
        $bocoranCol = $this->nextColumn($bocoranCol, 2); // Skip kode
        $numericCols[] = $bocoranCol; // Pipa P1
        
        // Semua kolom perhitungan adalah numerik
        // Mulai dari setelah Bocoran Baru (6 kolom)
        $calcStart = $this->nextColumn($afterSR, 6);
        
        // Perhitungan Thomson (5 kolom)
        for ($i = 0; $i < 5; $i++) {
            $numericCols[] = $calcStart;
            $calcStart = $this->nextColumn($calcStart, 1);
        }
        
        // Perhitungan SR (17 kolom)
        for ($i = 0; $i < 17; $i++) {
            $numericCols[] = $calcStart;
            $calcStart = $this->nextColumn($calcStart, 1);
        }
        
        // Perhitungan Bocoran Baru (3 kolom)
        for ($i = 0; $i < 3; $i++) {
            $numericCols[] = $calcStart;
            $calcStart = $this->nextColumn($calcStart, 1);
        }
        
        // Perhitungan Inti Galery (2 kolom)
        for ($i = 0; $i < 2; $i++) {
            $numericCols[] = $calcStart;
            $calcStart = $this->nextColumn($calcStart, 1);
        }
        
        // Perhitungan Spillway (2 kolom)
        for ($i = 0; $i < 2; $i++) {
            $numericCols[] = $calcStart;
            $calcStart = $this->nextColumn($calcStart, 1);
        }
        
        // Perhitungan Tebing Kanan (3 kolom)
        for ($i = 0; $i < 3; $i++) {
            $numericCols[] = $calcStart;
            $calcStart = $this->nextColumn($calcStart, 1);
        }
        
        // Total Bocoran (1 kolom)
        $numericCols[] = $calcStart;
        $calcStart = $this->nextColumn($calcStart, 1);
        
        // Batasan Maksimal (1 kolom)
        $numericCols[] = $calcStart;
        
        return $numericCols;
    }
    
    private function getLastColumn()
    {
        // Total kolom: 6 + 5 + (17×2) + 6 + 5 + 17 + 3 + 2 + 2 + 3 + 1 + 1 = 85
        $totalCols = 85;
        return $this->getColumnByNumber($totalCols);
    }
    
    private function getAllData()
    {
        $db = \Config\Database::connect();
        
        try {
            // Gunakan DISTINCT dan GROUP BY untuk menghindari duplikat
            $builder = $db->table('t_data_pengukuran p')
                ->select("
                    p.id,
                    p.tahun, p.bulan, p.periode, p.tanggal,
                    p.tma_waduk, p.curah_hujan,
                    tw.a1_r, tw.a1_l, tw.b1, tw.b3, tw.b5,
                    sr.sr_1_nilai, sr.sr_1_kode, sr.sr_40_nilai, sr.sr_40_kode, 
                    sr.sr_66_nilai, sr.sr_66_kode, sr.sr_68_nilai, sr.sr_68_kode,
                    sr.sr_70_nilai, sr.sr_70_kode, sr.sr_79_nilai, sr.sr_79_kode,
                    sr.sr_81_nilai, sr.sr_81_kode, sr.sr_83_nilai, sr.sr_83_kode,
                    sr.sr_85_nilai, sr.sr_85_kode, sr.sr_92_nilai, sr.sr_92_kode,
                    sr.sr_94_nilai, sr.sr_94_kode, sr.sr_96_nilai, sr.sr_96_kode,
                    sr.sr_98_nilai, sr.sr_98_kode, sr.sr_100_nilai, sr.sr_100_kode,
                    sr.sr_102_nilai, sr.sr_102_kode, sr.sr_104_nilai, sr.sr_104_kode,
                    sr.sr_106_nilai, sr.sr_106_kode,
                    bb.elv_624_t1, bb.elv_624_t1_kode, bb.elv_615_t2, bb.elv_615_t2_kode,
                    bb.pipa_p1, bb.pipa_p1_kode,
                    pt.a1_r as thomson_r, pt.a1_l as thomson_l, pt.b1 as thomson_b1,
                    pt.b3 as thomson_b3, pt.b5 as thomson_b5,
                    psr.sr_1_q, psr.sr_40_q, psr.sr_66_q, psr.sr_68_q, psr.sr_70_q,
                    psr.sr_79_q, psr.sr_81_q, psr.sr_83_q, psr.sr_85_q, psr.sr_92_q,
                    psr.sr_94_q, psr.sr_96_q, psr.sr_98_q, psr.sr_100_q, psr.sr_102_q,
                    psr.sr_104_q, psr.sr_106_q,
                    pb.talang1, pb.talang2, pb.pipa,
                    pig.a1 as a1_inti, pig.ambang_a1,
                    psp.B3, psp.ambang as ambang_spillway,
                    ptk.sr as sr_tebing, ptk.ambang as ambang_tebing, ptk.B5 as B5_tebing,
                    tb.R1,
                    pbm.batas_maksimal
                ")
                ->join('t_thomson_weir tw', 'tw.pengukuran_id = p.id', 'left')
                ->join('t_sr sr', 'sr.pengukuran_id = p.id', 'left')
                ->join('t_bocoran_baru bb', 'bb.pengukuran_id = p.id', 'left')
                ->join('p_thomson_weir pt', 'pt.pengukuran_id = p.id', 'left')
                ->join('p_sr psr', 'psr.pengukuran_id = p.id', 'left')
                ->join('p_bocoran_baru pb', 'pb.pengukuran_id = p.id', 'left')
                ->join('p_intigalery pig', 'pig.pengukuran_id = p.id', 'left')
                ->join('p_spillway psp', 'psp.pengukuran_id = p.id', 'left')
                ->join('p_tebingkanan ptk', 'ptk.pengukuran_id = p.id', 'left')
                ->join('p_totalbocoran tb', 'tb.pengukuran_id = p.id', 'left')
                ->join('p_batasmaksimal pbm', 'pbm.pengukuran_id = p.id', 'left')
                ->groupBy('p.id')  // Tambahkan GROUP BY untuk menghindari duplikat
                ->orderBy('p.tahun', 'ASC')
                ->orderBy("FIELD(p.bulan, 
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                )")
                ->orderBy('p.periode', 'ASC')
                ->orderBy('p.tanggal', 'ASC');
            
            $result = $builder->get()->getResultArray();
            
            // Log jumlah data untuk debugging
            log_message('debug', 'Data dari database sebelum filter: ' . count($result));
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching data: ' . $e->getMessage());
            return [];
        }
    }
    
    private function formatNumberRaw($value, $decimals = 6)
    {
        if ($value === null || $value === '' || $value === '-') {
            return '-';
        }
        
        try {
            $num = (float) $value;
            
            // Jika nilai 0, tampilkan "0" saja
            if ($num == 0) {
                return '0';
            }
            
            // Format dengan jumlah desimal yang diminta
            $formatted = number_format($num, $decimals, '.', '');
            
            // Hapus trailing zero dan titik desimal yang tidak perlu
            $formatted = rtrim(rtrim($formatted, '0'), '.');
            
            return $formatted;
        } catch (\Exception $e) {
            return '-';
        }
    }
    
    private function formatNumber($value, $decimals = 2)
    {
        if ($value === null || $value === '' || $value === '-') {
            return '-';
        }
        
        try {
            $num = (float) $value;
            
            // Jika nilai 0, tampilkan "0" saja
            if ($num == 0) {
                return '0';
            }
            
            return number_format($num, $decimals, '.', '');
        } catch (\Exception $e) {
            return '-';
        }
    }
    
    private function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        
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
    }
    
    // Helper functions
    private function nextColumn($column, $steps = 1)
    {
        $column = strtoupper($column);
        for ($i = 0; $i < $steps; $i++) {
            $column++;
        }
        return $column;
    }
    
    private function columnToNumber($column)
    {
        $column = strtoupper($column);
        $length = strlen($column);
        $number = 0;
        
        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + (ord($column[$i]) - ord('A') + 1);
        }
        
        return $number;
    }
    
    private function getColumnByNumber($number)
    {
        $column = '';
        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $column = chr($mod + 65) . $column;
            $number = floor(($number - $mod) / 26);
        }
        return $column;
    }
}