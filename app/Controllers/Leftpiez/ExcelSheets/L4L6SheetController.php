<?php

namespace App\Controllers\LeftPiez\ExcelSheets;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class L4L6SheetController extends BaseSheetController
{
    /**
     * Create Sheet untuk Grafik History L4-L6 dengan struktur sama persis seperti L1-L3
     */
    public function createGrafikHistoryL4L6Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
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
        $lastCol = 'S';
        
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
        
        // ===== HEADER TABEL =====
        $currentRow = 4;
        $headerStartRow = $currentRow;
        $headerEndRow = $currentRow + 6;
        
        // ===== ROW 4: Main Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Pisometer No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $this->applyRowspanStyle($sheet, 'A' . $currentRow, 'A' . ($currentRow + 1), $headerLightBlue);
        
        $sheet->setCellValue('B' . $currentRow, 'L-4');
        $sheet->mergeCells('B' . $currentRow . ':G' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'G' . $currentRow, $headerBlue);
        
        $sheet->setCellValue('H' . $currentRow, 'L-5');
        $sheet->mergeCells('H' . $currentRow . ':M' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'M' . $currentRow, $headerBlue);
        
        $sheet->setCellValue('N' . $currentRow, 'L-6');
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
        
        // ===== ROW 6: Data Headers =====
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
        
        // L-4 Ambang Batas - SAMA DENGAN L1-L3
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
        
