<?php 
namespace App\Controllers;

use App\Models\Rembesan\DataGabunganModel;
use App\Models\Rembesan\MAmbangBatas;
use App\Models\Rembesan\MBocoranBaru;
use App\Models\Rembesan\MDataPengukuran;
use App\Models\Rembesan\MSR;
use App\Models\Rembesan\MThomsonWeir;
use App\Models\Rembesan\PerhitunganBocoranModel;
use App\Models\Rembesan\PerhitunganIntiGaleryModel;
use App\Models\Rembesan\PerhitunganSpillwayModel;
use App\Models\Rembesan\PerhitunganSRModel;
use App\Models\Rembesan\PerhitunganThomsonModel;
use App\Models\Rembesan\PerhitunganBatasMaksimalModel;
use App\Models\Rembesan\TebingKananModel;
use App\Models\Rembesan\TotalBocoranModel;

class DataInputController extends BaseController
{
    public function rembesan()
    {
        $modelPengukuran = new MDataPengukuran();
        
        $data = [
            'gabungan'               => (new DataGabunganModel())->findAll(),
            'ambang'                 => (new MAmbangBatas())->findAll(),
            'bocoran'                => (new MBocoranBaru())->findAll(),
            'pengukuran' => $modelPengukuran
                                    ->orderBy("FIELD(bulan, 'JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES')", '', false)
                                    ->orderBy('tanggal', 'ASC')
                                    ->findAll(),

            'sr'                     => (new MSR())->findAll(),
            'thomson'                => (new MThomsonWeir())->findAll(),
            'perhitungan_bocoran'    => (new PerhitunganBocoranModel())->findAll(),
            'perhitungan_ig'         => (new PerhitunganIntiGaleryModel())->findAll(),
            'perhitungan_spillway'   => (new PerhitunganSpillwayModel())->findAll(),
            'perhitungan_sr'         => (new PerhitunganSRModel())->findAll(),
            'perhitungan_thomson'    => (new PerhitunganThomsonModel())->getAllWithPengukuran(),
            'perhitungan_batas'      => (new PerhitunganBatasMaksimalModel())->getAllWithPengukuran(),
            'tebing_kanan'           => (new TebingKananModel())->findAll(),
            'total_bocoran'          => (new TotalBocoranModel())->findAll(),
            // Ambil data periode unik dari database
            'periode_options'        => $modelPengukuran->distinct()->select('periode')->orderBy('periode', 'ASC')->findAll()
        ];

        return view('Data/data_rembesan', $data);
    }

