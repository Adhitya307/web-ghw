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
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        try {
            $data = [
                'tahun' => $this->request->getPost('tahun'),
                'tanggal' => $this->request->getPost('tanggal'),
                'periode' => $this->request->getPost('periode'),
                'tma' => $this->request->getPost('tma') ?: null,
                'ch_hujan' => $this->request->getPost('ch_hujan') ?: null,
                'temp_id' => 'RPZ_' . time() . '_' . rand(1000, 9999)
            ];
            
            $this->modelPengukuran->insert($data);
            
            return redirect()->to('/right-piez')->with('success', 'Data berhasil ditambahkan');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah data: ' . $e->getMessage());
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
            $hasil = $this->modelPerhitungan->prosesPerhitunganLengkap($id);
            
            if ($hasil) {
                return $this->response->setJSON(['success' => true, 'message' => 'Perhitungan berhasil', 'data' => $hasil]);
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
}