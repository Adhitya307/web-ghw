<?php

namespace App\Controllers\LeftPiez\ExcelSheets;

use App\Controllers\BaseController;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\MetrikModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

abstract class BaseSheetController extends BaseController
{
    protected $pembacaanModel;
    protected $metrikModel;
    protected $ireadingA;
    protected $ireadingB;
    protected $perhitunganModel;

    public function __construct()
    {
        // Initialize models
        $this->pembacaanModel = new TPembacaanLeftPiezModel();
        $this->metrikModel = new MetrikModel();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->perhitunganModel = new PerhitunganLeftPiezModel();
    }

    /**
     * Helper function untuk konversi yang aman
     */
    protected function safeConvert($value, $conversionFactor = 0.3048)
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
    protected function safeNumberFormat($value, $decimals = 2)
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
     * Apply style untuk rowspan
     */
    protected function applyRowspanStyle($sheet, $startCell, $endCell, $fillColor)
    {
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }

    /**
     * Apply style untuk colspan
     */
    protected function applyColspanStyle($sheet, $startCell, $endCell, $fillColor)
    {
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }

    /**
     * Apply style untuk single cell
     */
    protected function applySingleCellStyle($sheet, $cell, $fillColor)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }

    /**
     * Apply style untuk merged cell
     */
    protected function applyMergedCellStyle($sheet, $startCell, $endCell, $fillColor, $center = true)
    {
        $alignment = $center ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT;
        
        $sheet->getStyle($startCell . ':' . $endCell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => $alignment, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ]);
    }

    /**
     * Helper function untuk mendapatkan kolom berikutnya
     */
    protected function nextColumn($currentCol, $steps = 1)
    {
        for ($i = 0; $i < $steps; $i++) {
            $currentCol++;
        }
        return $currentCol;
    }

    /**
     * Helper function untuk mendapatkan range kolom
     */
    protected function getColumnRange($start, $end)
    {
        $columns = [];
        for ($col = $start; $col <= $end; $col++) {
            $columns[] = $col;
        }
        return $columns;
    }

    /**
     * Setup Excel Header & Footer
     */
    protected function setupExcelHeaderFooter($sheet, $tahunFilter, $periodeFilter, $dmaFilter)
    {
        // Header
        $sheet->getHeaderFooter()
            ->setOddHeader('&C&PIEZOMETER MONITORING SYSTEM - PT INDONESIA POWER');
        
        // Footer
        $footerText = 'Halaman &P dari &N';
        if (!empty($tahunFilter)) $footerText .= ' | Tahun: ' . $tahunFilter;
        if (!empty($periodeFilter)) $footerText .= ' | Periode: ' . $periodeFilter;
        if (!empty($dmaFilter)) $footerText .= ' | DMA: ' . $dmaFilter;
        $footerText .= ' | Diekspor: ' . date('d/m/Y H:i:s');
        
        $sheet->getHeaderFooter()
            ->setOddFooter('&C' . $footerText);
    }

    /**
     * Apply status color untuk kolom
     */
    protected function applyStatusColor($sheet, $cell, $status)
    {
        $colors = [
            'aman' => ['bg' => 'FFD4EDDA', 'text' => 'FF155724'],
            'peringatan' => ['bg' => 'FFFFF3CD', 'text' => 'FF856404'],
            'bahaya' => ['bg' => 'FFF8D7DA', 'text' => 'FF721C24']
        ];
        
        $color = $colors[$status] ?? $colors['aman'];
        
        $sheet->getStyle($cell)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => $color['text']]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color['bg']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
    }
}