    // Method untuk AJAX polling
    public function getLatestData()
    {
        $modelPengukuran      = new MDataPengukuran();
        $modelThomson         = new MThomsonWeir();
        $modelSR              = new MSR();
        $modelBocoran         = new MBocoranBaru();
        $modelPerhitunganThomson = new PerhitunganThomsonModel();
        $modelPerhitunganSR   = new PerhitunganSRModel();
        $modelPerhitunganBocoran = new PerhitunganBocoranModel();
        $modelPerhitunganIG   = new PerhitunganIntiGaleryModel();
        $modelPerhitunganSpillway = new PerhitunganSpillwayModel();
        $modelTebingKanan     = new TebingKananModel();
        $modelTotalBocoran    = new TotalBocoranModel();
        $modelPerhitunganBatas = new PerhitunganBatasMaksimalModel();

        $pengukuran = $modelPengukuran->findAll();
        $thomson    = $modelThomson->findAll();
        $sr         = $modelSR->findAll();
        $bocoran    = $modelBocoran->findAll();
        $perhitunganThomson = $modelPerhitunganThomson->getAllWithPengukuran();
        $perhitunganSR = $modelPerhitunganSR->findAll();
        $perhitunganBocoran = $modelPerhitunganBocoran->findAll();
        $perhitunganIG = $modelPerhitunganIG->findAll();
        $perhitunganSpillway = $modelPerhitunganSpillway->findAll();
        $tebingKanan = $modelTebingKanan->findAll();
        $totalBocoran = $modelTotalBocoran->findAll();
        $perhitunganBatas = $modelPerhitunganBatas->getAllWithPengukuran();

        // Fungsi indexing yang lebih robust
        $indexBy = function(array $rows, $idField = 'pengukuran_id') {
            $result = [];
            foreach ($rows as $row) {
                // Cari field ID yang sesuai
                if (isset($row[$idField])) {
                    $result[$row[$idField]] = $row;
                } 
                // Alternatif: jika menggunakan field 'id_pengukuran'
                elseif (isset($row['id_pengukuran'])) {
                    $result[$row['id_pengukuran']] = $row;
                }
                // Alternatif lain: jika data sudah memiliki ID langsung
                elseif (isset($row['id'])) {
                    $result[$row['id']] = $row;
                }
            }
            return $result;
        };

        // Index semua data dengan field yang sesuai
        $thomsonBy = $indexBy($thomson);
        $srBy = $indexBy($sr);
        $bocoranBy = $indexBy($bocoran);
        $perhitunganThomsonBy = $indexBy($perhitunganThomson);
        $perhitunganSrBy = $indexBy($perhitunganSR);
        $perhitunganBocoranBy = $indexBy($perhitunganBocoran);
        $perhitunganIgBy = $indexBy($perhitunganIG);
        $perhitunganSpillwayBy = $indexBy($perhitunganSpillway);
        $tebingKananBy = $indexBy($tebingKanan);
        $totalBocoranBy = $indexBy($totalBocoran);
        $perhitunganBatasBy = $indexBy($perhitunganBatas);

        $dataToSend = [];

        foreach ($pengukuran as $p) {
            $pid = $p['id'];
            $dataToSend[] = [
                'pengukuran' => $p,
                'thomson'    => $thomsonBy[$pid] ?? [],
                'sr'         => $srBy[$pid] ?? [],
                'bocoran'    => $bocoranBy[$pid] ?? [],
                'perhitungan_thomson' => $perhitunganThomsonBy[$pid] ?? [],
                'perhitungan_sr' => $perhitunganSrBy[$pid] ?? [],
                'perhitungan_bocoran' => $perhitunganBocoranBy[$pid] ?? [],
                'perhitungan_ig' => $perhitunganIgBy[$pid] ?? [],
                'perhitungan_spillway' => $perhitunganSpillwayBy[$pid] ?? [],
                'tebing_kanan' => $tebingKananBy[$pid] ?? [],
                'total_bocoran' => $totalBocoranBy[$pid] ?? [],
                'perhitungan_batas' => $perhitunganBatasBy[$pid] ?? []
            ];
        }

        return $this->response->setJSON($dataToSend);
    }

   public function edit($id)
{
    $modelPengukuran = new MDataPengukuran();
    $modelThomson = new MThomsonWeir();
    $modelBocoran = new MBocoranBaru();
    $modelSR = new MSR();
    
    // Ambil data pengukuran dengan ID yang sesuai
    $data['pengukuran'] = $modelPengukuran->find($id);
    
    if (!$data['pengukuran']) {
        return redirect()->to('input-data')->with('error', 'Data tidak ditemukan');
    }
    
    // Ambil data terkait - pastikan menggunakan where yang benar
    $data['thomson'] = $modelThomson->where('pengukuran_id', $id)->first() ?? [];
    $data['bocoran'] = $modelBocoran->where('pengukuran_id', $id)->first() ?? [];
    $data['sr'] = $modelSR->where('pengukuran_id', $id)->first() ?? [];
    
    // Debug data untuk memastikan data diambil dengan benar
    log_message('debug', 'Pengukuran Data: ' . print_r($data['pengukuran'], true));
    log_message('debug', 'Thomson Data: ' . print_r($data['thomson'], true));
    log_message('debug', 'Bocoran Data: ' . print_r($data['bocoran'], true));
    
    // Ambil data periode untuk dropdown
    $data['periode_options'] = $modelPengukuran->distinct()->select('periode')->orderBy('periode', 'ASC')->findAll();
    
    return view('Data/edit_data', $data);
}

public function update($id)
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $modelPengukuran = new MDataPengukuran();
    $modelThomson    = new MThomsonWeir();
    $modelBocoran    = new MBocoranBaru();
    $modelSR         = new MSR();