        // L-5: 700.76 dengan colspan 2
        $sheet->setCellValue('H' . $currentRow, '700.76');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-5 Ambang Batas - SAMA DENGAN L1-L3
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':M' . ($currentRow + 3));
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
        
        // L-6: 690.09 dengan colspan 2
        $sheet->setCellValue('N' . $currentRow, '690.09');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        // L-6 Ambang Batas - SAMA DENGAN L1-L3
        $sheet->setCellValue('P' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('P' . $currentRow . ':S' . ($currentRow + 3));
        $sheet->getStyle('P' . $currentRow)->applyFromArray([
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
        
        $sheet->setCellValue('B' . $currentRow, '50.00');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, '62.00');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('N' . $currentRow, '62.00');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 8: Koordinat X =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat X(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        $sheet->setCellValue('B' . $currentRow, '6.116,59');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, '6.168,84');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('N' . $currentRow, '6.106,56');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 9: Koordinat Y =====
        $sheet->setCellValue('A' . $currentRow, 'Koordinat Y(m)');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        $sheet->setCellValue('B' . $currentRow, '(8.669,64)');
        $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $this->applyColspanStyle($sheet, 'B' . $currentRow, 'C' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('H' . $currentRow, '(9.057,75)');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        $sheet->setCellValue('N' . $currentRow, '(8.921,46)');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        $currentRow++;
        
        // ===== ROW 10: Final Headers =====
        $sheet->setCellValue('A' . $currentRow, 'Tanggal');
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['argb' => Color::COLOR_WHITE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']]
        ]);
        
        // ===== L-4 Columns =====
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
        
        // ===== L-5 Columns =====
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
        
        // ===== L-6 Columns =====
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
        
        // ===== ISI DATA AMBANG BATAS =====
        $this->fillAmbangBatasDataL4($sheet, 'D6', 'G9');
        $this->fillAmbangBatasDataL5($sheet, 'J6', 'M9');
        $this->fillAmbangBatasDataL6($sheet, 'P6', 'S9');
        
        // ===== APPLY BORDER =====
        $headerOuterRange = 'A' . $headerStartRow . ':S' . $headerEndRow;
        $sheet->getStyle($headerOuterRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
        
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
            $sheet->mergeCells('A' . $currentRow . ':S' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'TIDAK ADA DATA PIEZOMETER YANG TERSEDIA');
            $sheet->getStyle('A' . $currentRow . ':S' . $currentRow)->applyFromArray([
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
                
                // ===== L-4 Data =====
                $bacaan_L04 = $pembacaanData['L04']['feet'] ?? 0;
                $bacaan_L04_m = $bacaan_L04 * 0.3048;
                $sheet->setCellValue('B' . $currentRow, number_format($bacaan_L04_m, 2));
                
                $t_psmetrik_L04 = 0;
                foreach ($perhitunganData as $perhitungan) {
                    if ($perhitungan['tipe_piezometer'] == 'L04') {
                        $t_psmetrik_L04 = $perhitungan['t_psmetrik'] ?? 0;
                        break;
                    }
                }
                $sheet->setCellValue('C' . $currentRow, number_format($t_psmetrik_L04, 2));
                
                $sheet->setCellValue('D' . $currentRow, '560.86');
                $sheet->setCellValue('E' . $currentRow, '565.36');
                $sheet->setCellValue('F' . $currentRow, '569.66');
                
                $status_L04 = $this->getStatusByType($t_psmetrik_L04, 'L04');
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
                
                $sheet->setCellValue('J' . $currentRow, '691.46');
                $sheet->setCellValue('K' . $currentRow, '692.36');
                $sheet->setCellValue('L' . $currentRow, '695.36');
                
                $status_L05 = $this->getStatusByType($t_psmetrik_L05, 'L05');
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
                
                $sheet->setCellValue('P' . $currentRow, '680.79');
                $sheet->setCellValue('Q' . $currentRow, '681.69');
                $sheet->setCellValue('R' . $currentRow, '684.69');
                
                $status_L06 = $this->getStatusByType($t_psmetrik_L06, 'L06');
                $sheet->setCellValue('S' . $currentRow, number_format($t_psmetrik_L06, 2));
                
                $statusData[$currentRow] = [
                    'L04' => $status_L04,
                    'L05' => $status_L05,
                    'L06' => $status_L06
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
                    $rowRange = 'A' . $row . ':S' . $row;
                    
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBgColor]]
                    ]);
                }
                
                // 2. Apply border untuk semua sel data
                $dataAreaRange = 'A' . $dataAreaStart . ':S' . $dataAreaEnd;
                $sheet->getStyle($dataAreaRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC']
                        ]
                    ]
                ]);
                
                // 3. Apply warna khusus untuk kolom ambang batas di area data (D, J, P untuk Aman; E, K, Q untuk Peringatan; F, L, R untuk Bahaya)
                $amanColumns = ['D', 'J', 'P'];
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusAmanText]]
                    ]);
                }
                
                $peringatanColumns = ['E', 'K', 'Q'];
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusPeringatanText]]
                    ]);
                }
                
                $bahayaColumns = ['F', 'L', 'R'];
                foreach ($bahayaColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusBahayaText]]
                    ]);
                }
                
                // 4. Apply warna background untuk kolom T.Psmetrik terakhir (G, M, S) berdasarkan status
                foreach ($statusData as $row => $statuses) {
                    $cellG = 'G' . $row;
                    $statusL4 = $statuses['L04'];
                    $this->applyStatusColorToCell($sheet, $cellG, $statusL4, $sheet->getCell($cellG)->getValue());
                    
                    $cellM = 'M' . $row;
                    $statusL5 = $statuses['L05'];
                    $this->applyStatusColorToCell($sheet, $cellM, $statusL5, $sheet->getCell($cellM)->getValue());
                    
                    $cellS = 'S' . $row;
                    $statusL6 = $statuses['L06'];
                    $this->applyStatusColorToCell($sheet, $cellS, $statusL6, $sheet->getCell($cellS)->getValue());
                }
                
                // 5. Alignment untuk kolom numerik (kanan)
                $numericColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
                
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
                $numericRange = 'B' . $dataAreaStart . ':S' . $dataAreaEnd;
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
            'H' => 10, 'I' => 14, 'J' => 8, 'K' => 10, 'L' => 8, 'M' => 14,
            'N' => 10, 'O' => 14, 'P' => 8, 'Q' => 10, 'R' => 8, 'S' => 14
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
     * Fill Ambang Batas Data L4 - DIUBAH MIRIP DENGAN L1-L3
     */
    private function fillAmbangBatasDataL4($sheet, $startCell, $endCell)
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
     * Fill Ambang Batas Data L5 - DIUBAH MIRIP DENGAN L1-L3
     */
    private function fillAmbangBatasDataL5($sheet, $startCell, $endCell)
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
     * Fill Ambang Batas Data L6 - DIUBAH MIRIP DENGAN L1-L3
     */
    private function fillAmbangBatasDataL6($sheet, $startCell, $endCell)
    {
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Isi data untuk area P6:S9 - SAMA DENGAN STRUKTUR L1-L3
        $data = [
            ['Peringatan', '≥681.69', '', ''],
            ['Bahaya', '≥684.69', '', ''],
            ['T.Psmetrik', '≤681.68', '≤684.68', '≥684.69']
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
        switch($type) {
            case 'L04':
                if ($t_psmetrik <= 565.35) return 'aman';
                if ($t_psmetrik <= 569.65) return 'peringatan';
                return 'bahaya';
            case 'L05':
                if ($t_psmetrik <= 692.35) return 'aman';
                if ($t_psmetrik <= 695.35) return 'peringatan';
                return 'bahaya';
            case 'L06':
                if ($t_psmetrik <= 681.68) return 'aman';
                if ($t_psmetrik <= 684.68) return 'peringatan';
                return 'bahaya';
            default:
                return 'aman';
        }
    }
}