<?php

namespace App\Controllers\BTM;

use App\Controllers\BaseController;
use App\Models\Btm\PengukuranBtmModel;
use App\Models\Btm\BacaanBt1Model;
use App\Models\Btm\BacaanBt2Model;
use App\Models\Btm\BacaanBt3Model;
use App\Models\Btm\BacaanBt4Model;
use App\Models\Btm\BacaanBt6Model;
use App\Models\Btm\BacaanBt7Model;
use App\Models\Btm\BacaanBt8Model;
use App\Models\Btm\PerhitunganBt1Model;
use App\Models\Btm\PerhitunganBt2Model;
use App\Models\Btm\PerhitunganBt3Model;
use App\Models\Btm\PerhitunganBt4Model;
use App\Models\Btm\PerhitunganBt6Model;
use App\Models\Btm\PerhitunganBt7Model;
use App\Models\Btm\PerhitunganBt8Model;
use App\Models\Btm\ScatterBt1Model;
use App\Models\Btm\ScatterBt2Model;
use App\Models\Btm\ScatterBt3Model;
use App\Models\Btm\ScatterBt4Model;
use App\Models\Btm\ScatterBt6Model;
use App\Models\Btm\ScatterBt7Model;
use App\Models\Btm\ScatterBt8Model;
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
    protected $bacaanModels;
    protected $perhitunganModels;
    protected $scatterModels;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Cek login
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        try {
            // Initialize models
            $this->pengukuranModel = new PengukuranBtmModel();
            
            // Initialize all bacaan models
            $this->bacaanModels = [
                'bt1' => new BacaanBt1Model(),
                'bt2' => new BacaanBt2Model(),
                'bt3' => new BacaanBt3Model(),
                'bt4' => new BacaanBt4Model(),
                'bt6' => new BacaanBt6Model(),
                'bt7' => new BacaanBt7Model(),
                'bt8' => new BacaanBt8Model()
            ];
            
            // Initialize all perhitungan models
            $this->perhitunganModels = [
                'bt1' => new PerhitunganBt1Model(),
                'bt2' => new PerhitunganBt2Model(),
                'bt3' => new PerhitunganBt3Model(),
                'bt4' => new PerhitunganBt4Model(),
                'bt6' => new PerhitunganBt6Model(),
                'bt7' => new PerhitunganBt7Model(),
                'bt8' => new PerhitunganBt8Model()
            ];
            
            // Initialize all scatter models
            $this->scatterModels = [
                'bt1' => new ScatterBt1Model(),
                'bt2' => new ScatterBt2Model(),
                'bt3' => new ScatterBt3Model(),
                'bt4' => new ScatterBt4Model(),
                'bt6' => new ScatterBt6Model(),
                'bt7' => new ScatterBt7Model(),
                'bt8' => new ScatterBt8Model()
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
            die("Error loading models: " . $e->getMessage());
        }
    }

    /**
     * Export Excel untuk BTM dengan struktur rowspan 4
     */
    public function export()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        try {
            // Get filter parameters
            $tahun = $this->request->getGet('tahun') ?? '';
            $periode = $this->request->getGet('periode') ?? '';
            $currentBt = $this->request->getGet('bt') ?? 'bt1';
            
            // DEBUG: Log parameter
            log_message('debug', 'Export parameters - Tahun: ' . $tahun . ', Periode: ' . $periode . ', BT: ' . $currentBt);
            
            // Build query dengan filter
            $builder = $this->pengukuranModel->builder();
            $builder->orderBy('tahun', 'ASC')
                    ->orderBy('periode', 'ASC')
                    ->orderBy('tanggal', 'ASC');
            
            // Apply filters if provided
            if (!empty($tahun) && $tahun != 'all') {
                $builder->where('tahun', $tahun);
            }
            
            if (!empty($periode) && $periode != 'all') {
                $builder->where('periode', $periode);
            }
            
            $query = $builder->get();
            $pengukuranData = $query->getResultArray();

            // DEBUG: Cek data yang diambil
            log_message('debug', 'Data found: ' . count($pengukuranData) . ' records');
            
            if (ENVIRONMENT === 'development' && empty($pengukuranData)) {
                echo "DEBUG: Tidak ada data untuk filter ini<br>";
                echo "Query: " . $this->pengukuranModel->getLastQuery() . "<br>";
            }

            if (empty($pengukuranData)) {
                throw new \Exception('Tidak ada data pengukuran yang ditemukan untuk filter yang dipilih.');
            }

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            
            // ===== SHEET 1: DATA BTM KONSOLIDASI =====
            $consolidatedSheet = $spreadsheet->getActiveSheet();
            $consolidatedSheet->setTitle('Data BTM');
            $this->createConsolidatedSheet($consolidatedSheet, $pengukuranData, $currentBt);
            
            // ===== SHEET 2-8: BTM INDIVIDUAL (BT-1 s/d BT-8) =====
            $btList = ['bt1', 'bt2', 'bt3', 'bt4', 'bt6', 'bt7', 'bt8'];
            
            foreach ($btList as $btKey) {
                $btSheet = $spreadsheet->createSheet();
                $btSheet->setTitle(strtoupper($btKey));
                $this->createBtmIndividualSheet($btSheet, $pengukuranData, $btKey);
            }

            // Set sheet pertama sebagai aktif
            $spreadsheet->setActiveSheetIndex(0);
            
            // ===== PERBAIKAN UTAMA: HAPUS SEMUA OUTPUT BUFFER =====
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Generate filename
            $filename = 'BTM_Data_Export';
            if (!empty($tahun) && $tahun != 'all') {
                $filename .= '_' . $tahun;
            }
            if (!empty($periode) && $periode != 'all') {
                $filename .= '_' . $periode;
            }
            if (!empty($currentBt)) {
                $filename .= '_' . strtoupper($currentBt);
            }
            $filename .= '_' . date('Ymd_His') . '.xlsx';
            
            // Set headers dengan benar
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            
            // Buat writer
            $writer = new Xlsx($spreadsheet);
            
            // Simpan ke output langsung
            $writer->save('php://output');
            
            // Pastikan tidak ada output lain
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting Excel: ' . $e->getMessage());
            
            // Jika dalam development, tampilkan error
            if (ENVIRONMENT === 'development') {
                die("Error exporting Excel: " . $e->getMessage() . "<br>" . $e->getTraceAsString());
            }
            
            // Jika AJAX request, kembalikan JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
            
            // Redirect dengan error message
            session()->setFlashdata('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    /**
     * Create consolidated sheet untuk semua BTM dengan struktur seperti view
     */
    private function createConsolidatedSheet($sheet, $pengukuranData, $currentBt)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // ===== HEADER UTAMA =====
        $lastCol = 'T'; // 20 kolom (A-T) - tanpa kolom aksi
        
        // Judul Utama (Row 1)
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'DATA BUBBLE TILT METER - PT INDONESIA POWER');
        $this->applyTitleStyle($sheet, 'A1:' . $lastCol . '1');
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Sub Judul (Row 2)
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA BUBBLE TILT METER - ' . strtoupper($currentBt));
        $this->applyTitleStyle($sheet, 'A2:' . $lastCol . '2');
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Informasi Tanggal Ekspor (Row 3)
        $filterInfo = 'Diekspor pada: ' . date('d F Y H:i:s');
        $tahunFilter = $this->request->getGet('tahun') ?? '';
        $periodeFilter = $this->request->getGet('periode') ?? '';
        
        if (!empty($tahunFilter) && $tahunFilter != 'all') {
            $filterInfo .= ' | Tahun: ' . $tahunFilter;
        }
        if (!empty($periodeFilter) && $periodeFilter != 'all') {
            $filterInfo .= ' | Periode: ' . $periodeFilter;
        }
        
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', $filterInfo);
        $this->applySubtitleStyle($sheet, 'A3:' . $lastCol . '3');
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // ===== HEADER TABEL DENGAN STRUCTURE SEPERTI VIEW =====
        $this->createTableHeaders($sheet, $currentBt);
        
        // ===== ISI DATA =====
        $row = 8; // Mulai dari row 8 setelah header (row 4-7 adalah header)
        
        // Kelompokkan data berdasarkan tahun
        $groupedData = [];
        foreach ($pengukuranData as $item) {
            $tahun = $item['tahun'];
            if (!isset($groupedData[$tahun])) {
                $groupedData[$tahun] = [];
            }
            $groupedData[$tahun][] = $item;
        }
        
        // Urutkan berdasarkan tahun
        ksort($groupedData);
        
        $dataRowCount = 0;
        foreach ($groupedData as $tahun => $items) {
            // Urutkan berdasarkan periode dan tanggal dalam grup tahun
            usort($items, function($a, $b) {
                $periodeComp = ($a['periode'] ?? '') <=> ($b['periode'] ?? '');
                if ($periodeComp !== 0) {
                    return $periodeComp;
                }
                $dateA = strtotime($a['tanggal'] ?? '');
                $dateB = strtotime($b['tanggal'] ?? '');
                return $dateA - $dateB;
            });
            
            $rowCount = count($items);
            $isFirstRow = true;
            
            foreach ($items as $index => $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data untuk BT yang dipilih
                $bacaanModel = $this->bacaanModels[$currentBt] ?? null;
                $perhitunganModel = $this->perhitunganModels[$currentBt] ?? null;
                $scatterModel = $this->scatterModels[$currentBt] ?? null;
                
                // Ambil data
                $bacaan = $bacaanModel ? $bacaanModel->getByPengukuran($pid) : [];
                $perhitungan = $perhitunganModel ? $perhitunganModel->getByPengukuran($pid) : [];
                $scatter = $scatterModel ? $scatterModel->getByPengukuran($pid) : [];
                
                // Format tanggal
                $displayDate = $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                
                // TAHUN - ROWSPAN untuk baris pertama dalam grup tahun
                if ($isFirstRow) {
                    $sheet->setCellValue('A' . $row, $tahun);
                    $sheet->mergeCells('A' . $row . ':A' . ($row + $rowCount - 1));
                    $isFirstRow = false;
                }
                
                // PERIODE & TANGGAL - tanpa rowspan
                $sheet->setCellValue('B' . $row, $p['periode'] ?? '-');
                $sheet->setCellValue('C' . $row, $displayDate);
                
                // BACAAN DATA BT
                $sheet->setCellValue('D' . $row, $this->formatNumberExcel($bacaan['US_GP'] ?? null));
                $sheet->setCellValue('E' . $row, $bacaan['US_Arah'] ?? '-');
                $sheet->setCellValue('F' . $row, $this->formatNumberExcel($bacaan['TB_GP'] ?? null));
                $sheet->setCellValue('G' . $row, $bacaan['TB_Arah'] ?? '-');
                
                // PERHITUNGAN DATA BT
                $sheet->setCellValue('H' . $row, $this->formatNumberExcel($perhitungan['A_sec'] ?? null));
                $sheet->setCellValue('I' . $row, $this->formatNumberExcel($perhitungan['sin_A_rad'] ?? null));
                $sheet->setCellValue('J' . $row, $this->formatNumberExcel($perhitungan['B_sec'] ?? null));
                $sheet->setCellValue('K' . $row, $this->formatNumberExcel($perhitungan['sin_B_rad'] ?? null));
                $sheet->setCellValue('L' . $row, $this->formatNumberExcel($perhitungan['sin_C_rad'] ?? null));
                $sheet->setCellValue('M' . $row, $this->formatNumberExcel($perhitungan['sin_C_deg'] ?? null));
                $sheet->setCellValue('N' . $row, $this->formatNumberExcel($perhitungan['Cosa'] ?? null));
                $sheet->setCellValue('O' . $row, $this->formatNumberExcel($perhitungan['a_rad'] ?? null));
                $sheet->setCellValue('P' . $row, $perhitungan['DMS'] ?? '-');
                
                // SCATTER DATA
                $sheet->setCellValue('Q' . $row, $this->formatNumberExcel($scatter['Y_US'] ?? null));
                $sheet->setCellValue('R' . $row, $this->formatNumberExcel($scatter['X_TB'] ?? null));
                $sheet->setCellValue('S' . $row, $this->formatNumberExcel($scatter['Y_cum'] ?? null));
                $sheet->setCellValue('T' . $row, $this->formatNumberExcel($scatter['X_cum'] ?? null));
                
                // Apply styling untuk baris
                $this->applyRowStyleConsolidated($sheet, $row, $dataRowCount);
                
                $row++;
                $dataRowCount++;
            }
        }
        
        // ===== STYLING KOLOM =====
        $this->applyConsolidatedColumnStyling($sheet);
        
        // ===== FORMAT ANGKA =====
        if ($row > 8) {
            // Format kolom numerik dengan desimal yang sesuai
            $numericColumns = ['D', 'F', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'Q', 'R', 'S', 'T'];
            foreach ($numericColumns as $col) {
                $range = $col . '8:' . $col . ($row - 1);
                $sheet->getStyle($range)
                    ->getNumberFormat()
                    ->setFormatCode('0.000000000');
            }
            
            // Atur alignment untuk kolom numerik ke kanan
            $numericRange = 'D8:T' . ($row - 1);
            $sheet->getStyle($numericRange)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Atur alignment untuk kolom teks ke center
            $textColumns = ['A', 'B', 'C', 'E', 'G', 'P'];
            foreach ($textColumns as $col) {
                $range = $col . '8:' . $col . ($row - 1);
                $sheet->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('D8'); // Freeze kolom A-C dan baris 1-7
        
        // ===== FOOTER =====
        $footerRow = $row + 1;
        $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 'Bubble Tilt Meter - Sistem Monitoring - PT Indonesia Power');
        $this->applyFooterStyle($sheet, 'A' . $footerRow . ':' . $lastCol . $footerRow);
        $sheet->getRowDimension($footerRow)->setRowHeight(30);
    }
    
    /**
     * Create individual BTM sheet untuk setiap BT
     */
    private function createBtmIndividualSheet($sheet, $pengukuranData, $btKey)
    {
        // ===== SET PAGE SETUP =====
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A3)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // ===== HEADER UTAMA =====
        $lastCol = 'T'; // 20 kolom (A-T) - tanpa kolom aksi
        
        // Judul Utama (Row 1)
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'DATA BUBBLE TILT METER - PT INDONESIA POWER');
        $this->applyTitleStyle($sheet, 'A1:' . $lastCol . '1');
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Sub Judul (Row 2)
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'LAPORAN DATA BUBBLE TILT METER - ' . strtoupper($btKey));
        $this->applyTitleStyle($sheet, 'A2:' . $lastCol . '2');
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        // Informasi Tanggal Ekspor (Row 3)
        $filterInfo = 'Diekspor pada: ' . date('d F Y H:i:s');
        $tahunFilter = $this->request->getGet('tahun') ?? '';
        $periodeFilter = $this->request->getGet('periode') ?? '';
        
        if (!empty($tahunFilter) && $tahunFilter != 'all') {
            $filterInfo .= ' | Tahun: ' . $tahunFilter;
        }
        if (!empty($periodeFilter) && $periodeFilter != 'all') {
            $filterInfo .= ' | Periode: ' . $periodeFilter;
        }
        
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', $filterInfo);
        $this->applySubtitleStyle($sheet, 'A3:' . $lastCol . '3');
        $sheet->getRowDimension(3)->setRowHeight(25);
        
        // ===== HEADER TABEL DENGAN STRUCTURE SEPERTI VIEW =====
        $this->createTableHeaders($sheet, $btKey);
        
        // ===== ISI DATA =====
        $row = 8; // Mulai dari row 8 setelah header (row 4-7 adalah header)
        
        // Ambil model untuk BT ini
        $bacaanModel = $this->bacaanModels[$btKey] ?? null;
        $perhitunganModel = $this->perhitunganModels[$btKey] ?? null;
        $scatterModel = $this->scatterModels[$btKey] ?? null;
        
        if (!$bacaanModel || !$perhitunganModel || !$scatterModel) {
            throw new \Exception("Model tidak ditemukan untuk BT: " . $btKey);
        }
        
        // Kelompokkan data berdasarkan tahun
        $groupedData = [];
        foreach ($pengukuranData as $item) {
            $tahun = $item['tahun'];
            if (!isset($groupedData[$tahun])) {
                $groupedData[$tahun] = [];
            }
            $groupedData[$tahun][] = $item;
        }
        
        // Urutkan berdasarkan tahun
        ksort($groupedData);
        
        $dataRowCount = 0;
        foreach ($groupedData as $tahun => $items) {
            // Urutkan berdasarkan periode dan tanggal dalam grup tahun
            usort($items, function($a, $b) {
                $periodeComp = ($a['periode'] ?? '') <=> ($b['periode'] ?? '');
                if ($periodeComp !== 0) {
                    return $periodeComp;
                }
                $dateA = strtotime($a['tanggal'] ?? '');
                $dateB = strtotime($b['tanggal'] ?? '');
                return $dateA - $dateB;
            });
            
            $rowCount = count($items);
            $isFirstRow = true;
            
            foreach ($items as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data dari masing-masing model
                $bacaan = $bacaanModel->getByPengukuran($pid);
                $perhitungan = $perhitunganModel->getByPengukuran($pid);
                $scatter = $scatterModel->getByPengukuran($pid);
                
                // Format tanggal
                $displayDate = $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-';
                
                // TAHUN - ROWSPAN untuk baris pertama dalam grup tahun
                if ($isFirstRow) {
                    $sheet->setCellValue('A' . $row, $tahun);
                    $sheet->mergeCells('A' . $row . ':A' . ($row + $rowCount - 1));
                    $isFirstRow = false;
                }
                
                // PERIODE & TANGGAL - tanpa rowspan
                $sheet->setCellValue('B' . $row, $p['periode'] ?? '-');
                $sheet->setCellValue('C' . $row, $displayDate);
                
                // BACAAN DATA BT
                $sheet->setCellValue('D' . $row, $this->formatNumberExcel($bacaan['US_GP'] ?? null));
                $sheet->setCellValue('E' . $row, $bacaan['US_Arah'] ?? '-');
                $sheet->setCellValue('F' . $row, $this->formatNumberExcel($bacaan['TB_GP'] ?? null));
                $sheet->setCellValue('G' . $row, $bacaan['TB_Arah'] ?? '-');
                
                // PERHITUNGAN DATA BT
                $sheet->setCellValue('H' . $row, $this->formatNumberExcel($perhitungan['A_sec'] ?? null));
                $sheet->setCellValue('I' . $row, $this->formatNumberExcel($perhitungan['sin_A_rad'] ?? null));
                $sheet->setCellValue('J' . $row, $this->formatNumberExcel($perhitungan['B_sec'] ?? null));
                $sheet->setCellValue('K' . $row, $this->formatNumberExcel($perhitungan['sin_B_rad'] ?? null));
                $sheet->setCellValue('L' . $row, $this->formatNumberExcel($perhitungan['sin_C_rad'] ?? null));
                $sheet->setCellValue('M' . $row, $this->formatNumberExcel($perhitungan['sin_C_deg'] ?? null));
                $sheet->setCellValue('N' . $row, $this->formatNumberExcel($perhitungan['Cosa'] ?? null));
                $sheet->setCellValue('O' . $row, $this->formatNumberExcel($perhitungan['a_rad'] ?? null));
                $sheet->setCellValue('P' . $row, $perhitungan['DMS'] ?? '-');
                
                // SCATTER DATA
                $sheet->setCellValue('Q' . $row, $this->formatNumberExcel($scatter['Y_US'] ?? null));
                $sheet->setCellValue('R' . $row, $this->formatNumberExcel($scatter['X_TB'] ?? null));
                $sheet->setCellValue('S' . $row, $this->formatNumberExcel($scatter['Y_cum'] ?? null));
                $sheet->setCellValue('T' . $row, $this->formatNumberExcel($scatter['X_cum'] ?? null));
                
                // Apply styling untuk baris
                $this->applyRowStyleIndividual($sheet, $row, $dataRowCount);
                
                $row++;
                $dataRowCount++;
            }
        }
        
        // ===== STYLING KOLOM =====
        $this->applyBtmColumnStyling($sheet);
        
        // ===== FORMAT ANGKA =====
        if ($row > 8) {
            // Format kolom numerik dengan desimal yang sesuai
            $numericColumns = ['D', 'F', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'Q', 'R', 'S', 'T'];
            foreach ($numericColumns as $col) {
                $range = $col . '8:' . $col . ($row - 1);
                $sheet->getStyle($range)
                    ->getNumberFormat()
                    ->setFormatCode('0.000000000');
            }
            
            // Atur alignment untuk kolom numerik ke kanan
            $numericRange = 'D8:T' . ($row - 1);
            $sheet->getStyle($numericRange)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Atur alignment untuk kolom teks ke center
            $textColumns = ['A', 'B', 'C', 'E', 'G', 'P'];
            foreach ($textColumns as $col) {
                $range = $col . '8:' . $col . ($row - 1);
                $sheet->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // ===== FREEZE PANES =====
        $sheet->freezePane('D8'); // Freeze kolom A-C dan baris 1-7
        
        // ===== FOOTER =====
        $footerRow = $row + 1;
        $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
        $sheet->setCellValue('A' . $footerRow, 'Bubble Tilt Meter ' . strtoupper($btKey) . ' - Sistem Monitoring - PT Indonesia Power');
        $this->applyFooterStyle($sheet, 'A' . $footerRow . ':' . $lastCol . $footerRow);
        $sheet->getRowDimension($footerRow)->setRowHeight(30);
    }
    
    /**
     * Create Table Headers dengan struktur seperti view
     */
    private function createTableHeaders($sheet, $btKey)
    {
        // Row 4: Main Header
        $row = 4;
        
        // Set header untuk TAHUN, PERIODE, TANGGAL dengan rowspan 4
        $sheet->setCellValue('A' . $row, 'TAHUN');
        $sheet->setCellValue('B' . $row, 'PERIODE');
        $sheet->setCellValue('C' . $row, 'TANGGAL');
        
        // Merge cells untuk rowspan 4 (row 4-7)
        $sheet->mergeCells('A' . $row . ':A' . ($row + 3));
        $sheet->mergeCells('B' . $row . ':B' . ($row + 3));
        $sheet->mergeCells('C' . $row . ':C' . ($row + 3));
        
        // Apply style untuk header TAHUN, PERIODE, TANGGAL
        $this->applyMainHeaderStyle($sheet, 'A' . $row . ':A' . ($row + 3), 'FFF8F9FA');
        $this->applyMainHeaderStyle($sheet, 'B' . $row . ':B' . ($row + 3), 'FFF8F9FA');
        $this->applyMainHeaderStyle($sheet, 'C' . $row . ':C' . ($row + 3), 'FFF8F9FA');
        
        // Main Headers untuk bagian lainnya - SESUAI DENGAN VIEW
        $mainHeaders = [
            ['label' => 'BACAAN ' . strtoupper($btKey), 'colspan' => 4, 'rowspan' => 1, 'col' => 'D', 'color' => 'FFE8F4FD'], // bg-reading
            ['label' => 'UTARA-SELATAN', 'colspan' => 2, 'rowspan' => 1, 'col' => 'H', 'color' => 'FFF0F9EB'], // bg-calculation
            ['label' => 'TIMUR-BARAT', 'colspan' => 2, 'rowspan' => 1, 'col' => 'J', 'color' => 'FFF0F9EB'], // bg-calculation
            ['label' => 'INCLINED ANGLE-C', 'colspan' => 2, 'rowspan' => 3, 'col' => 'L', 'color' => 'FFE6F7FF'], // bg-result (rowspan 3)
            ['label' => 'DIPPED DIRECTION OF-C', 'colspan' => 3, 'rowspan' => 3, 'col' => 'N', 'color' => 'FFE6F7FF'], // bg-result (rowspan 3)
            ['label' => 'SCATTER', 'colspan' => 4, 'rowspan' => 3, 'col' => 'Q', 'color' => 'FFFFF2CC'], // bg-scatter (rowspan 3)
        ];
        
        foreach ($mainHeaders as $header) {
            $endRow = $row + $header['rowspan'] - 1;
            $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
            $range = $header['col'] . $row . ':' . $endCol . $endRow;
            
            $sheet->mergeCells($range);
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $this->applyMainHeaderStyle($sheet, $range, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Row 5: Sub Headers
        $row = 5;
        
        $subHeaders = [
            ['label' => 'UTARA-SELATAN', 'colspan' => 2, 'col' => 'D', 'color' => 'FFE8F4FD'], // bagian dari BACAAN
            ['label' => 'TIMUR-BARAT', 'colspan' => 2, 'col' => 'F', 'color' => 'FFE8F4FD'],  // bagian dari BACAAN
            ['label' => 'INCLINED ANGLE-A', 'colspan' => 2, 'rowspan' => 2, 'col' => 'H', 'color' => 'FFF0F9EB'], // bg-calculation (rowspan 2)
            ['label' => 'INCLINED ANGLE-B', 'colspan' => 2, 'rowspan' => 2, 'col' => 'J', 'color' => 'FFF0F9EB']  // bg-calculation (rowspan 2)
        ];
        
        foreach ($subHeaders as $header) {
            $endRow = $row + (isset($header['rowspan']) ? $header['rowspan'] : 1) - 1;
            $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
            $range = $header['col'] . $row . ':' . $endCol . $endRow;
            
            $sheet->mergeCells($range);
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $this->applySubHeaderStyle($sheet, $range, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(22);
        
        // Row 6: Measurement Headers
        $row = 6;
        
        $measurementHeaders = [
            ['label' => 'GP & ARAH', 'colspan' => 2, 'col' => 'D', 'color' => 'FFE8F4FD'],
            ['label' => 'GP & ARAH', 'colspan' => 2, 'col' => 'F', 'color' => 'FFE8F4FD']
        ];
        
        foreach ($measurementHeaders as $header) {
            $endCol = $this->nextColumn($header['col'], $header['colspan'] - 1);
            $range = $header['col'] . $row . ':' . $endCol . $row;
            
            $sheet->mergeCells($range);
            $sheet->setCellValue($header['col'] . $row, $header['label']);
            
            $this->applyMeasurementHeaderStyle($sheet, $range, $header['color']);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(20);
        
        // Row 7: Column Headers Detail
        $row = 7;
        
        $headersRow7 = [
            'D' => 'US GP',
            'E' => 'US Arah',
            'F' => 'TB GP',
            'G' => 'TB Arah',
            'H' => 'A_sec',
            'I' => 'sin_A_rad',
            'J' => 'B_sec',
            'K' => 'sin_B_rad',
            'L' => 'sin_C_rad',
            'M' => 'sin_C_deg',
            'N' => 'Cos α',
            'O' => 'α (Rad)',
            'P' => 'DMS',
            'Q' => 'Y (U-S)',
            'R' => 'X (T-B)',
            'S' => 'Y (cum)',
            'T' => 'X (cum)'
        ];
        
        // Background colors untuk setiap kolom (sesuai dengan section view)
        $columnColors = [
            'D' => 'FFE8F4FD', 'E' => 'FFE8F4FD',                     // Bacaan US - Light blue (bg-reading)
            'F' => 'FFE8F4FD', 'G' => 'FFE8F4FD',                     // Bacaan TB - Light blue (bg-reading)
            'H' => 'FFF0F9EB', 'I' => 'FFF0F9EB',                     // Perhitungan US - Light green (bg-calculation)
            'J' => 'FFF0F9EB', 'K' => 'FFF0F9EB',                     // Perhitungan TB - Light green (bg-calculation)
            'L' => 'FFE6F7FF', 'M' => 'FFE6F7FF',                     // Hasil - Light cyan (bg-result)
            'N' => 'FFE6F7FF', 'O' => 'FFE6F7FF', 'P' => 'FFE6F7FF',  // Hasil - Light cyan (bg-result)
            'Q' => 'FFFFF2CC', 'R' => 'FFFFF2CC',                     // Scatter - Light yellow (bg-scatter)
            'S' => 'FFFFF2CC', 'T' => 'FFFFF2CC'                      // Scatter - Light yellow (bg-scatter)
        ];
        
        foreach ($headersRow7 as $col => $value) {
            $sheet->setCellValue($col . $row, $value);
            
            $color = $columnColors[$col] ?? 'FFFFFFFF';
            $textColor = 'FF000000';
            
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => $textColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]
            ]);
        }
        
        $sheet->getRowDimension($row)->setRowHeight(18);
    }
    
    /**
     * Format number untuk display di Excel
     */
    private function formatNumberExcel($value)
    {
        if ($value === null || $value === '' || $value === false) {
            return '-';
        }
        
        if (is_numeric($value)) {
            $floatValue = (float)$value;
            
            // Format dengan 9 digit desimal
            $formatted = number_format($floatValue, 9, '.', '');
            
            // Hapus trailing zeros setelah decimal point
            $formatted = preg_replace('/(\.\d*?)0+$/', '$1', $formatted);
            
            // Hapus decimal point jika tidak ada digit setelahnya
            $formatted = rtrim($formatted, '.');
            
            // Jika setelah pembersihan string kosong, return 0
            if ($formatted === '' || $formatted === '-') {
                return '0';
            }
            
            return $formatted;
        }
        
        return $value;
    }
    
    /**
     * Apply title style
     */
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
                'startColor' => ['argb' => 'FF2F75B5']
            ]
        ]);
    }
    
    /**
     * Apply subtitle style
     */
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
    
    /**
     * Apply main header style
     */
    private function applyMainHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => 'FF000000']
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
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);
    }
    
    /**
     * Apply sub header style
     */
    private function applySubHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['argb' => 'FF000000']
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
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);
    }
    
    /**
     * Apply measurement header style
     */
    private function applyMeasurementHeaderStyle($sheet, $range, $color)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 8,
                'color' => ['argb' => 'FF000000']
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
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);
    }
    
    /**
     * Apply row style untuk sheet konsolidasi
     */
    private function applyRowStyleConsolidated($sheet, $row, $globalIndex)
    {
        $range = 'A' . $row . ':T' . $row;
        
        // Base style
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Zebra stripes (alternating row colors)
        if ($globalIndex % 2 == 0) {
            $sheet->getStyle($range)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFF8F9FA');
        }
        
        // Set row height
        $sheet->getRowDimension($row)->setRowHeight(20);
    }
    
    /**
     * Apply row style untuk sheet individual
     */
    private function applyRowStyleIndividual($sheet, $row, $globalIndex)
    {
        $range = 'A' . $row . ':T' . $row;
        
        // Base style
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Zebra stripes (alternating row colors)
        if ($globalIndex % 2 == 0) {
            $sheet->getStyle($range)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFF8F9FA');
        }
        
        // Set row height
        $sheet->getRowDimension($row)->setRowHeight(20);
    }
    
    /**
     * Apply column styling untuk sheet konsolidasi
     */
    private function applyConsolidatedColumnStyling($sheet)
    {
        $columnWidths = [
            'A' => 10,  // TAHUN
            'B' => 12,  // PERIODE
            'C' => 15,  // TANGGAL
            'D' => 15,  // US GP
            'E' => 12,  // US Arah
            'F' => 15,  // TB GP
            'G' => 12,  // TB Arah
            'H' => 15,  // A_sec
            'I' => 15,  // sin_A_rad
            'J' => 15,  // B_sec
            'K' => 15,  // sin_B_rad
            'L' => 15,  // sin_C_rad
            'M' => 15,  // sin_C_deg
            'N' => 12,  // Cos α
            'O' => 15,  // α (Rad)
            'P' => 12,  // DMS
            'Q' => 15,  // Y (U-S)
            'R' => 15,  // X (T-B)
            'S' => 15,  // Y (cum)
            'T' => 15   // X (cum)
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
    
    /**
     * Apply column styling untuk sheet BTM individual
     */
    private function applyBtmColumnStyling($sheet)
    {
        $columnWidths = [
            'A' => 10,  // TAHUN
            'B' => 12,  // PERIODE
            'C' => 15,  // TANGGAL
            'D' => 15,  // US GP
            'E' => 12,  // US Arah
            'F' => 15,  // TB GP
            'G' => 12,  // TB Arah
            'H' => 15,  // A_sec
            'I' => 15,  // sin_A_rad
            'J' => 15,  // B_sec
            'K' => 15,  // sin_B_rad
            'L' => 15,  // sin_C_rad
            'M' => 15,  // sin_C_deg
            'N' => 12,  // Cos α
            'O' => 15,  // α (Rad)
            'P' => 12,  // DMS
            'Q' => 15,  // Y (U-S)
            'R' => 15,  // X (T-B)
            'S' => 15,  // Y (cum)
            'T' => 15   // X (cum)
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
    
    /**
     * Apply footer style
     */
    private function applyFooterStyle($sheet, $range)
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
    
    /**
     * Export filtered data (untuk AJAX dari frontend)
     */
    public function exportFiltered()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }
        
        try {
            $filterData = $this->request->getJSON(true);
            
            // Kembalikan URL untuk download dengan parameter filter
            $url = base_url('btm/export-excel/export');
            $params = [];
            
            if (!empty($filterData['tahun'])) {
                $params[] = 'tahun=' . urlencode($filterData['tahun']);
            }
            
            if (!empty($filterData['periode'])) {
                $params[] = 'periode=' . urlencode($filterData['periode']);
            }
            
            if (!empty($filterData['bt'])) {
                $params[] = 'bt=' . urlencode($filterData['bt']);
            }
            
            if (!empty($params)) {
                $url .= '?' . implode('&', $params);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data siap diexport',
                'download_url' => $url
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting filtered data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test function untuk debugging
     */
    public function test()
    {
        echo "<h1>Test Export Excel Controller BTM</h1>";
        echo "<p>URL untuk test export: " . base_url('btm/export-excel/export') . "</p>";
        echo "<p>Test dengan filter: " . base_url('btm/export-excel/export?tahun=2023&periode=1&bt=bt1') . "</p>";
        
        // Test model connection
        try {
            $model = new PengukuranBtmModel();
            $count = $model->countAll();
            echo "<p>Total Data in PengukuranBtmModel: " . $count . "</p>";
        } catch (\Exception $e) {
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    }
}