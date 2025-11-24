<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganL01Model;
use App\Models\LeftPiez\PerhitunganL02Model;
use App\Models\LeftPiez\PerhitunganL03Model;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanL01Model;
use App\Models\LeftPiez\TPembacaanL02Model;
use App\Models\LeftPiez\TPembacaanL03Model;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL1L3 extends BaseController
{
    protected $perhitunganL01Model;
    protected $perhitunganL02Model;
    protected $perhitunganL03Model;
    protected $ireadingA;
    protected $ireadingB;
    protected $pembacaanL01Model;
    protected $pembacaanL02Model;
    protected $pembacaanL03Model;
    protected $pengukuranModel;

    public function __construct()
    {
        $this->perhitunganL01Model = new PerhitunganL01Model();
        $this->perhitunganL02Model = new PerhitunganL02Model();
        $this->perhitunganL03Model = new PerhitunganL03Model();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pembacaanL01Model = new TPembacaanL01Model();
        $this->pembacaanL02Model = new TPembacaanL02Model();
        $this->pembacaanL03Model = new TPembacaanL03Model();
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
    }

    public function index()
    {
        // Dapatkan data yang diperlukan untuk tabel
        $dataPengukuran = $this->getDataForTable();

        $data = [
            'title' => 'Grafik History L1-L3',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L1-L3' => current_url()
            ],
            'pengukuran' => $dataPengukuran, // Data untuk tabel
            'dataL01' => $this->getDataL01(),
            'dataL02' => $this->getDataL02(),
            'dataL03' => $this->getDataL03(),
            'initialReadingsA' => $this->getInitialReadingsA(),
            'initialReadingsB' => $this->getInitialReadingsB()
        ];

        return view('left_piez/grafik-history-l1-l3', $data);
    }

    /**
     * Method baru untuk mendapatkan data terstruktur untuk tabel
     */
    private function getDataForTable()
    {
        try {
            $data = [];
            
            // Ambil semua data pengukuran dari TPengukuranLeftpiezModel
            $allPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->findAll();
            
            foreach ($allPengukuran as $pengukuran) {
                $id_pengukuran = $pengukuran['id_pengukuran'];
                
                // Ambil data pembacaan untuk setiap titik
                $pembacaanL01 = $this->pembacaanL01Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanL02 = $this->pembacaanL02Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanL03 = $this->pembacaanL03Model->where('id_pengukuran', $id_pengukuran)->first();
                
                // Ambil data perhitungan untuk setiap titik
                $perhitunganL01 = $this->perhitunganL01Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganL02 = $this->perhitunganL02Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganL03 = $this->perhitunganL03Model->where('id_pengukuran', $id_pengukuran)->first();
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_01' => $pembacaanL01 ?? ['feet' => 0, 'inch' => 0],
                        'L_02' => $pembacaanL02 ?? ['feet' => 0, 'inch' => 0],
                        'L_03' => $pembacaanL03 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l01' => $perhitunganL01 ?? ['t_psmetrik_L01' => 0],
                    'perhitungan_l02' => $perhitunganL02 ?? ['t_psmetrik_L02' => 0],
                    'perhitungan_l03' => $perhitunganL03 ?? ['t_psmetrik_L03' => 0]
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataForTable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L01 dengan handling field yang tidak konsisten
     */
    private function getDataL01()
    {
        try {
            // Ambil semua data perhitungan L01
            $perhitunganL01 = $this->perhitunganL01Model->findAll();
            
            $data = [];
            foreach ($perhitunganL01 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L01' => $item['t_psmetrik_L01'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL01: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L02 dengan handling field yang tidak konsisten
     */
    private function getDataL02()
    {
        try {
            $perhitunganL02 = $this->perhitunganL02Model->findAll();
            
            $data = [];
            foreach ($perhitunganL02 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L02' => $item['t_psmetrik_L02'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL02: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L03 dengan handling field yang tidak konsisten
     */
    private function getDataL03()
    {
        try {
            $perhitunganL03 = $this->perhitunganL03Model->findAll();
            
            $data = [];
            foreach ($perhitunganL03 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L03' => $item['t_psmetrik_L03'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL03: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get initial readings from IreadingA dengan error handling
     */
    private function getInitialReadingsA()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L01, L02, L03 dari IreadingA dengan error handling
            $readings['L_01'] = method_exists($this->ireadingA, 'forAL01') ? $this->ireadingA->forAL01() : [];
            $readings['L_02'] = method_exists($this->ireadingA, 'forAL02') ? $this->ireadingA->forAL02() : [];
            $readings['L_03'] = method_exists($this->ireadingA, 'forAL03') ? $this->ireadingA->forAL03() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsA: ' . $e->getMessage());
            return [
                'L_01' => [],
                'L_02' => [],
                'L_03' => []
            ];
        }
    }

    /**
     * Get initial readings from IreadingB dengan error handling
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L01, L02, L03 dari IreadingB dengan error handling
            $readings['L_01'] = method_exists($this->ireadingB, 'forBL01') ? $this->ireadingB->forBL01() : [];
            $readings['L_02'] = method_exists($this->ireadingB, 'forBL02') ? $this->ireadingB->forBL02() : [];
            $readings['L_03'] = method_exists($this->ireadingB, 'forBL03') ? $this->ireadingB->forBL03() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsB: ' . $e->getMessage());
            return [
                'L_01' => [],
                'L_02' => [],
                'L_03' => []
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
                'l01' => $this->getDataL01(),
                'l02' => $this->getDataL02(),
                'l03' => $this->getDataL03(),
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
                'l01' => [],
                'l02' => [],
                'l03' => [],
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
        
        // L01
        $sampleL01 = $this->perhitunganL01Model->first();
        $debugData['L01_fields'] = $sampleL01 ? array_keys($sampleL01) : 'No data';
        
        // L02
        $sampleL02 = $this->perhitunganL02Model->first();
        $debugData['L02_fields'] = $sampleL02 ? array_keys($sampleL02) : 'No data';
        
        // L03
        $sampleL03 = $this->perhitunganL03Model->first();
        $debugData['L03_fields'] = $sampleL03 ? array_keys($sampleL03) : 'No data';
        
        // Pembacaan L01
        $samplePembacaanL01 = $this->pembacaanL01Model->first();
        $debugData['PembacaanL01_fields'] = $samplePembacaanL01 ? array_keys($samplePembacaanL01) : 'No data';
        
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