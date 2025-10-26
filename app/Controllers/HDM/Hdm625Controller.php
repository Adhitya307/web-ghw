<?php

namespace App\Controllers\HDM;

use App\Controllers\BaseController;
use App\Models\HDM\MPengukuranHdm;
use App\Models\HDM\MPembacaanElv625;
use App\Models\HDM\MInitialReadingElv625;
use App\Models\HDM\MPergerakanElv625;
use App\Models\HDM\DepthElv625Model;
use App\Models\HDM\AmbangBatas625H1Model;
use App\Models\HDM\AmbangBatas625H2Model;
use App\Models\HDM\AmbangBatas625H3Model;

class Hdm625Controller extends BaseController
{
    protected $pengukuranModel;
    protected $pembacaan625Model;
    protected $initial625Model;
    protected $pergerakan625Model;
    protected $depth625Model;
    protected $ambangBatasH1Model;
    protected $ambangBatasH2Model;
    protected $ambangBatasH3Model;

    public function __construct()
    {
        $this->pengukuranModel = new MPengukuranHdm();
        $this->pembacaan625Model = new MPembacaanElv625();
        $this->initial625Model = new MInitialReadingElv625();
        $this->pergerakan625Model = new MPergerakanElv625();
        $this->depth625Model = new DepthElv625Model();
        $this->ambangBatasH1Model = new AmbangBatas625H1Model();
        $this->ambangBatasH2Model = new AmbangBatas625H2Model();
        $this->ambangBatasH3Model = new AmbangBatas625H3Model();
    }

    /**
     * Menampilkan data HDM 625
     */
    public function index()
    {
        try {
            // Get all pengukuran data - UBAH URUTAN MENJADI ASCENDING
            $pengukuranData = $this->pengukuranModel
                ->orderBy('tahun', 'ASC')  // Ubah dari DESC ke ASC
                ->orderBy('tanggal', 'ASC') // Ubah dari DESC ke ASC
                ->findAll();

            $data = [];
            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                
                // Get data untuk setiap tabel
                $pembacaan = $this->pembacaan625Model->getByPengukuran($id);
                $initial = $this->initial625Model->getByPengukuran($id);
                $pergerakan = $this->pergerakan625Model->getByPengukuran($id);
                $depth = $this->depth625Model->where('id_pengukuran', $id)->first();
                
                // Pastikan data ambang batas ada, jika tidak buat default
                $this->ensureAmbangBatasExists($id);
                
                // Update pergerakan di ambang batas (dikali 10)
                $this->updatePergerakanAmbangBatas($id, $pergerakan);

                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan_elv625' => $pembacaan,
                    'initial_reading_elv625' => $initial,
                    'pergerakan_elv625' => $pergerakan,
                    'depth_elv625' => $depth,
                    'ambang_batas' => [
                        'h1' => $this->ambangBatasH1Model->getByPengukuran($id),
                        'h2' => $this->ambangBatasH2Model->getByPengukuran($id),
                        'h3' => $this->ambangBatasH3Model->getByPengukuran($id)
                    ]
                ];
            }

            // Get unique values for filters
            $uniqueYears = array_unique(array_column($pengukuranData, 'tahun'));
            $uniquePeriods = array_unique(array_column($pengukuranData, 'periode'));
            $uniqueDMA = array_unique(array_column($pengukuranData, 'dma'));

            sort($uniqueYears); // Ubah dari rsort() ke sort() untuk ascending
            sort($uniquePeriods);
            sort($uniqueDMA);

            $viewData = [
                'title' => 'HDM 625 - Horizontal Displacement Meter',
                'data' => $data,
                'uniqueYears' => $uniqueYears,
                'uniquePeriods' => $uniquePeriods,
                'uniqueDMA' => $uniqueDMA
            ];

