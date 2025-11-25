<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganL10Model;
use App\Models\LeftPiez\PerhitunganSpz02Model;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanL10Model;
use App\Models\LeftPiez\TPembacaanSpz02Model;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL10Spz02 extends BaseController
{
    protected $perhitunganL10Model;
    protected $perhitunganSpz02Model;
    protected $ireadingA;
    protected $ireadingB;
    protected $pembacaanL10Model;
    protected $pembacaanSpz02Model;
    protected $pengukuranModel;

    public function __construct()
    {
        $this->perhitunganL10Model = new PerhitunganL10Model();
        $this->perhitunganSpz02Model = new PerhitunganSpz02Model();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pembacaanL10Model = new TPembacaanL10Model();
        $this->pembacaanSpz02Model = new TPembacaanSpz02Model();
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
    }

    public function index()
    {
        // Dapatkan data yang diperlukan untuk tabel
        $dataPengukuran = $this->getDataForTable();

        $data = [
            'title' => 'Grafik History L10 & SPZ-02',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L10 & SPZ-02' => current_url()
            ],
            'pengukuran' => $dataPengukuran, // Data untuk tabel
            'dataL10' => $this->getDataL10(),
            'dataSpz02' => $this->getDataSpz02(),
            'initialReadingsA' => $this->getInitialReadingsA(),
            'initialReadingsB' => $this->getInitialReadingsB()
        ];

        return view('left_piez/grafik-history-l10-spz02', $data);
    }

    /**
     * Method untuk mendapatkan data terstruktur untuk tabel
     */
    private function getDataForTable()
    {
        try {
            $data = [];
            
            // Ambil semua data pengukuran dari TPengukuranLeftpiezModel
            $allPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->findAll();
            
            foreach ($allPengukuran as $pengukuran) {
                $id_pengukuran = $pengukuran['id_pengukuran'];
                
                // Ambil data pembacaan untuk L10 dan SPZ-02
                $pembacaanL10 = $this->pembacaanL10Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanSpz02 = $this->pembacaanSpz02Model->where('id_pengukuran', $id_pengukuran)->first();
                
                // Ambil data perhitungan untuk L10 dan SPZ-02
                $perhitunganL10 = $this->perhitunganL10Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganSpz02 = $this->perhitunganSpz02Model->where('id_pengukuran', $id_pengukuran)->first();
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_10' => $pembacaanL10 ?? ['feet' => 0, 'inch' => 0],
                        'SPZ_02' => $pembacaanSpz02 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l10' => $perhitunganL10 ?? ['t_psmetrik_L10' => 0],
                    'perhitungan_spz02' => $perhitunganSpz02 ?? ['t_psmetrik_SPZ02' => 0]
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataForTable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L10 dengan handling field yang tidak konsisten
     */
    private function getDataL10()
    {
        try {
            // Ambil semua data perhitungan L10
            $perhitunganL10 = $this->perhitunganL10Model->findAll();
            
            $data = [];
            foreach ($perhitunganL10 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L10' => $item['t_psmetrik_L10'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL10: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for SPZ-02 dengan handling field yang tidak konsisten
     */
    private function getDataSpz02()
    {
        try {
            $perhitunganSpz02 = $this->perhitunganSpz02Model->findAll();
            
            $data = [];
            foreach ($perhitunganSpz02 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_SPZ02' => $item['t_psmetrik_SPZ02'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataSpz02: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get initial readings from IreadingA untuk L10 dan SPZ-02
     */
    private function getInitialReadingsA()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L10 dan SPZ-02 dari IreadingA dengan error handling
            $readings['L_10'] = method_exists($this->ireadingA, 'forAL10') ? $this->ireadingA->forAL10() : [];
            $readings['SPZ_02'] = method_exists($this->ireadingA, 'forASpz02') ? $this->ireadingA->forASpz02() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsA: ' . $e->getMessage());
            return [
                'L_10' => [],
                'SPZ_02' => []
            ];
        }
    }

    /**
     * Get initial readings from IreadingB untuk L10 dan SPZ-02
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L10 dan SPZ-02 dari IreadingB dengan error handling
            $readings['L_10'] = method_exists($this->ireadingB, 'forBL10') ? $this->ireadingB->forBL10() : [];
            $readings['SPZ_02'] = method_exists($this->ireadingB, 'forBSpz02') ? $this->ireadingB->forBSpz02() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsB: ' . $e->getMessage());
            return [
                'L_10' => [],
                'SPZ_02' => []
            ];
        }
    }

    /**
     * API endpoint untuk data grafik dengan error handling
     */
    public function apiData()
    {
        try {
            $data = [
                'success' => true,
                'l10' => $this->getDataL10(),
                'spz02' => $this->getDataSpz02(),
                'initialA' => $this->getInitialReadingsA(),
                'initialB' => $this->getInitialReadingsB()
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in apiData: ' . $e->getMessage());
            
            $errorData = [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage(),
                'l10' => [],
                'spz02' => [],
                'initialA' => [],
                'initialB' => []
            ];

            return $this->response->setStatusCode(500)->setJSON($errorData);
        }
    }

    /**
     * Method untuk debug - melihat struktur field sebenarnya
     */
    public function debugStructure()
    {
        // Ambil satu record dari setiap tabel untuk melihat struktur
        $debugData = [];
        
        // Pengukuran
        $samplePengukuran = $this->pengukuranModel->first();
        $debugData['Pengukuran_fields'] = $samplePengukuran ? array_keys($samplePengukuran) : 'No data';
        
        // L10
        $sampleL10 = $this->perhitunganL10Model->first();
        $debugData['L10_fields'] = $sampleL10 ? array_keys($sampleL10) : 'No data';
        
        // SPZ-02
        $sampleSpz02 = $this->perhitunganSpz02Model->first();
        $debugData['SPZ02_fields'] = $sampleSpz02 ? array_keys($sampleSpz02) : 'No data';
        
        // Pembacaan L10
        $samplePembacaanL10 = $this->pembacaanL10Model->first();
        $debugData['PembacaanL10_fields'] = $samplePembacaanL10 ? array_keys($samplePembacaanL10) : 'No data';
        
        return $this->response->setJSON($debugData);
    }

    /**
     * Method untuk debug data tabel
     */
    public function debugTableData()
    {
        $testData = $this->getDataForTable();
        echo "<pre>";
        print_r($testData);
        echo "</pre>";
    }
}