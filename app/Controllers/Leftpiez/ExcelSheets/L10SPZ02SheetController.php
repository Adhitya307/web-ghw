<?php

namespace App\Controllers\LeftPiez\ExcelSheets;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class L10SPZ02SheetController extends BaseSheetController
{
    /**
     * Create Sheet untuk Grafik History L10-SPZ02 dengan struktur sama persis seperti L1-L3
     */
    public function createGrafikHistoryL10SPZ02Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // ===== WARNA =====
        $headerBlue = 'FF0D6EFD';
        $headerLightBlue = 'FFE3F2FD';
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        $headerAman = 'FF28A745';
        $headerPeringatan = 'FFFFC107';
        $headerBahaya = 'FFDC3545';
        $headerLightGray = 'FFCED4DA';
        $dataWhite = 'FFFFFFFF';
        $dataLightGray = 'FFF8F9FA';
        
        // Warna untuk status T.Psmetrik (background) - SAMA DENGAN VIEW
        $statusAmanBg = 'FFD4EDDA';
        $statusPeringatanBg = 'FFFFF3CD';
        $statusBahayaBg = 'FFF8D7DA';
        
        // Warna untuk teks status T.Psmetrik - SAMA DENGAN VIEW
        $statusAmanText = 'FF155724';
        $statusPeringatanText = 'FF856404';
        $statusBahayaText = 'FF721C24';
        
        // ===== JUDUL UTAMA =====
        $lastCol = 'M'; // Diubah dari 'S' ke 'M'
        
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
        $headerEndRow = $currentRow + 6;
        
        // ===== ROW 4: Main Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Pisometer No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . ($currentRow + 1), $headerLightBlue);
        
