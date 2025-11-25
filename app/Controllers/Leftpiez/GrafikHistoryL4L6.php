<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganL04Model;
use App\Models\LeftPiez\PerhitunganL05Model;
use App\Models\LeftPiez\PerhitunganL06Model;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanL04Model;
use App\Models\LeftPiez\TPembacaanL05Model;
use App\Models\LeftPiez\TPembacaanL06Model;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL4L6 extends BaseController
{
    protected $perhitunganL04Model;
    protected $perhitunganL05Model;
    protected $perhitunganL06Model;
    protected $ireadingA;
    protected $ireadingB;
    protected $pembacaanL04Model;
    protected $pembacaanL05Model;
    protected $pembacaanL06Model;
    protected $pengukuranModel;

    public function __construct()
    {
        $this->perhitunganL04Model = new PerhitunganL04Model();
        $this->perhitunganL05Model = new PerhitunganL05Model();
        $this->perhitunganL06Model = new PerhitunganL06Model();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pembacaanL04Model = new TPembacaanL04Model();
        $this->pembacaanL05Model = new TPembacaanL05Model();
        $this->pembacaanL06Model = new TPembacaanL06Model();
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
    }

    public function index()
    {
        // Dapatkan data yang diperlukan untuk tabel
        $dataPengukuran = $this->getDataForTable();

        $data = [
            'title' => 'Grafik History L4-L6',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L4-L6' => current_url()
            ],
            'pengukuran' => $dataPengukuran, // Data untuk tabel
            'dataL04' => $this->getDataL04(),
            'dataL05' => $this->getDataL05(),
            'dataL06' => $this->getDataL06(),
            'initialReadingsA' => $this->getInitialReadingsA(),
            'initialReadingsB' => $this->getInitialReadingsB()
        ];

        return view('left_piez/grafik-history-l4-l6', $data);
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
                
                // Ambil data pembacaan untuk setiap titik L4-L6
                $pembacaanL04 = $this->pembacaanL04Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanL05 = $this->pembacaanL05Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanL06 = $this->pembacaanL06Model->where('id_pengukuran', $id_pengukuran)->first();
                
                // Ambil data perhitungan untuk setiap titik L4-L6
                $perhitunganL04 = $this->perhitunganL04Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganL05 = $this->perhitunganL05Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganL06 = $this->perhitunganL06Model->where('id_pengukuran', $id_pengukuran)->first();
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_04' => $pembacaanL04 ?? ['feet' => 0, 'inch' => 0],
                        'L_05' => $pembacaanL05 ?? ['feet' => 0, 'inch' => 0],
                        'L_06' => $pembacaanL06 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l04' => $perhitunganL04 ?? ['t_psmetrik_L04' => 0],
                    'perhitungan_l05' => $perhitunganL05 ?? ['t_psmetrik_L05' => 0],
                    'perhitungan_l06' => $perhitunganL06 ?? ['t_psmetrik_L06' => 0]
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataForTable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L04 dengan handling field yang tidak konsisten
     */
    private function getDataL04()
    {
        try {
            // Ambil semua data perhitungan L04
            $perhitunganL04 = $this->perhitunganL04Model->findAll();
            
            $data = [];
            foreach ($perhitunganL04 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L04' => $item['t_psmetrik_L04'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL04: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L05 dengan handling field yang tidak konsisten
     */
    private function getDataL05()
    {
        try {
            $perhitunganL05 = $this->perhitunganL05Model->findAll();
            
            $data = [];
            foreach ($perhitunganL05 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L05' => $item['t_psmetrik_L05'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL05: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L06 dengan handling field yang tidak konsisten
     */
    private function getDataL06()
    {
        try {
            $perhitunganL06 = $this->perhitunganL06Model->findAll();
            
            $data = [];
            foreach ($perhitunganL06 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L06' => $item['t_psmetrik_L06'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL06: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get initial readings from IreadingA untuk L4-L6
     */
    private function getInitialReadingsA()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L04, L05, L06 dari IreadingA dengan error handling
            $readings['L_04'] = method_exists($this->ireadingA, 'forAL04') ? $this->ireadingA->forAL04() : [];
            $readings['L_05'] = method_exists($this->ireadingA, 'forAL05') ? $this->ireadingA->forAL05() : [];
            $readings['L_06'] = method_exists($this->ireadingA, 'forAL06') ? $this->ireadingA->forAL06() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsA: ' . $e->getMessage());
            return [
                'L_04' => [],
                'L_05' => [],
                'L_06' => []
            ];
        }
    }

    /**
     * Get initial readings from IreadingB untuk L4-L6
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L04, L05, L06 dari IreadingB dengan error handling
            $readings['L_04'] = method_exists($this->ireadingB, 'forBL04') ? $this->ireadingB->forBL04() : [];
            $readings['L_05'] = method_exists($this->ireadingB, 'forBL05') ? $this->ireadingB->forBL05() : [];
            $readings['L_06'] = method_exists($this->ireadingB, 'forBL06') ? $this->ireadingB->forBL06() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsB: ' . $e->getMessage());
            return [
                'L_04' => [],
                'L_05' => [],
                'L_06' => []
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
                'l04' => $this->getDataL04(),
                'l05' => $this->getDataL05(),
                'l06' => $this->getDataL06(),
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
                'l04' => [],
                'l05' => [],
                'l06' => [],
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
        
        // L04
        $sampleL04 = $this->perhitunganL04Model->first();
        $debugData['L04_fields'] = $sampleL04 ? array_keys($sampleL04) : 'No data';
        
        // L05
        $sampleL05 = $this->perhitunganL05Model->first();
        $debugData['L05_fields'] = $sampleL05 ? array_keys($sampleL05) : 'No data';
        
        // L06
        $sampleL06 = $this->perhitunganL06Model->first();
        $debugData['L06_fields'] = $sampleL06 ? array_keys($sampleL06) : 'No data';
        
        // Pembacaan L04
        $samplePembacaanL04 = $this->pembacaanL04Model->first();
        $debugData['PembacaanL04_fields'] = $samplePembacaanL04 ? array_keys($samplePembacaanL04) : 'No data';
        
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