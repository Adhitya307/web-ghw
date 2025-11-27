<?php

namespace App\Controllers\Rightpiezo;

use App\Controllers\BaseController;
use App\Models\Rightpiezo\T_pengukuran_rightpiez;
use App\Models\Rightpiezo\B_piezo_metrik;
use App\Models\Rightpiezo\I_reading_atas;
use App\Models\Rightpiezo\Perhitungan_t_psmetrik;
use App\Models\Rightpiezo\T_pembacaan;

class RightpiezController extends BaseController
{
    protected $modelPengukuran;
    protected $modelMetrik;
    protected $modelIReading;
    protected $modelPerhitungan;
    protected $modelPembacaan;

    public function __construct()
    {
        helper(['number']);
        $this->modelPengukuran = new T_pengukuran_rightpiez();
        $this->modelMetrik = new B_piezo_metrik();
        $this->modelIReading = new I_reading_atas();
        $this->modelPerhitungan = new Perhitungan_t_psmetrik();
        $this->modelPembacaan = new T_pembacaan();
    }

    public function index()
    {
        // Ambil semua data pengukuran dengan urutan terbaru
        $pengukuranData = $this->modelPengukuran->orderBy('tanggal', 'DESC')
                                               ->orderBy('id_pengukuran', 'DESC')
                                               ->findAll();
        
        $data = [];
        foreach ($pengukuranData as $pengukuran) {
            $id = $pengukuran['id_pengukuran'];
            
            // Ambil data terkait
            $metrik = $this->modelMetrik->find($id) ?? [];
            $initial = $this->modelIReading->where('id_pengukuran', $id)->findAll();
            $perhitungan = $this->modelPerhitungan->find($id) ?? [];
            $pembacaan = $this->modelPembacaan->where('id_pengukuran', $id)->findAll();
            
            // Format data pembacaan per titik
            $pembacaanFormatted = [];
            foreach ($pembacaan as $bacaan) {
                $pembacaanFormatted[$bacaan['lokasi']] = [
                    'feet' => $bacaan['feet'],
                    'inch' => $bacaan['inch']
                ];
            }
            
            // Format data initial per titik
            $initialFormatted = [];
            foreach ($initial as $init) {
                $initialFormatted[$init['titik_piezometer']] = [
                    'Elv_Piez' => $init['Elv_Piez'],
                    'kedalaman' => $init['kedalaman']
                ];
            }
            
            $data[] = [
                'pengukuran' => $pengukuran,
                'metrik' => $metrik,
                'initial' => $initialFormatted,
                'perhitungan' => $perhitungan,
                'pembacaan' => $pembacaanFormatted
            ];
        }
        
        return view('right_piez/index', [
            'title' => 'Right Piezometer - Monitoring Data',
            'pengukuran' => $data
        ]);
    }
    
    public function create()
    {
        return view('right_piez/create', [
            'title' => 'Tambah Data Right Piezometer'
        ]);
    }
    