        $sheet->setCellValue('B' . $currentRow, 'L-10');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'SPZ-02');
        $sheet->mergeCells('H' . $currentRow . ':' . $lastCol . $currentRow); // Diubah dari M ke lastCol
        $this->applyColspanStyle($sheet, 'H' . $currentRow, $lastCol . $currentRow, $headerBlue);
        
        $currentRow++;
        
        // ===== ROW 5: Sub Headers =====
        $sheet->setCellValue('B' . $currentRow, 'Downstream');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'Downstream');
        $sheet->mergeCells('H' . $currentRow . ':' . $lastCol . $currentRow); // Diubah dari M ke lastCol
        $this->applyColspanStyle($sheet, 'H' . $currentRow, $lastCol . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 6: Data Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Elev.Piso.Atas(El.m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        $sheet->setCellValue('B' . $currentRow, '580.36');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        // L-10 Ambang Batas - SAMA DENGAN L1-L3
        $sheet->setCellValue('D' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('D' . $currentRow . ':G' . ($currentRow + 3));
        $sheet->getStyle('D' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE3F2FD']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        $sheet->setCellValue('H' . $currentRow, '700.08');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // SPZ-02 Ambang Batas - SAMA DENGAN L1-L3
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':' . $lastCol . ($currentRow + 3)); // Diubah dari M ke lastCol
        $sheet->getStyle('J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE3F2FD']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        $currentRow++;
        
        // ===== ROW 7: Kedalaman =====
        $sheet->setCellValue('A' . $currentRow, 'Kedalaman(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        $sheet->setCellValue('B' . $currentRow, '51.50');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
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
        
        $sheet->setCellValue('B' . $currentRow, '5.958,64');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
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
        
        $sheet->setCellValue('B' . $currentRow, '(8.413,89)');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, '(9.004,50)');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 10: Final Headers =====
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
        
        // Di row 10, label "Aman", "Peringatan", "Bahaya"
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
        
        // ===== ISI DATA AMBANG BATAS =====
        $this->fillAmbangBatasDataL10($sheet, 'D6', 'G9');
        $this->fillAmbangBatasDataSPZ02($sheet, 'J6', 'M9');
        
        // ===== APPLY BORDER =====
        $headerOuterRange = 'A' . $headerStartRow . ':' . $lastCol . $headerEndRow; // Diubah dari S ke lastCol
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
        $headerBottomRange = 'A' . $headerEndRow . ':' . $lastCol . $headerEndRow; // Diubah dari S ke lastCol
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
            $sheet->mergeCells('A' . $currentRow . ':' . $lastCol . $currentRow); // Diubah dari S ke lastCol
            $sheet->setCellValue('A' . $currentRow, 'TIDAK ADA DATA PIEZOMETER YANG TERSEDIA');
            $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9F9F9']]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
            $currentRow++;
        } else {
            $statusData = [];
            
            usort($pengukuranData, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($pengukuranData as $index => $p) {
                $pid = $p['id_pengukuran'];
                
                $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $currentRow, $tanggal);
                
                // ===== L-10 Data =====
                // PERBAIKAN: Gunakan null coalescing dan type casting
                $bacaan_L10 = $pembacaanData['L10']['feet'] ?? 0;
                // Konversi ke float dengan aman
                $bacaan_L10_float = (float) $bacaan_L10;
                $bacaan_L10_m = $bacaan_L10_float * 0.3048;
                $sheet->setCellValue('B' . $currentRow, number_format($bacaan_L10_m, 2));
                
                $t_psmetrik_L10 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L10') {
                        $t_psmetrik_L10 = (float) ($perhitungan['t_psmetrik'] ?? 0);
                        break;
                    }
                }
                $sheet->setCellValue('C' . $currentRow, number_format($t_psmetrik_L10, 2));
                
                $sheet->setCellValue('D' . $currentRow, '560.86');
                $sheet->setCellValue('E' . $currentRow, '565.36');
                $sheet->setCellValue('F' . $currentRow, '569.66');
                
                $status_L10 = $this->getStatusByType($t_psmetrik_L10, 'L10');
                $sheet->setCellValue('G' . $currentRow, number_format($t_psmetrik_L10, 2));
                
                // ===== SPZ-02 Data =====
                $bacaan_SPZ02 = $pembacaanData['SPZ02']['feet'] ?? 0;
                // Konversi ke float dengan aman
                $bacaan_SPZ02_float = (float) $bacaan_SPZ02;
                $bacaan_SPZ02_m = $bacaan_SPZ02_float * 0.3048;
                $sheet->setCellValue('H' . $currentRow, number_format($bacaan_SPZ02_m, 2));
                
                $t_psmetrik_SPZ02 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'SPZ02') {
                        $t_psmetrik_SPZ02 = (float) ($perhitungan['t_psmetrik'] ?? 0);
                        break;
                    }
                }
                $sheet->setCellValue('I' . $currentRow, number_format($t_psmetrik_SPZ02, 2));
                
                $sheet->setCellValue('J' . $currentRow, '691.46');
                $sheet->setCellValue('K' . $currentRow, '692.36');
                $sheet->setCellValue('L' . $currentRow, '695.36');
                
                $status_SPZ02 = $this->getStatusByType($t_psmetrik_SPZ02, 'SPZ02');
                $sheet->setCellValue('M' . $currentRow, number_format($t_psmetrik_SPZ02, 2));
                
                $statusData[$currentRow] = [
                    'L10' => $status_L10,
                    'SPZ02' => $status_SPZ02
                ];
                
                $currentRow++;
            }
            
            // ===== APPLY STYLES UNTUK DATA =====
            if ($currentRow > $startDataRow) {
                $dataAreaStart = $startDataRow;
                $dataAreaEnd = $currentRow - 1;
                
                // 1. Apply warna latar belakang untuk semua data (selang-seling)
                for ($row = $dataAreaStart; $row <= $dataAreaEnd; $row++) {
                    $isEvenRow = (($row - $dataAreaStart) % 2 == 0);
                    $rowBgColor = $isEvenRow ? $dataWhite : $dataLightGray;
                    $rowRange = 'A' . $row . ':' . $lastCol . $row; // Diubah dari S ke lastCol
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border untuk semua sel data
                $dataAreaRange = 'A' . $dataAreaStart . ':' . $lastCol . $dataAreaEnd; // Diubah dari S ke lastCol
                $sheet->getStyle($dataAreaRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ]
                ]);
                
                // 3. Apply warna khusus untuk kolom ambang batas di area data (D, J untuk Aman; E, K untuk Peringatan; F, L untuk Bahaya)
                $amanColumns = ['D', 'J'];
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusAmanText]]
                    ]);
                }
                
                $peringatanColumns = ['E', 'K'];
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusPeringatanText]]
                    ]);
                }
                
                $bahayaColumns = ['F', 'L'];
                foreach ($bahayaColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusBahayaText]]
                    ]);
                }
                
                // 4. Apply warna background untuk kolom T.Psmetrik terakhir (G, M) berdasarkan status
                foreach ($statusData as $row => $statuses) {
                    // Kolom G untuk L10
                    $cellG = 'G' . $row;
                    $statusL10 = $statuses['L10'];
                    $this->applyStatusColorToCell($sheet, $cellG, $statusL10, $sheet->getCell($cellG)->getValue());
                    
                    // Kolom M untuk SPZ02
                    $cellM = 'M' . $row;
                    $statusSPZ02 = $statuses['SPZ02'];
                    $this->applyStatusColorToCell($sheet, $cellM, $statusSPZ02, $sheet->getCell($cellM)->getValue());
                }
                
                // 5. Alignment untuk kolom numerik (kanan)
                $numericColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
                
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
                
                // 7. Format angka dengan 2 desimal
                $numericRange = 'B' . $dataAreaStart . ':' . $lastCol . $dataAreaEnd; // Diubah dari M ke lastCol
                $sheet->getStyle($numericRange)
                    ->getNumberFormat()
                    ->setFormatCode('0.00');
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('A11');
        
        // ===== SET COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 12, 'B' => 10, 'C' => 14, 'D' => 8, 'E' => 10, 'F' => 8, 'G' => 14,
            'H' => 10, 'I' => 14, 'J' => 8, 'K' => 10, 'L' => 8, 'M' => 14
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
            $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow); // Diubah dari S ke lastCol
            
            $totalRecords = count($pengukuranData);
            
            $sheet->setCellValue('A' . $footerRow, 
                'TOTAL REKORD: ' . $totalRecords . 
                ' | Grafik History L10-SPZ02 | Piezometer Monitoring System - PT Indonesia Power'
            );
            
            $sheet->getStyle('A' . $footerRow . ':' . $lastCol . $footerRow)->applyFromArray([
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
            $sheet->getPageSetup()->setPrintArea('A1:' . $lastCol . ($currentRow - 1)); // Diubah dari S ke lastCol
        } else {
            $sheet->getPageSetup()->setPrintArea('A1:' . $lastCol . '10'); // Diubah dari S ke lastCol
        }
        
        // ===== SETUP HEADER & FOOTER =====
        $this->setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter);
    }
    
    /**
     * Apply background color untuk kolom T.Psmetrik berdasarkan status
     */
    private function applyStatusColorToCell($sheet, $cell, $status, $value)
    {
        $bgColors = [
            'aman' => 'FFD4EDDA',
            'peringatan' => 'FFFFF3CD',
            'bahaya' => 'FFF8D7DA'
        ];
        
        $textColors = [
            'aman' => 'FF155724',
            'peringatan' => 'FF856404',
            'bahaya' => 'FF721C24'
        ];
        
        $bgColor = $bgColors[$status] ?? 'FFFFFFFF';
        $textColor = $textColors[$status] ?? 'FF000000';
        
        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'bold' => true, 
                'size' => 8, 
                'color' => ['argb' => $textColor]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 
                'startColor' => ['argb' => $bgColor]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);
        
        if (empty($sheet->getCell($cell)->getValue()) && !empty($value)) {
            $sheet->setCellValue($cell, $value);
        }
    }
    
    /**
     * Fill Ambang Batas Data L10 - DIUBAH MIRIP DENGAN L1-L3
     */
    private function fillAmbangBatasDataL10($sheet, $startCell, $endCell)
    {
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Isi data untuk area D6:G9 - SAMA DENGAN STRUKTUR L1-L3
        $data = [
            ['Peringatan', '≥565.36', '', ''],  // Baris 7
            ['Bahaya', '≥569.66', '', ''],      // Baris 8
            ['T.Psmetrik', '≤565.35', '≤569.65', '≥569.66']  // Baris 9
        ];
        
        $cellReference = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($startCell);
        $startRow = $cellReference[1];
        $startCol = $cellReference[0];
        
        // Mulai dari $i = 1 karena baris pertama (row 6) sudah ada "Ambang Batas"
        for ($i = 1; $i < 4; $i++) {
            $row = $startRow + $i;
            $dataIndex = $i - 1;
            for ($j = 0; $j < 4; $j++) {
                $col = chr(ord($startCol) + $j);
                $cell = $col . $row;
                if (isset($data[$dataIndex][$j])) {
                    $sheet->setCellValue($cell, $data[$dataIndex][$j]);
                    
                    // Apply warna berdasarkan baris
                    if ($i == 1) $bgColor = $bgPeringatan;
                    elseif ($i == 2) $bgColor = $bgBahaya;
                    elseif ($i == 3) $bgColor = 'FFE3F2FD';
                    else $bgColor = 'FFE3F2FD';
                    
                    // Font bold untuk baris Peringatan dan Bahaya
                    $isBold = ($i == 1 || $i == 2);
                    
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => $isBold, 'size' => 8],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000']
                            ]
                        ]
                    ]);
                }
            }
        }
    }
    
    /**
     * Fill Ambang Batas Data SPZ02 - DIUBAH MIRIP DENGAN L1-L3
     */
    private function fillAmbangBatasDataSPZ02($sheet, $startCell, $endCell)
    {
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Isi data untuk area J6:M9 - SAMA DENGAN STRUKTUR L1-L3
        $data = [
            ['Peringatan', '≥692.36', '', ''],
            ['Bahaya', '≥695.36', '', ''],
            ['T.Psmetrik', '≤692.35', '≤695.35', '≥695.36']
        ];
        
        $cellReference = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($startCell);
        $startRow = $cellReference[1];
        $startCol = $cellReference[0];
        
        for ($i = 1; $i < 4; $i++) {
            $row = $startRow + $i;
            $dataIndex = $i - 1;
            for ($j = 0; $j < 4; $j++) {
                $col = chr(ord($startCol) + $j);
                $cell = $col . $row;
                if (isset($data[$dataIndex][$j])) {
                    $sheet->setCellValue($cell, $data[$dataIndex][$j]);
                    
                    if ($i == 1) $bgColor = $bgPeringatan;
                    elseif ($i == 2) $bgColor = $bgBahaya;
                    elseif ($i == 3) $bgColor = 'FFE3F2FD';
                    else $bgColor = 'FFE3F2FD';
                    
                    $isBold = ($i == 1 || $i == 2);
                    
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => $isBold, 'size' => 8],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000']
                            ]
                        ]
                    ]);
                }
            }
        }
    }

    /**
     * Get status berdasarkan tipe piezometer dan nilai T.Psmetrik
     */
    private function getStatusByType($t_psmetrik, $type)
    {
        // Pastikan nilai adalah float
        $t_psmetrik = (float) $t_psmetrik;
        
        switch($type) {
            case 'L10':
                if ($t_psmetrik <= 565.35) return 'aman';
                if ($t_psmetrik <= 569.65) return 'peringatan';
                return 'bahaya';
            case 'SPZ02':
                if ($t_psmetrik <= 692.35) return 'aman';
                if ($t_psmetrik <= 695.35) return 'peringatan';
                return 'bahaya';
            default:
                return 'aman';
        }
    }
}