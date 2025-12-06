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
use CodeIgniter\HTTP\ResponseInterface;

class BtmController extends BaseController
{
    protected $pengukuranModel;
    protected $bacaanModels;
    protected $perhitunganModels;
    protected $scatterModels;

    // Helper untuk cek role admin
    private function isAdmin()
    {
        $session = session();
        return $session->get('role') == 'admin';
    }
    
    // Helper untuk redirect jika bukan admin
    private function requireAdmin()
    {
        $session = session();
        if (!$this->isAdmin()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya admin yang dapat melakukan tindakan ini.'
                ]);
            }
            session()->setFlashdata('error', 'Akses ditolak. Hanya admin yang dapat melakukan tindakan ini.');
            return redirect()->to('btm');
        }
        return true;
    }

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Cek login di setiap aksi
        $session = session();
        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                die(json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']));
            }
            return redirect()->to('/auth/login');
        }
        
        try {
            // Initialize models
            $this->pengukuranModel = new PengukuranBtmModel();
            
            // Inisialisasi model bacaan untuk semua BT (tanpa BT5)
            $this->bacaanModels = [
                'bt1' => new BacaanBt1Model(),
                'bt2' => new BacaanBt2Model(),
                'bt3' => new BacaanBt3Model(),
                'bt4' => new BacaanBt4Model(),
                'bt6' => new BacaanBt6Model(),
                'bt7' => new BacaanBt7Model(),
                'bt8' => new BacaanBt8Model()
            ];
            
            // Inisialisasi model perhitungan untuk semua BT (tanpa BT5)
            $this->perhitunganModels = [
                'bt1' => new PerhitunganBt1Model(),
                'bt2' => new PerhitunganBt2Model(),
                'bt3' => new PerhitunganBt3Model(),
                'bt4' => new PerhitunganBt4Model(),
                'bt6' => new PerhitunganBt6Model(),
                'bt7' => new PerhitunganBt7Model(),
                'bt8' => new PerhitunganBt8Model()
            ];

            // Inisialisasi model scatter untuk semua BT (tanpa BT5)
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
            die("Error loading models: " . $e->getMessage());
        }
    }

    // METHOD INDEX - Tampilkan BT1
    public function index()
    {
        return $this->bt1();
    }

    public function checkDuplicate()
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
     * Mendapatkan semua data pengukuran beserta bacaan dan perhitungan untuk SEMUA BT (tanpa BT5)
     */
    private function getAllDataWithCalculations()
    {
        try {
            $pengukuranData = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                                   ->orderBy('tanggal', 'DESC')
                                                   ->findAll();
            $result = [];

            foreach ($pengukuranData as $pengukuran) {
                $id_pengukuran = $pengukuran['id_pengukuran'];
                
                $item = [
                    'pengukuran' => $pengukuran,
                    'bacaan' => [],
                    'perhitungan' => [],
                    'scatter' => []
                ];

                // Ambil data bacaan untuk semua BT (1-4,6-8)
                foreach ($this->bacaanModels as $key => $model) {
                    $bacaan = $model->getByPengukuran($id_pengukuran);
                    if ($bacaan) {
                        $item['bacaan'][$key] = $bacaan;
                    } else {
                        // Default values jika tidak ada data
                        $item['bacaan'][$key] = [
                            'US_GP' => null,
                            'US_Arah' => 'U',
                            'TB_GP' => null,
                            'TB_Arah' => 'T'
                        ];
                    }
                }

                // Ambil data perhitungan untuk semua BT (1-4,6-8)
                foreach ($this->perhitunganModels as $key => $model) {
                    $perhitungan = $model->getByPengukuran($id_pengukuran);
                    if ($perhitungan) {
                        $item['perhitungan'][$key] = $perhitungan;
                    } else {
                        // Default values jika tidak ada data
                        $item['perhitungan'][$key] = [
                            'A_sec' => null,
                            'sin_A_rad' => null,
                            'B_sec' => null,
                            'sin_B_rad' => null,
                            'sin_C_rad' => null,
                            'sin_C_deg' => null,
                            'Cosa' => null,
                            'a_rad' => null,
                            'DMS' => null
                        ];
                    }
                }

                // Ambil data scatter untuk semua BT (1-4,6-8)
                foreach ($this->scatterModels as $key => $model) {
                    $scatter = $model->where('id_pengukuran', $id_pengukuran)->first();
                    if ($scatter) {
                        $item['scatter'][$key] = $scatter;
                    } else {
                        // Default values jika tidak ada data
                        $item['scatter'][$key] = [
                            'Y_US' => null,
                            'X_TB' => null,
                            'Y_cum' => null,
                            'X_cum' => null
                        ];
                    }
                }

                $result[] = $item;
            }

            return $result;

        } catch (\Exception $e) {
            log_message('error', 'Error in getAllDataWithCalculations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create form
     */
    public function create()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        // Cek role admin
        if (!$this->isAdmin()) {
            session()->setFlashdata('error', 'Akses ditolak. Hanya admin yang dapat menambah data.');
            return redirect()->to('btm');
        }

        $data = [
            'title' => 'Tambah Data BTM - PT Indonesia Power',
            'pageTitle' => 'Tambah Data Bubble Tilt Meter',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'Tambah Data' => ''
            ],
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $this->isAdmin()
        ];

        return view('btm/create', $data);
    }

    /**
     * Store data
     */
    public function store()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }
        
        // Cek role admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menambah data.'
            ])->setStatusCode(403);
        }

        try {
            $validation = \Config\Services::validation();
            
            $rules = [
                'tahun' => 'required|numeric',
                'periode' => 'required',
                'tanggal' => 'required|valid_date'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors()
                ]);
            }

            $postData = $this->request->getPost();

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
                'temp_id' => $postData['temp_id'] ?? null
            ];

            $pengukuranId = $this->pengukuranModel->insert($pengukuranData);

            if (!$pengukuranId) {
                throw new \Exception('Gagal menyimpan data pengukuran');
            }

            // Simpan data bacaan untuk setiap BT (1-4,6-8)
            $btKeys = ['bt1', 'bt2', 'bt3', 'bt4', 'bt6', 'bt7', 'bt8'];
            foreach ($btKeys as $btKey) {
                $model = $this->bacaanModels[$btKey] ?? null;
                
                if ($model) {
                    $us_gp = $this->sanitizeNumericInput($this->request->getPost("{$btKey}_US_GP"));
                    $tb_gp = $this->sanitizeNumericInput($this->request->getPost("{$btKey}_TB_GP"));
                    
                    $bacaanData = [
                        'id_pengukuran' => $pengukuranId,
                        'US_GP' => $us_gp,
                        'US_Arah' => $this->request->getPost("{$btKey}_US_Arah") ?? 'U',
                        'TB_GP' => $tb_gp,
                        'TB_Arah' => $this->request->getPost("{$btKey}_TB_Arah") ?? 'T'
                    ];

                    if (!$model->insert($bacaanData)) {
                        throw new \Exception("Gagal menyimpan data bacaan {$btKey}");
                    }
                }
            }

            // Hitung perhitungan untuk data baru dengan data sebelumnya
            $this->calculateForPengukuran($pengukuranId);

            // Hitung scatter dengan penanganan khusus untuk data pertama
            $this->calculateScatterForPengukuran($pengukuranId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data BTM berhasil disimpan',
                'redirect' => base_url('btm')
            ]);

        } catch (\Exception $e) {
            // Rollback jika ada error
            if (isset($pengukuranId)) {
                $this->pengukuranModel->delete($pengukuranId);
                foreach ($this->bacaanModels as $model) {
                    $model->where('id_pengukuran', $pengukuranId)->delete();
                }
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    private function sanitizeNumericInput($input)
    {
        if (empty($input) || $input === '') {
            return null;
        }
        
        $cleaned = preg_replace('/[^\d\.\-]/', '', $input);
        
        if (strpos($cleaned, '-') !== false) {
            $cleaned = '-' . str_replace('-', '', $cleaned);
        }
        
        if (!preg_match('/^-?\d*\.?\d*$/', $cleaned)) {
            return null;
        }
        
        return $cleaned !== '' ? (float)$cleaned : null;
    }

    /**
     * Hitung perhitungan untuk pengukuran tertentu dengan data sebelumnya
     */
    private function calculateForPengukuran($id_pengukuran_sekarang)
    {
        // Ambil data pengukuran saat ini
        $pengukuranSekarang = $this->pengukuranModel->find($id_pengukuran_sekarang);
        
        if (!$pengukuranSekarang) {
            throw new \Exception('Data pengukuran tidak ditemukan');
        }

        // Ambil data pengukuran sebelumnya (berdasarkan tanggal)
        $pengukuranSebelumnya = $this->pengukuranModel
            ->where('tanggal <', $pengukuranSekarang['tanggal'])
            ->orderBy('tanggal', 'DESC')
            ->first();

        // Hitung untuk setiap BT (1-4,6-8)
        foreach ($this->perhitunganModels as $key => $perhitunganModel) {
            $bacaanModel = $this->bacaanModels[$key];
            
            // Ambil data bacaan sebelumnya jika ada
            $bacaanSebelumnya = null;
            if ($pengukuranSebelumnya) {
                $bacaanSebelumnya = $bacaanModel->getByPengukuran($pengukuranSebelumnya['id_pengukuran']);
            }

            // Hitung rumus dengan data sebelumnya
            $methodName = 'hitungRumus' . ucfirst($key);
            if (method_exists($perhitunganModel, $methodName)) {
                $result = $perhitunganModel->$methodName(
                    $id_pengukuran_sekarang, 
                    $bacaanModel, 
                    $bacaanSebelumnya
                );

                // Simpan ke database
                $existing = $perhitunganModel->getByPengukuran($id_pengukuran_sekarang);
                if ($existing) {
                    $perhitunganModel->update($existing['id_perhitungan'], $result);
                } else {
                    $perhitunganModel->insert($result);
                }
            }
        }
    }

    /**
     * SCATTER: Data pertama di-set manual, data kedua+ hitung dari rumus
     */
    private function calculateScatterForPengukuran($id_pengukuran_sekarang)
    {
        // Cek apakah ini data pertama
        $isFirstData = $this->isFirstPengukuran($id_pengukuran_sekarang);
        
        foreach ($this->scatterModels as $btKey => $scatterModel) {
            if ($isFirstData) {
                // DATA PERTAMA SCATTER: Set nilai manual sesuai Excel
                $this->setFirstScatterData($scatterModel, $id_pengukuran_sekarang, $btKey);
            } else {
                // DATA KEDUA DAN SETERUSNYA SCATTER: Hitung berdasarkan rumus
                $this->calculateScatterFromFormula($scatterModel, $id_pengukuran_sekarang, $btKey);
            }
        }
    }

    /**
     * Cek apakah ini data pengukuran pertama
     */
    private function isFirstPengukuran($id_pengukuran)
    {
        $firstPengukuran = $this->pengukuranModel
            ->orderBy('tanggal', 'ASC')
            ->orderBy('tahun', 'ASC')
            ->orderBy('periode', 'ASC')
            ->first();
        
        return $firstPengukuran && $firstPengukuran['id_pengukuran'] == $id_pengukuran;
    }

    /**
     * SCATTER: Set data pertama dengan nilai manual
     */
    private function setFirstScatterData($scatterModel, $id_pengukuran, $btKey)
    {
        // Nilai manual untuk data pertama sesuai Excel
        $firstScatterValues = [
            'bt1' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => 29.72554, 'X_cum' => 35.46528],
            'bt2' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => 751.1415, 'X_cum' => 297.492],
            'bt3' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => -388.8682, 'X_cum' => -280.21875],
            'bt4' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => 73.17, 'X_cum' => -74.3438],
            'bt6' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => -608.714, 'X_cum' => -1142.37],
            'bt7' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => 219.6, 'X_cum' => -631.35],
            'bt8' => ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => 171.56, 'X_cum' => -106.37]
        ];

        $values = $firstScatterValues[$btKey] ?? ['Y_US' => 0, 'X_TB' => 0, 'Y_cum' => 0, 'X_cum' => 0];
        
        $scatterData = [
            'id_pengukuran' => $id_pengukuran,
            'Y_US' => $values['Y_US'],
            'X_TB' => $values['X_TB'],
            'Y_cum' => $values['Y_cum'],
            'X_cum' => $values['X_cum']
        ];
        
        $existing = $scatterModel->where('id_pengukuran', $id_pengukuran)->first();
        if ($existing) {
            $scatterModel->update($existing['id_scatter'], $scatterData);
        } else {
            $scatterModel->insert($scatterData);
        }
    }

    /**
     * SCATTER: Hitung data kedua dan seterusnya berdasarkan rumus
     */
    private function calculateScatterFromFormula($scatterModel, $id_pengukuran, $btKey)
    {
        try {
            // Ambil data bacaan dan perhitungan
            $bacaanData = $this->bacaanModels[$btKey]->getByPengukuran($id_pengukuran);
            $perhitunganData = $this->perhitunganModels[$btKey]->getByPengukuran($id_pengukuran);
            
            if (!$bacaanData || !$perhitunganData) {
                log_message('error', "Data bacaan atau perhitungan tidak ditemukan untuk $btKey, id_pengukuran: $id_pengukuran");
                return;
            }

            // Pastikan nilai A_sec dan B_sec tersedia
            if ($perhitunganData['A_sec'] === null || $perhitunganData['B_sec'] === null) {
                log_message('error', "Nilai A_sec atau B_sec null untuk $btKey, id_pengukuran: $id_pengukuran");
                return;
            }

            // Rumus: Y_US = IF(US_Arah="U";A_sec;(-A_sec))
            $Y_US = ($bacaanData['US_Arah'] == 'U') ? $perhitunganData['A_sec'] : (-$perhitunganData['A_sec']);
            
            // Rumus: X_TB = IF(TB_Arah="T";B_sec;(-B_sec))
            $X_TB = ($bacaanData['TB_Arah'] == 'T') ? $perhitunganData['B_sec'] : (-$perhitunganData['B_sec']);
            
            // Ambil scatter data sebelumnya untuk kumulatif
            $previousScatter = $this->getPreviousScatterData($scatterModel, $id_pengukuran);
            
            // Hitung kumulatif - gunakan 0 jika tidak ada data sebelumnya
            $previous_Y_cum = $previousScatter ? (float)$previousScatter['Y_cum'] : 0;
            $previous_X_cum = $previousScatter ? (float)$previousScatter['X_cum'] : 0;
            
            $Y_cum = $previous_Y_cum + $Y_US;
            $X_cum = $previous_X_cum + $X_TB;
            
            $scatterData = [
                'id_pengukuran' => $id_pengukuran,
                'Y_US' => $Y_US,
                'X_TB' => $X_TB,
                'Y_cum' => $Y_cum,
                'X_cum' => $X_cum
            ];
            
            // Simpan ke database
            $existing = $scatterModel->where('id_pengukuran', $id_pengukuran)->first();
            if ($existing) {
                $scatterModel->update($existing['id_scatter'], $scatterData);
            } else {
                $scatterModel->insert($scatterData);
            }
            
        } catch (\Exception $e) {
            log_message('error', "Error calculating scatter for $btKey, id: $id_pengukuran: " . $e->getMessage());
        }
    }

    /**
     * Ambil scatter data sebelumnya untuk perhitungan kumulatif
     */
    private function getPreviousScatterData($scatterModel, $id_pengukuran)
    {
        try {
            // Dapatkan nama tabel dari model secara dinamis
            $tableName = $scatterModel->table;
            
            return $scatterModel->select("$tableName.*")
                ->join('t_pengukuran_btm t', "t.id_pengukuran = $tableName.id_pengukuran")
                ->where("$tableName.id_pengukuran <", $id_pengukuran)
                ->orderBy('t.tanggal', 'DESC')
                ->orderBy('t.tahun', 'DESC')
                ->orderBy('t.periode', 'DESC')
                ->first();
        } catch (\Exception $e) {
            log_message('error', 'Error in getPreviousScatterData: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        // Cek role admin
        if (!$this->isAdmin()) {
            session()->setFlashdata('error', 'Akses ditolak. Hanya admin yang dapat mengedit data.');
            return redirect()->to('btm');
        }

        try {
            // Ambil data pengukuran
            $pengukuran = $this->pengukuranModel->find($id);
            if (!$pengukuran) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }

            $data = [
                'title' => 'Edit Data BTM - PT Indonesia Power',
                'pageTitle' => 'Edit Data Bubble Tilt Meter',
                'breadcrumbs' => [
                    'Dashboard' => base_url(),
                    'BTM' => base_url('btm'),
                    'Edit Data' => ''
                ],
                'pengukuran' => $pengukuran,
                'bacaan' => [],
                // Tambahkan data user untuk view
                'username' => $session->get('username'),
                'role' => $session->get('role'),
                'isAdmin' => $this->isAdmin()
            ];

            // Ambil data bacaan untuk semua BT (1-4,6-8)
            $btKeys = ['bt1', 'bt2', 'bt3', 'bt4', 'bt6', 'bt7', 'bt8'];
            foreach ($btKeys as $btKey) {
                $bacaanModel = $this->bacaanModels[$btKey] ?? null;
                
                if ($bacaanModel) {
                    $bacaan = $bacaanModel->getByPengukuran($id);
                    $data['bacaan'][$btKey] = $bacaan ?: [
                        'US_GP' => null,
                        'US_Arah' => 'U',
                        'TB_GP' => null,
                        'TB_Arah' => 'T'
                    ];
                }
            }

            return view('btm/edit', $data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update data
     */
    public function update($id)
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }
        
        // Cek role admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat memperbarui data.'
            ])->setStatusCode(403);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        try {
            $validation = \Config\Services::validation();
            
            $rules = [
                'tahun' => 'required|numeric',
                'periode' => 'required',
                'tanggal' => 'required|valid_date'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors()
                ]);
            }

            $postData = $this->request->getPost();

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

            // Update data pengukuran
            $pengukuranData = [
                'tahun' => $postData['tahun'],
                'periode' => $postData['periode'],
                'tanggal' => $postData['tanggal'],
                'temp_id' => $postData['temp_id'] ?? null
            ];

            $this->pengukuranModel->update($id, $pengukuranData);

            // Update data bacaan untuk setiap BT (1-4,6-8)
            $btKeys = ['bt1', 'bt2', 'bt3', 'bt4', 'bt6', 'bt7', 'bt8'];
            foreach ($btKeys as $btKey) {
                $model = $this->bacaanModels[$btKey] ?? null;
                
                if ($model) {
                    $us_gp = $this->sanitizeNumericInput($this->request->getPost("{$btKey}_US_GP"));
                    $tb_gp = $this->sanitizeNumericInput($this->request->getPost("{$btKey}_TB_GP"));
                    
                    $bacaanData = [
                        'US_GP' => $us_gp,
                        'US_Arah' => $this->request->getPost("{$btKey}_US_Arah") ?? 'U',
                        'TB_GP' => $tb_gp,
                        'TB_Arah' => $this->request->getPost("{$btKey}_TB_Arah") ?? 'T'
                    ];

                    $existingBacaan = $model->getByPengukuran($id);
                    if ($existingBacaan) {
                        $model->update($existingBacaan['id_bacaan'], $bacaanData);
                    } else {
                        $bacaanData['id_pengukuran'] = $id;
                        $model->insert($bacaanData);
                    }
                }
            }

            // Hitung ulang perhitungan untuk data yang diubah dengan data sebelumnya
            $this->calculateForPengukuran($id);

            // Hitung ulang scatter data setelah perubahan
            $this->recalculateAllScatterData();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'redirect' => base_url('btm')
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
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
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }
        
        // Cek role admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menghapus data.'
            ])->setStatusCode(403);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        try {
            // Hapus data scatter untuk semua BT
            foreach ($this->scatterModels as $model) {
                $model->where('id_pengukuran', $id)->delete();
            }

            // Hapus data perhitungan untuk semua BT
            foreach ($this->perhitunganModels as $model) {
                $perhitungan = $model->getByPengukuran($id);
                if ($perhitungan) {
                    $model->delete($perhitungan['id_perhitungan']);
                }
            }

            // Hapus data bacaan untuk semua BT
            foreach ($this->bacaanModels as $model) {
                $bacaan = $model->getByPengukuran($id);
                if ($bacaan) {
                    $model->delete($bacaan['id_bacaan']);
                }
            }

            // Hapus data pengukuran
            $this->pengukuranModel->delete($id);

            // Hitung ulang semua scatter data setelah penghapusan
            $this->recalculateAllScatterData();

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
     * Hitung ulang semua scatter data dari awal
     */
    private function recalculateAllScatterData()
    {
        try {
            // Ambil semua data pengukuran diurutkan
            $allPengukuran = $this->pengukuranModel
                ->orderBy('tanggal', 'ASC')
                ->orderBy('tahun', 'ASC')
                ->orderBy('periode', 'ASC')
                ->findAll();
            
            if (empty($allPengukuran)) {
                return true;
            }

            // Reset semua scatter data
            foreach ($this->scatterModels as $btKey => $scatterModel) {
                $scatterModel->truncate();
            }
            
            // Proses data pertama (nilai manual)
            $firstPengukuran = $allPengukuran[0];
            
            foreach ($this->scatterModels as $btKey => $scatterModel) {
                $this->setFirstScatterData($scatterModel, $firstPengukuran['id_pengukuran'], $btKey);
            }
            
            // Proses data kedua dan seterusnya
            for ($i = 1; $i < count($allPengukuran); $i++) {
                $pengukuran = $allPengukuran[$i];
                
                foreach ($this->scatterModels as $btKey => $scatterModel) {
                    $this->calculateScatterFromFormula($scatterModel, $pengukuran['id_pengukuran'], $btKey);
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error recalculating all scatter data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get scatter chart data untuk BT tertentu
     */
    private function getScatterChartData($btKey = 'bt1')
    {
        if (!isset($this->scatterModels[$btKey])) {
            log_message('error', "Scatter model not found for: $btKey");
            return [];
        }

        try {
            $scatterModel = $this->scatterModels[$btKey];
            $tableName = $scatterModel->table;
            
            $data = $scatterModel->select("$tableName.X_cum, $tableName.Y_cum, t.tahun, t.periode, t.tanggal")
                ->join('t_pengukuran_btm t', "t.id_pengukuran = $tableName.id_pengukuran")
                ->orderBy('t.tanggal', 'ASC')
                ->orderBy('t.tahun', 'ASC')
                ->orderBy('t.periode', 'ASC')
                ->findAll();
            
            $chartData = [];
            
            foreach ($data as $item) {
                if (isset($item['X_cum']) && isset($item['Y_cum'])) {
                    $chartData[] = [
                        'x' => (float)$item['X_cum'],
                        'y' => (float)$item['Y_cum'],
                        'label' => "Tahun: " . ($item['tahun'] ?? '') . ", Periode: " . ($item['periode'] ?? ''),
                        'tahun' => $item['tahun'] ?? '',
                        'periode' => $item['periode'] ?? ''
                    ];
                }
            }
            
            return $chartData;
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting scatter chart data for ' . $btKey . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Import SQL untuk BTM - VERSI YANG DIPERBAIKI
     */
    public function importSQL()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }
        
        // Cek role admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengimport data SQL.'
            ])->setStatusCode(403);
        }

        log_message('info', '[IMPORT BTM SQL] === START IMPORT PROCESS ===');
        log_message('info', '[IMPORT BTM SQL] Request Method: ' . $this->request->getMethod());
        log_message('info', '[IMPORT BTM SQL] Is AJAX: ' . ($this->request->isAJAX() ? 'YES' : 'NO'));

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false, 
                'message' => 'Hanya AJAX request yang diizinkan.'
            ]);
        }

        try {
            if (empty($_FILES)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada file yang diupload.'
                ]);
            }

            $sqlFile = $this->request->getFile('sql_file');
            if (!$sqlFile || !$sqlFile->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File upload gagal: ' . ($sqlFile ? $sqlFile->getErrorString() : 'File tidak ditemukan')
                ]);
            }

            // Validasi file
            $originalName = $sqlFile->getClientName();
            if (!str_ends_with(strtolower($originalName), '.sql')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File harus berekstensi .sql'
                ]);
            }

            // Baca file SQL
            $sqlContent = file_get_contents($sqlFile->getTempName());
            if (empty($sqlContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File SQL kosong'
                ]);
            }

            log_message('info', '[IMPORT BTM SQL] Original SQL content length: ' . strlen($sqlContent));

            // ===== KONVERSI SQLITE → MYSQL =====
            $mysqlContent = $this->convertSQLiteToMySQL($sqlContent);
            log_message('info', '[IMPORT BTM SQL] Converted to MySQL syntax');

            $queries = $this->splitSQLQueries($mysqlContent);
            log_message('info', "[IMPORT BTM SQL] Total queries after conversion: " . count($queries));

            if (empty($queries)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada query SQL valid setelah konversi.'
                ]);
            }

            // Eksekusi queries
            $db = \Config\Database::connect('btm');
            $stats = [
                'total' => count($queries), 
                'success' => 0, 
                'failed' => 0, 
                'affected_rows' => 0,
                'errors' => [],
                'tables' => []
            ];

            $db->transStart();
            $db->query('SET FOREIGN_KEY_CHECKS=0');

            foreach ($queries as $index => $query) {
                $trimmedQuery = trim($query);
                if (empty($trimmedQuery)) continue;

                // Skip commands yang tidak perlu
                if (preg_match('/^(SET|LOCK|UNLOCK|USE|DELIMITER)/i', $trimmedQuery)) {
                    continue;
                }

                try {
                    $result = $db->query($trimmedQuery);
                    if ($result !== false) {
                        $stats['success']++;
                        if (preg_match('/^(INSERT|UPDATE|DELETE|REPLACE)/i', $trimmedQuery)) {
                            $stats['affected_rows'] += $db->affectedRows();
                        }
                        
                        // Track table imports
                        if (preg_match('/INSERT\s+INTO\s+`?(\w+)`?/i', $trimmedQuery, $matches)) {
                            $tableName = $matches[1];
                            if (!isset($stats['tables'][$tableName])) {
                                $stats['tables'][$tableName] = 0;
                            }
                            $stats['tables'][$tableName]++;
                        }
                        
                        log_message('debug', "[IMPORT BTM SQL] ✅ Query #" . ($index + 1) . " success");
                    } else {
                        $stats['failed']++;
                        $error = $db->error();
                        $stats['errors'][] = [
                            'query' => $index + 1,
                            'error' => $error['message'] ?? 'Unknown error',
                            'sql' => substr($trimmedQuery, 0, 100) . '...'
                        ];
                        log_message('error', "[IMPORT BTM SQL] ❌ Query #" . ($index + 1) . " failed: " . ($error['message'] ?? 'Unknown'));
                    }
                } catch (\Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'query' => $index + 1,
                        'error' => $e->getMessage(),
                        'sql' => substr($trimmedQuery, 0, 100) . '...'
                    ];
                    log_message('error', "[IMPORT BTM SQL] ❌ Query #" . ($index + 1) . " exception: " . $e->getMessage());
                }
            }

            $db->transComplete();
            $db->query('SET FOREIGN_KEY_CHECKS=1');

            $success = $stats['failed'] === 0;
            
            log_message('info', "[IMPORT BTM SQL] Import completed. Success: {$stats['success']}, Failed: {$stats['failed']}");

            // Hitung ulang semua scatter data setelah import
            $this->recalculateAllScatterData();
            
            $response = [
                'success' => $success,
                'message' => "Import selesai. Total: {$stats['total']}, Berhasil: {$stats['success']}, Gagal: {$stats['failed']}, Affected Rows: {$stats['affected_rows']}",
                'stats' => $stats
            ];

            if (!$success && !empty($stats['errors'])) {
                $response['error_display'] = "Beberapa query gagal:\n" . 
                    implode("\n", array_map(function($error) {
                        return "Query #{$error['query']}: {$error['error']}";
                    }, $stats['errors']));
            }

            return $this->response->setJSON($response);

        } catch (\Exception $e) {
            log_message('error', '[IMPORT BTM SQL] System Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Konversi SQLite syntax ke MySQL syntax
     */
    private function convertSQLiteToMySQL($sqlContent)
    {
        // 1. Hapus komentar
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        
        // 2. Konversi INSERT OR REPLACE → REPLACE INTO (MySQL equivalent)
        $sqlContent = preg_replace('/INSERT OR REPLACE INTO/i', 'REPLACE INTO', $sqlContent);
        
        // 3. Konversi datetime('now') → NOW() (MySQL equivalent)
        $sqlContent = preg_replace("/datetime\('now'\)/i", 'NOW()', $sqlContent);
        
        // 4. Konversi table names jika diperlukan
        $tableMappings = [
            't_pengukuran_btm' => 't_pengukuran_btm',
            't_bacaan_bt_1' => 't_bacaan_bt_1',
            't_bacaan_bt_2' => 't_bacaan_bt_2',
            't_bacaan_bt_3' => 't_bacaan_bt_3',
            't_bacaan_bt_4' => 't_bacaan_bt_4',
            't_bacaan_bt_6' => 't_bacaan_bt_6',
            't_bacaan_bt_7' => 't_bacaan_bt_7',
            't_bacaan_bt_8' => 't_bacaan_bt_8',
            'p_bt_1' => 'p_bt_1',
            'p_bt_2' => 'p_bt_2',
            'p_bt_3' => 'p_bt_3',
            'p_bt_4' => 'p_bt_4',
            'p_bt_6' => 'p_bt_6',
            'p_bt_7' => 'p_bt_7',
            'p_bt_8' => 'p_bt_8',
            'p_scatter_bt_1' => 'p_scatter_bt_1',
            'p_scatter_bt_2' => 'p_scatter_bt_2',
            'p_scatter_bt_3' => 'p_scatter_bt_3',
            'p_scatter_bt_4' => 'p_scatter_bt_4',
            'p_scatter_bt_6' => 'p_scatter_bt_6',
            'p_scatter_bt_7' => 'p_scatter_bt_7',
            'p_scatter_bt_8' => 'p_scatter_bt_8'
        ];
        
        foreach ($tableMappings as $sqliteTable => $mysqlTable) {
            if ($sqliteTable !== $mysqlTable) {
                $sqlContent = str_ireplace($sqliteTable, $mysqlTable, $sqlContent);
            }
        }
        
        // 5. Hapus AUTOINCREMENT jika ada (MySQL menggunakan AUTO_INCREMENT)
        $sqlContent = preg_replace('/AUTOINCREMENT/i', 'AUTO_INCREMENT', $sqlContent);
        
        log_message('info', '[IMPORT BTM SQL] SQLite to MySQL conversion completed');
        
        return $sqlContent;
    }

    /**
     * Split SQL queries
     */
    private function splitSQLQueries($sqlContent)
    {
        // Normalize line endings
        $sqlContent = str_replace(["\r\n", "\r"], "\n", $sqlContent);
        
        // Remove comments
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/#.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);
        
        // Split by semicolon
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
                $inString = false;
                $stringChar = '';
            }
            
            if ($char === ';' && !$inString) {
                $tempQueries[] = trim($currentQuery);
                $currentQuery = '';
            } else {
                $currentQuery .= $char;
            }
        }
        
        // Add last query
        if (!empty(trim($currentQuery))) {
            $tempQueries[] = trim($currentQuery);
        }
        
        // Clean queries
        $queries = [];
        foreach ($tempQueries as $query) {
            $trimmedQuery = trim($query);
            if (!empty($trimmedQuery) && strlen($trimmedQuery) > 10) {
                $queries[] = $trimmedQuery;
            }
        }
        
        return $queries;
    }

    /**
     * Export Excel
     */
    public function exportExcel($bt = 'bt1')
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        try {
            $pengukuran = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();

            $data = [];
            
            // Header Excel berdasarkan BT yang dipilih
            $headers = $this->getExcelHeaders($bt);
            
            foreach ($pengukuran as $p) {
                $pid = $p['id_pengukuran'];
                
                // Ambil data untuk BT yang dipilih
                $bacaan = $this->bacaanModels[$bt] ? $this->bacaanModels[$bt]->getByPengukuran($pid) : [];
                $perhitungan = $this->perhitunganModels[$bt] ? $this->perhitunganModels[$bt]->getByPengukuran($pid) : [];
                $scatter = $this->scatterModels[$bt] ? $this->scatterModels[$bt]->where('id_pengukuran', $pid)->first() : [];
                
                $row = [
                    // Data dasar
                    'Tahun' => $p['tahun'] ?? '-',
                    'Periode' => $p['periode'] ?? '-',
                    'Tanggal' => $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-',
                    
                    // Data bacaan
                    'US_GP' => $bacaan['US_GP'] ?? '-',
                    'US_Arah' => $bacaan['US_Arah'] ?? '-',
                    'TB_GP' => $bacaan['TB_GP'] ?? '-',
                    'TB_Arah' => $bacaan['TB_Arah'] ?? '-',
                    
                    // Data perhitungan
                    'A_sec' => $perhitungan['A_sec'] ?? '-',
                    'sin_A_rad' => $perhitungan['sin_A_rad'] ?? '-',
                    'B_sec' => $perhitungan['B_sec'] ?? '-',
                    'sin_B_rad' => $perhitungan['sin_B_rad'] ?? '-',
                    'sin_C_rad' => $perhitungan['sin_C_rad'] ?? '-',
                    'sin_C_deg' => $perhitungan['sin_C_deg'] ?? '-',
                    'Cosa' => $perhitungan['Cosa'] ?? '-',
                    'a_rad' => $perhitungan['a_rad'] ?? '-',
                    'DMS' => $perhitungan['DMS'] ?? '-',
                    
                    // Data scatter
                    'Y_US' => $scatter['Y_US'] ?? '-',
                    'X_TB' => $scatter['X_TB'] ?? '-',
                    'Y_cum' => $scatter['Y_cum'] ?? '-',
                    'X_cum' => $scatter['X_cum'] ?? '-'
                ];
                
                $data[] = $row;
            }

            // Load library PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title
            $sheet->setTitle('Data ' . strtoupper($bt));
            
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
            $filename = 'data_btm_' . strtoupper($bt) . '_' . date('Ymd_His') . '.xlsx';
            
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
     * Get Excel headers berdasarkan BT
     */
    private function getExcelHeaders($bt)
    {
        $headers = [
            'Tahun', 'Periode', 'Tanggal',
            'US_GP', 'US_Arah', 'TB_GP', 'TB_Arah',
            'A_sec', 'sin_A_rad', 'B_sec', 'sin_B_rad',
            'sin_C_rad', 'sin_C_deg', 'Cosa', 'a_rad', 'DMS',
            'Y_US', 'X_TB', 'Y_cum', 'X_cum'
        ];
        
        return $headers;
    }

    // ============ METHOD UNTUK SETIAP BT (tanpa BT5) ============

    public function bt1()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-1 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-1',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt1'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt1'
        ];

        return view('btm/index', $data);
    }

    public function bt2()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-2 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-2',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'BT-2' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt2'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt2'
        ];

        return view('btm/bt2', $data);
    }

    public function bt3()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-3 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-3',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'BT-3' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt3'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt3'
        ];

        return view('btm/bt3', $data);
    }

    public function bt4()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-4 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-4',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'BT-4' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt4'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt4'
        ];

        return view('btm/bt4', $data);
    }

    public function bt6()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-6 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-6',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'BT-6' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt6'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt6'
        ];

        return view('btm/bt6', $data);
    }

    public function bt7()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-7 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-7',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'BT-7' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt7'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt7'
        ];

        return view('btm/bt7', $data);
    }

    public function bt8()
    {
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bubble Tilt Meter BT-8 - PT Indonesia Power',
            'pageTitle' => 'Data Bubble Tilt Meter BT-8',
            'breadcrumbs' => [
                'Dashboard' => base_url(),
                'BTM' => base_url('btm'),
                'BT-8' => ''
            ],
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt8'),
            // Tambahkan data user untuk view
            'username' => $session->get('username'),
            'role' => $session->get('role'),
            'isAdmin' => $session->get('role') == 'admin',
            'currentBt' => 'bt8'
        ];

        return view('btm/bt8', $data);
    }
}