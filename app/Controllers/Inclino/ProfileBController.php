<?php

namespace App\Controllers\Inclino;

use App\Controllers\BaseController;
use App\Models\Inclino\ProfilBModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProfileBController extends BaseController
{
    protected $profilBModel;

    public function __construct()
    {
        $this->profilBModel = new ProfilBModel();
    }
    
    public function view()
    {
        // Get available years from profil_b table
        $years = $this->getAvailableYears();
        
        $data = [
            'title' => 'Profil B Monitoring - PT Indonesia Power',
            'years' => $years
        ];
        
        return view('inclino/profile_b_view', $data);
    }
    
    /**
     * Get data Profil B berdasarkan tahun - AJAX
     */
    public function getDataByYear()
    {
        $year = $this->request->getGet('year');
        
        try {
            $data = $this->getProfilBDataByYear($year);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getDataByYear Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Export Profil B to Excel - Download langsung
     */
    public function exportToExcel()
    {
        $year = $this->request->getGet('year');
        
        try {
            // Get data
            $data = $this->getProfilBDataByYear($year);
            
            if (empty($data['data'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada data untuk diexport'
                ]);
            }
            
            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul utama
            $sheet->setCellValue('A1', 'PROFIL B DATA - PT INDONESIA POWER');
            $sheet->mergeCells('A1:' . $this->getExcelColumn(count($data['dates']) + 1) . '1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
            
            // Set informasi filter
            $sheet->setCellValue('A3', 'Tahun:');
            $sheet->setCellValue('B3', $year ?: 'Semua Tahun');
            $sheet->setCellValue('A4', 'Total Data:');
            $sheet->setCellValue('B4', $data['metadata']['total_records']);
            $sheet->setCellValue('A5', 'Rentang Kedalaman:');
            $sheet->setCellValue('B5', $data['metadata']['depth_range']);
            
            // Start data dari baris 7
            $row = 7;
            $col = 1; // Kolom A = 1
            
            // Set header Depth
            $sheet->setCellValueByColumnAndRow($col, $row, 'Depth (m)');
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow($col, $row)->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setARGB('FFE7F1FF'); // Warna biru muda
            
            // Set header tanggal
            $col = 2;
            foreach ($data['dates'] as $date) {
                $formattedDate = date('d-m-Y', strtotime($date));
                $sheet->setCellValueByColumnAndRow($col, $row, $formattedDate);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF6C757D'); // Warna abu-abu gelap
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FFFFFFFF'); // Teks putih
                $col++;
            }
            
            // Style header row
            $headerRange = 'A' . $row . ':' . $this->getExcelColumn($col - 1) . $row;
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Isi data - depth dari 0.5 hingga 80.0
            $row++;
            foreach ($data['data'] as $depthData) {
                $col = 1;
                
                // Depth - warna biru muda
                $sheet->setCellValueByColumnAndRow($col, $row, $depthData['depth']);
                $sheet->getStyleByColumnAndRow($col, $row)->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFE7F1FF');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $col++;
                
                // Nilai untuk setiap tanggal - TANPA WARNA
                foreach ($data['dates'] as $date) {
                    $value = $depthData[$date] ?? '';
                    if ($value !== null && $value !== '') {
                        $sheet->setCellValueByColumnAndRow($col, $row, (float)$value);
                        $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('0.0000');
                        // TIDAK ADA WARNA MERAH/HIJAU
                    } else {
                        $sheet->setCellValueByColumnAndRow($col, $row, '-');
                    }
                    $col++;
                }
                
                $row++;
            }
            
            // Add border ke data
            $lastRow = $row - 1;
            $lastCol = $col - 1;
            $dataRange = 'A7:' . $this->getExcelColumn($lastCol) . $lastRow;
            
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFDEE2E6'],
                    ],
                ],
            ];
            $sheet->getStyle($dataRange)->applyFromArray($styleArray);
            
            // Set auto filter
            $sheet->setAutoFilter('A7:' . $this->getExcelColumn($lastCol) . '7');
            
            // Freeze panes (Depth column tetap terlihat)
            $sheet->freezePane('B8');
            
            // Auto size columns
            for ($i = 1; $i <= $lastCol; $i++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }
            
            // Set specific widths
            $sheet->getColumnDimension('A')->setWidth(12); // Depth column
            
            // Generate filename
            $filename = 'Profil_B_Data_' . ($year ?: 'All') . '_' . date('Ymd_His') . '.xlsx';
            
            // Set headers untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            
            // Output file
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            log_message('error', 'exportToExcel Error: ' . $e->getMessage());
            
            // Return error via AJAX jika request AJAX
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengexport data: ' . $e->getMessage()
                ]);
            } else {
                // Redirect atau tampilkan error
                return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Get Profil B data by year in pivot format
     */
    private function getProfilBDataByYear($year)
    {
        try {
            $builder = $this->profilBModel->db->table('profil_b');
            
            // Base query - ambil semua data untuk tahun yang dipilih
            $builder->select('depth, reading_date, nilai_profil_b')
                    ->orderBy('depth', 'ASC')
                    ->orderBy('reading_date', 'ASC');
            
            if (!empty($year)) {
                $builder->where('YEAR(reading_date)', $year);
            }
            
            $rawData = $builder->get()->getResultArray();
            
            if (empty($rawData)) {
                return [
                    'dates' => [],
                    'data' => [],
                    'metadata' => [
                        'year' => $year,
                        'total_records' => 0,
                        'total_dates' => 0,
                        'depth_range' => '-'
                    ]
                ];
            }
            
            // Get unique dates and depths
            $dates = [];
            $depths = [];
            
            foreach ($rawData as $row) {
                $date = date('Y-m-d', strtotime($row['reading_date']));
                if (!in_array($date, $dates)) {
                    $dates[] = $date;
                }
                
                // Depth dijadikan positif dan diambil nilai absolutnya
                $depthValue = abs((float)$row['depth']);
                $depth = number_format($depthValue, 1); // Format dengan 1 desimal
                
                if (!in_array($depth, $depths)) {
                    $depths[] = $depth;
                }
            }
            
            // Sort dates chronologically
            sort($dates);
            
            // Sort depths dari 0.5 ke 80.0
            usort($depths, function($a, $b) {
                return floatval($a) - floatval($b);
            });
            
            // Buat array depth dari 0.5 hingga 80.0 dengan interval 0.5
            $allDepths = [];
            for ($d = 0.5; $d <= 80.0; $d += 0.5) {
                $allDepths[] = number_format($d, 1);
            }
            
            // Gabungkan depths yang ada di database dengan depths default
            $finalDepths = array_unique(array_merge($allDepths, $depths));
            usort($finalDepths, function($a, $b) {
                return floatval($a) - floatval($b);
            });
            
            // Create pivot data structure
            $pivotData = [];
            
            // Initialize pivot structure
            foreach ($finalDepths as $depth) {
                $pivotData[$depth] = ['depth' => $depth];
                foreach ($dates as $date) {
                    $pivotData[$depth][$date] = null;
                }
            }
            
            // Fill with actual data
            foreach ($rawData as $row) {
                $date = date('Y-m-d', strtotime($row['reading_date']));
                $depthValue = abs((float)$row['depth']); // Ambil nilai absolut
                $depth = number_format($depthValue, 1); // Format dengan 1 desimal
                $value = $row['nilai_profil_b'];
                
                if (isset($pivotData[$depth])) {
                    $pivotData[$depth][$date] = $value;
                }
            }
            
            // Convert to indexed array
            $data = array_values($pivotData);
            
            // Get metadata
            $depthsFloat = array_map('floatval', $finalDepths);
            $minDepth = min($depthsFloat);
            $maxDepth = max($depthsFloat);
            
            return [
                'dates' => $dates,
                'data' => $data,
                'metadata' => [
                    'year' => $year,
                    'total_records' => count($rawData),
                    'total_dates' => count($dates),
                    'total_depths' => count($finalDepths),
                    'min_depth' => $minDepth,
                    'max_depth' => $maxDepth,
                    'depth_range' => $minDepth . ' m - ' . $maxDepth . ' m'
                ]
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'getProfilBDataByYear Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get available years from database
     */
    private function getAvailableYears()
    {
        try {
            $query = $this->profilBModel->db->query("
                SELECT DISTINCT YEAR(reading_date) as year 
                FROM profil_b 
                WHERE reading_date IS NOT NULL 
                ORDER BY year DESC
            ");
            
            $years = [];
            foreach ($query->getResultArray() as $row) {
                $years[] = $row['year'];
            }
            
            return $years;
            
        } catch (\Exception $e) {
            log_message('error', 'getAvailableYears Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Helper function for Excel column letter
     */
    private function getExcelColumn($number)
    {
        $letter = '';
        while ($number > 0) {
            $temp = ($number - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $number = ($number - $temp - 1) / 26;
        }
        return $letter;
    }
}