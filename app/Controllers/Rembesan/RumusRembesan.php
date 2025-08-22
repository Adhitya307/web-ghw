<?php

namespace App\Controllers\Rembesan;

use App\Controllers\BaseController;
use App\Models\Rembesan\DataGabunganModel;
use App\Models\Rembesan\PerhitunganSRModel;
use App\Models\Rembesan\PerhitunganBocoranModel;
use App\Models\Rembesan\PerhitunganIntiGaleryModel;
use App\Models\Rembesan\PerhitunganSpillwayModel;
use App\Models\Rembesan\TebingKananModel;
use App\Models\Rembesan\TotalBocoranModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RumusRembesan extends BaseController
{
    protected $srModel;
    protected $bocoranModel;
    protected $intiGaleryModel;
    protected $spillwayModel;
    protected $tebingModel;
    protected $totalBocoranModel;
    protected $dataGabunganModel;

    public function __construct()
    {
        parent::__construct();
        
        // Load models
        $this->srModel = new PerhitunganSRModel();
        $this->bocoranModel = new PerhitunganBocoranModel();
        $this->intiGaleryModel = new PerhitunganIntiGaleryModel();
        $this->spillwayModel = new PerhitunganSpillwayModel();
        $this->tebingModel = new TebingKananModel();
        $this->totalBocoranModel = new TotalBocoranModel();
        $this->dataGabunganModel = new DataGabunganModel();
        
        // Load helper dengan path yang benar
        helper([
            'Rembesan/thomson', 
            'Rembesan/sr', 
            'Rembesan/bocoran', 
            'Rembesan/ambang', 
            'Rembesan/spillway',
            'Rembesan/TebingKanan', 
            'Rembesan/totalBocoran', 
            'Rembesan/BatasMaksimal'
        ]);

        // Enable debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        return view('menu');
    }

    public function inputData()
    {
        log_message('debug', '=== START inputData() ===');
        
        try {
            // Ambil data gabungan
            log_message('debug', 'Mengambil data gabungan dari model');
            $dataGabungan = $this->dataGabunganModel->getDataGabungan();
            
            if (empty($dataGabungan)) {
                log_message('debug', 'Data gabungan kosong');
                return view('Data/data_rembesan', [
                    'dataGabungan' => [],
                    'active'       => 'pengukuran'
                ]);
            }

            log_message('debug', 'Data gabungan ditemukan: ' . count($dataGabungan) . ' records');

            // Load Excel
            log_message('debug', 'Memuat file Excel Thomson');
            $thomsonPath = FCPATH . 'assets/excel/tabel_thomson.xlsx';
            if (!file_exists($thomsonPath)) {
                log_message('error', 'File Thomson tidak ditemukan: ' . $thomsonPath);
                throw new \Exception('File Thomson tidak ditemukan');
            }
            
            $spreadsheetThomson = IOFactory::load($thomsonPath);
            $sheetThomson = $spreadsheetThomson->getSheetByName('Tabel Thomson');
            if (!$sheetThomson) {
                log_message('error', 'Sheet Tabel Thomson tidak ditemukan');
                throw new \Exception('Sheet Tabel Thomson tidak ditemukan');
            }

            log_message('debug', 'Memuat file Excel Ambang');
            $ambangPath = FCPATH . 'assets/excel/tabel_ambang.xlsx';
            if (!file_exists($ambangPath)) {
                log_message('error', 'File Ambang tidak ditemukan: ' . $ambangPath);
                throw new \Exception('File Ambang tidak ditemukan');
            }
            
            $spreadsheetAmbang = IOFactory::load($ambangPath);
            $sheetAmbang = $spreadsheetAmbang->getSheetByName('AMBANG TIAP CM') ?: $spreadsheetAmbang->getActiveSheet();

            // Test helper functions
            log_message('debug', 'Testing helper functions');
            if (!function_exists('perhitunganQ_thomson')) {
                log_message('error', 'Function perhitunganQ_thomson() tidak ditemukan');
                throw new \Exception('Thomson helper tidak terload');
            }

            // Ambang Inti Galeri dan Tebing
            $ambangData = getAmbangData($sheetAmbang);
            $ambangDataTebing = getAmbangTebingKanan($ambangPath, 'AMBANG TIAP CM');
            $spillwayDataArray = loadAmbangSpillway($ambangPath, 'AMBANG TIAP CM');

            $sr_fields = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];

            foreach ($dataGabungan as &$data) {
                $pengukuran_id = $data['pengukuran_id'] ?? null;
                log_message('debug', 'Processing pengukuran_id: ' . $pengukuran_id);
                
                if (!$pengukuran_id) {
                    log_message('warning', 'Pengukuran ID kosong, skipping');
                    continue;
                }

                $tma = (float)($data['pengukuran']['tma_waduk'] ?? 0);
                log_message('debug', 'TMA: ' . $tma);

                // ================== Perhitungan Thomson ==================
                log_message('debug', 'Memulai perhitungan Thomson');
                $a1_r = $data['thomson']['a1_r'] ?? 0;
                $a1_l = $data['thomson']['a1_l'] ?? 0;
                $b1 = $data['thomson']['b1'] ?? 0;
                $b3 = $data['thomson']['b3'] ?? 0;
                $b5 = $data['thomson']['b5'] ?? 0;
                
                log_message('debug', 'Thomson values - a1_r: ' . $a1_r . ', a1_l: ' . $a1_l . ', b1: ' . $b1 . ', b3: ' . $b3 . ', b5: ' . $b5);

                $thomson = [
                    'r'  => perhitunganQ_thomson($a1_r, $sheetThomson),
                    'l'  => perhitunganQ_thomson($a1_l, $sheetThomson),
                    'b1' => perhitunganQ_thomson($b1, $sheetThomson),
                    'b3' => perhitunganQ_thomson($b3, $sheetThomson),
                    'b5' => perhitunganQ_thomson($b5, $sheetThomson),
                ];
                
                log_message('debug', 'Hasil Thomson - r: ' . $thomson['r'] . ', l: ' . $thomson['l'] . ', b1: ' . $thomson['b1'] . ', b3: ' . $thomson['b3'] . ', b5: ' . $thomson['b5']);
                $data['perhitungan_thomson'] = $thomson;

                // ================== Perhitungan SR ==================
                $perhitunganSR = [];
                foreach ($sr_fields as $field) {
                    $nilai = $data['sr']["sr_{$field}_nilai"] ?? 0;
                    $kode  = $data['sr']["sr_{$field}_kode"] ?? '';
                    $perhitunganSR["sr_{$field}_q"] = perhitunganQ_sr($nilai, $kode);
                }

                // ================== Perhitungan Bocoran ==================
                $perhitunganBocoran = [
                    'talang1' => perhitunganQ_bocoran($data['bocoran']['elv_624_t1'] ?? 0, $data['bocoran']['elv_624_t1_kode'] ?? ''),
                    'talang2' => perhitunganQ_bocoran($data['bocoran']['elv_615_t2'] ?? 0, $data['bocoran']['elv_615_t2_kode'] ?? ''),
                    'pipa'    => perhitunganQ_bocoran($data['bocoran']['pipa_p1'] ?? 0, $data['bocoran']['pipa_p1_kode'] ?? ''),
                ];

                // ================== Perhitungan Inti Galeri ==================
                $a1 = $thomson['r'] + $thomson['l'];
                $ambang_a1 = ($tma > 0) ? cariAmbangArray($tma, $ambangData) : null;
                $perhitunganInti = [
                    'pengukuran_id' => $pengukuran_id,
                    'a1'            => $a1,
                    'ambang_a1'     => $ambang_a1,
                ];

                // ================== Perhitungan Spillway ==================
                $B3 = hitungSpillway($thomson['b1'], $thomson['b3']);
                $ambangSpillway = ($tma > 0) ? cariAmbangSpillway($tma, $spillwayDataArray) : null;
                $spillwayData = [
                    'pengukuran_id' => $pengukuran_id,
                    'B3'            => $B3,
                    'ambang'        => $ambangSpillway
                ];

                // ================== Perhitungan Tebing Kanan ==================
                $sr_tebing = hitungSrTebingKanan($data['sr'] ?? [], $sr_fields);
                $ambang_tebing = ($tma > 0) ? cariAmbangTebingKanan($tma, $ambangDataTebing) : null;
                $b5_thomson = $data['perhitungan_thomson']['b5'] ?? 0;

                $perhitunganTebing = [
                    'sr' => $sr_tebing,
                    'ambang' => $ambang_tebing,
                    'pengukuran_id' => $pengukuran_id,
                    'b5' => $b5_thomson,
                ];

                // ================== Perhitungan Total Bocoran ==================
                $r1 = hitungTotalBocoran($perhitunganInti['a1'], $spillwayData['B3'], $perhitunganTebing['sr']);
                log_message('debug', 'Total Bocoran R1: ' . $r1);

                // ================== Perhitungan Batas Maksimal ==================
                $batasData = loadBatasMaksimal($sheetAmbang);
                $batasMaksimal = cariBatasMaksimal($tma, $batasData);

                $data['batas_maksimal'] = $batasMaksimal;

                // ================== Simpan ke DB ==================
                log_message('debug', 'Menyimpan data ke database');

                // SR
                $perhitunganSR['pengukuran_id'] = $pengukuran_id;
                $cekSR = $this->srModel->where('pengukuran_id', $pengukuran_id)->first();
                if ($cekSR) {
                    $this->srModel->update($cekSR['id'], $perhitunganSR);
                    log_message('debug', 'Updated SR data untuk pengukuran_id: ' . $pengukuran_id);
                } else {
                    $this->srModel->insert($perhitunganSR);
                    log_message('debug', 'Inserted SR data untuk pengukuran_id: ' . $pengukuran_id);
                }

                // Bocoran
                $perhitunganBocoran['pengukuran_id'] = $pengukuran_id;
                $cekBocoran = $this->bocoranModel->where('pengukuran_id', $pengukuran_id)->first();
                if ($cekBocoran) {
                    $this->bocoranModel->update($cekBocoran['id'], $perhitunganBocoran);
                    log_message('debug', 'Updated Bocoran data');
                } else {
                    $this->bocoranModel->insert($perhitunganBocoran);
                    log_message('debug', 'Inserted Bocoran data');
                }

                // Inti Galeri
                $cekInti = $this->intiGaleryModel->where('pengukuran_id', $pengukuran_id)->first();
                if ($cekInti) {
                    $this->intiGaleryModel->update($cekInti['id'], $perhitunganInti);
                    log_message('debug', 'Updated Inti data');
                } else {
                    $this->intiGaleryModel->insert($perhitunganInti);
                    log_message('debug', 'Inserted Inti data');
                }

                // Spillway
                $cekSpillway = $this->spillwayModel->where('pengukuran_id', $pengukuran_id)->first();
                if ($cekSpillway) {
                    $this->spillwayModel->update($cekSpillway['id'], $spillwayData);
                    log_message('debug', 'Updated Spillway data');
                } else {
                    $this->spillwayModel->insert($spillwayData);
                    log_message('debug', 'Inserted Spillway data');
                }

                // Tebing Kanan
                $cekTebing = $this->tebingModel->where('pengukuran_id', $pengukuran_id)->first();
                if ($cekTebing) {
                    $this->tebingModel->update($cekTebing['id'], $perhitunganTebing);
                    log_message('debug', 'Updated Tebing data');
                } else {
                    $this->tebingModel->insert($perhitunganTebing);
                    log_message('debug', 'Inserted Tebing data');
                }

                // Total Bocoran
                $totalBocoranData = [
                    'pengukuran_id' => $pengukuran_id,
                    'R1' => $r1
                ];
                $cekTotal = $this->totalBocoranModel->where('pengukuran_id', $pengukuran_id)->first();
                if ($cekTotal) {
                    $this->totalBocoranModel->update($cekTotal['id'], $totalBocoranData);
                    log_message('debug', 'Updated Total Bocoran data');
                } else {
                    $this->totalBocoranModel->insert($totalBocoranData);
                    log_message('debug', 'Inserted Total Bocoran data');
                }

                // ================== Untuk view ==================
                $data['perhitungan_sr']       = $perhitunganSR;
                $data['perhitungan_bocoran']  = $perhitunganBocoran;
                $data['perhitungan_inti']     = $perhitunganInti;
                $data['perhitungan_spillway'] = $spillwayData;
                $data['perhitungan_tebing_kanan'] = $perhitunganTebing;
                $data['perhitungan_total_bocoran'] = ['r1' => $r1];
            }

            log_message('debug', '=== END inputData() - SUCCESS ===');
            return view('Data/data_rembesan', [
                'dataGabungan' => $dataGabungan,
                'active'       => 'pengukuran'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in inputData(): ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Tampilkan error untuk debugging
            return "Error: " . $e->getMessage() . "<br>File: " . $e->getFile() . "<br>Line: " . $e->getLine();
        }
    }

    public function getRembesanData()
    {
        $dataGabungan = $this->dataGabunganModel->getDataGabungan();

        $sheetThomson = IOFactory::load(FCPATH . 'assets/excel/tabel_thomson.xlsx')
                                 ->getSheetByName('Tabel Thomson');

        $spreadsheetAmbang = IOFactory::load(FCPATH . 'assets/excel/tabel_ambang.xlsx');
        $sheetAmbang = $spreadsheetAmbang->getSheetByName('AMBANG TIAP CM')
                      ?: $spreadsheetAmbang->getActiveSheet();

        $ambangData = getAmbangData($sheetAmbang);
        $ambangDataTebing = getAmbangTebingKanan(FCPATH . 'assets/excel/tabel_ambang.xlsx', 'AMBANG TIAP CM');
        $spillwayDataArray = loadAmbangSpillway(FCPATH . 'assets/excel/tabel_ambang.xlsx', 'AMBANG TIAP CM');

        $sr_fields = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];

        foreach ($dataGabungan as &$data) {
            $tma = (float)($data['pengukuran']['tma_waduk'] ?? 0);

            // Perhitungan Thomson
            $thomson = [
                'r'  => perhitunganQ_thomson($data['thomson']['a1_r'] ?? 0, $sheetThomson),
                'l'  => perhitunganQ_thomson($data['thomson']['a1_l'] ?? 0, $sheetThomson),
                'b1' => perhitunganQ_thomson($data['thomson']['b1'] ?? 0, $sheetThomson),
                'b3' => perhitunganQ_thomson($data['thomson']['b3'] ?? 0, $sheetThomson),
                'b5' => perhitunganQ_thomson($data['thomson']['b5'] ?? 0, $sheetThomson),
            ];
            $data['perhitungan_thomson'] = $thomson;

            // Perhitungan SR
            $perhitunganSR = [];
            foreach ($sr_fields as $field) {
                $nilai = $data['sr']["sr_{$field}_nilai"] ?? 0;
                $kode  = $data['sr']["sr_{$field}_kode"] ?? '';
                $perhitunganSR["sr_{$field}_q"] = perhitunganQ_sr($nilai, $kode);
            }
            $data['perhitungan_sr'] = $perhitunganSR;

            // Perhitungan Bocoran
            $data['perhitungan_bocoran'] = [
                'talang1' => perhitunganQ_bocoran($data['bocoran']['elv_624_t1'] ?? 0, $data['bocoran']['elv_624_t1_kode'] ?? ''),
                'talang2' => perhitunganQ_bocoran($data['bocoran']['elv_615_t2'] ?? 0, $data['bocoran']['elv_615_t2_kode'] ?? ''),
                'pipa'    => perhitunganQ_bocoran($data['bocoran']['pipa_p1'] ?? 0, $data['bocoran']['pipa_p1_kode'] ?? ''),
            ];

            // Perhitungan Inti
            $a1 = $thomson['r'] + $thomson['l'];
            $ambang_a1 = ($tma > 0) ? cariAmbangArray($tma, $ambangData) : null;
            $data['perhitungan_inti'] = ['a1' => $a1, 'ambang_a1' => $ambang_a1];

            // Spillway
            $B3 = hitungSpillway($thomson['b1'], $thomson['b3']);
            $ambangSpillway = ($tma > 0) ? cariAmbangSpillway($tma, $spillwayDataArray) : null;
            $data['perhitungan_spillway'] = ['B3' => $B3, 'ambang' => $ambangSpillway];

            // Tebing kanan
            $sr_tebing = hitungSrTebingKanan($data['sr'] ?? [], $sr_fields);
            $ambang_tebing = ($tma > 0) ? cariAmbangTebingKanan($tma, $ambangDataTebing) : null;
            $b5_thomson = $data['perhitungan_thomson']['b5'] ?? 0;
            $data['perhitungan_tebing_kanan'] = [
                'sr' => $sr_tebing,
                'ambang' => $ambang_tebing,
                'b5' => $b5_thomson,
            ];

            // Total Bocoran
            $r1 = hitungTotalBocoran($a1, $B3, $sr_tebing);
            $data['perhitungan_total_bocoran'] = ['r1' => $r1];

            // Batas Maksimal
            $batasData = loadBatasMaksimal($sheetAmbang);
            $data['batas_maksimal'] = cariBatasMaksimal($tma, $batasData);
        }

        return $this->response->setJSON($dataGabungan);
    }

    public function inputDataForId($pengukuran_id)
    {
        $data = $this->dataGabunganModel->find($pengukuran_id);

        if (!$data) return false;

        // Load Excel
        $sheetThomson = IOFactory::load(FCPATH . 'assets/excel/tabel_thomson.xlsx')
                                 ->getSheetByName('Tabel Thomson');
        $spreadsheetAmbang = IOFactory::load(FCPATH . 'assets/excel/tabel_ambang.xlsx');
        $sheetAmbang = $spreadsheetAmbang->getSheetByName('AMBANG TIAP CM') 
                      ?: $spreadsheetAmbang->getActiveSheet();

        // Ambang & spillway
        $ambangData = getAmbangData($sheetAmbang);
        $ambangDataTebing = getAmbangTebingKanan(FCPATH . 'assets/excel/tabel_ambang.xlsx', 'AMBANG TIAP CM');
        $spillwayDataArray = loadAmbangSpillway(FCPATH . 'assets/excel/tabel_ambang.xlsx', 'AMBANG TIAP CM');

        $sr_fields = [1,40,66,68,70,79,81,83,85,92,94,96,98,100,102,104,106];

        // --------------------- Perhitungan ---------------------
        $tma = (float)($data['pengukuran']['tma_waduk'] ?? 0);

        $thomson = [
            'r'  => perhitunganQ_thomson($data['thomson']['a1_r'] ?? 0, $sheetThomson),
            'l'  => perhitunganQ_thomson($data['thomson']['a1_l'] ?? 0, $sheetThomson),
            'b1' => perhitunganQ_thomson($data['thomson']['b1'] ?? 0, $sheetThomson),
            'b3' => perhitunganQ_thomson($data['thomson']['b3'] ?? 0, $sheetThomson),
            'b5' => perhitunganQ_thomson($data['thomson']['b5'] ?? 0, $sheetThomson),
        ];

        $perhitunganSR = [];
        foreach ($sr_fields as $field) {
            $nilai = $data['sr']["sr_{$field}_nilai"] ?? 0;
            $kode  = $data['sr']["sr_{$field}_kode"] ?? '';
            $perhitunganSR["sr_{$field}_q"] = perhitunganQ_sr($nilai, $kode);
        }

        $perhitunganBocoran = [
            'talang1' => perhitunganQ_bocoran($data['bocoran']['elv_624_t1'] ?? 0, $data['bocoran']['elv_624_t1_kode'] ?? ''),
            'talang2' => perhitunganQ_bocoran($data['bocoran']['elv_615_t2'] ?? 0, $data['bocoran']['elv_615_t2_kode'] ?? ''),
            'pipa'    => perhitunganQ_bocoran($data['bocoran']['pipa_p1'] ?? 0, $data['bocoran']['pipa_p1_kode'] ?? ''),
        ];

        $a1 = $thomson['r'] + $thomson['l'];
        $ambang_a1 = ($tma > 0) ? cariAmbangArray($tma, $ambangData) : null;
        $perhitunganInti = [
            'pengukuran_id' => $pengukuran_id,
            'a1' => $a1, 
            'ambang_a1' => $ambang_a1
        ];

        $B3 = hitungSpillway($thomson['b1'], $thomson['b3']);
        $ambangSpillway = ($tma > 0) ? cariAmbangSpillway($tma, $spillwayDataArray) : null;
        $spillwayData = [
            'pengukuran_id' => $pengukuran_id,
            'B3' => $B3, 
            'ambang' => $ambangSpillway
        ];

        $sr_tebing = hitungSrTebingKanan($data['sr'] ?? [], $sr_fields);
        $ambang_tebing = ($tma > 0) ? cariAmbangTebingKanan($tma, $ambangDataTebing) : null;
        $b5_thomson = $thomson['b5'];
        $perhitunganTebing = [
            'pengukuran_id' => $pengukuran_id,
            'sr' => $sr_tebing,
            'ambang' => $ambang_tebing,
            'b5' => $b5_thomson
        ];

        $r1 = hitungTotalBocoran($a1, $B3, $sr_tebing);
        $batasData = loadBatasMaksimal($sheetAmbang);
        $batasMaksimal = cariBatasMaksimal($tma, $batasData);

        // --------------------- Simpan ke DB ---------------------
        // SR
        $perhitunganSR['pengukuran_id'] = $pengukuran_id;
        $cekSR = $this->srModel->where('pengukuran_id', $pengukuran_id)->first();
        $cekSR ? $this->srModel->update($cekSR['id'], $perhitunganSR) : $this->srModel->insert($perhitunganSR);

        // Bocoran
        $perhitunganBocoran['pengukuran_id'] = $pengukuran_id;
        $cekBocoran = $this->bocoranModel->where('pengukuran_id', $pengukuran_id)->first();
        $cekBocoran ? $this->bocoranModel->update($cekBocoran['id'], $perhitunganBocoran) : $this->bocoranModel->insert($perhitunganBocoran);

        // Inti
        $cekInti = $this->intiGaleryModel->where('pengukuran_id', $pengukuran_id)->first();
        $cekInti ? $this->intiGaleryModel->update($cekInti['id'], $perhitunganInti) : $this->intiGaleryModel->insert($perhitunganInti);

        // Spillway
        $cekSpillway = $this->spillwayModel->where('pengukuran_id', $pengukuran_id)->first();
        $cekSpillway ? $this->spillwayModel->update($cekSpillway['id'], $spillwayData) : $this->spillwayModel->insert($spillwayData);

        // Tebing
        $cekTebing = $this->tebingModel->where('pengukuran_id', $pengukuran_id)->first();
        $cekTebing ? $this->tebingModel->update($cekTebing['id'], $perhitunganTebing) : $this->tebingModel->insert($perhitunganTebing);

        // Total Bocoran
        $totalBocoranData = [
            'pengukuran_id' => $pengukuran_id,
            'R1' => $r1
        ];
        $cekTotal = $this->totalBocoranModel->where('pengukuran_id', $pengukuran_id)->first();
        $cekTotal ? $this->totalBocoranModel->update($cekTotal['id'], $totalBocoranData) : $this->totalBocoranModel->insert($totalBocoranData);

        return $batasMaksimal;
    }
}