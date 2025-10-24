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
     * Export Excel
     */
    public function exportExcel()
    {
        try {
            $pengukuran = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();

            $data = [];
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

                $data[] = [
                    'Tahun' => $p['tahun'] ?? '-',
                    'Periode' => $p['periode'] ?? '-',
                    'Tanggal' => $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-',
                    'DMA' => $p['dma'] ?? '-',
                    // ELV 625
                    'ELV625_HV1' => $pembacaanElv625['hv_1'] ?? '-',
                    'ELV625_HV2' => $pembacaanElv625['hv_2'] ?? '-',
                    'ELV625_HV3' => $pembacaanElv625['hv_3'] ?? '-',
                    // ELV 600
                    'ELV600_HV1' => $pembacaanElv600['hv_1'] ?? '-',
                    'ELV600_HV2' => $pembacaanElv600['hv_2'] ?? '-',
                    'ELV600_HV3' => $pembacaanElv600['hv_3'] ?? '-',
                    'ELV600_HV4' => $pembacaanElv600['hv_4'] ?? '-',
                    'ELV600_HV5' => $pembacaanElv600['hv_5'] ?? '-',
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
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
            $this->pembacaanElv600Model->where('id_pengukuran', $id)->delete();
            $this->pembacaanElv625Model->where('id_pengukuran', $id)->delete();
            $this->pergerakanElv600Model->where('id_pengukuran', $id)->delete();
            $this->pergerakanElv625Model->where('id_pengukuran', $id)->delete();
            $this->initialReadingElv600Model->where('id_pengukuran', $id)->delete();
            $this->initialReadingElv625Model->where('id_pengukuran', $id)->delete();
            $this->depthElv600Model->where('id_pengukuran', $id)->delete();
            $this->depthElv625Model->where('id_pengukuran', $id)->delete();
            
            // Hapus data pengukuran
            $this->pengukuranModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

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
            
            $existingElv625 = $this->pembacaanElv625Model->where('id_pengukuran', $id)->first();
            if ($existingElv625) {
                $this->pembacaanElv625Model->update($existingElv625['id_pembacaan'], $elv625Data);
            } else {
                $elv625Data['id_pengukuran'] = $id;
                $this->pembacaanElv625Model->insert($elv625Data);
            }

            // Update data pembacaan ELV 600
            $elv600Data = [
                'hv_1' => !empty($postData['elv600_hv1']) ? (float) $postData['elv600_hv1'] : 0,
                'hv_2' => !empty($postData['elv600_hv2']) ? (float) $postData['elv600_hv2'] : 0,
                'hv_3' => !empty($postData['elv600_hv3']) ? (float) $postData['elv600_hv3'] : 0,
                'hv_4' => !empty($postData['elv600_hv4']) ? (float) $postData['elv600_hv4'] : 0,
                'hv_5' => !empty($postData['elv600_hv5']) ? (float) $postData['elv600_hv5'] : 0
            ];
            
            $existingElv600 = $this->pembacaanElv600Model->where('id_pengukuran', $id)->first();
            if ($existingElv600) {
                $this->pembacaanElv600Model->update($existingElv600['id_pembacaan'], $elv600Data);
            } else {
                $elv600Data['id_pengukuran'] = $id;
                $this->pembacaanElv600Model->insert($elv600Data);
            }

            // Cek apakah ada perubahan pada data pembacaan
            $hasChanges = $this->deteksiPerubahan($oldData, [
                'pengukuran' => $pengukuranData,
                'pembacaanElv625' => $elv625Data,
                'pembacaanElv600' => $elv600Data
            ]);

            // Jika ada perubahan pada data pembacaan, hitung ulang pergerakan
            if ($hasChanges) {
                $this->hitungUlangPergerakan($id);
                $message = 'Data berhasil diupdate dan pergerakan dihitung ulang';
            } else {
                $message = 'Data berhasil disimpan tanpa perubahan';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'hasChanges' => $hasChanges,
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
     * Deteksi perubahan data
     */
    private function deteksiPerubahan($oldData, $newData)
    {
        $hasChanges = false;

        // Bandingkan data pengukuran
        foreach (['tahun', 'periode', 'tanggal', 'dma'] as $field) {
            $oldValue = $oldData['pengukuran'][$field] ?? '';
            $newValue = $newData['pengukuran'][$field] ?? '';
            
            if ($oldValue != $newValue) {
                $hasChanges = true;
                break;
            }
        }

        // Bandingkan data ELV 625
        if (!$hasChanges && $oldData['pembacaanElv625']) {
            foreach (['hv_1', 'hv_2', 'hv_3'] as $field) {
                $oldValue = (float) ($oldData['pembacaanElv625'][$field] ?? 0);
                $newValue = (float) ($newData['pembacaanElv625'][$field] ?? 0);
                
                if (abs($oldValue - $newValue) > 0.0001) { // Tolerance untuk floating point
                    $hasChanges = true;
                    break;
                }
            }
        }

        // Bandingkan data ELV 600
        if (!$hasChanges && $oldData['pembacaanElv600']) {
            foreach (['hv_1', 'hv_2', 'hv_3', 'hv_4', 'hv_5'] as $field) {
                $oldValue = (float) ($oldData['pembacaanElv600'][$field] ?? 0);
                $newValue = (float) ($newData['pembacaanElv600'][$field] ?? 0);
                
                if (abs($oldValue - $newValue) > 0.0001) { // Tolerance untuk floating point
                    $hasChanges = true;
                    break;
                }
            }
        }

        return $hasChanges;
    }

    /**
     * Hitung ulang pergerakan
     */
    private function hitungUlangPergerakan($pengukuran_id)
    {
        try {
            // Hitung ulang pergerakan untuk kedua elevasi
            $this->pergerakanElv625Model->hitungPergerakan($pengukuran_id);
            $this->pergerakanElv600Model->hitungPergerakan($pengukuran_id);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Gagal hitung ulang pergerakan: ' . $e->getMessage());
            return false;
        }
    }
}