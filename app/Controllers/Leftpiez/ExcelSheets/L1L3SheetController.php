<?php

namespace App\Controllers\LeftPiez\ExcelSheets;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class L1L3SheetController extends BaseSheetController
{
    /**
     * Create Sheet untuk Grafik History L1-L3 dengan struktur sama persis seperti view
     */
    public function createGrafikHistoryL1L3Sheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
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
        
        // Warna untuk status T.Psmetrik (background) - SAMA DENGAN VIEW
        $statusAmanBg = 'FFD4EDDA';       // Hijau muda untuk Aman - sama dengan view
        $statusPeringatanBg = 'FFFFF3CD'; // Kuning muda untuk Peringatan - sama dengan view
        $statusBahayaBg = 'FFF8D7DA';     // Merah muda untuk Bahaya - sama dengan view
        
        // Warna untuk teks status T.Psmetrik - SAMA DENGAN VIEW
        $statusAmanText = 'FF155724';     // Hijau gelap untuk teks Aman - sama dengan view
        $statusPeringatanText = 'FF856404'; // Coklat untuk teks Peringatan - sama dengan view
        $statusBahayaText = 'FF721C24';   // Merah gelap untuk teks Bahaya - sama dengan view
        
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
        // Style untuk Ambang Batas L1
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
        
        // L-2: 650.66 dengan colspan 2
        $sheet->setCellValue('H' . $currentRow, '650.66');
        $sheet->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->applyColspanStyle($sheet, 'H' . $currentRow, 'I' . $currentRow, $headerLightBlue);
        
        // L-2 Ambang Batas dengan colspan 4 dan rowspan 4 (J6:M9)
        $sheet->setCellValue('J' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('J' . $currentRow . ':M' . ($currentRow + 3));
        // Style untuk Ambang Batas L2
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
        
        // L-3: 616.55 dengan colspan 2
        $sheet->setCellValue('N' . $currentRow, '616.55');
        $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
        $this->applyColspanStyle($sheet, 'N' . $currentRow, 'O' . $currentRow, $headerLightBlue);
        
        // L-3 Ambang Batas dengan colspan 4 dan rowspan 4 (P6:S9)
        $sheet->setCellValue('P' . $currentRow, 'Ambang Batas');
        $sheet->mergeCells('P' . $currentRow . ':S' . ($currentRow + 3));
        // Style untuk Ambang Batas L3
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
            $sheet->setCellValue('A' . $currentRow, 'TIDAK ADA DATA PIEZOMETER YANG TERSEDIA');
            $sheet->getStyle('A' . $currentRow . ':S' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9F9F9']]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
        } else {
            // Simpan status untuk setiap baris
            $statusData = [];
            
            // Urutkan berdasarkan tanggal
            usort($pengukuranData, function($a, $b) {
                $dateA = strtotime($a['tanggal']);
                $dateB = strtotime($b['tanggal']);
                return $dateA - $dateB;
            });
            
            foreach ($pengukuranData as $index => $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data terkait
                $pembacaanData = $this->pembacaanModel->getSemuaByPengukuran($pid);
                $perhitunganData = $this->perhitunganModel->where('id_pengukuran', $pid)->findAll();
                
                // Format tanggal
                $tanggal = !empty($p['tanggal']) ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                $sheet->setCellValue('A' . $currentRow, $tanggal);
                
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
                
                // T.Psmetrik(El.m) L1 - DENGAN BACKGROUND WARNA SESUAI STATUS
                $status_L01 = $this->getStatusByType($t_psmetrik_L01, 'L01');
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
                
                // T.Psmetrik(El.m) L2 - DENGAN BACKGROUND WARNA SESUAI STATUS
                $status_L02 = $this->getStatusByType($t_psmetrik_L02, 'L02');
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
                
                // T.Psmetrik(El.m) L3 - DENGAN BACKGROUND WARNA SESUAI STATUS
                $status_L03 = $this->getStatusByType($t_psmetrik_L03, 'L03');
                $sheet->setCellValue('S' . $currentRow, number_format($t_psmetrik_L03, 2));
                
                // Simpan status untuk baris ini
                $statusData[$currentRow] = [
                    'L01' => $status_L01,
                    'L02' => $status_L02,
                    'L03' => $status_L03
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
                // Kolom Aman
                $amanColumns = ['D', 'J', 'P'];
                foreach ($amanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusAmanText]]
                    ]);
                }
                
