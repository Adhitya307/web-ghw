<?php

namespace App\Controllers\LeftPiez\ExcelSheets;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class MainSheetController extends BaseSheetController
{
    /**
     * Create Main Sheet dengan struktur header sama persis seperti view
     */
    public function createMainSheet($sheet, $pengukuranData, $tahunFilter, $periodeFilter, $dmaFilter)
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
}