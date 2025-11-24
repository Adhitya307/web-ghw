<?php
namespace App\Controllers\LeftPiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\MetrikModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\PerhitunganL01Model;
use App\Models\LeftPiez\PerhitunganL02Model;
use App\Models\LeftPiez\PerhitunganL03Model;
use App\Models\LeftPiez\PerhitunganL04Model;
use App\Models\LeftPiez\PerhitunganL05Model;
use App\Models\LeftPiez\PerhitunganL06Model;
use App\Models\LeftPiez\PerhitunganL07Model;
use App\Models\LeftPiez\PerhitunganL08Model;
use App\Models\LeftPiez\PerhitunganL09Model;
use App\Models\LeftPiez\PerhitunganL10Model;
use App\Models\LeftPiez\PerhitunganSpz02Model;
use App\Models\LeftPiez\TPembacaanL01Model;
use App\Models\LeftPiez\TPembacaanL02Model;
use App\Models\LeftPiez\TPembacaanL03Model;
use App\Models\LeftPiez\TPembacaanL04Model;
use App\Models\LeftPiez\TPembacaanL05Model;
use App\Models\LeftPiez\TPembacaanL06Model;
use App\Models\LeftPiez\TPembacaanL07Model;
use App\Models\LeftPiez\TPembacaanL08Model;
use App\Models\LeftPiez\TPembacaanL09Model;
use App\Models\LeftPiez\TPembacaanL10Model;
use App\Models\LeftPiez\TPembacaanSpz02Model;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class PiezometerController extends BaseController
{
    protected $metrikModel;
    protected $ireadingA;
    protected $ireadingB;
    protected $pengukuranModel;
    protected $perhitunganModels;
    protected $pembacaanModels;

    public function __construct()
    {
        helper('format');
        $this->metrikModel = new MetrikModel();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
        
        // Inisialisasi model perhitungan
        $this->perhitunganModels = [
            'L_01' => new PerhitunganL01Model(),
            'L_02' => new PerhitunganL02Model(),
            'L_03' => new PerhitunganL03Model(),
            'L_04' => new PerhitunganL04Model(),
            'L_05' => new PerhitunganL05Model(),
            'L_06' => new PerhitunganL06Model(),
            'L_07' => new PerhitunganL07Model(),
            'L_08' => new PerhitunganL08Model(),
            'L_09' => new PerhitunganL09Model(),
            'L_10' => new PerhitunganL10Model(),
            'SPZ_02' => new PerhitunganSpz02Model()
        ];
        
        // Inisialisasi model pembacaan
        $this->pembacaanModels = [
            'L_01' => new TPembacaanL01Model(),
            'L_02' => new TPembacaanL02Model(),
            'L_03' => new TPembacaanL03Model(),
            'L_04' => new TPembacaanL04Model(),
            'L_05' => new TPembacaanL05Model(),
            'L_06' => new TPembacaanL06Model(),
            'L_07' => new TPembacaanL07Model(),
            'L_08' => new TPembacaanL08Model(),
            'L_09' => new TPembacaanL09Model(),
            'L_10' => new TPembacaanL10Model(),
            'SPZ_02' => new TPembacaanSpz02Model()
        ];
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
     * Mengambil semua data piezometer dengan struktur yang benar
     */
    private function getAllData()
    {
        // Ambil semua data dari tabel pengukuran sebagai base
        $pengukuranData = $this->pengukuranModel->orderBy('created_at', 'DESC')->findAll();
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
    private function getMetrikData($idPengukuran)
    {
        $data = $this->metrikModel->where('id_pengukuran', $idPengukuran)->first();
        
        if ($data) {
            // Format data metrik untuk memudahkan akses di view
            return [
                'M_feet' => $data['M_feet'] ?? 0.3048,
                'M_inch' => $data['M_inch'] ?? 0.0254,
                'L_01' => $data['l_01'] ?? null,
                'L_02' => $data['l_02'] ?? null,
                'L_03' => $data['l_03'] ?? null,
                'L_04' => $data['l_04'] ?? null,
                'L_05' => $data['l_05'] ?? null,
                'L_06' => $data['l_06'] ?? null,
                'L_07' => $data['l_07'] ?? null,
                'L_08' => $data['l_08'] ?? null,
                'L_09' => $data['l_09'] ?? null,
                'L_10' => $data['l_10'] ?? null,
                'SPZ_02' => $data['spz_02'] ?? null
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
        
        // Format data menjadi array asosiatif dengan titik sebagai key
        $formattedData = [];
        foreach ($data as $item) {
            $titik = strtoupper($item['titik_piezometer']);
            $formattedData[$titik] = [
                'Elv_Piez' => $item['Elv_Piez'] ?? null
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
        
        // Format data menjadi array asosiatif dengan titik sebagai key
        $formattedData = [];
        foreach ($data as $item) {
            $titik = strtoupper($item['titik_piezometer']);
            $formattedData[$titik] = [
                'Elv_Piez' => $item['Elv_Piez'] ?? null
            ];
        }
        
        return $formattedData;
    }

    /**
     * Mendapatkan data perhitungan untuk semua titik dari tabel perhitungan_L_xx
     */
    /**
 * Mendapatkan data perhitungan untuk semua titik dari tabel perhitungan_L_xx
 */
private function getPerhitunganData($idPengukuran)
{
    $perhitunganData = [];
    
    foreach ($this->perhitunganModels as $titik => $model) {
        $data = $model->where('id_pengukuran', $idPengukuran)->first();
        if ($data) {
            // Ambil nilai dari kolom t_psmetrik_Lxx
            $kolomPerhitungan = 't_psmetrik_' . str_replace('_', '', $titik);
            $nilaiPerhitungan = $data[$kolomPerhitungan] ?? null;
            
            $perhitunganData[$titik] = [
                't_psmetrik' => $nilaiPerhitungan,
                'Elv_Piez' => $data['Elv_Piez'] ?? null,
                'kedalaman' => $data['kedalaman'] ?? null,
                'record_max' => $data['record_max'] ?? null,
                'record_min' => $data['record_min'] ?? null,
                'koordinat_x' => $data['koordinat_x'] ?? null,
                'koordinat_y' => $data['koordinat_y'] ?? null
            ];
        } else {
            $perhitunganData[$titik] = [
                't_psmetrik' => null,
                'Elv_Piez' => null,
                'kedalaman' => null,
                'record_max' => null,
                'record_min' => null,
                'koordinat_x' => null,
                'koordinat_y' => null
            ];
        }
    }
    
    return $perhitunganData;
}

    /**
     * Mendapatkan data pembacaan (BACAAN METRIK) dari tabel t_pembacaan_L_xx
     */
    private function getPembacaanData($idPengukuran)
    {
        $pembacaanData = [];
        
        foreach ($this->pembacaanModels as $titik => $model) {
            $data = $model->where('id_pengukuran', $idPengukuran)->first();
            if ($data) {
                $pembacaanData[$titik] = [
                    'feet' => $data['feet'] ?? null,
                    'inch' => $data['inch'] ?? null
                ];
            } else {
                $pembacaanData[$titik] = [
                    'feet' => null,
                    'inch' => null
                ];
            }
        }
        
        return $pembacaanData;
    }

    /**
     * Get data by ID untuk edit
     */
    private function getDataById($id)
    {
        $pengukuran = $this->pengukuranModel->where('id_pengukuran', $id)->first();
        
        if (!$pengukuran) {
            return null;
        }

        return [
            'pengukuran' => $pengukuran,
            'metrik' => $this->getMetrikData($id),
            'initial_a' => $this->getInitialReadingA($id),
            'initial_b' => $this->getInitialReadingB($id),
            'perhitungan' => $this->getPerhitunganData($id),
            'pembacaan' => $this->getPembacaanData($id)
        ];
    }

    /**
     * DELETE METHOD - Menghapus data piezometer beserta semua relasinya
     */
    public function delete($id)
    {
        // Cek metode request harus DELETE
        if (!$this->request->is('delete')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        try {
            // Mulai transaction database
            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Hapus data dari tabel pembacaan (semua titik)
            foreach ($this->pembacaanModels as $titik => $model) {
                $model->where('id_pengukuran', $id)->delete();
            }

            // 2. Hapus data dari tabel perhitungan (semua titik)
            foreach ($this->perhitunganModels as $titik => $model) {
                $model->where('id_pengukuran', $id)->delete();
            }

            // 3. Hapus data initial reading B
            $this->ireadingB->where('id_pengukuran', $id)->delete();

            // 4. Hapus data initial reading A
            $this->ireadingA->where('id_pengukuran', $id)->delete();

            // 5. Hapus data metrik
            $this->metrikModel->where('id_pengukuran', $id)->delete();

            // 6. Hapus data pengukuran utama
            $deleted = $this->pengukuranModel->where('id_pengukuran', $id)->delete();

            // Commit transaction
            $db->transComplete();

            if ($db->transStatus() === FALSE || $deleted === false) {
                throw new \Exception('Gagal menghapus data dari database');
            }

            // Jika berhasil dihapus
            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data piezometer berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

        } catch (\Exception $e) {
            // Rollback transaction jika error
            $db->transRollback();

            log_message('error', 'Error deleting piezometer data: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Method untuk menampilkan form create (jika ada)
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Data Piezometer - Left Bank'
        ];

        return view('left_piez/create', $data);
    }

    /**
     * Method untuk menampilkan form edit (jika ada)
     */
    public function edit($id)
    {
        $dataPiezometer = $this->getDataById($id);
        
        if (!$dataPiezometer) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Data Piezometer - Left Bank',
            'data' => $dataPiezometer
        ];

        return view('left_piez/edit', $data);
    }

    /**
     * Method untuk import SQL (jika ada)
     */
    public function importSql()
    {
        // Handle SQL import logic here
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        // Your SQL import logic here
        return $this->response->setJSON([
            'success' => true,
            'message' => 'SQL import functionality'
        ]);
    }
}