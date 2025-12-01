<?php
namespace App\Controllers\LeftPiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\MetrikModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class PiezometerController extends BaseController
{
    protected $metrikModel;
    protected $ireadingA;
    protected $ireadingB;
    protected $pengukuranModel;
    protected $perhitunganModel;
    protected $pembacaanModel;

    public function __construct()
    {
        helper(['format', 'number']);
        $this->metrikModel = new MetrikModel();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
        $this->perhitunganModel = new PerhitunganLeftPiezModel();
        $this->pembacaanModel = new TPembacaanLeftPiezModel();
    }

    /**
     * Menampilkan semua data piezometer
     */
    public function index()
    {
        $data = [
            'title' => 'Piezometer - Left Bank',
            'pengukuran' => $this->getAllData()
        ];

        return view('left_piez/index', $data);
    }

    /**
     * CREATE - Menampilkan form tambah data
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Data Piezometer - Left Bank'
        ];

        return view('left_piez/create', $data);
    }

    /**
     * Mengambil semua data piezometer dengan struktur yang benar
     */
    private function getAllData()
    {
        // Ambil semua data dari tabel pengukuran sebagai base
        $pengukuranData = $this->pengukuranModel
            ->orderBy('tahun', 'DESC')
            ->orderBy('periode', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->findAll();
            
        $result = [];

        foreach ($pengukuranData as $pengukuran) {
            $idPengukuran = $pengukuran['id_pengukuran'];
            
            $result[] = [
                'pengukuran' => $pengukuran,
                'metrik' => $this->getMetrikData($idPengukuran),
                'initial_a' => $this->getInitialReadingA($idPengukuran),
                'initial_b' => $this->getInitialReadingB($idPengukuran),
                'perhitungan' => $this->getPerhitunganData($idPengukuran),
                'pembacaan' => $this->getPembacaanData($idPengukuran)
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan data metrik (BACAAN PIEZOMETER METRIK) dari tabel b_piezo_metrik
     */
    /**
 * Mendapatkan data metrik (BACAAN PIEZOMETER METRIK) dari tabel b_piezo_metrik
 */
/**
 * Mendapatkan data metrik (BACAAN PIEZOMETER METRIK) dari tabel b_piezo_metrik
 */
private function getMetrikData($idPengukuran)
{
    $data = $this->metrikModel->where('id_pengukuran', $idPengukuran)->first();
    
    if ($data) {
        // Format yang sesuai untuk view (L_01, L_02, etc) dengan key HURUF BESAR
        return [
            'L_01' => isset($data['l_01']) ? $data['l_01'] : null,
            'L_02' => isset($data['l_02']) ? $data['l_02'] : null,
            'L_03' => isset($data['l_03']) ? $data['l_03'] : null,
            'L_04' => isset($data['l_04']) ? $data['l_04'] : null,
            'L_05' => isset($data['l_05']) ? $data['l_05'] : null,
            'L_06' => isset($data['l_06']) ? $data['l_06'] : null,
            'L_07' => isset($data['l_07']) ? $data['l_07'] : null,
            'L_08' => isset($data['l_08']) ? $data['l_08'] : null,
            'L_09' => isset($data['l_09']) ? $data['l_09'] : null,
            'L_10' => isset($data['l_10']) ? $data['l_10'] : null,
            'SPZ_02' => isset($data['spz_02']) ? $data['spz_02'] : null
        ];
    }
    
    return [];
}

    /**
     * Mendapatkan initial reading A dari tabel i_reading_a_all
     */
    private function getInitialReadingA($idPengukuran)
    {
        $data = $this->ireadingA->where('id_pengukuran', $idPengukuran)->findAll();
        
        $formattedData = [];
        foreach ($data as $item) {
            $titik = strtoupper($item['titik_piezometer']); // Format: L_01, L_02, etc
            $formattedData[$titik] = [
                'Elv_Piez' => isset($item['Elv_Piez']) ? (float)$item['Elv_Piez'] : null
            ];
        }
        
        return $formattedData;
    }

    /**
     * Mendapatkan initial reading B dari tabel i_reading_b_all
     */
    private function getInitialReadingB($idPengukuran)
    {
        $data = $this->ireadingB->where('id_pengukuran', $idPengukuran)->findAll();
        
        $formattedData = [];
        foreach ($data as $item) {
            $titik = strtoupper($item['titik_piezometer']); // Format: L_01, L_02, etc
            $formattedData[$titik] = [
                'Elv_Piez' => isset($item['Elv_Piez']) ? (float)$item['Elv_Piez'] : null
            ];
        }
        
        return $formattedData;
    }

    /**
     * Mendapatkan data perhitungan untuk semua titik dari tabel perhitungan_left_piez
     */
    private function getPerhitunganData($idPengukuran)
    {
        $perhitunganData = [];
        
        // Ambil semua data perhitungan untuk pengukuran ini
        $dataPerhitungan = $this->perhitunganModel
            ->where('id_pengukuran', $idPengukuran)
            ->findAll();
        
        // Format data menjadi array dengan tipe sebagai key
        foreach ($dataPerhitungan as $data) {
            $tipe = $data['tipe_piezometer']; // Format: L01, L02, SPZ02
            
            // Simpan data dengan key sesuai format database
            $perhitunganData[$tipe] = [
                't_psmetrik' => isset($data['t_psmetrik']) ? (float)$data['t_psmetrik'] : null,
                'elv_piez' => isset($data['elv_piez']) ? (float)$data['elv_piez'] : null,
                'kedalaman' => isset($data['kedalaman']) ? (float)$data['kedalaman'] : null,
                'record_max' => isset($data['record_max']) ? (float)$data['record_max'] : null,
                'record_min' => isset($data['record_min']) ? (float)$data['record_min'] : null
            ];
        }
        
        // Pastikan semua tipe ada (termasuk yang kosong)
        $tipeList = ['L01', 'L02', 'L03', 'L04', 'L05', 'L06', 'L07', 'L08', 'L09', 'L10', 'SPZ02'];
        foreach ($tipeList as $tipe) {
            if (!isset($perhitunganData[$tipe])) {
                $defaultsByType = [
                    'L01' => ['elv_piez' => 650.64, 'kedalaman' => 71.5],
                    'L02' => ['elv_piez' => 650.66, 'kedalaman' => 73],
                    'L03' => ['elv_piez' => 616.55, 'kedalaman' => 59],
                    'L04' => ['elv_piez' => 580.26, 'kedalaman' => 50],
                    'L05' => ['elv_piez' => 700.76, 'kedalaman' => 62],
                    'L06' => ['elv_piez' => 690.09, 'kedalaman' => 62],
                    'L07' => ['elv_piez' => 653.36, 'kedalaman' => 40],
                    'L08' => ['elv_piez' => 659.14, 'kedalaman' => 55.5],
                    'L09' => ['elv_piez' => 622.45, 'kedalaman' => 57],
                    'L10' => ['elv_piez' => 580.36, 'kedalaman' => 51.5],
                    'SPZ02' => ['elv_piez' => 700.08, 'kedalaman' => 70],
                ];
                
                $defaults = $defaultsByType[$tipe] ?? $defaultsByType['L01'];
                
                // Hitung default t_psmetrik jika tidak ada data
                $defaultTpsmetrik = $defaults['elv_piez'] - $defaults['kedalaman'];
                
                $perhitunganData[$tipe] = [
                    't_psmetrik' => round($defaultTpsmetrik, 4),
                    'elv_piez' => $defaults['elv_piez'],
                    'kedalaman' => $defaults['kedalaman'],
                    'record_max' => null,
                    'record_min' => null
                ];
            }
        }
        
        return $perhitunganData;
    }

    /**
     * Mendapatkan data pembacaan dari tabel t_pembacaan_left_piez
     */
    private function getPembacaanData($idPengukuran)
    {
        $pembacaanData = [];
        
        // Ambil semua data pembacaan untuk pengukuran ini
        $dataPembacaan = $this->pembacaanModel
            ->where('id_pengukuran', $idPengukuran)
            ->findAll();
        
        // Format data menjadi array dengan tipe sebagai key (dalam format view)
        foreach ($dataPembacaan as $data) {
            $tipe = $data['tipe_piezometer']; // Format: L01, L02, SPZ02
            
            // Konversi format: L01 → L_01, SPZ02 → SPZ_02
            $keyView = $this->convertToViewFormat($tipe);
            
            $pembacaanData[$keyView] = [
                'feet' => isset($data['feet']) && $data['feet'] !== '' ? (float)$data['feet'] : null,
                'inch' => isset($data['inch']) && $data['inch'] !== '' ? (float)$data['inch'] : null
            ];
        }
        
        // Pastikan semua tipe ada (termasuk yang kosong)
        $tipeList = ['L01', 'L02', 'L03', 'L04', 'L05', 'L06', 'L07', 'L08', 'L09', 'L10', 'SPZ02'];
        foreach ($tipeList as $tipe) {
            $keyView = $this->convertToViewFormat($tipe);
            if (!isset($pembacaanData[$keyView])) {
                $pembacaanData[$keyView] = [
                    'feet' => null,
                    'inch' => null
                ];
            }
        }
        
        return $pembacaanData;
    }

    /**
     * Helper: Konversi format tipe dari DB ke View
     * L01 → L_01, SPZ02 → SPZ_02
     */
    private function convertToViewFormat($tipe)
    {
        if (strpos($tipe, 'SPZ') !== false) {
            // Untuk SPZ02
            return str_replace('02', '_02', $tipe);
        } else {
            // Untuk L01, L02, etc
            return preg_replace('/([A-Za-z]+)(\d+)/', '$1_$2', $tipe);
        }
    }

    /**
     * EDIT - Menampilkan form edit data
     */
    public function edit($id)
    {
        // Ambil data berdasarkan ID pengukuran
        $data = [
            'title' => 'Edit Data Piezometer - Left Bank',
            'id_pengukuran' => $id,
            'data' => $this->getSingleData($id)
        ];

        return view('left_piez/edit', $data);
    }

    /**
     * Mendapatkan data single untuk edit
     */
    private function getSingleData($idPengukuran)
    {
        // Ambil data pengukuran utama
        $pengukuranData = $this->pengukuranModel->find($idPengukuran);
        
        if (!$pengukuranData) {
            return null;
        }

        return [
            'pengukuran' => $pengukuranData,
            'metrik' => $this->getMetrikData($idPengukuran),
            'initial_a' => $this->getInitialReadingA($idPengukuran),
            'initial_b' => $this->getInitialReadingB($idPengukuran),
            'perhitungan' => $this->getPerhitunganData($idPengukuran),
            'pembacaan' => $this->getPembacaanData($idPengukuran)
        ];
    }

    // ... (method store, update, delete, checkDuplicate, importSql tetap sama seperti sebelumnya)
    
    // STORE METHOD - Menyimpan data piezometer baru
    public function store()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Data pengukuran utama
            $pengukuranData = [
                'tahun' => $this->request->getPost('tahun'),
                'periode' => $this->request->getPost('periode'),
                'tanggal' => $this->request->getPost('tanggal'),
                'dma' => $this->request->getPost('dma'),
                'temp_id' => $this->request->getPost('temp_id')
            ];

            // Simpan data pengukuran utama
            $this->pengukuranModel->insert($pengukuranData);
            $idPengukuran = $this->pengukuranModel->getInsertID();

            // Data pembacaan (feet & inch)
            $pembacaanData = $this->request->getPost('pembacaan');
            $metrikValues = [];

            if ($pembacaanData && is_array($pembacaanData)) {
                foreach ($pembacaanData as $titik => $data) {
                    // Konversi format L_01 → L01 untuk database
                    $tipe = str_replace('_', '', $titik);
                    
                    // Handle empty values
                    $feet = isset($data['feet']) && $data['feet'] !== '' ? (float)$data['feet'] : null;
                    $inch = isset($data['inch']) && $data['inch'] !== '' ? (float)$data['inch'] : null;
                    
                    // Simpan data pembacaan
                    $pembacaanInsert = [
                        'id_pengukuran' => $idPengukuran,
                        'tipe_piezometer' => $tipe,
                        'feet' => $feet,
                        'inch' => $inch
                    ];
                    
                    $this->pembacaanModel->insert($pembacaanInsert);
                    
                    // Hitung nilai dalam meter untuk disimpan di metrik
                    if ($feet !== null || $inch !== null) {
                        // Konversi feet dan inch ke meter
                        $totalInch = ($feet * 12) + ($inch ?? 0);
                        $nilaiMeter = $totalInch * 0.0254; // Konversi inch ke meter
                        
                        // Simpan ke array metrikValues dengan format yang benar
                        $metrikKey = strtolower(str_replace('_', '', $titik));
                        $metrikKey = str_replace(['l01','l02','l03','l04','l05','l06','l07','l08','l09','l10','spz02'], 
                                                ['l_01','l_02','l_03','l_04','l_05','l_06','l_07','l_08','l_09','l_10','spz_02'], $metrikKey);
                        $metrikValues[$metrikKey] = $nilaiMeter;
                    }
                }
            }

            // Simpan data metrik
            $metrikData = [
                'id_pengukuran' => $idPengukuran,
                'M_feet' => 0.3048, // 1 feet = 0.3048 meter
                'M_inch' => 0.0254  // 1 inch = 0.0254 meter
            ];
            
            // Tambahkan nilai l_01, l_02, dst ke data metrik
            foreach ($metrikValues as $key => $value) {
                $metrikData[$key] = $value;
            }
            
            $this->metrikModel->insert($metrikData);

            // Default values untuk Initial Reading A
            $initialAValues = [
                'L_01' => 650.64,
                'L_02' => 650.60,
                'L_03' => 616.55,
                'L_04' => 580.26,
                'L_05' => 700.76,
                'L_06' => 690.09,
                'L_07' => 653.36,
                'L_08' => 659.14,
                'L_09' => 622.45,
                'L_10' => 580.36,
                'SPZ_02' => 700.08
            ];

            // Default values untuk Initial Reading B
            $initialBValues = [
                'L_01' => 71.50,
                'L_02' => 73.00,
                'L_03' => 59.00,
                'L_04' => 50.00,
                'L_05' => 62.00,
                'L_06' => 62.00,
                'L_07' => 40.00,
                'L_08' => 55.50,
                'L_09' => 57.00,
                'L_10' => 51.50,
                'SPZ_02' => 70.00
            ];

            // Simpan Initial Reading A
            foreach ($initialAValues as $titik => $elevasi) {
                $this->ireadingA->insert([
                    'id_pengukuran' => $idPengukuran,
                    'titik_piezometer' => $titik,
                    'Elv_Piez' => $elevasi
                ]);
            }

            // Simpan Initial Reading B
            foreach ($initialBValues as $titik => $elevasi) {
                $this->ireadingB->insert([
                    'id_pengukuran' => $idPengukuran,
                    'titik_piezometer' => $titik,
                    'Elv_Piez' => $elevasi
                ]);
            }

            // Simpan data perhitungan untuk setiap titik
            $tipeList = ['L01', 'L02', 'L03', 'L04', 'L05', 'L06', 'L07', 'L08', 'L09', 'L10', 'SPZ02'];
            
            foreach ($tipeList as $tipe) {
                // Hitung nilai t_psmetrik
                $metrikKey = strtolower($tipe);
                $metrikKey = str_replace(['l01','l02','l03','l04','l05','l06','l07','l08','l09','l10','spz02'], 
                                        ['l_01','l_02','l_03','l_04','l_05','l_06','l_07','l_08','l_09','l_10','spz_02'], $metrikKey);
                
                $nilaiMetrik = $metrikValues[$metrikKey] ?? null;
                
                // Default values berdasarkan tipe
                $defaultsByType = [
                    'L01' => ['elv_piez' => 650.64, 'kedalaman' => 71.5],
                    'L02' => ['elv_piez' => 650.66, 'kedalaman' => 73],
                    'L03' => ['elv_piez' => 616.55, 'kedalaman' => 59],
                    'L04' => ['elv_piez' => 580.26, 'kedalaman' => 50],
                    'L05' => ['elv_piez' => 700.76, 'kedalaman' => 62],
                    'L06' => ['elv_piez' => 690.09, 'kedalaman' => 62],
                    'L07' => ['elv_piez' => 653.36, 'kedalaman' => 40],
                    'L08' => ['elv_piez' => 659.14, 'kedalaman' => 55.5],
                    'L09' => ['elv_piez' => 622.45, 'kedalaman' => 57],
                    'L10' => ['elv_piez' => 580.36, 'kedalaman' => 51.5],
                    'SPZ02' => ['elv_piez' => 700.08, 'kedalaman' => 70],
                ];
                
                $defaults = $defaultsByType[$tipe] ?? $defaultsByType['L01'];
                $elvPiez = $defaults['elv_piez'];
                $kedalamanDefault = $defaults['kedalaman'];
                
                // Hitung t_psmetrik
                if ($nilaiMetrik !== null && is_numeric($nilaiMetrik) && $nilaiMetrik > 0) {
                    $tPsMetrik = $elvPiez - $nilaiMetrik;
                } else {
                    $tPsMetrik = $elvPiez - $kedalamanDefault;
                }
                
                $perhitunganData = [
                    'id_pengukuran' => $idPengukuran,
                    'tipe_piezometer' => $tipe,
                    'elv_piez' => $elvPiez,
                    'kedalaman' => $kedalamanDefault,
                    't_psmetrik' => round($tPsMetrik, 4)
                ];
                
                $this->perhitunganModel->insert($perhitunganData);
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menyimpan data ke database');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data piezometer berhasil disimpan',
                'redirect' => base_url('left-piez')
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            log_message('error', 'Error storing piezometer data: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * UPDATE METHOD - Mengupdate data piezometer
     */
    public function update($id)
    {
        if (!$this->request->is('put') && !$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Validasi data pengukuran utama
            $pengukuranData = [
                'tahun' => $this->request->getPost('tahun'),
                'periode' => $this->request->getPost('periode'),
                'tanggal' => $this->request->getPost('tanggal'),
                'dma' => $this->request->getPost('dma'),
                'temp_id' => $this->request->getPost('temp_id')
            ];

            // Cek apakah data pengukuran ada
            $existingPengukuran = $this->pengukuranModel->where('id_pengukuran', $id)->first();
            if (!$existingPengukuran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pengukuran tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Update data pengukuran utama
            $this->pengukuranModel->update($id, $pengukuranData);

            // Data pembacaan (feet & inch)
            $pembacaanData = $this->request->getPost('pembacaan');
            $metrikValues = [];

            if ($pembacaanData && is_array($pembacaanData)) {
                foreach ($pembacaanData as $titik => $data) {
                    // Konversi format L_01 → L01 untuk database
                    $tipe = str_replace('_', '', $titik);
                    
                    // Handle empty values properly
                    $feet = isset($data['feet']) && $data['feet'] !== '' ? (float)$data['feet'] : null;
                    $inch = isset($data['inch']) && $data['inch'] !== '' ? (float)$data['inch'] : null;
                    
                    // Update data pembacaan
                    $pembacaanUpdate = [
                        'feet' => $feet,
                        'inch' => $inch
                    ];
                    
                    // Cek apakah data pembacaan sudah ada
                    $existingPembacaan = $this->pembacaanModel
                        ->where('id_pengukuran', $id)
                        ->where('tipe_piezometer', $tipe)
                        ->first();
                    
                    if ($existingPembacaan) {
                        // Update data yang sudah ada
                        $this->pembacaanModel
                            ->where('id_pengukuran', $id)
                            ->where('tipe_piezometer', $tipe)
                            ->set($pembacaanUpdate)
                            ->update();
                    } else {
                        // Insert data baru
                        $pembacaanUpdate['id_pengukuran'] = $id;
                        $pembacaanUpdate['tipe_piezometer'] = $tipe;
                        $this->pembacaanModel->insert($pembacaanUpdate);
                    }
                    
                    // Hitung nilai dalam meter untuk disimpan di metrik
                    if ($feet !== null || $inch !== null) {
                        // Konversi feet dan inch ke meter
                        $totalInch = ($feet * 12) + ($inch ?? 0);
                        $nilaiMeter = $totalInch * 0.0254; // Konversi inch ke meter
                        
                        // Simpan ke array metrikValues dengan format yang benar
                        $metrikKey = strtolower(str_replace('_', '', $titik));
                        $metrikKey = str_replace(['l01','l02','l03','l04','l05','l06','l07','l08','l09','l10','spz02'], 
                                                ['l_01','l_02','l_03','l_04','l_05','l_06','l_07','l_08','l_09','l_10','spz_02'], $metrikKey);
                        $metrikValues[$metrikKey] = $nilaiMeter;
                    }
                }
            }

            // Update data metrik
            $existingMetrik = $this->metrikModel
                ->where('id_pengukuran', $id)
                ->first();
                
            if ($existingMetrik) {
                $metrikUpdateData = [
                    'M_feet' => 0.3048,
                    'M_inch' => 0.0254
                ];
                
                // Tambahkan nilai l_01, l_02, dst ke data metrik
                foreach ($metrikValues as $key => $value) {
                    $metrikUpdateData[$key] = $value;
                }
                
                $this->metrikModel
                    ->where('id_pengukuran', $id)
                    ->set($metrikUpdateData)
                    ->update();
            } else {
                // Jika data metrik belum ada, buat baru
                $metrikData = [
                    'id_pengukuran' => $id,
                    'M_feet' => 0.3048,
                    'M_inch' => 0.0254
                ];
                
                foreach ($metrikValues as $key => $value) {
                    $metrikData[$key] = $value;
                }
                
                $this->metrikModel->insert($metrikData);
            }

            // Update perhitungan untuk setiap titik
            $tipeList = ['L01', 'L02', 'L03', 'L04', 'L05', 'L06', 'L07', 'L08', 'L09', 'L10', 'SPZ02'];
            
            foreach ($tipeList as $tipe) {
                // Ambil data metrik untuk titik ini
                $metrikKey = strtolower($tipe);
                $metrikKey = str_replace(['l01','l02','l03','l04','l05','l06','l07','l08','l09','l10','spz02'], 
                                        ['l_01','l_02','l_03','l_04','l_05','l_06','l_07','l_08','l_09','l_10','spz_02'], $metrikKey);
                
                $nilaiMetrik = $metrikValues[$metrikKey] ?? null;
                
                // Default values berdasarkan tipe
                $defaultsByType = [
                    'L01' => ['elv_piez' => 650.64, 'kedalaman' => 71.5],
                    'L02' => ['elv_piez' => 650.66, 'kedalaman' => 73],
                    'L03' => ['elv_piez' => 616.55, 'kedalaman' => 59],
                    'L04' => ['elv_piez' => 580.26, 'kedalaman' => 50],
                    'L05' => ['elv_piez' => 700.76, 'kedalaman' => 62],
                    'L06' => ['elv_piez' => 690.09, 'kedalaman' => 62],
                    'L07' => ['elv_piez' => 653.36, 'kedalaman' => 40],
                    'L08' => ['elv_piez' => 659.14, 'kedalaman' => 55.5],
                    'L09' => ['elv_piez' => 622.45, 'kedalaman' => 57],
                    'L10' => ['elv_piez' => 580.36, 'kedalaman' => 51.5],
                    'SPZ02' => ['elv_piez' => 700.08, 'kedalaman' => 70],
                ];
                
                $defaults = $defaultsByType[$tipe] ?? $defaultsByType['L01'];
                $elvPiez = $defaults['elv_piez'];
                $kedalamanDefault = $defaults['kedalaman'];
                
                // Hitung t_psmetrik
                if ($nilaiMetrik !== null && is_numeric($nilaiMetrik) && $nilaiMetrik > 0) {
                    $tPsMetrik = $elvPiez - $nilaiMetrik;
                } else {
                    $tPsMetrik = $elvPiez - $kedalamanDefault;
                }
                
                // Cek apakah data perhitungan sudah ada
                $existingPerhitungan = $this->perhitunganModel
                    ->where('id_pengukuran', $id)
                    ->where('tipe_piezometer', $tipe)
                    ->first();
                    
                if ($existingPerhitungan) {
                    // Update nilai perhitungan
                    $updateData = [
                        't_psmetrik' => round($tPsMetrik, 4)
                    ];
                    
                    $this->perhitunganModel
                        ->where('id_pengukuran', $id)
                        ->where('tipe_piezometer', $tipe)
                        ->set($updateData)
                        ->update();
                } else {
                    // Insert data baru jika belum ada
                    $perhitunganData = [
                        'id_pengukuran' => $id,
                        'tipe_piezometer' => $tipe,
                        'elv_piez' => $elvPiez,
                        'kedalaman' => $kedalamanDefault,
                        't_psmetrik' => round($tPsMetrik, 4)
                    ];
                    
                    $this->perhitunganModel->insert($perhitunganData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal mengupdate data di database');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data piezometer berhasil diupdate',
                'redirect' => base_url('left-piez')
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            log_message('error', 'Error updating piezometer data: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * DELETE METHOD - Menghapus data piezometer
     */
    public function delete($id)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Hapus data dari semua tabel terkait
            $this->pengukuranModel->delete($id);
            $this->metrikModel->where('id_pengukuran', $id)->delete();
            $this->ireadingA->where('id_pengukuran', $id)->delete();
            $this->ireadingB->where('id_pengukuran', $id)->delete();
            $this->perhitunganModel->where('id_pengukuran', $id)->delete();
            $this->pembacaanModel->where('id_pengukuran', $id)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menghapus data dari database');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data piezometer berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            log_message('error', 'Error deleting piezometer data: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * CHECK DUPLICATE METHOD - Mengecek data duplikat
     */
    public function checkDuplicate()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        try {
            $tahun = $this->request->getPost('tahun');
            $periode = $this->request->getPost('periode');
            $tanggal = $this->request->getPost('tanggal');
            $currentId = $this->request->getPost('current_id');

            if (!$tahun || !$periode || !$tanggal) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            $builder = $this->pengukuranModel
                ->where('tahun', $tahun)
                ->where('periode', $periode)
                ->where('tanggal', $tanggal);

            // Jika ada current_id (untuk edit), kecualikan data yang sedang diedit
            if ($currentId) {
                $builder->where('id_pengukuran !=', $currentId);
            }

            $existing = $builder->first();

            return $this->response->setJSON([
                'success' => true,
                'isDuplicate' => $existing !== null
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error checking duplicate: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengecek duplikat data'
            ])->setStatusCode(500);
        }
    }

    /**
     * IMPORT SQL METHOD - Import data dari file SQL
     */
    public function importSql()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        try {
            $sqlFile = $this->request->getFile('sql_file');
            
            if (!$sqlFile || !$sqlFile->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File SQL tidak valid'
                ]);
            }

            if ($sqlFile->getExtension() !== 'sql') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File harus berformat .sql'
                ]);
            }

            if ($sqlFile->getSize() > 50 * 1024 * 1024) { // 50MB
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file maksimal 50MB'
                ]);
            }

            // Baca file SQL
            $sqlContent = file_get_contents($sqlFile->getTempName());
            
            // Eksekusi SQL
            $db = \Config\Database::connect();
            $queries = explode(';', $sqlContent);
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $db->query($query);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data SQL berhasil diimpor'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error importing SQL: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}