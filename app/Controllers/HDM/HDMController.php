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
     * Store data
     */
    public function store()
    {
        // PERBAIKAN: Hilangkan pengecekan AJAX untuk form submission biasa
        try {
            $validation = \Config\Services::validation();
            $validation->setRules($this->getValidationRules());

            if (!$validation->run($this->request->getPost())) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors()
                ]);
            }

            $postData = $this->request->getPost();

            // Simpan data pengukuran
            $pengukuranData = [
                'tahun' => $postData['tahun'],
                'periode' => $postData['periode'],
                'tanggal' => $postData['tanggal'],
                'dma' => $postData['dma']
            ];

            $pengukuranId = $this->pengukuranModel->insert($pengukuranData);

            if (!$pengukuranId) {
                throw new \Exception('Gagal menyimpan data pengukuran');
            }

            // Simpan data pembacaan ELV 625 - PERBAIKAN: selalu insert meski kosong
            $this->pembacaanElv625Model->insert([
                'id_pengukuran' => $pengukuranId,
                'hv_1' => !empty($postData['elv625_hv1']) ? (float)$postData['elv625_hv1'] : null,
                'hv_2' => !empty($postData['elv625_hv2']) ? (float)$postData['elv625_hv2'] : null,
                'hv_3' => !empty($postData['elv625_hv3']) ? (float)$postData['elv625_hv3'] : null
            ]);

            // Simpan data pembacaan ELV 600 - PERBAIKAN: selalu insert meski kosong
            $this->pembacaanElv600Model->insert([
                'id_pengukuran' => $pengukuranId,
                'hv_1' => !empty($postData['elv600_hv1']) ? (float)$postData['elv600_hv1'] : null,
                'hv_2' => !empty($postData['elv600_hv2']) ? (float)$postData['elv600_hv2'] : null,
                'hv_3' => !empty($postData['elv600_hv3']) ? (float)$postData['elv600_hv3'] : null,
                'hv_4' => !empty($postData['elv600_hv4']) ? (float)$postData['elv600_hv4'] : null,
                'hv_5' => !empty($postData['elv600_hv5']) ? (float)$postData['elv600_hv5'] : null
            ]);

            // Insert data default (initial reading dan depth)
            $this->insertDefaultData($pengukuranId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'redirect' => base_url('horizontal-displacement')
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check duplicate data - PERBAIKAN: Handle both POST and JSON
     */
    /**
 * Check duplicate data - PERBAIKAN: Handle both POST and JSON
 */
/**
 * Check duplicate data - PERBAIKAN: Cek berdasarkan tahun dan tanggal saja
 */
public function checkDuplicate()
{
    try {
        // VERSI PALING SIMPLE: Gunakan getVar() saja yang handle semua type
        $tahun = $this->request->getVar('tahun');
        $tanggal = $this->request->getVar('tanggal');

        if (!$tahun || !$tanggal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tahun dan tanggal harus diisi'
            ]);
        }

        $existing = $this->pengukuranModel->where('tahun', $tahun)
                                         ->where('tanggal', $tanggal)
                                         ->first();

        return $this->response->setJSON([
            'success' => true,
            'exists' => $existing !== null,
            'isDuplicate' => $existing !== null,
            'message' => $existing ? 'Data dengan Tahun: ' . $tahun . ' dan Tanggal: ' . $tanggal . ' sudah ada dalam database.' : 'Data belum ada'
        ]);

    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Insert data default (initial reading dan depth)
     */
    private function insertDefaultData($pengukuran_id)
    {
        // Insert initial reading
        $this->initialReadingElv625Model->insertReading($pengukuran_id);
        $this->initialReadingElv600Model->insertReading($pengukuran_id);

        // Insert depth
        $this->depthElv625Model->insertDefault($pengukuran_id);
        $this->depthElv600Model->insertDefault($pengukuran_id);

        // Hitung pergerakan setelah semua data tersimpan
        $this->pergerakanElv625Model->hitungPergerakan($pengukuran_id);
        $this->pergerakanElv600Model->hitungPergerakan($pengukuran_id);
    }

    private function getValidationRules(): array
    {
        return [
            'tahun' => [
                'rules' => 'required|numeric|min_length[4]|max_length[4]',
                'errors' => [
                    'required' => 'Tahun harus diisi',
                    'numeric' => 'Tahun harus berupa angka',
                    'min_length' => 'Tahun harus 4 digit',
                    'max_length' => 'Tahun harus 4 digit'
                ]
            ],
            'periode' => [
                'rules' => 'required|in_list[TW-1,TW-2,TW-3,TW-4]',
                'errors' => [
                    'required' => 'Periode harus diisi',
                    'in_list' => 'Periode harus TW-1, TW-2, TW-3, atau TW-4'
                ]
            ],
            'tanggal' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal harus diisi',
                    'valid_date' => 'Format tanggal tidak valid'
                ]
            ],
            'dma' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'DMA harus diisi'
                ]
            ],
            // PERBAIKAN: Ubah semua field HV menjadi optional dengan validasi lebih longgar
            'elv600_hv1' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV600 HV1 harus berupa angka'
                ]
            ],
            'elv600_hv2' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV600 HV2 harus berupa angka'
                ]
            ],
            'elv600_hv3' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV600 HV3 harus berupa angka'
                ]
            ],
            'elv600_hv4' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV600 HV4 harus berupa angka'
                ]
            ],
            'elv600_hv5' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV600 HV5 harus berupa angka'
                ]
            ],
            'elv625_hv1' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV625 HV1 harus berupa angka'
                ]
            ],
            'elv625_hv2' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV625 HV2 harus berupa angka'
                ]
            ],
            'elv625_hv3' => [
                'rules' => 'permit_empty|numeric',
                'errors' => [
                    'numeric' => 'ELV625 HV3 harus berupa angka'
                ]
            ]
        ];
    }
}