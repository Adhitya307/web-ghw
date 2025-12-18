<?php

namespace App\Controllers\LeftPiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportExcelController extends BaseController
{
    protected $pengukuranModel;

    public function __construct()
    {
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
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
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 300);
            gc_enable();
            
            // Get filter parameters
            $tahun = $this->request->getGet('tahun');
            $periode = $this->request->getGet('periode');
            $dma = $this->request->getGet('dma');
            
            // ===== OPTIMASI QUERY =====
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
            $query->limit(1000);
            
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
            
            // Gunakan MainSheetController
            $mainSheetController = new \App\Controllers\LeftPiez\ExcelSheets\MainSheetController();
            $mainSheetController->createMainSheet($mainSheet, $pengukuranData, $tahun, $periode, $dma);
            
            // ===== SHEET 2: GRAFIK HISTORY L1-L3 =====
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Grafik History L1-L3');
            
            $l1l3Controller = new \App\Controllers\LeftPiez\ExcelSheets\L1L3SheetController();
            $l1l3Controller->createGrafikHistoryL1L3Sheet($sheet2, $pengukuranData, $tahun, $periode, $dma);
            
            // ===== SHEET 3: GRAFIK HISTORY L4-L6 =====
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Grafik History L4-L6');
            
            $l4l6Controller = new \App\Controllers\LeftPiez\ExcelSheets\L4L6SheetController();
            $l4l6Controller->createGrafikHistoryL4L6Sheet($sheet3, $pengukuranData, $tahun, $periode, $dma);
            
            // ===== SHEET 4: GRAFIK HISTORY L7-L9 =====
            $sheet4 = $spreadsheet->createSheet();
            $sheet4->setTitle('Grafik History L7-L9');
            
            $l7l9Controller = new \App\Controllers\LeftPiez\ExcelSheets\L7L9SheetController();
            $l7l9Controller->createGrafikHistoryL7L9Sheet($sheet4, $pengukuranData, $tahun, $periode, $dma);
            
            // ===== SHEET 5: GRAFIK HISTORY L10-SPZ02 =====
            $sheet5 = $spreadsheet->createSheet();
            $sheet5->setTitle('Grafik History L10-SPZ02');
            
            $l10spzController = new \App\Controllers\LeftPiez\ExcelSheets\L10SPZ02SheetController();
            $l10spzController->createGrafikHistoryL10SPZ02Sheet($sheet5, $pengukuranData, $tahun, $periode, $dma);
            
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
            gc_collect_cycles();
            
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
}