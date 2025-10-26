<?php

namespace App\Controllers\HDM;

use App\Controllers\BaseController;
use App\Models\HDM\MPengukuranHdm;
use App\Models\HDM\MPembacaanElv600;
use App\Models\HDM\MInitialReadingElv600;
use App\Models\HDM\MPergerakanElv600;
use App\Models\HDM\DepthElv600Model;
use App\Models\HDM\AmbangBatas600H1Model;
use App\Models\HDM\AmbangBatas600H2Model;
use App\Models\HDM\AmbangBatas600H3Model;
use App\Models\HDM\AmbangBatas600H4Model;
use App\Models\HDM\AmbangBatas600H5Model;

class Hdm600Controller extends BaseController
{
    protected $pengukuranModel;
    protected $pembacaan600Model;
    protected $initial600Model;
    protected $pergerakan600Model;
    protected $depth600Model;
    protected $ambangBatasH1Model;
    protected $ambangBatasH2Model;
    protected $ambangBatasH3Model;
    protected $ambangBatasH4Model;
    protected $ambangBatasH5Model;

    public function __construct()
    {
        $this->pengukuranModel = new MPengukuranHdm();
        $this->pembacaan600Model = new MPembacaanElv600();
        $this->initial600Model = new MInitialReadingElv600();
        $this->pergerakan600Model = new MPergerakanElv600();
        $this->depth600Model = new DepthElv600Model();
        $this->ambangBatasH1Model = new AmbangBatas600H1Model();
        $this->ambangBatasH2Model = new AmbangBatas600H2Model();
        $this->ambangBatasH3Model = new AmbangBatas600H3Model();
        $this->ambangBatasH4Model = new AmbangBatas600H4Model();
        $this->ambangBatasH5Model = new AmbangBatas600H5Model();
    }