    $validation = \Config\Services::validation();
    $validation->setRules([
        'tahun'   => 'required|numeric',
        'bulan'   => 'required',
        'periode' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
        log_message('error', 'Validation errors: ' . print_r($errors, true));

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $errors
            ]);
        }
        return redirect()->back()->withInput()->with('errors', $errors);
    }

    $parseEmptyValue = function ($value) {
        if ($value === '' || $value === null || $value === '0' || $value === '0.00' || $value === '0.0') {
            return null;
        }
        return is_numeric($value) ? (float)$value : $value;
    };

    try {
        // 🔹 Update data pengukuran
        $pengukuranData = [
            'tahun'       => $this->request->getPost('tahun'),
            'bulan'       => $this->request->getPost('bulan'),
            'periode'     => $this->request->getPost('periode'),
            'tanggal'     => $this->request->getPost('tanggal'),
            'tma_waduk'   => $parseEmptyValue($this->request->getPost('tma_waduk')),
            'curah_hujan' => $parseEmptyValue($this->request->getPost('curah_hujan'))
        ];
        $modelPengukuran->update($id, $pengukuranData);

        // 🔹 Update / Insert Thomson
        $thomsonData = [
            'a1_r' => $parseEmptyValue($this->request->getPost('a1_r')),
            'a1_l' => $parseEmptyValue($this->request->getPost('a1_l')),
            'b1'   => $parseEmptyValue($this->request->getPost('b1')),
            'b3'   => $parseEmptyValue($this->request->getPost('b3')),
            'b5'   => $parseEmptyValue($this->request->getPost('b5'))
        ];
        $existingThomson = $modelThomson->where('pengukuran_id', $id)->first();
        if ($existingThomson) {
            $modelThomson->update($existingThomson['id'], $thomsonData);
        } else {
            $thomsonData['pengukuran_id'] = $id;
            $modelThomson->insert($thomsonData);
        }

        // 🔹 Update / Insert Bocoran
        $bocoranData = [
            'elv_624_t1'     => $parseEmptyValue($this->request->getPost('elv_624_t1')),
            'elv_624_t1_kode'=> $this->request->getPost('elv_624_t1_kode'),
            'elv_615_t2'     => $parseEmptyValue($this->request->getPost('elv_615_t2')),
            'elv_615_t2_kode'=> $this->request->getPost('elv_615_t2_kode'),
            'pipa_p1'        => $parseEmptyValue($this->request->getPost('pipa_p1')),
            'pipa_p1_kode'   => $this->request->getPost('pipa_p1_kode')
        ];
        $existingBocoran = $modelBocoran->where('pengukuran_id', $id)->first();
        if ($existingBocoran) {
            $modelBocoran->update($existingBocoran['id'], $bocoranData);
        } else {
            $bocoranData['pengukuran_id'] = $id;
            $modelBocoran->insert($bocoranData);
        }

        // 🔹 Update / Insert SR
        $srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
        $srData = [];
        foreach ($srList as $srNum) {
            $srData["sr_{$srNum}_nilai"] = $parseEmptyValue($this->request->getPost("sr_{$srNum}_nilai"));
            $srData["sr_{$srNum}_kode"]  = $this->request->getPost("sr_{$srNum}_kode");
        }
        $existingSR = $modelSR->where('pengukuran_id', $id)->first();
        if ($existingSR) {
            $modelSR->update($existingSR['id'], $srData);
        } else {
            $srData['pengukuran_id'] = $id;
            $modelSR->insert($srData);
        }

        // 🔥 Trigger API RumusRembesan di API_Android
        $apiUrl = "http://localhost/API_Android/public/rembesan/Rumus-Rembesan";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['pengukuran_id' => $id]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        log_message('debug', "[UPDATE] Trigger RumusRembesan untuk ID={$id} | Status={$httpCode} | Response={$result}");

        // 🔹 Response untuk AJAX / redirect
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil diperbarui & RumusRembesan dijalankan',
                'api_status' => $httpCode,
                'api_result' => $result
            ]);
        }
        return redirect()->to('input-data')->with('success', 'Data berhasil diperbarui & RumusRembesan dijalankan');

    } catch (\Exception $e) {
        log_message('error', 'Update error: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function delete($id)
{
    $modelPengukuran = new MDataPengukuran();
    $modelThomson    = new MThomsonWeir();
    $modelBocoran    = new MBocoranBaru();
    $modelSR         = new MSR();
    $modelLookBurt   = new \App\Models\Rembesan\AnalisaLookBurtModel();
    $modelTebingKanan = new \App\Models\Rembesan\TebingKananModel(); // ✅ ditambahkan

    try {
        // Hapus data terkait terlebih dahulu
        $modelThomson->where('pengukuran_id', $id)->delete();
        $modelBocoran->where('pengukuran_id', $id)->delete();
        $modelSR->where('pengukuran_id', $id)->delete();
        $modelLookBurt->where('pengukuran_id', $id)->delete();
        $modelTebingKanan->where('pengukuran_id', $id)->delete(); // ✅ hapus tebing kanan juga

        // Hapus data utama
        if ($modelPengukuran->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            }
            return redirect()->to('input-data')->with('success', 'Data berhasil dihapus');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
        return redirect()->to('input-data')->with('error', 'Gagal menghapus data');

    } catch (\Exception $e) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
        return redirect()->to('input-data')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function create()
{
    $modelPengukuran = new MDataPengukuran();
    
    $data = [
        'periode_options' => $modelPengukuran->distinct()->select('periode')->orderBy('periode', 'ASC')->findAll()
    ];
    
    return view('Data/add_data', $data);
}

public function store()
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $modelPengukuran = new MDataPengukuran();
    $modelThomson    = new MThomsonWeir();
    $modelBocoran    = new MBocoranBaru();
    $modelSR         = new MSR();

    $validation = \Config\Services::validation();
    $validation->setRules([
        'tahun'   => 'required|numeric',
        'bulan'   => 'required',
        'periode' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
        log_message('error', 'Validation errors: ' . print_r($errors, true));

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $errors
            ]);
        }
        return redirect()->back()->withInput()->with('errors', $errors);
    }

    $parseEmptyValue = function ($value) {
        if ($value === '' || $value === null || $value === '0' || $value === '0.00' || $value === '0.0') {
            return null;
        }
        return is_numeric($value) ? (float)$value : $value;
    };

    try {
        // 🔹 Insert data pengukuran
        $pengukuranData = [
            'tahun'       => $this->request->getPost('tahun'),
            'bulan'       => $this->request->getPost('bulan'),
            'periode'     => $this->request->getPost('periode'),
            'tanggal'     => $this->request->getPost('tanggal'),
            'tma_waduk'   => $parseEmptyValue($this->request->getPost('tma_waduk')),
            'curah_hujan' => $parseEmptyValue($this->request->getPost('curah_hujan'))
        ];
        $pengukuranId = $modelPengukuran->insert($pengukuranData, true);

        // 🔹 Insert Thomson
        $thomsonData = [
            'pengukuran_id' => $pengukuranId,
            'a1_r' => $parseEmptyValue($this->request->getPost('a1_r')),
            'a1_l' => $parseEmptyValue($this->request->getPost('a1_l')),
            'b1'   => $parseEmptyValue($this->request->getPost('b1')),
            'b3'   => $parseEmptyValue($this->request->getPost('b3')),
            'b5'   => $parseEmptyValue($this->request->getPost('b5'))
        ];
        $modelThomson->insert($thomsonData);

        // 🔹 Insert Bocoran
        $bocoranData = [
            'pengukuran_id'      => $pengukuranId,
            'elv_624_t1'         => $parseEmptyValue($this->request->getPost('elv_624_t1')),
            'elv_624_t1_kode'    => $this->request->getPost('elv_624_t1_kode'),
            'elv_615_t2'         => $parseEmptyValue($this->request->getPost('elv_615_t2')),
            'elv_615_t2_kode'    => $this->request->getPost('elv_615_t2_kode'),
            'pipa_p1'            => $parseEmptyValue($this->request->getPost('pipa_p1')),
            'pipa_p1_kode'       => $this->request->getPost('pipa_p1_kode')
        ];
        $modelBocoran->insert($bocoranData);

        // 🔹 Insert SR
        $srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
        $srData = ['pengukuran_id' => $pengukuranId];
        
        foreach ($srList as $srNum) {
            $srData["sr_{$srNum}_nilai"] = $parseEmptyValue($this->request->getPost("sr_{$srNum}_nilai"));
            $srData["sr_{$srNum}_kode"]  = $this->request->getPost("sr_{$srNum}_kode");
        }
        $modelSR->insert($srData);

        // 🔥 Trigger API RumusRembesan di API_Android
        $apiUrl = "http://localhost/API_Android/public/rembesan/Rumus-Rembesan";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['pengukuran_id' => $pengukuranId]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        log_message('debug', "[STORE] Trigger RumusRembesan untuk ID={$pengukuranId} | Status={$httpCode} | Response={$result}");

        // 🔹 Response untuk AJAX / redirect
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil ditambahkan & RumusRembesan dijalankan',
                'pengukuran_id' => $pengukuranId,
                'api_status' => $httpCode,
                'api_result' => $result
            ]);
        }
        return redirect()->to('input-data')->with('success', 'Data berhasil ditambahkan & RumusRembesan dijalankan');

    } catch (\Exception $e) {
        log_message('error', 'Store error: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}