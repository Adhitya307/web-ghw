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

class BtmController extends BaseController
{
    protected $pengukuranModel;
    protected $bacaanModels;
    protected $perhitunganModels;
    protected $scatterModels;

    public function __construct()
    {
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
    }

    // METHOD INDEX - Tampilkan BT1
    public function index()
    {
        return $this->bt1();
    }

    public function checkDuplicate()
    {
        $tahun = $this->request->getPost('tahun');
        $periode = $this->request->getPost('periode');
        $tanggal = $this->request->getPost('tanggal');
        $current_id = $this->request->getPost('current_id');

        $existing = $this->pengukuranModel
            ->where('tahun', $tahun)
            ->where('periode', $periode)
            ->where('tanggal', $tanggal)
            ->first();

        $isDuplicate = false;
        if ($existing) {
            $isDuplicate = $existing['id_pengukuran'] != $current_id;
        }

        return $this->response->setJSON([
            'success' => true,
            'isDuplicate' => $isDuplicate,
            'existing_data' => $isDuplicate ? $existing : null
        ]);
    }

    /**
     * Mendapatkan semua data pengukuran beserta bacaan dan perhitungan untuk SEMUA BT (tanpa BT5)
     */
    private function getAllDataWithCalculations()
    {
        try {
            $pengukuranData = $this->pengukuranModel->getAllPengukuran();
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

    public function create()
    {
        $data = [
            'title' => 'Tambah Data BTM - PT Indonesia Power'
        ];

        return view('btm/create', $data);
    }

    public function store()
    {
        try {
            $validation = \Config\Services::validation();
            
            $rules = [
                'tahun' => 'required|numeric',
                'periode' => 'required',
                'tanggal' => 'required|valid_date'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Simpan data pengukuran
            $pengukuranData = [
                'tahun' => $this->request->getPost('tahun'),
                'periode' => $this->request->getPost('periode'),
                'tanggal' => $this->request->getPost('tanggal'),
                'temp_id' => $this->request->getPost('temp_id') ?? null
            ];

            $pengukuranId = $this->pengukuranModel->insert($pengukuranData);

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

                    $model->insert($bacaanData);
                }
            }

            // Hitung perhitungan untuk data baru dengan data sebelumnya
            $this->calculateForPengukuran($pengukuranId);

            // Hitung scatter dengan penanganan khusus untuk data pertama
            $this->calculateScatterForPengukuran($pengukuranId);

            return redirect()->to('/btm')->with('success', 'Data BTM berhasil ditambahkan dan dihitung');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
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
            
            // Debug informasi
            log_message('debug', "=== Scatter Calculation for $btKey ===");
            log_message('debug', "ID Pengukuran: $id_pengukuran");
            log_message('debug', "A_sec: {$perhitunganData['A_sec']}, B_sec: {$perhitunganData['B_sec']}");
            log_message('debug', "US_Arah: {$bacaanData['US_Arah']}, TB_Arah: {$bacaanData['TB_Arah']}");
            log_message('debug', "Y_US: $Y_US, X_TB: $X_TB");
            log_message('debug', "Previous scatter: " . ($previousScatter ? 'found' : 'not found'));
            
            if ($previousScatter) {
                log_message('debug', "Previous Y_cum: {$previousScatter['Y_cum']}, X_cum: {$previousScatter['X_cum']}");
            }
            
            // Hitung kumulatif - gunakan 0 jika tidak ada data sebelumnya
            $previous_Y_cum = $previousScatter ? (float)$previousScatter['Y_cum'] : 0;
            $previous_X_cum = $previousScatter ? (float)$previousScatter['X_cum'] : 0;
            
            $Y_cum = $previous_Y_cum + $Y_US;
            $X_cum = $previous_X_cum + $X_TB;
            
            log_message('debug', "New cum - Y: $Y_cum, X: $X_cum");
            log_message('debug', "=== End Scatter Calculation ===");
            
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
                log_message('debug', "Updated scatter data for $btKey, id: $id_pengukuran");
            } else {
                $scatterModel->insert($scatterData);
                log_message('debug', "Inserted scatter data for $btKey, id: $id_pengukuran");
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

    public function edit($id)
    {
        $pengukuran = $this->pengukuranModel->find($id);
        
        if (!$pengukuran) {
            return redirect()->to('/btm')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Data BTM - PT Indonesia Power',
            'pengukuran' => $pengukuran,
            'bacaan' => []
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
    }

    public function update($id)
    {
        try {
            $validation = \Config\Services::validation();
            
            $rules = [
                'tahun' => 'required|numeric',
                'periode' => 'required',
                'tanggal' => 'required|valid_date'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Update data pengukuran
            $pengukuranData = [
                'tahun' => $this->request->getPost('tahun'),
                'periode' => $this->request->getPost('periode'),
                'tanggal' => $this->request->getPost('tanggal'),
                'temp_id' => $this->request->getPost('temp_id') ?? null
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

            return redirect()->to('/btm')->with('success', 'Data BTM berhasil diupdate dan dihitung ulang');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
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
                'message' => 'Data BTM berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
            
            log_message('debug', '=== START RECALCULATING ALL SCATTER DATA ===');
            log_message('debug', 'Total pengukuran found: ' . count($allPengukuran));
            
            if (empty($allPengukuran)) {
                log_message('debug', 'No pengukuran data found');
                return true;
            }

            // Reset semua scatter data
            foreach ($this->scatterModels as $btKey => $scatterModel) {
                $scatterModel->truncate();
                log_message('debug', "Truncated scatter table for $btKey");
            }
            
            // Proses data pertama (nilai manual)
            $firstPengukuran = $allPengukuran[0];
            log_message('debug', "Processing FIRST scatter data - ID: " . $firstPengukuran['id_pengukuran']);
            
            foreach ($this->scatterModels as $btKey => $scatterModel) {
                $this->setFirstScatterData($scatterModel, $firstPengukuran['id_pengukuran'], $btKey);
                log_message('debug', "Set first scatter data for $btKey");
            }
            
            // Proses data kedua dan seterusnya
            for ($i = 1; $i < count($allPengukuran); $i++) {
                $pengukuran = $allPengukuran[$i];
                log_message('debug', "Processing scatter data $i - ID: " . $pengukuran['id_pengukuran']);
                
                foreach ($this->scatterModels as $btKey => $scatterModel) {
                    $this->calculateScatterFromFormula($scatterModel, $pengukuran['id_pengukuran'], $btKey);
                }
            }
            
            log_message('debug', '=== FINISHED RECALCULATING ALL SCATTER DATA ===');
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
            
            log_message('debug', "Scatter data retrieved for $btKey: " . count($data) . " records");
            
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

    // Method tambahan dari routes
    public function calculateAll()
    {
        // Implementasi calculate all jika diperlukan
        return $this->response->setJSON(['success' => true, 'message' => 'Calculate all executed']);
    }

    public function exportExcel()
    {
        // Implementasi export excel jika diperlukan
        return $this->response->setJSON(['success' => true, 'message' => 'Export excel executed']);
    }

    /**
     * Import SQL untuk BTM
     */
    public function importSQL()
    {
        try {
            $sql = $this->request->getPost('sql');
            
            if (empty($sql)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SQL query tidak boleh kosong'
                ]);
            }

            $db = \Config\Database::connect('btm');
            
            // Eksekusi multiple queries
            $queries = explode(';', $sql);
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    try {
                        $db->query($query);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = $e->getMessage();
                    }
                }
            }

            // Hitung ulang semua data setelah import
            $this->recalculateAllScatterData();

            return $this->response->setJSON([
                'success' => true,
                'message' => "Import selesai. Berhasil: $successCount, Gagal: $errorCount",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Debug method untuk melihat data scatter
     */
    public function debugScatter($id_pengukuran = null)
    {
        if (!$id_pengukuran) {
            $allPengukuran = $this->pengukuranModel->findAll();
            $id_pengukuran = $allPengukuran[0]['id_pengukuran'] ?? null;
        }
        
        $debugData = [];
        
        foreach ($this->scatterModels as $btKey => $scatterModel) {
            $tableName = $scatterModel->table;
            $currentData = $scatterModel->where('id_pengukuran', $id_pengukuran)->first();
            $previousData = $this->getPreviousScatterData($scatterModel, $id_pengukuran);
            
            $debugData[$btKey] = [
                'table' => $tableName,
                'current' => $currentData,
                'previous' => $previousData
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'debug_data' => $debugData
        ]);
    }

    // ============ METHOD UNTUK SETIAP BT (tanpa BT5) ============

    public function bt1()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-1 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt1')
        ];

        return view('btm/index', $data);
    }

    public function bt2()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-2 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt2')
        ];

        return view('btm/bt2', $data);
    }

    public function bt3()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-3 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt3')
        ];

        return view('btm/bt3', $data);
    }

    public function bt4()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-4 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt4')
        ];

        return view('btm/bt4', $data);
    }

    public function bt6()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-6 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt6')
        ];

        return view('btm/bt6', $data);
    }

    public function bt7()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-7 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt7')
        ];

        return view('btm/bt7', $data);
    }

    public function bt8()
    {
        $data = [
            'title' => 'Bubble Tilt Meter BT-8 - PT Indonesia Power',
            'pengukuran' => $this->getAllDataWithCalculations(),
            'scatterData' => $this->getScatterChartData('bt8')
        ];

        return view('btm/bt8', $data);
    }
}