    /**
     * Simpan initial readings dengan nilai default
     */
    public function storeInitialReadings($id_pengukuran)
    {
        try {
            // Data initial readings default untuk semua titik
            $initialData = [
                'R-01' => ['elv_piez' => 651.48, 'kedalaman' => 50.00],
                'R-02' => ['elv_piez' => 647.22, 'kedalaman' => 60.00],
                'R-03' => ['elv_piez' => 606.43, 'kedalaman' => 50.00],
                'R-04' => ['elv_piez' => 586.41, 'kedalaman' => 51.00],
                'R-05' => ['elv_piez' => 655.30, 'kedalaman' => 50.27],
                'R-06' => ['elv_piez' => 661.03, 'kedalaman' => 60.00],
                'R-07' => ['elv_piez' => 649.06, 'kedalaman' => 50.00],
                'R-08' => ['elv_piez' => 671.51, 'kedalaman' => 40.00],
                'R-09' => ['elv_piez' => 656.48, 'kedalaman' => 42.00],
                'R-10' => ['elv_piez' => 677.35, 'kedalaman' => 0],
                'R-11' => ['elv_piez' => 644.90, 'kedalaman' => 57.00],
                'R-12' => ['elv_piez' => 630.49, 'kedalaman' => 42.00],
                'IPZ-01' => ['elv_piez' => 649.90, 'kedalaman' => 0],
                'PZ-04' => ['elv_piez' => 651.39, 'kedalaman' => 73.50]
            ];
            
            $dataToInsert = [];
            
            foreach ($initialData as $titik => $reading) {
                $dataToInsert[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'titik_piezometer' => $titik,
                    'Elv_Piez' => $reading['elv_piez'],
                    'kedalaman' => $reading['kedalaman']
                ];
            }
            
            return $this->modelIReading->insertBatch($dataToInsert);
        } catch (\Exception $e) {
            log_message('error', 'Error storeInitialReadings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Simpan data pembacaan
     */
    public function storePembacaan($id_pengukuran, $pembacaanData)
    {
        try {
            $dataToInsert = [];
            
            foreach ($pembacaanData as $lokasi => $bacaan) {
                // Validasi dan konversi nilai
                $feet = $bacaan['feet'] ?? '';
                $inch = $bacaan['inch'] ?? '0';
                
                // Jika feet kosong, set ke 0
                if ($feet === '') {
                    $feet = '0';
                }
                
                // Konversi inch ke float jika numeric
                if (is_numeric($inch)) {
                    $inch = floatval($inch);
                }
                
                $dataToInsert[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'lokasi' => $lokasi,
                    'feet' => $feet,
                    'inch' => $inch
                ];
            }
            
            return $this->modelPembacaan->insertBatch($dataToInsert);
        } catch (\Exception $e) {
            log_message('error', 'Error storePembacaan: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Proses perhitungan lengkap setelah data tersimpan
     */
    private function prosesPerhitunganSetelahSimpan($id_pengukuran)
    {
        try {
            log_message('debug', 'Memulai proses perhitungan untuk ID: ' . $id_pengukuran);
            
            // 1. Ambil data pembacaan
            $pembacaanData = $this->modelPembacaan->where('id_pengukuran', $id_pengukuran)->findAll();
            if (empty($pembacaanData)) {
                log_message('error', 'Data pembacaan kosong untuk ID: ' . $id_pengukuran);
                return false;
            }
            
            // Format data pembacaan
            $formattedPembacaan = [];
            foreach ($pembacaanData as $bacaan) {
                $formattedPembacaan[$bacaan['lokasi']] = [
                    'feet' => $bacaan['feet'],
                    'inch' => $bacaan['inch']
                ];
            }
            
            // 2. Ambil data initial reading (kedalaman)
            $initialData = $this->modelIReading->where('id_pengukuran', $id_pengukuran)->findAll();
            if (empty($initialData)) {
                log_message('error', 'Data initial reading kosong untuk ID: ' . $id_pengukuran);
                return false;
            }
            
            // Format data kedalaman
            $formattedKedalaman = [];
            foreach ($initialData as $init) {
                $formattedKedalaman[$init['titik_piezometer']] = $init['kedalaman'];
            }
            
            // 3. Hitung B_piezo_metrik
            $hasilBMetrik = $this->modelMetrik->hitungSemuaLokasi($id_pengukuran, $formattedPembacaan, $formattedKedalaman);
            if (empty($hasilBMetrik)) {
                log_message('error', 'Perhitungan B_metrik gagal untuk ID: ' . $id_pengukuran);
                return false;
            }
            
            // Simpan hasil B_metrik
            $simpanBMetrik = $this->modelMetrik->simpanHasilPerhitungan($id_pengukuran, $hasilBMetrik);
            if (!$simpanBMetrik) {
                log_message('error', 'Gagal menyimpan hasil B_metrik untuk ID: ' . $id_pengukuran);
                return false;
            }
            
            // 4. Format data Elv_Piez untuk perhitungan final
            $formattedElvPiez = [];
            foreach ($initialData as $init) {
                $formattedElvPiez[$init['titik_piezometer']] = $init['Elv_Piez'];
            }
            
            // 5. Format hasil B_metrik untuk perhitungan final
            $formattedHasilBMetrik = [];
            foreach ($hasilBMetrik as $lokasi => $hasil) {
                $formattedHasilBMetrik[$lokasi] = is_array($hasil) ? ($hasil['hasil'] ?? 0) : $hasil;
            }
            
            // 6. Hitung perhitungan final
            $hasilFinal = $this->modelPerhitungan->hitungSemuaLokasi($id_pengukuran, $formattedElvPiez, $formattedHasilBMetrik);
            if (empty($hasilFinal)) {
                log_message('error', 'Perhitungan final gagal untuk ID: ' . $id_pengukuran);
                return false;
            }
            
            // 7. Simpan hasil final
            $simpanFinal = $this->modelPerhitungan->simpanHasilPerhitungan($id_pengukuran, $hasilFinal);
            if (!$simpanFinal) {
                log_message('error', 'Gagal menyimpan hasil final untuk ID: ' . $id_pengukuran);
                return false;
            }
            
            log_message('debug', 'Proses perhitungan berhasil untuk ID: ' . $id_pengukuran);
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error prosesPerhitunganSetelahSimpan: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Store method utama
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'tahun' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'periode' => 'required',
            'tma' => 'permit_empty|numeric',
            'ch_hujan' => 'permit_empty|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }
        
        $db = \Config\Database::connect('db_right_piez');
        
        try {
            // Mulai transaction
            $db->transBegin();
            
            // 1. Simpan data pengukuran utama
            $dataPengukuran = [
                'tahun' => $this->request->getPost('tahun'),
                'tanggal' => $this->request->getPost('tanggal'),
                'periode' => $this->request->getPost('periode'),
                'tma' => $this->request->getPost('tma') ?: null,
                'ch_hujan' => $this->request->getPost('ch_hujan') ?: null,
                'temp_id' => 'RPZ_' . time() . '_' . rand(1000, 9999)
            ];
            
            if (!$this->modelPengukuran->insert($dataPengukuran)) {
                throw new \Exception('Gagal menyimpan data pengukuran');
            }
            
            $id_pengukuran = $this->modelPengukuran->getInsertID();
            
            // 2. Simpan initial readings (default)
            if (!$this->storeInitialReadings($id_pengukuran)) {
                throw new \Exception('Gagal menyimpan initial readings');
            }
            
            // 3. Simpan data pembacaan
            $pembacaanData = $this->request->getPost('pembacaan');
            if ($pembacaanData && !$this->storePembacaan($id_pengukuran, $pembacaanData)) {
                throw new \Exception('Gagal menyimpan data pembacaan');
            }
            
            // Commit transaction - data utama sudah tersimpan
            $db->transCommit();
            
            // 4. Proses perhitungan di luar transaction (jika gagal, data tetap tersimpan)
            $calculationResult = false;
            $calculationMessage = '';
            
            try {
                $calculationResult = $this->prosesPerhitunganSetelahSimpan($id_pengukuran);
                $calculationMessage = $calculationResult ? 
                    'Data berhasil disimpan dan perhitungan selesai' : 
                    'Data berhasil disimpan. Perhitungan gagal, dapat dilakukan manual nanti.';
            } catch (\Exception $e) {
                $calculationMessage = 'Data berhasil disimpan. Error dalam perhitungan: ' . $e->getMessage();
                log_message('error', 'Calculation error after store: ' . $e->getMessage());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $calculationMessage,
                'id_pengukuran' => $id_pengukuran,
                'calculation_success' => $calculationResult
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Store error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambah data: ' . $e->getMessage()
            ]);
        }
    }
    
    public function edit($id)
    {
        $pengukuran = $this->modelPengukuran->find($id);
        if (!$pengukuran) {
            return redirect()->to('/right-piez')->with('error', 'Data tidak ditemukan');
        }
        
        // Ambil data terkait
        $metrik = $this->modelMetrik->find($id) ?? [];
        $initial = $this->modelIReading->where('id_pengukuran', $id)->findAll();
        $perhitungan = $this->modelPerhitungan->find($id) ?? [];
        $pembacaan = $this->modelPembacaan->where('id_pengukuran', $id)->findAll();
        
        // Format data
        $pembacaanFormatted = [];
        foreach ($pembacaan as $bacaan) {
            $pembacaanFormatted[$bacaan['lokasi']] = [
                'feet' => $bacaan['feet'],
                'inch' => $bacaan['inch']
            ];
        }
        
        $initialFormatted = [];
        foreach ($initial as $init) {
            $initialFormatted[$init['titik_piezometer']] = [
                'Elv_Piez' => $init['Elv_Piez'],
                'kedalaman' => $init['kedalaman']
            ];
        }
        
        return view('right_piez/edit', [
            'title' => 'Edit Data Right Piezometer',
            'pengukuran' => $pengukuran,
            'metrik' => $metrik,
            'initial' => $initialFormatted,
            'perhitungan' => $perhitungan,
            'pembacaan' => $pembacaanFormatted
        ]);
    }
    
    public function update($id)
    {
        $pengukuran = $this->modelPengukuran->find($id);
        if (!$pengukuran) {
            return redirect()->to('/right-piez')->with('error', 'Data tidak ditemukan');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'tahun' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'periode' => 'required',
            'tma' => 'permit_empty|numeric',
            'ch_hujan' => 'permit_empty|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        try {
            $data = [
                'tahun' => $this->request->getPost('tahun'),
                'tanggal' => $this->request->getPost('tanggal'),
                'periode' => $this->request->getPost('periode'),
                'tma' => $this->request->getPost('tma') ?: null,
                'ch_hujan' => $this->request->getPost('ch_hujan') ?: null
            ];
            
            $this->modelPengukuran->update($id, $data);
            
            return redirect()->to('/right-piez')->with('success', 'Data berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
    
    public function delete($id)
    {
        if (!$this->request->is('delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }
        
        try {
            // Hapus data terkait terlebih dahulu
            $this->modelPembacaan->where('id_pengukuran', $id)->delete();
            $this->modelIReading->where('id_pengukuran', $id)->delete();
            $this->modelMetrik->delete($id);
            $this->modelPerhitungan->delete($id);
            
            // Hapus data pengukuran utama
            $this->modelPengukuran->delete($id);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            log_message('error', 'Delete error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
    
    public function calculate($id)
    {
        try {
            // Proses perhitungan lengkap
            $hasil = $this->prosesPerhitunganSetelahSimpan($id);
            
            if ($hasil) {
                return $this->response->setJSON(['success' => true, 'message' => 'Perhitungan berhasil']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal melakukan perhitungan']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Calculate error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function importSql()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }
        
        $sqlFile = $this->request->getFile('sql_file');
        
        if (!$sqlFile || !$sqlFile->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File SQL tidak valid']);
        }
        
        if ($sqlFile->getExtension() !== 'sql') {
            return $this->response->setJSON(['success' => false, 'message' => 'File harus berformat .sql']);
        }
        
        if ($sqlFile->getSize() > 50 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 50MB']);
        }
        
        try {
            // Baca file SQL
            $sqlContent = file_get_contents($sqlFile->getTempName());
            
            // Eksekusi query SQL
            $db = \Config\Database::connect('db_right_piez');
            $queries = $this->splitSqlQueries($sqlContent);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($queries as $query) {
                if (!empty(trim($query))) {
                    try {
                        $db->query($query);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = $e->getMessage();
                        log_message('error', 'SQL Import Error: ' . $e->getMessage());
                    }
                }
            }
            
            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
            if ($errorCount > 0) {
                $message .= ". Error: " . implode('; ', array_slice($errors, 0, 3));
            }
            
            return $this->response->setJSON([
                'success' => $errorCount === 0, 
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Import SQL error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    private function splitSqlQueries($sql)
    {
        // Split SQL by semicolon, but ignore semicolons in strings
        $queries = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            if (($char === "'" || $char === '"') && ($i === 0 || $sql[$i-1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } else if ($char === $stringChar) {
                    $inString = false;
                }
            }
            
            if ($char === ';' && !$inString) {
                $queries[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        if (!empty(trim($current))) {
            $queries[] = trim($current);
        }
        
        return $queries;
    }

    /**
     * Check duplicate data
     */
    public function checkDuplicate()
    {
        $tahun = $this->request->getPost('tahun');
        $periode = $this->request->getPost('periode');
        $tanggal = $this->request->getPost('tanggal');

        $existing = $this->modelPengukuran->where('tahun', $tahun)
                                         ->where('periode', $periode)
                                         ->where('tanggal', $tanggal)
                                         ->first();

        return $this->response->setJSON([
            'success' => true,
            'isDuplicate' => $existing !== null
        ]);
    }

    /**
     * Debug calculation process
     */
    public function debugCalculation($id_pengukuran)
    {
        try {
            log_message('debug', '=== DEBUG CALCULATION START ===');
            log_message('debug', 'ID Pengukuran: ' . $id_pengukuran);
            
            // Cek data yang ada
            $pengukuran = $this->modelPengukuran->find($id_pengukuran);
            log_message('debug', 'Data Pengukuran: ' . json_encode($pengukuran));
            
            $pembacaan = $this->modelPembacaan->where('id_pengukuran', $id_pengukuran)->findAll();
            log_message('debug', 'Jumlah Data Pembacaan: ' . count($pembacaan));
            log_message('debug', 'Data Pembacaan: ' . json_encode($pembacaan));
            
            $initial = $this->modelIReading->where('id_pengukuran', $id_pengukuran)->findAll();
            log_message('debug', 'Jumlah Data Initial: ' . count($initial));
            log_message('debug', 'Data Initial: ' . json_encode($initial));
            
            // Coba panggil perhitungan
            log_message('debug', 'Memanggil prosesPerhitunganSetelahSimpan...');
            $result = $this->prosesPerhitunganSetelahSimpan($id_pengukuran);
            
            log_message('debug', 'Hasil Perhitungan: ' . ($result ? 'SUKSES' : 'GAGAL'));
            log_message('debug', '=== DEBUG CALCULATION END ===');
            
            return $this->response->setJSON([
                'success' => true,
                'debug_info' => [
                    'pengukuran_exists' => !empty($pengukuran),
                    'pembacaan_count' => count($pembacaan),
                    'initial_count' => count($initial),
                    'calculation_result' => $result
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Calculation Debug Error: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
 * Check duplicate data for edit (exclude current record)
 */
public function checkDuplicateEdit()
{
    $tahun = $this->request->getPost('tahun');
    $periode = $this->request->getPost('periode');
    $tanggal = $this->request->getPost('tanggal');
    $current_id = $this->request->getPost('current_id');

    $existing = $this->modelPengukuran->where('tahun', $tahun)
                                     ->where('periode', $periode)
                                     ->where('tanggal', $tanggal)
                                     ->where('id_pengukuran !=', $current_id)
                                     ->first();

    return $this->response->setJSON([
        'success' => true,
        'isDuplicate' => $existing !== null
    ]);
}
}