    /**
     * Menampilkan data HDM 600
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
                $pembacaan = $this->pembacaan600Model->getByPengukuran($id);
                $initial = $this->initial600Model->getByPengukuran($id);
                $pergerakan = $this->pergerakan600Model->getByPengukuran($id);
                $depth = $this->depth600Model->where('id_pengukuran', $id)->first();
                
                // Pastikan data ambang batas ada, jika tidak buat default
                $this->ensureAmbangBatasExists($id);
                
                // Update pergerakan di ambang batas (dikali 10)
                $this->updatePergerakanAmbangBatas($id, $pergerakan);

                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan_elv600' => $pembacaan,
                    'initial_reading_elv600' => $initial,
                    'pergerakan_elv600' => $pergerakan,
                    'depth_elv600' => $depth,
                    'ambang_batas' => [
                        'h1' => $this->ambangBatasH1Model->getByPengukuran($id),
                        'h2' => $this->ambangBatasH2Model->getByPengukuran($id),
                        'h3' => $this->ambangBatasH3Model->getByPengukuran($id),
                        'h4' => $this->ambangBatasH4Model->getByPengukuran($id),
                        'h5' => $this->ambangBatasH5Model->getByPengukuran($id)
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
                'title' => 'HDM 600 - Horizontal Displacement Meter',
                'data' => $data,
                'uniqueYears' => $uniqueYears,
                'uniquePeriods' => $uniquePeriods,
                'uniqueDMA' => $uniqueDMA
            ];

            return view('hdm/hdm_600', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data HDM 600: ' . $e->getMessage());
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
        if (!$this->ambangBatasH4Model->getByPengukuran($pengukuran_id)) {
            $this->ambangBatasH4Model->insertDefault($pengukuran_id);
        }
        if (!$this->ambangBatasH5Model->getByPengukuran($pengukuran_id)) {
            $this->ambangBatasH5Model->insertDefault($pengukuran_id);
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
            if (isset($pergerakan['hv_4'])) {
                $this->ambangBatasH4Model->updateByPengukuran($pengukuran_id, [
                    'pergerakan' => $formatAngka($pergerakan['hv_4'] * 10)
                ]);
            }
            if (isset($pergerakan['hv_5'])) {
                $this->ambangBatasH5Model->updateByPengukuran($pengukuran_id, [
                    'pergerakan' => $formatAngka($pergerakan['hv_5'] * 10)
                ]);
            }
        }
    }

    /**
     * Menampilkan detail data HDM 600
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
                'pembacaan_elv600' => $this->pembacaan600Model->getByPengukuran($id_pengukuran),
                'initial_reading_elv600' => $this->initial600Model->getByPengukuran($id_pengukuran),
                'pergerakan_elv600' => $this->pergerakan600Model->getByPengukuran($id_pengukuran),
                'depth_elv600' => $this->depth600Model->where('id_pengukuran', $id_pengukuran)->first(),
                'ambang_batas' => [
                    'h1' => $this->ambangBatasH1Model->getByPengukuran($id_pengukuran),
                    'h2' => $this->ambangBatasH2Model->getByPengukuran($id_pengukuran),
                    'h3' => $this->ambangBatasH3Model->getByPengukuran($id_pengukuran),
                    'h4' => $this->ambangBatasH4Model->getByPengukuran($id_pengukuran),
                    'h5' => $this->ambangBatasH5Model->getByPengukuran($id_pengukuran)
                ]
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $detailData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::detail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memuat detail data'
            ]);
        }
    }

    /**
     * Export data HDM 600 ke Excel
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
                
                $pembacaan = $this->pembacaan600Model->getByPengukuran($id);
                $initial = $this->initial600Model->getByPengukuran($id);
                $pergerakan = $this->pergerakan600Model->getByPengukuran($id);
                $depth = $this->depth600Model->where('id_pengukuran', $id)->first();
                
                // Pastikan ambang batas ada
                $this->ensureAmbangBatasExists($id);
                
                $ambangBatasH1 = $this->ambangBatasH1Model->getByPengukuran($id);
                $ambangBatasH2 = $this->ambangBatasH2Model->getByPengukuran($id);
                $ambangBatasH3 = $this->ambangBatasH3Model->getByPengukuran($id);
                $ambangBatasH4 = $this->ambangBatasH4Model->getByPengukuran($id);
                $ambangBatasH5 = $this->ambangBatasH5Model->getByPengukuran($id);

                $data[] = [
                    'TAHUN' => $pengukuran['tahun'],
                    'PERIODE' => $pengukuran['periode'],
                    'TANGGAL' => $pengukuran['tanggal'],
                    'DMA' => $pengukuran['dma'],
                    
                    // Initial Reading
                    'INITIAL_HV1' => $initial['hv_1'] ?? '26.60',
                    'INITIAL_HV2' => $initial['hv_2'] ?? '25.50',
                    'INITIAL_HV3' => $initial['hv_3'] ?? '24.50',
                    'INITIAL_HV4' => $initial['hv_4'] ?? '23.40',
                    'INITIAL_HV5' => $initial['hv_5'] ?? '23.60',
                    
                    // Pembacaan HDM
                    'PEMBACAAN_HV1' => $pembacaan['hv_1'] ?? '-',
                    'PEMBACAAN_HV2' => $pembacaan['hv_2'] ?? '-',
                    'PEMBACAAN_HV3' => $pembacaan['hv_3'] ?? '-',
                    'PEMBACAAN_HV4' => $pembacaan['hv_4'] ?? '-',
                    'PEMBACAAN_HV5' => $pembacaan['hv_5'] ?? '-',
                    
                    // Pergerakan (asli dari tabel pergerakan)
                    'PERGERAKAN_HV1' => $pergerakan['hv_1'] ?? '-',
                    'PERGERAKAN_HV2' => $pergerakan['hv_2'] ?? '-',
                    'PERGERAKAN_HV3' => $pergerakan['hv_3'] ?? '-',
                    'PERGERAKAN_HV4' => $pergerakan['hv_4'] ?? '-',
                    'PERGERAKAN_HV5' => $pergerakan['hv_5'] ?? '-',
                    
                    // Depth
                    'DEPTH_HV1' => $depth['hv_1'] ?? '10.00',
                    'DEPTH_HV2' => $depth['hv_2'] ?? '30.00',
                    'DEPTH_HV3' => $depth['hv_3'] ?? '50.00',
                    'DEPTH_HV4' => $depth['hv_4'] ?? '70.00',
                    'DEPTH_HV5' => $depth['hv_5'] ?? '84.50',
                    
                    // Ambang Batas H1
                    'AMBANG_H1_AMAN' => $ambangBatasH1['aman'] ?? '-44.29',
                    'AMBANG_H1_PERINGATAN' => $ambangBatasH1['peringatan'] ?? '-51.11',
                    'AMBANG_H1_BAHAYA' => $ambangBatasH1['bahaya'] ?? '-60.40',
                    'AMBANG_H1_PERGRAKAN' => $ambangBatasH1['pergerakan'] ?? '-',
                    
                    // Ambang Batas H2
                    'AMBANG_H2_AMAN' => $ambangBatasH2['aman'] ?? '-39.75',
                    'AMBANG_H2_PERINGATAN' => $ambangBatasH2['peringatan'] ?? '-45.86',
                    'AMBANG_H2_BAHAYA' => $ambangBatasH2['bahaya'] ?? '-54.20',
                    'AMBANG_H2_PERGRAKAN' => $ambangBatasH2['pergerakan'] ?? '-',
                    
                    // Ambang Batas H3
                    'AMBANG_H3_AMAN' => $ambangBatasH3['aman'] ?? '-40.63',
                    'AMBANG_H3_PERINGATAN' => $ambangBatasH3['peringatan'] ?? '-46.88',
                    'AMBANG_H3_BAHAYA' => $ambangBatasH3['bahaya'] ?? '-55.40',
                    'AMBANG_H3_PERGRAKAN' => $ambangBatasH3['pergerakan'] ?? '-',
                    
                    // Ambang Batas H4
                    'AMBANG_H4_AMAN' => $ambangBatasH4['aman'] ?? '-24.86',
                    'AMBANG_H4_PERINGATAN' => $ambangBatasH4['peringatan'] ?? '-28.68',
                    'AMBANG_H4_BAHAYA' => $ambangBatasH4['bahaya'] ?? '-33.90',
                    'AMBANG_H4_PERGRAKAN' => $ambangBatasH4['pergerakan'] ?? '-',
                    
                    // Ambang Batas H5
                    'AMBANG_H5_AMAN' => $ambangBatasH5['aman'] ?? '-11.22',
                    'AMBANG_H5_PERINGATAN' => $ambangBatasH5['peringatan'] ?? '-12.95',
                    'AMBANG_H5_BAHAYA' => $ambangBatasH5['bahaya'] ?? '-15.30',
                    'AMBANG_H5_PERGRAKAN' => $ambangBatasH5['pergerakan'] ?? '-'
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::exportExcel: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat export data'
            ]);
        }
    }

    /**
     * Update ambang batas untuk HDM 600
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
            if (isset($data['h4'])) {
                $this->ambangBatasH4Model->updateByPengukuran($id_pengukuran, $data['h4']);
            }
            if (isset($data['h5'])) {
                $this->ambangBatasH5Model->updateByPengukuran($id_pengukuran, $data['h5']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ambang batas berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::updateAmbangBatas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat update ambang batas'
            ]);
        }
    }

    /**
     * Get data untuk chart HDM 600
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
                'hv3' => [],
                'hv4' => [],
                'hv5' => []
            ];

            foreach ($pengukuranData as $pengukuran) {
                $id = $pengukuran['id_pengukuran'];
                $pergerakan = $this->pergerakan600Model->getByPengukuran($id);
                
                $chartData['labels'][] = $pengukuran['periode'] . ' ' . $pengukuran['tahun'];
                $chartData['hv1'][] = $pergerakan['hv_1'] ?? 0;
                $chartData['hv2'][] = $pergerakan['hv_2'] ?? 0;
                $chartData['hv3'][] = $pergerakan['hv_3'] ?? 0;
                $chartData['hv4'][] = $pergerakan['hv_4'] ?? 0;
                $chartData['hv5'][] = $pergerakan['hv_5'] ?? 0;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::chartData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memuat data chart'
            ]);
        }
    }

    /**
     * API untuk mendapatkan data HDM 600
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
                    'pembacaan' => $this->pembacaan600Model->getByPengukuran($id),
                    'initial_reading' => $this->initial600Model->getByPengukuran($id),
                    'pergerakan' => $this->pergerakan600Model->getByPengukuran($id),
                    'depth' => $this->depth600Model->where('id_pengukuran', $id)->first(),
                    'ambang_batas' => [
                        'h1' => $this->ambangBatasH1Model->getByPengukuran($id),
                        'h2' => $this->ambangBatasH2Model->getByPengukuran($id),
                        'h3' => $this->ambangBatasH3Model->getByPengukuran($id),
                        'h4' => $this->ambangBatasH4Model->getByPengukuran($id),
                        'h5' => $this->ambangBatasH5Model->getByPengukuran($id)
                    ]
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::apiData: ' . $e->getMessage());
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
            $this->ambangBatasH4Model->insertDefault($id_pengukuran);
            $this->ambangBatasH5Model->insertDefault($id_pengukuran);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data ambang batas default berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::insertDefaultAmbangBatas: ' . $e->getMessage());
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
                    $this->ambangBatasH4Model->insertDefault($id);
                    $this->ambangBatasH5Model->insertDefault($id);
                    $count++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Data ambang batas default berhasil ditambahkan untuk {$count} pengukuran"
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Hdm600Controller::bulkInsertDefaultAmbangBatas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menambahkan ambang batas default secara bulk'
            ]);
        }
    }
}