                // Kolom Peringatan
                $peringatanColumns = ['E', 'K', 'Q'];
                foreach ($peringatanColumns as $col) {
                    $colRange = $col . $dataAreaStart . ':' . $col . $dataAreaEnd;
                    $sheet->getStyle($colRange)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                        'font' => ['bold' => true, 'color' => ['argb' => $statusPeringatanText]]
                    ]);
                }
                
                // Kolom Bahaya
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
                    // Kolom G untuk L1
                    $cellG = 'G' . $row;
                    $statusL1 = $statuses['L01'];
                    $this->applyStatusColorToCell($sheet, $cellG, $statusL1, $sheet->getCell($cellG)->getValue());
                    
                    // Kolom M untuk L2
                    $cellM = 'M' . $row;
                    $statusL2 = $statuses['L02'];
                    $this->applyStatusColorToCell($sheet, $cellM, $statusL2, $sheet->getCell($cellM)->getValue());
                    
                    // Kolom S untuk L3
                    $cellS = 'S' . $row;
                    $statusL3 = $statuses['L03'];
                    $this->applyStatusColorToCell($sheet, $cellS, $statusL3, $sheet->getCell($cellS)->getValue());
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
        $sheet->freezePane('A11'); // Freeze header rows, mulai data di row 11
        
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
     * Apply background color untuk kolom T.Psmetrik berdasarkan status - SAMA DENGAN VIEW
     */
    private function applyStatusColorToCell($sheet, $cell, $status, $value)
    {
        // Warna background sesuai status (SESUAI DENGAN VIEW HTML)
        $bgColors = [
            'aman' => 'FFD4EDDA',       // Hijau muda - sama dengan view (.status-aman)
            'peringatan' => 'FFFFF3CD', // Kuning muda - sama dengan view (.status-peringatan)  
            'bahaya' => 'FFF8D7DA'      // Merah muda - sama dengan view (.status-bahaya)
        ];
        
        // Warna teks sesuai status (SESUAI DENGAN VIEW HTML)
        $textColors = [
            'aman' => 'FF155724',       // Hijau gelap - sama dengan view (.status-aman)
            'peringatan' => 'FF856404', // Coklat - sama dengan view (.status-peringatan)
            'bahaya' => 'FF721C24'      // Merah gelap - sama dengan view (.status-bahaya)
        ];
        
        $bgColor = $bgColors[$status] ?? 'FFFFFFFF';
        $textColor = $textColors[$status] ?? 'FF000000';
        
        // Apply style - PENTING: Background color sesuai status
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
        
        // Set nilai sel jika belum diset
        if (empty($sheet->getCell($cell)->getValue()) && !empty($value)) {
            $sheet->setCellValue($cell, $value);
        }
    }
    
    /**
     * Apply styles untuk kolom ambang batas
     */
    private function applyAmbangBatasStyles($sheet, $startRow, $endRow)
    {
        // Warna untuk kolom ambang batas
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Kolom Aman (D, J, P)
        $amanCols = ['D', 'J', 'P'];
        foreach ($amanCols as $col) {
            $range = $col . $startRow . ':' . $col . $endRow;
            $sheet->getStyle($range)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgAman]],
                'font' => ['bold' => true, 'color' => ['argb' => 'FF155724']]
            ]);
        }
        
        // Kolom Peringatan (E, K, Q)
        $peringatanCols = ['E', 'K', 'Q'];
        foreach ($peringatanCols as $col) {
            $range = $col . $startRow . ':' . $col . $endRow;
            $sheet->getStyle($range)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgPeringatan]],
                'font' => ['bold' => true, 'color' => ['argb' => 'FF856404']]
            ]);
        }
        
        // Kolom Bahaya (F, L, R)
        $bahayaCols = ['F', 'L', 'R'];
        foreach ($bahayaCols as $col) {
            $range = $col . $startRow . ':' . $col . $endRow;
            $sheet->getStyle($range)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgBahaya]],
                'font' => ['bold' => true, 'color' => ['argb' => 'FF721C24']]
            ]);
        }
    }
    
    /**
     * Apply alignment untuk data
     */
    private function applyDataAlignment($sheet, $startRow, $endRow)
    {
        // Alignment untuk kolom numerik (kanan)
        $numericColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
        
        foreach ($numericColumns as $col) {
            $colRange = $col . $startRow . ':' . $col . $endRow;
            $sheet->getStyle($colRange)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
        
        // Alignment untuk kolom tanggal (tengah)
        $sheet->getStyle('A' . $startRow . ':A' . $endRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }
    
    /**
     * Fill Ambang Batas Data L1
     */
    private function fillAmbangBatasDataL1($sheet, $startCell, $endCell)
    {
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Isi data untuk area D6:G9 - SEL D6 SUDAH ADA "AMBANG BATAS"
        $data = [
            ['Peringatan', '≥648.94', '', ''],  // Baris 7 (sekarang jadi baris 1 dalam loop)
            ['Bahaya', '≥650.64', '', ''],      // Baris 8 (sekarang jadi baris 2 dalam loop)
            ['T.Psmetrik', '≤650.63', '≤650.65', '≥650.64']  // Baris 9 (sekarang jadi baris 3 dalam loop)
        ];
        
        $cellReference = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($startCell);
        $startRow = $cellReference[1];
        $startCol = $cellReference[0];
        
        // Mulai dari $i = 1 karena baris pertama (row 6) sudah ada "Ambang Batas"
        for ($i = 1; $i < 4; $i++) {
            $row = $startRow + $i;
            $dataIndex = $i - 1; // Index untuk array data
            for ($j = 0; $j < 4; $j++) {
                $col = chr(ord($startCol) + $j);
                $cell = $col . $row;
                if (isset($data[$dataIndex][$j])) {
                    $sheet->setCellValue($cell, $data[$dataIndex][$j]);
                    
                    // Apply warna berdasarkan baris
                    if ($i == 1) $bgColor = $bgPeringatan; // Baris 7: Peringatan
                    elseif ($i == 2) $bgColor = $bgBahaya;  // Baris 8: Bahaya
                    elseif ($i == 3) $bgColor = 'FFE3F2FD'; // Baris 9: T.Psmetrik (biru muda)
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
     * Fill Ambang Batas Data L2
     */
    private function fillAmbangBatasDataL2($sheet, $startCell, $endCell)
    {
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Isi data untuk area J6:M9
        $data = [
            ['Peringatan', '≥648.96', '', ''],
            ['Bahaya', '≥650.66', '', ''],
            ['T.Psmetrik', '≤650.65', '≤650.65', '≥650.66']
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
     * Fill Ambang Batas Data L3
     */
    private function fillAmbangBatasDataL3($sheet, $startCell, $endCell)
    {
        $bgAman = 'FFD4EDDA';
        $bgPeringatan = 'FFFFF3CD';
        $bgBahaya = 'FFF8D7DA';
        
        // Isi data untuk area P6:S9
        $data = [
            ['Peringatan', '≥614.85', '', ''],
            ['Bahaya', '≥616.55', '', ''],
            ['T.Psmetrik', '≤616.54', '≤616.54', '≥616.55']
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
     * Get status berdasarkan tipe piezometer dan nilai T.Psmetrik
     */
    private function getStatusByType($t_psmetrik, $type)
    {
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
    }
}