            return view('hdm/hdm_625', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data HDM 625: ' . $e->getMessage());
        }
    }

    /**
     * Pastikan data ambang batas ada untuk pengukuran
     */
    private function ensureAmbangBatasExists($pengukuran_id)
    {
        // Check masing-masing tabel ambang batas
        if (!$this->ambangBatasH1Model->getByPengukuran($pengukuran_id)) {
            $this->ambangBatasH1Model->insertDefault($pengukuran_id);
        }
        if (!$this->ambangBatasH2Model->getByPengukuran($pengukuran_id)) {
            $this->ambangBatasH2Model->insertDefault($pengukuran_id);
        }
        if (!$this->ambangBatasH3Model->getByPengukuran($pengukuran_id)) {
            $this->ambangBatasH3Model->insertDefault($pengukuran_id);
        }
    }

    /**
     * Update pergerakan di tabel ambang batas (dikali 10)
     */
    private function updatePergerakanAmbangBatas($pengukuran_id, $pergerakan)
    {
        if ($pergerakan) {
            // Helper function untuk format angka dengan 2 desimal
            $formatAngka = function($value) {
                if ($value === null || $value === '' || $value === '-') return null;
                return number_format(floatval($value), 2, '.', '');
            };

            // Update pergerakan untuk setiap H (dikali 10) dengan format 2 desimal
            if (isset($pergerakan['hv_1'])) {
                $this->ambangBatasH1Model->updateByPengukuran($pengukuran_id, [
                    'pergerakan' => $formatAngka($pergerakan['hv_1'] * 10)
                ]);
            }
            if (isset($pergerakan['hv_2'])) {
                $this->ambangBatasH2Model->updateByPengukuran($pengukuran_id, [
                    'pergerakan' => $formatAngka($pergerakan['hv_2'] * 10)
                ]);
            }
            if (isset($pergerakan['hv_3'])) {
                $this->ambangBatasH3Model->updateByPengukuran($pengukuran_id, [
                    'pergerakan' => $formatAngka($pergerakan['hv_3'] * 10)
                ]);
            }
        }
    }

    /**
     * Menampilkan detail data HDM 625
     */
    public function detail($id_pengukuran)
    {
        try {
            $pengukuran = $this->pengukuranModel->find($id_pengukuran);
            
            if (!$pengukuran) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Data pengukuran tidak ditemukan'
                ]);
            }

            // Pastikan data ambang batas ada
            $this->ensureAmbangBatasExists($id_pengukuran);

            $detailData = [
                'pengukuran' => $pengukuran,
                'pembacaan_elv625' => $this->pembacaan625Model->getByPengukuran($id_pengukuran),
                'initial_reading_elv625' => $this->initial625Model->getByPengukuran($id_pengukuran),
                'pergerakan_elv625' => $this->pergerakan625Model->getByPengukuran($id_pengukuran),
                'depth_elv625' => $this->depth625Model->where('id_pengukuran', $id_pengukuran)->first(),
                'ambang_batas' => [
                    'h1' => $this->ambangBatasH1Model->getByPengukuran($id_pengukuran),
                    'h2' => $this->ambangBatasH2Model->getByPengukuran($id_pengukuran),
                    'h3' => $this->ambangBatasH3Model->getByPengukuran($id_pengukuran)
                ]
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $detailData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::detail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memuat detail data'
            ]);
        }
    }

    /**
     * Export data HDM 625 ke Excel
     */
    public function exportExcel()
    {
        try {
            // Get all data - UBAH URUTAN MENJADI ASCENDING
            $pengukuranData = $this->pengukuranModel
                ->orderBy('tahun', 'ASC')  // Ubah dari DESC ke ASC
                ->orderBy('tanggal', 'ASC') // Ubah dari DESC ke ASC
                ->findAll();

            $data = [];
            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                
                $pembacaan = $this->pembacaan625Model->getByPengukuran($id);
                $initial = $this->initial625Model->getByPengukuran($id);
                $pergerakan = $this->pergerakan625Model->getByPengukuran($id);
                $depth = $this->depth625Model->where('id_pengukuran', $id)->first();
                
                // Pastikan ambang batas ada
                $this->ensureAmbangBatasExists($id);
                
                $ambangBatasH1 = $this->ambangBatasH1Model->getByPengukuran($id);
                $ambangBatasH2 = $this->ambangBatasH2Model->getByPengukuran($id);
                $ambangBatasH3 = $this->ambangBatasH3Model->getByPengukuran($id);

                $data[] = [
                    'TAHUN' => $pengukuran['tahun'],
                    'PERIODE' => $pengukuran['periode'],
                    'TANGGAL' => $pengukuran['tanggal'],
                    'DMA' => $pengukuran['dma'],
                    
                    // Initial Reading
                    'INITIAL_HV1' => $initial['hv_1'] ?? '36.00',
                    'INITIAL_HV2' => $initial['hv_2'] ?? '35.50',
                    'INITIAL_HV3' => $initial['hv_3'] ?? '35.00',
                    
                    // Pembacaan HDM
                    'PEMBACAAN_HV1' => $pembacaan['hv_1'] ?? '-',
                    'PEMBACAAN_HV2' => $pembacaan['hv_2'] ?? '-',
                    'PEMBACAAN_HV3' => $pembacaan['hv_3'] ?? '-',
                    
                    // Pergerakan (asli dari tabel pergerakan)
                    'PERGERAKAN_HV1' => $pergerakan['hv_1'] ?? '-',
                    'PERGERAKAN_HV2' => $pergerakan['hv_2'] ?? '-',
                    'PERGERAKAN_HV3' => $pergerakan['hv_3'] ?? '-',
                    
                    // Depth
                    'DEPTH_HV1' => $depth['hv_1'] ?? '20.00',
                    'DEPTH_HV2' => $depth['hv_2'] ?? '40.00',
                    'DEPTH_HV3' => $depth['hv_3'] ?? '50.00',
                    
                    // Ambang Batas H1
                    'AMBANG_H1_AMAN' => $ambangBatasH1['aman'] ?? '-18.77',
                    'AMBANG_H1_PERINGATAN' => $ambangBatasH1['peringatan'] ?? '-21.66',
                    'AMBANG_H1_BAHAYA' => $ambangBatasH1['bahaya'] ?? '-25.60',
                    'AMBANG_H1_PERGRAKAN' => $ambangBatasH1['pergerakan'] ?? '-',
                    
                    // Ambang Batas H2
                    'AMBANG_H2_AMAN' => $ambangBatasH2['aman'] ?? '-9.02',
                    'AMBANG_H2_PERINGATAN' => $ambangBatasH2['peringatan'] ?? '-10.41',
                    'AMBANG_H2_BAHAYA' => $ambangBatasH2['bahaya'] ?? '-12.30',
                    'AMBANG_H2_PERGRAKAN' => $ambangBatasH2['pergerakan'] ?? '-',
                    
                    // Ambang Batas H3
                    'AMBANG_H3_AMAN' => $ambangBatasH3['aman'] ?? '-5.94',
                    'AMBANG_H3_PERINGATAN' => $ambangBatasH3['peringatan'] ?? '-6.85',
                    'AMBANG_H3_BAHAYA' => $ambangBatasH3['bahaya'] ?? '-8.10',
                    'AMBANG_H3_PERGRAKAN' => $ambangBatasH3['pergerakan'] ?? '-'
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::exportExcel: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat export data'
            ]);
        }
    }

    /**
     * Hitung ulang pergerakan untuk HDM 625
     */
    public function recalculatePergerakan()
    {
        try {
            $pengukuranData = $this->pengukuranModel->findAll();
            $results = [];

            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                
                // Pastikan ambang batas ada
                $this->ensureAmbangBatasExists($id);
                
                $success = $this->pergerakan625Model->hitungPergerakan($id);
                
                // Update pergerakan di ambang batas setelah perhitungan
                $pergerakan = $this->pergerakan625Model->getByPengukuran($id);
                $this->updatePergerakanAmbangBatas($id, $pergerakan);
                
                $results[] = [
                    'id_pengukuran' => $id,
                    'tahun' => $pengukuran['tahun'],
                    'periode' => $pengukuran['periode'],
                    'success' => $success
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Perhitungan pergerakan HDM 625 selesai',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::recalculatePergerakan: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menghitung pergerakan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update ambang batas untuk HDM 625
     */
    public function updateAmbangBatas($id_pengukuran)
    {
        try {
            $data = $this->request->getJSON(true);

            // Pastikan data ambang batas ada sebelum update
            $this->ensureAmbangBatasExists($id_pengukuran);

            // Update masing-masing ambang batas
            if (isset($data['h1'])) {
                $this->ambangBatasH1Model->updateByPengukuran($id_pengukuran, $data['h1']);
            }
            if (isset($data['h2'])) {
                $this->ambangBatasH2Model->updateByPengukuran($id_pengukuran, $data['h2']);
            }
            if (isset($data['h3'])) {
                $this->ambangBatasH3Model->updateByPengukuran($id_pengukuran, $data['h3']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ambang batas berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::updateAmbangBatas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat update ambang batas'
            ]);
        }
    }

    /**
     * Get data untuk chart HDM 625
     */
    public function chartData()
    {
        try {
            $pengukuranData = $this->pengukuranModel
                ->orderBy('tanggal', 'ASC')
                ->findAll();

            $chartData = [
                'labels' => [],
                'hv1' => [],
                'hv2' => [],
                'hv3' => []
            ];

            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                $pergerakan = $this->pergerakan625Model->getByPengukuran($id);
                
                $chartData['labels'][] = $pengukuran['periode'] . ' ' . $pengukuran['tahun'];
                $chartData['hv1'][] = $pergerakan['hv_1'] ?? 0;
                $chartData['hv2'][] = $pergerakan['hv_2'] ?? 0;
                $chartData['hv3'][] = $pergerakan['hv_3'] ?? 0;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::chartData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memuat data chart'
            ]);
        }
    }

    /**
     * API untuk mendapatkan data HDM 625
     */
    public function apiData()
    {
        try {
            $tahun = $this->request->getGet('tahun');
            $periode = $this->request->getGet('periode');
            $dma = $this->request->getGet('dma');

            $pengukuranModel = $this->pengukuranModel;

            if ($tahun) {
                $pengukuranModel->where('tahun', $tahun);
            }

            if ($periode) {
                $pengukuranModel->where('periode', $periode);
            }

            if ($dma) {
                $pengukuranModel->where('dma', $dma);
            }

            $pengukuranData = $pengukuranModel
                ->orderBy('tanggal', 'DESC')
                ->findAll();

            $data = [];
            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                
                // Pastikan ambang batas ada
                $this->ensureAmbangBatasExists($id);

                $data[] = [
                    'id_pengukuran' => $id,
                    'tahun' => $pengukuran['tahun'],
                    'periode' => $pengukuran['periode'],
                    'tanggal' => $pengukuran['tanggal'],
                    'dma' => $pengukuran['dma'],
                    'pembacaan' => $this->pembacaan625Model->getByPengukuran($id),
                    'initial_reading' => $this->initial625Model->getByPengukuran($id),
                    'pergerakan' => $this->pergerakan625Model->getByPengukuran($id),
                    'depth' => $this->depth625Model->where('id_pengukuran', $id)->first(),
                    'ambang_batas' => [
                        'h1' => $this->ambangBatasH1Model->getByPengukuran($id),
                        'h2' => $this->ambangBatasH2Model->getByPengukuran($id),
                        'h3' => $this->ambangBatasH3Model->getByPengukuran($id)
                    ]
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::apiData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memuat data API'
            ]);
        }
    }

    /**
     * Insert data default ambang batas untuk pengukuran baru
     */
    public function insertDefaultAmbangBatas($id_pengukuran)
    {
        try {
            $this->ambangBatasH1Model->insertDefault($id_pengukuran);
            $this->ambangBatasH2Model->insertDefault($id_pengukuran);
            $this->ambangBatasH3Model->insertDefault($id_pengukuran);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data ambang batas default berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::insertDefaultAmbangBatas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menambahkan ambang batas default'
            ]);
        }
    }

    /**
     * Bulk insert default ambang batas untuk semua pengukuran yang belum memiliki
     */
    public function bulkInsertDefaultAmbangBatas()
    {
        try {
            $pengukuranData = $this->pengukuranModel->findAll();
            $count = 0;

            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                
                // Check if ambang batas already exists
                if (!$this->ambangBatasH1Model->getByPengukuran($id)) {
                    $this->ambangBatasH1Model->insertDefault($id);
                    $this->ambangBatasH2Model->insertDefault($id);
                    $this->ambangBatasH3Model->insertDefault($id);
                    $count++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Data ambang batas default berhasil ditambahkan untuk {$count} pengukuran"
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::bulkInsertDefaultAmbangBatas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menambahkan ambang batas default secara bulk'
            ]);
        }
    }

    /**
     * Sync pergerakan ambang batas dari tabel pergerakan untuk semua data
     */
    public function syncPergerakanAmbangBatas()
    {
        try {
            $pengukuranData = $this->pengukuranModel->findAll();
            $results = [];

            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                
                // Pastikan ambang batas ada
                $this->ensureAmbangBatasExists($id);
                
                // Update pergerakan di ambang batas
                $pergerakan = $this->pergerakan625Model->getByPengukuran($id);
                $this->updatePergerakanAmbangBatas($id, $pergerakan);
                
                $results[] = [
                    'id_pengukuran' => $id,
                    'tahun' => $pengukuran['tahun'],
                    'periode' => $pengukuran['periode'],
                    'success' => true
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sync pergerakan ambang batas berhasil',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm625Controller::syncPergerakanAmbangBatas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat sync pergerakan ambang batas'
            ]);
        }
    }
}