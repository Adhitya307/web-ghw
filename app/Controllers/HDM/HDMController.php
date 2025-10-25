<?php

namespace App\Controllers\HDM;

use App\Controllers\BaseController;
use App\Models\HDM\MPengukuranHdm;
use App\Models\HDM\MPembacaanElv625;
use App\Models\HDM\MPembacaanElv600;
use App\Models\HDM\MPergerakanElv625;
use App\Models\HDM\MPergerakanElv600;
use App\Models\HDM\MInitialReadingElv625;
use App\Models\HDM\MInitialReadingElv600;
use App\Models\HDM\DepthElv625Model;
use App\Models\HDM\DepthElv600Model;
use CodeIgniter\HTTP\ResponseInterface;

class HDMController extends BaseController
{
    protected $pengukuranModel;
    protected $pembacaanElv625Model;
    protected $pembacaanElv600Model;
    protected $pergerakanElv625Model;
    protected $pergerakanElv600Model;
    protected $initialReadingElv625Model;
    protected $initialReadingElv600Model;
    protected $depthElv625Model;
    protected $depthElv600Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        try {
            // Initialize models
            $this->pengukuranModel = new MPengukuranHdm();
            $this->pembacaanElv625Model = new MPembacaanElv625();
            $this->pembacaanElv600Model = new MPembacaanElv600();
            $this->pergerakanElv625Model = new MPergerakanElv625();
            $this->pergerakanElv600Model = new MPergerakanElv600();
            $this->initialReadingElv625Model = new MInitialReadingElv625();
            $this->initialReadingElv600Model = new MInitialReadingElv600();
            $this->depthElv625Model = new DepthElv625Model();
            $this->depthElv600Model = new DepthElv600Model();
        } catch (\Exception $e) {
            die("Error loading models: " . $e->getMessage());
        }
    }

    public function index()
    {
        // Get all data untuk filter dropdown dan tabel
        $pengukuran = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                           ->orderBy('tanggal', 'DESC')
                                           ->findAll();

        // Get semua data untuk ditampilkan di tabel
        $dataWithReadings = [];
        foreach ($pengukuran as $p) {
            $pid = $p['id_pengukuran'];
            
            $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $pid)->first();
            $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $pid)->first();
            $pergerakanElv600 = $this->pergerakanElv600Model->where('id_pengukuran', $pid)->first();
            $pergerakanElv625 = $this->pergerakanElv625Model->where('id_pengukuran', $pid)->first();
            $initialReadingElv600 = $this->initialReadingElv600Model->where('id_pengukuran', $pid)->first();
            $initialReadingElv625 = $this->initialReadingElv625Model->where('id_pengukuran', $pid)->first();
            $depthElv600 = $this->depthElv600Model->where('id_pengukuran', $pid)->first();
            $depthElv625 = $this->depthElv625Model->where('id_pengukuran', $pid)->first();
            
            $dataWithReadings[] = [
                'pengukuran' => $p,
                'pembacaan_elv600' => $pembacaanElv600 ?: [],
                'pembacaan_elv625' => $pembacaanElv625 ?: [],
                'pergerakan_elv600' => $pergerakanElv600 ?: [],
                'pergerakan_elv625' => $pergerakanElv625 ?: [],
                'initial_reading_elv600' => $initialReadingElv600 ?: [],
                'initial_reading_elv625' => $initialReadingElv625 ?: [],
                'depth_elv600' => $depthElv600 ?: [],
                'depth_elv625' => $depthElv625 ?: []
            ];
        }

        $data = [
            'title' => 'Horizontal Displacement Meter - PT Indonesia Power',
            'pageTitle' => 'Data Horizontal Displacement Meter',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'HDM' => ''
            ],
            'pengukuran' => $pengukuran,
            'dataWithReadings' => $dataWithReadings
        ];

        return view('hdm/index', $data);
    }

    /**
     * Tampilkan data lengkap HDM
     */
    public function dataLengkap()
    {
        // Get all data
        $pengukuran = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                           ->orderBy('tanggal', 'DESC')
                                           ->findAll();

        // Get semua data untuk ditampilkan di tabel lengkap
        $dataLengkap = [];
        foreach ($pengukuran as $p) {
            $pid = $p['id_pengukuran'];
            
            $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $pid)->first();
            $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $pid)->first();
            $pergerakanElv600 = $this->pergerakanElv600Model->where('id_pengukuran', $pid)->first();
            $pergerakanElv625 = $this->pergerakanElv625Model->where('id_pengukuran', $pid)->first();
            $initialReadingElv600 = $this->initialReadingElv600Model->where('id_pengukuran', $pid)->first();
            $initialReadingElv625 = $this->initialReadingElv625Model->where('id_pengukuran', $pid)->first();
            $depthElv600 = $this->depthElv600Model->where('id_pengukuran', $pid)->first();
            $depthElv625 = $this->depthElv625Model->where('id_pengukuran', $pid)->first();
            
            $dataLengkap[] = [
                'pengukuran' => $p,
                'pembacaan_elv600' => $pembacaanElv600 ?: [],
                'pembacaan_elv625' => $pembacaanElv625 ?: [],
                'pergerakan_elv600' => $pergerakanElv600 ?: [],
                'pergerakan_elv625' => $pergerakanElv625 ?: [],
                'initial_reading_elv600' => $initialReadingElv600 ?: [],
                'initial_reading_elv625' => $initialReadingElv625 ?: [],
                'depth_elv600' => $depthElv600 ?: [],
                'depth_elv625' => $depthElv625 ?: []
            ];
        }

        $data = [
            'title' => 'Data Lengkap HDM - PT Indonesia Power',
            'pageTitle' => 'Data Lengkap Horizontal Displacement Meter',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'HDM' => base_url('horizontal-displacement'),
                'Data Lengkap' => ''
            ],
            'dataLengkap' => $dataLengkap
        ];

        return view('hdm/data_lengkap', $data);
    }

    /**
     * Get data untuk AJAX
     */
    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        try {
            $pengukuran = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();

            $dataWithReadings = [];
            foreach ($pengukuran as $p) {
                $pid = $p['id_pengukuran'];
                
                $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $pid)->first();
                $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $pid)->first();
                $pergerakanElv600 = $this->pergerakanElv600Model->where('id_pengukuran', $pid)->first();
                $pergerakanElv625 = $this->pergerakanElv625Model->where('id_pengukuran', $pid)->first();
                $initialReadingElv600 = $this->initialReadingElv600Model->where('id_pengukuran', $pid)->first();
                $initialReadingElv625 = $this->initialReadingElv625Model->where('id_pengukuran', $pid)->first();
                $depthElv600 = $this->depthElv600Model->where('id_pengukuran', $pid)->first();
                $depthElv625 = $this->depthElv625Model->where('id_pengukuran', $pid)->first();
                
                $dataWithReadings[] = [
                    'pengukuran' => $p,
                    'pembacaan_elv600' => $pembacaanElv600 ?: [],
                    'pembacaan_elv625' => $pembacaanElv625 ?: [],
                    'pergerakan_elv600' => $pergerakanElv600 ?: [],
                    'pergerakan_elv625' => $pergerakanElv625 ?: [],
                    'initial_reading_elv600' => $initialReadingElv600 ?: [],
                    'initial_reading_elv625' => $initialReadingElv625 ?: [],
                    'depth_elv600' => $depthElv600 ?: [],
                    'depth_elv625' => $depthElv625 ?: []
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $dataWithReadings
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Detail data HDM
     */
    public function detail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        try {
            $pengukuran = $this->pengukuranModel->find($id);
            if (!$pengukuran) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Data tidak ditemukan'
                ]);
            }

            $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $id)->first();
            $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $id)->first();
            $pergerakanElv600 = $this->pergerakanElv600Model->where('id_pengukuran', $id)->first();
            $pergerakanElv625 = $this->pergerakanElv625Model->where('id_pengukuran', $id)->first();
            $initialReadingElv600 = $this->initialReadingElv600Model->where('id_pengukuran', $id)->first();
            $initialReadingElv625 = $this->initialReadingElv625Model->where('id_pengukuran', $id)->first();
            $depthElv600 = $this->depthElv600Model->where('id_pengukuran', $id)->first();
            $depthElv625 = $this->depthElv625Model->where('id_pengukuran', $id)->first();

            $data = [
                'pengukuran' => $pengukuran,
                'pembacaan_elv600' => $pembacaanElv600 ?: [],
                'pembacaan_elv625' => $pembacaanElv625 ?: [],
                'pergerakan_elv600' => $pergerakanElv600 ?: [],
                'pergerakan_elv625' => $pergerakanElv625 ?: [],
                'initial_reading_elv600' => $initialReadingElv600 ?: [],
                'initial_reading_elv625' => $initialReadingElv625 ?: [],
                'depth_elv600' => $depthElv600 ?: [],
                'depth_elv625' => $depthElv625 ?: []
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Excel - VERSI CODEIGNITER 4 (TANPA KOLOM AKSI)
     */
    public function exportExcel()
    {
        try {
            $pengukuran = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();

            $data = [];
            
            // Header Excel (TANPA AKSI)
            $headers = [
                'Tahun', 'Periode', 'Tanggal', 'DMA',
                // ELV 625 - Pembacaan HDM
                'ELV625_HV1', 'ELV625_HV2', 'ELV625_HV3',
                // ELV 600 - Pembacaan HDM  
                'ELV600_HV1', 'ELV600_HV2', 'ELV600_HV3', 'ELV600_HV4', 'ELV600_HV5',
                // ELV 625 - Depth (S)
                'ELV625_Depth_HV1', 'ELV625_Depth_HV2', 'ELV625_Depth_HV3',
                // ELV 600 - Depth (S)
                'ELV600_Depth_HV1', 'ELV600_Depth_HV2', 'ELV600_Depth_HV3', 'ELV600_Depth_HV4', 'ELV600_Depth_HV5',
                // ELV 625 - Readings (S)
                'ELV625_Readings_HV1', 'ELV625_Readings_HV2', 'ELV625_Readings_HV3',
                // ELV 600 - Readings (S)
                'ELV600_Readings_HV1', 'ELV600_Readings_HV2', 'ELV600_Readings_HV3', 'ELV600_Readings_HV4', 'ELV600_Readings_HV5',
                // ELV 625 - Pergerakan (CM)
                'ELV625_Pergerakan_HV1', 'ELV625_Pergerakan_HV2', 'ELV625_Pergerakan_HV3',
                // ELV 600 - Pergerakan (CM)
                'ELV600_Pergerakan_HV1', 'ELV600_Pergerakan_HV2', 'ELV600_Pergerakan_HV3', 'ELV600_Pergerakan_HV4', 'ELV600_Pergerakan_HV5'
                // AKSI DIHAPUS
            ];

            foreach ($pengukuran as $p) {
                $pid = $p['id_pengukuran'];
                
                $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $pid)->first();
                $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $pid)->first();
                $pergerakanElv600 = $this->pergerakanElv600Model->where('id_pengukuran', $pid)->first();
                $pergerakanElv625 = $this->pergerakanElv625Model->where('id_pengukuran', $pid)->first();
                $initialReadingElv600 = $this->initialReadingElv600Model->where('id_pengukuran', $pid)->first();
                $initialReadingElv625 = $this->initialReadingElv625Model->where('id_pengukuran', $pid)->first();
                $depthElv600 = $this->depthElv600Model->where('id_pengukuran', $pid)->first();
                $depthElv625 = $this->depthElv625Model->where('id_pengukuran', $pid)->first();

                $row = [
                    // Data dasar
                    'Tahun' => $p['tahun'] ?? '-',
                    'Periode' => $p['periode'] ?? '-',
                    'Tanggal' => $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-',
                    'DMA' => $p['dma'] ?? '-',
                    
                    // ELV 625 - Pembacaan HDM
                    'ELV625_HV1' => $pembacaanElv625['hv_1'] ?? '-',
                    'ELV625_HV2' => $pembacaanElv625['hv_2'] ?? '-',
                    'ELV625_HV3' => $pembacaanElv625['hv_3'] ?? '-',
                    
                    // ELV 600 - Pembacaan HDM
                    'ELV600_HV1' => $pembacaanElv600['hv_1'] ?? '-',
                    'ELV600_HV2' => $pembacaanElv600['hv_2'] ?? '-',
                    'ELV600_HV3' => $pembacaanElv600['hv_3'] ?? '-',
                    'ELV600_HV4' => $pembacaanElv600['hv_4'] ?? '-',
                    'ELV600_HV5' => $pembacaanElv600['hv_5'] ?? '-',
                    
                    // ELV 625 - Depth (S)
                    'ELV625_Depth_HV1' => $depthElv625['hv_1'] ?? '-',
                    'ELV625_Depth_HV2' => $depthElv625['hv_2'] ?? '-',
                    'ELV625_Depth_HV3' => $depthElv625['hv_3'] ?? '-',
                    
                    // ELV 600 - Depth (S)
                    'ELV600_Depth_HV1' => $depthElv600['hv_1'] ?? '-',
                    'ELV600_Depth_HV2' => $depthElv600['hv_2'] ?? '-',
                    'ELV600_Depth_HV3' => $depthElv600['hv_3'] ?? '-',
                    'ELV600_Depth_HV4' => $depthElv600['hv_4'] ?? '-',
                    'ELV600_Depth_HV5' => $depthElv600['hv_5'] ?? '-',
                    
                    // ELV 625 - Readings (S)
                    'ELV625_Readings_HV1' => $initialReadingElv625['hv_1'] ?? '-',
                    'ELV625_Readings_HV2' => $initialReadingElv625['hv_2'] ?? '-',
                    'ELV625_Readings_HV3' => $initialReadingElv625['hv_3'] ?? '-',
                    
                    // ELV 600 - Readings (S)
                    'ELV600_Readings_HV1' => $initialReadingElv600['hv_1'] ?? '-',
                    'ELV600_Readings_HV2' => $initialReadingElv600['hv_2'] ?? '-',
                    'ELV600_Readings_HV3' => $initialReadingElv600['hv_3'] ?? '-',
                    'ELV600_Readings_HV4' => $initialReadingElv600['hv_4'] ?? '-',
                    'ELV600_Readings_HV5' => $initialReadingElv600['hv_5'] ?? '-',
                    
                    // ELV 625 - Pergerakan (CM)
                    'ELV625_Pergerakan_HV1' => $pergerakanElv625['hv_1'] ?? '-',
                    'ELV625_Pergerakan_HV2' => $pergerakanElv625['hv_2'] ?? '-',
                    'ELV625_Pergerakan_HV3' => $pergerakanElv625['hv_3'] ?? '-',
                    
                    // ELV 600 - Pergerakan (CM)
                    'ELV600_Pergerakan_HV1' => $pergerakanElv600['hv_1'] ?? '-',
                    'ELV600_Pergerakan_HV2' => $pergerakanElv600['hv_2'] ?? '-',
                    'ELV600_Pergerakan_HV3' => $pergerakanElv600['hv_3'] ?? '-',
                    'ELV600_Pergerakan_HV4' => $pergerakanElv600['hv_4'] ?? '-',
                    'ELV600_Pergerakan_HV5' => $pergerakanElv600['hv_5'] ?? '-'
                    // AKSI DIHAPUS
                ];
                
                $data[] = $row;
            }

            // Load library PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title
            $sheet->setTitle('Data HDM');
            
            // Set header row
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }
            
            // Set data rows
            $row = 2;
            foreach ($data as $item) {
                $col = 'A';
                foreach ($headers as $header) {
                    $value = $item[$header] ?? '-';
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', chr(ord('A') + count($headers) - 1)) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Set headers untuk download
            $filename = 'data_horizontal_displacement_meter_' . date('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Output file
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Data HDM - PT Indonesia Power',
            'pageTitle' => 'Tambah Data Horizontal Displacement Meter',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'HDM' => base_url('horizontal-displacement'),
                'Tambah Data' => ''
            ]
        ];

        return view('hdm/create', $data);
    }

    /**
     * Store data - VERSI FINAL YANG BERHASIL
     */
    public function store()
    {
        try {
            $postData = $this->request->getPost();

            // Validasi input dasar
            if (empty($postData['tahun']) || empty($postData['periode']) || empty($postData['tanggal']) || empty($postData['dma'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun, Periode, Tanggal, dan DMA harus diisi'
                ]);
            }

            // Cek duplikat
            $existing = $this->pengukuranModel->where('tahun', $postData['tahun'])
                                             ->where('periode', $postData['periode'])
                                             ->where('tanggal', $postData['tanggal'])
                                             ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data dengan tahun, periode, dan tanggal tersebut sudah ada'
                ]);
            }

            // Simpan data pengukuran
            $pengukuranData = [
                'tahun' => $postData['tahun'],
                'periode' => $postData['periode'],
                'tanggal' => $postData['tanggal'],
                'dma' => (float) $postData['dma']
            ];

            $pengukuranId = $this->pengukuranModel->insert($pengukuranData);

            if (!$pengukuranId) {
                throw new \Exception('Gagal menyimpan data pengukuran');
            }

            // Simpan data pembacaan ELV 625
            $elv625Data = [
                'id_pengukuran' => $pengukuranId,
                'hv_1' => !empty($postData['elv625_hv1']) ? (float) $postData['elv625_hv1'] : 0,
                'hv_2' => !empty($postData['elv625_hv2']) ? (float) $postData['elv625_hv2'] : 0,
                'hv_3' => !empty($postData['elv625_hv3']) ? (float) $postData['elv625_hv3'] : 0
            ];
            
            if (!$this->pembacaanElv625Model->insert($elv625Data)) {
                throw new \Exception('Gagal menyimpan data ELV 625');
            }

            // Simpan data pembacaan ELV 600
            $elv600Data = [
                'id_pengukuran' => $pengukuranId,
                'hv_1' => !empty($postData['elv600_hv1']) ? (float) $postData['elv600_hv1'] : 0,
                'hv_2' => !empty($postData['elv600_hv2']) ? (float) $postData['elv600_hv2'] : 0,
                'hv_3' => !empty($postData['elv600_hv3']) ? (float) $postData['elv600_hv3'] : 0,
                'hv_4' => !empty($postData['elv600_hv4']) ? (float) $postData['elv600_hv4'] : 0,
                'hv_5' => !empty($postData['elv600_hv5']) ? (float) $postData['elv600_hv5'] : 0
            ];
            
            if (!$this->pembacaanElv600Model->insert($elv600Data)) {
                throw new \Exception('Gagal menyimpan data ELV 600');
            }

            // Insert data default
            $this->insertDefaultData($pengukuranId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'redirect' => base_url('horizontal-displacement')
            ]);

        } catch (\Exception $e) {
            // Rollback jika ada error
            if (isset($pengukuranId)) {
                $this->pengukuranModel->delete($pengukuranId);
                $this->pembacaanElv625Model->where('id_pengukuran', $pengukuranId)->delete();
                $this->pembacaanElv600Model->where('id_pengukuran', $pengukuranId)->delete();
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check duplicate - VERSI SIMPLE
     */
    public function checkDuplicate()
    {
        try {
            $tahun = $this->request->getPost('tahun');
            $periode = $this->request->getPost('periode');
            $tanggal = $this->request->getPost('tanggal');
            $current_id = $this->request->getPost('current_id');

            if (!$tahun || !$periode || !$tanggal) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            $query = $this->pengukuranModel->where('tahun', $tahun)
                                         ->where('periode', $periode)
                                         ->where('tanggal', $tanggal);

            // Jika ada current_id (saat edit), exclude data yang sedang diedit
            if ($current_id) {
                $query->where('id_pengukuran !=', $current_id);
            }

            $existing = $query->first();

            return $this->response->setJSON([
                'success' => true,
                'isDuplicate' => $existing !== null,
                'message' => $existing ? 'Data sudah ada' : 'Data belum ada'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Insert data default (initial reading dan depth)
     */
    private function insertDefaultData($pengukuran_id)
    {
        try {
            // Insert initial reading
            $this->initialReadingElv625Model->insertReading($pengukuran_id);
            $this->initialReadingElv600Model->insertReading($pengukuran_id);

            // Insert depth
            $this->depthElv625Model->insertDefault($pengukuran_id);
            $this->depthElv600Model->insertDefault($pengukuran_id);

            // Hitung pergerakan setelah semua data tersimpan
            $this->pergerakanElv625Model->hitungPergerakan($pengukuran_id);
            $this->pergerakanElv600Model->hitungPergerakan($pengukuran_id);
            
        } catch (\Exception $e) {
            log_message('error', 'Gagal insert default data: ' . $e->getMessage());
        }
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        try {
            // Ambil data pengukuran
            $pengukuran = $this->pengukuranModel->find($id);
            if (!$pengukuran) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }

            // Ambil data terkait
            $pembacaanElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $id)->first();
            $pembacaanElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $id)->first();

            $data = [
                'title' => 'Edit Data HDM - PT Indonesia Power',
                'pageTitle' => 'Edit Data Horizontal Displacement Meter',
                'breadcrumbs' => [
                    'Dashboard' => base_url(),
                    'HDM' => base_url('horizontal-displacement'),
                    'Edit Data' => ''
                ],
                'pengukuran' => $pengukuran,
                'pembacaanElv600' => $pembacaanElv600 ?: [],
                'pembacaanElv625' => $pembacaanElv625 ?: []
            ];

            return view('hdm/edit', $data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update data dengan deteksi perubahan
     */
    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        try {
            $postData = $this->request->getPost();

            // Validasi input dasar
            if (empty($postData['tahun']) || empty($postData['periode']) || empty($postData['tanggal']) || empty($postData['dma'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun, Periode, Tanggal, dan DMA harus diisi'
                ]);
            }

            // Cek apakah data pengukuran ada
            $pengukuran = $this->pengukuranModel->find($id);
            if (!$pengukuran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // Cek duplikat (kecuali dengan data yang sedang diupdate)
            $existing = $this->pengukuranModel
                ->where('tahun', $postData['tahun'])
                ->where('periode', $postData['periode'])
                ->where('tanggal', $postData['tanggal'])
                ->where('id_pengukuran !=', $id)
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data dengan tahun, periode, dan tanggal tersebut sudah ada'
                ]);
            }

            // Simpan data lama untuk pengecekan perubahan
            $oldData = [
                'pengukuran' => $pengukuran,
                'pembacaanElv625' => $this->pembacaanElv625Model->where('id_pengukuran', $id)->first(),
                'pembacaanElv600' => $this->pembacaanElv600Model->where('id_pengukuran', $id)->first()
            ];

            // Update data pengukuran
            $pengukuranData = [
                'tahun' => $postData['tahun'],
                'periode' => $postData['periode'],
                'tanggal' => $postData['tanggal'],
                'dma' => (float) $postData['dma']
            ];

            $this->pengukuranModel->update($id, $pengukuranData);

            // Update data pembacaan ELV 625
            $elv625Data = [
                'hv_1' => !empty($postData['elv625_hv1']) ? (float) $postData['elv625_hv1'] : 0,
                'hv_2' => !empty($postData['elv625_hv2']) ? (float) $postData['elv625_hv2'] : 0,
                'hv_3' => !empty($postData['elv625_hv3']) ? (float) $postData['elv625_hv3'] : 0
            ];
            
            $this->pembacaanElv625Model->where('id_pengukuran', $id)->update(null, $elv625Data);

            // Update data pembacaan ELV 600
            $elv600Data = [
                'hv_1' => !empty($postData['elv600_hv1']) ? (float) $postData['elv600_hv1'] : 0,
                'hv_2' => !empty($postData['elv600_hv2']) ? (float) $postData['elv600_hv2'] : 0,
                'hv_3' => !empty($postData['elv600_hv3']) ? (float) $postData['elv600_hv3'] : 0,
                'hv_4' => !empty($postData['elv600_hv4']) ? (float) $postData['elv600_hv4'] : 0,
                'hv_5' => !empty($postData['elv600_hv5']) ? (float) $postData['elv600_hv5'] : 0
            ];
            
            $this->pembacaanElv600Model->where('id_pengukuran', $id)->update(null, $elv600Data);

            // Cek apakah ada perubahan pada data pembacaan
            $isDataChanged = $this->isDataChanged($oldData, $elv625Data, $elv600Data);

            // Jika ada perubahan, update pergerakan
            if ($isDataChanged) {
                $this->pergerakanElv625Model->hitungPergerakan($id);
                $this->pergerakanElv600Model->hitungPergerakan($id);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'redirect' => base_url('horizontal-displacement')
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cek apakah ada perubahan data
     */
    private function isDataChanged($oldData, $newElv625, $newElv600)
    {
        // Cek perubahan pada data dasar
        if ($oldData['pengukuran']['tahun'] != $this->request->getPost('tahun') ||
            $oldData['pengukuran']['periode'] != $this->request->getPost('periode') ||
            $oldData['pengukuran']['tanggal'] != $this->request->getPost('tanggal') ||
            (float)$oldData['pengukuran']['dma'] != (float)$this->request->getPost('dma')) {
            return true;
        }

        // Cek perubahan pada ELV 625
        if ($oldData['pembacaanElv625']) {
            foreach (['hv_1', 'hv_2', 'hv_3'] as $field) {
                if ((float)$oldData['pembacaanElv625'][$field] != (float)$newElv625[$field]) {
                    return true;
                }
            }
        }

        // Cek perubahan pada ELV 600
        if ($oldData['pembacaanElv600']) {
            foreach (['hv_1', 'hv_2', 'hv_3', 'hv_4', 'hv_5'] as $field) {
                if ((float)$oldData['pembacaanElv600'][$field] != (float)$newElv600[$field]) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Delete data
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        try {
            // Hapus semua data terkait
            $this->pengukuranModel->delete($id);
            $this->pembacaanElv625Model->where('id_pengukuran', $id)->delete();
            $this->pembacaanElv600Model->where('id_pengukuran', $id)->delete();
            $this->pergerakanElv625Model->where('id_pengukuran', $id)->delete();
            $this->pergerakanElv600Model->where('id_pengukuran', $id)->delete();
            $this->initialReadingElv625Model->where('id_pengukuran', $id)->delete();
            $this->initialReadingElv600Model->where('id_pengukuran', $id)->delete();
            $this->depthElv625Model->where('id_pengukuran', $id)->delete();
            $this->depthElv600Model->where('id_pengukuran', $id)->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * IMPORT SQL - VERSI FIXED
     */
    /**
 * IMPORT SQL - VERSI FIXED DENGAN VALIDASI LENGKAP
 */
public function importSQL()
{
    // Validasi AJAX request
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(405)->setJSON([
            'success' => false, 
            'message' => 'Method not allowed. Hanya AJAX request yang diizinkan.'
        ]);
    }

    try {
        // Validasi method POST
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya metode POST yang diizinkan'
            ]);
        }

        // Debug: Cek apakah file ada
        if (empty($_FILES)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada file yang diupload. Pastikan form menggunakan enctype="multipart/form-data"'
            ]);
        }

        // Validasi file upload
        $sqlFile = $this->request->getFile('sql_file');
        
        if (!$sqlFile) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File SQL tidak ditemukan dalam request. Pastikan name="sql_file"'
            ]);
        }

        // Validasi apakah file berhasil diupload
        if (!$sqlFile->isValid()) {
            $errorMessage = $sqlFile->getErrorString();
            if (empty($errorMessage)) {
                $errorMessage = 'File upload gagal. Pastikan ukuran file tidak melebihi limit server.';
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage
            ]);
        }

        // Validasi ekstensi file
        $originalName = $sqlFile->getClientName();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        if (strtolower($extension) !== 'sql') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File harus berekstensi .sql. File yang diupload: ' . $originalName
            ]);
        }

        // Validasi ukuran file (max 50MB)
        if ($sqlFile->getSize() > 50 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ukuran file maksimal 50MB. File Anda: ' . round($sqlFile->getSize() / 1024 / 1024, 2) . 'MB'
            ]);
        }

        if ($sqlFile->getSize() == 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File kosong (0 byte)'
            ]);
        }

        // Baca isi file SQL
        $sqlContent = file_get_contents($sqlFile->getTempName());
        
        if (empty($sqlContent)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File SQL kosong atau tidak dapat dibaca'
            ]);
        }

        // Deteksi encoding dan convert ke UTF-8 jika perlu
        $encoding = mb_detect_encoding($sqlContent, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($encoding !== 'UTF-8') {
            $sqlContent = mb_convert_encoding($sqlContent, 'UTF-8', $encoding);
        }

        // Hapus BOM jika ada
        $sqlContent = preg_replace('/^\xEF\xBB\xBF/', '', $sqlContent);

        // Pisahkan query SQL
        $queries = $this->splitSQLQueries($sqlContent);
        
        if (empty($queries)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada query SQL yang valid ditemukan dalam file'
            ]);
        }

        // Setup database
        $db = \Config\Database::connect();
        $stats = [
            'total' => count($queries),
            'success' => 0,
            'failed' => 0,
            'affected_rows' => 0,
            'errors' => []
        ];

        // Mulai transaction
        $db->transStart();

        try {
            // Nonaktifkan foreign key checks sementara
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            $db->query('SET UNIQUE_CHECKS=0');
            $db->query('SET AUTOCOMMIT=0');

            // Eksekusi setiap query
            foreach ($queries as $index => $query) {
                try {
                    $trimmedQuery = trim($query);
                    
                    // Skip query yang kosong atau terlalu pendek
                    if (empty($trimmedQuery) || strlen($trimmedQuery) < 10) {
                        continue;
                    }

                    // Skip komentar
                    if (strpos($trimmedQuery, '--') === 0 || 
                        strpos($trimmedQuery, '/*') === 0 ||
                        preg_match('/^#/', $trimmedQuery)) {
                        continue;
                    }

                    // Skip SET statements tertentu
                    if (preg_match('/^(SET|LOCK|UNLOCK|USE|DELIMITER)/i', $trimmedQuery)) {
                        continue;
                    }

                    // Eksekusi query
                    $result = $db->query($trimmedQuery);
                    
                    if ($result !== false) {
                        $stats['success']++;
                        
                        // Hitung affected rows untuk INSERT/UPDATE/DELETE
                        if (preg_match('/^(INSERT|UPDATE|DELETE)/i', $trimmedQuery)) {
                            $stats['affected_rows'] += $db->affectedRows();
                        }
                    } else {
                        $stats['failed']++;
                        $errorInfo = $db->error();
                        $stats['errors'][] = [
                            'query' => $index + 1,
                            'error' => $errorInfo['message'] ?? 'Unknown error',
                            'sql' => substr($trimmedQuery, 0, 100) . '...' // Simpan sebagian query untuk debug
                        ];
                    }

                } catch (\Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'query' => $index + 1,
                        'error' => $e->getMessage(),
                        'sql' => substr($trimmedQuery, 0, 100) . '...'
                    ];
                }
            }

            // Commit transaction
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed during SQL import');
            }

        } finally {
            // Selalu aktifkan kembali foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS=1');
            $db->query('SET UNIQUE_CHECKS=1');
            $db->query('SET AUTOCOMMIT=1');
        }

        // Siapkan response
        $response = [
            'success' => $stats['failed'] === 0,
            'message' => "Import selesai. \nTotal Query: {$stats['total']} \nBerhasil: {$stats['success']} \nGagal: {$stats['failed']} \nAffected Rows: {$stats['affected_rows']}",
            'stats' => $stats
        ];

        // Tambahkan sample error jika ada
        if ($stats['failed'] > 0 && !empty($stats['errors'])) {
            $response['error_samples'] = array_slice($stats['errors'], 0, 5);
            
            // Format error samples untuk tampilan yang lebih baik
            $errorMessages = [];
            foreach ($response['error_samples'] as $error) {
                $errorMessages[] = "Query {$error['query']}: {$error['error']}";
            }
            $response['error_display'] = implode("\n", $errorMessages);
        }

        return $this->response->setJSON($response);

    } catch (\Exception $e) {
        // Log error untuk debugging
        log_message('error', 'SQL Import Error: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
}

/**
 * Fungsi improved untuk memisahkan query SQL
 */
private function splitSQLQueries($sqlContent)
{
    // Normalize line endings
    $sqlContent = str_replace(["\r\n", "\r"], "\n", $sqlContent);
    
    // Remove single line comments
    $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
    $sqlContent = preg_replace('/#.*$/m', '', $sqlContent);
    
    // Remove multi-line comments
    $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);
    
    // Split by semicolon, but ignore semicolons inside quotes
    $tempQueries = [];
    $currentQuery = '';
    $inString = false;
    $stringChar = '';
    
    for ($i = 0; $i < strlen($sqlContent); $i++) {
        $char = $sqlContent[$i];
        
        if (($char === "'" || $char === '"') && !$inString) {
            $inString = true;
            $stringChar = $char;
        } elseif ($char === $stringChar && $inString) {
            // Cek untuk escaped quotes
            if ($i > 0 && $sqlContent[$i-1] === '\\') {
                // Ini escaped quote, lanjutkan
            } else {
                $inString = false;
                $stringChar = '';
            }
        }
        
        if ($char === ';' && !$inString) {
            $tempQueries[] = trim($currentQuery);
            $currentQuery = '';
        } else {
            $currentQuery .= $char;
        }
    }
    
    // Tambahkan query terakhir jika ada
    if (!empty(trim($currentQuery))) {
        $tempQueries[] = trim($currentQuery);
    }
    
    // Clean queries
    $queries = [];
    foreach ($tempQueries as $query) {
        $trimmedQuery = trim($query);
        
        if (empty($trimmedQuery)) {
            continue;
        }
        
        // Skip queries yang terlalu pendek (biasanya komentar)
        if (strlen($trimmedQuery) < 10) {
            continue;
        }
        
        // Skip specific commands
        if (preg_match('/^(SET|LOCK|UNLOCK|USE|DELIMITER|CREATE DATABASE|DROP DATABASE)/i', $trimmedQuery)) {
            continue;
        }
        
        $queries[] = $trimmedQuery . ';';
    }
    
    return $queries;
}
}