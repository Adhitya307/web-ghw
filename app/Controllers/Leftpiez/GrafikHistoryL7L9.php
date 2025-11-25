<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganL07Model;
use App\Models\LeftPiez\PerhitunganL08Model;
use App\Models\LeftPiez\PerhitunganL09Model;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanL07Model;
use App\Models\LeftPiez\TPembacaanL08Model;
use App\Models\LeftPiez\TPembacaanL09Model;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL7L9 extends BaseController
{
    protected $perhitunganL07Model;
    protected $perhitunganL08Model;
    protected $perhitunganL09Model;
    protected $ireadingA;
    protected $ireadingB;
    protected $pembacaanL07Model;
    protected $pembacaanL08Model;
    protected $pembacaanL09Model;
    protected $pengukuranModel;

    public function __construct()
    {
        $this->perhitunganL07Model = new PerhitunganL07Model();
        $this->perhitunganL08Model = new PerhitunganL08Model();
        $this->perhitunganL09Model = new PerhitunganL09Model();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pembacaanL07Model = new TPembacaanL07Model();
        $this->pembacaanL08Model = new TPembacaanL08Model();
        $this->pembacaanL09Model = new TPembacaanL09Model();
        $this->pengukuranModel = new TPengukuranLeftpiezModel();
    }

    public function index()
    {
        // Dapatkan data yang diperlukan untuk tabel
        $dataPengukuran = $this->getDataForTable();

        $data = [
            'title' => 'Grafik History L7-L9',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L7-L9' => current_url()
            ],
            'pengukuran' => $dataPengukuran, // Data untuk tabel
            'dataL07' => $this->getDataL07(),
            'dataL08' => $this->getDataL08(),
            'dataL09' => $this->getDataL09(),
            'initialReadingsA' => $this->getInitialReadingsA(),
            'initialReadingsB' => $this->getInitialReadingsB()
        ];

        return view('left_piez/grafik-history-l7-l9', $data);
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
                
                // Ambil data pembacaan untuk setiap titik L7-L9
                $pembacaanL07 = $this->pembacaanL07Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanL08 = $this->pembacaanL08Model->where('id_pengukuran', $id_pengukuran)->first();
                $pembacaanL09 = $this->pembacaanL09Model->where('id_pengukuran', $id_pengukuran)->first();
                
                // Ambil data perhitungan untuk setiap titik L7-L9
                $perhitunganL07 = $this->perhitunganL07Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganL08 = $this->perhitunganL08Model->where('id_pengukuran', $id_pengukuran)->first();
                $perhitunganL09 = $this->perhitunganL09Model->where('id_pengukuran', $id_pengukuran)->first();
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_07' => $pembacaanL07 ?? ['feet' => 0, 'inch' => 0],
                        'L_08' => $pembacaanL08 ?? ['feet' => 0, 'inch' => 0],
                        'L_09' => $pembacaanL09 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l07' => $perhitunganL07 ?? ['t_psmetrik_L07' => 0],
                    'perhitungan_l08' => $perhitunganL08 ?? ['t_psmetrik_L08' => 0],
                    'perhitungan_l09' => $perhitunganL09 ?? ['t_psmetrik_L09' => 0]
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataForTable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L07 dengan handling field yang tidak konsisten
     */
    private function getDataL07()
    {
        try {
            // Ambil semua data perhitungan L07
            $perhitunganL07 = $this->perhitunganL07Model->findAll();
            
            $data = [];
            foreach ($perhitunganL07 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L07' => $item['t_psmetrik_L07'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL07: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L08 dengan handling field yang tidak konsisten
     */
    private function getDataL08()
    {
        try {
            $perhitunganL08 = $this->perhitunganL08Model->findAll();
            
            $data = [];
            foreach ($perhitunganL08 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L08' => $item['t_psmetrik_L08'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL08: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for L09 dengan handling field yang tidak konsisten
     */
    private function getDataL09()
    {
        try {
            $perhitunganL09 = $this->perhitunganL09Model->findAll();
            
            $data = [];
            foreach ($perhitunganL09 as $item) {
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $item['Elv_Piez'] ?? null,
                    'Elv_Piez' => $item['Elv_Piez'] ?? $item['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? null,
                    't_psmetrik_L09' => $item['t_psmetrik_L09'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataL09: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get initial readings from IreadingA untuk L7-L9
     */
    private function getInitialReadingsA()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L07, L08, L09 dari IreadingA dengan error handling
            $readings['L_07'] = method_exists($this->ireadingA, 'forAL07') ? $this->ireadingA->forAL07() : [];
            $readings['L_08'] = method_exists($this->ireadingA, 'forAL08') ? $this->ireadingA->forAL08() : [];
            $readings['L_09'] = method_exists($this->ireadingA, 'forAL09') ? $this->ireadingA->forAL09() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsA: ' . $e->getMessage());
            return [
                'L_07' => [],
                'L_08' => [],
                'L_09' => []
            ];
        }
    }

    /**
     * Get initial readings from IreadingB untuk L7-L9
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil data untuk L07, L08, L09 dari IreadingB dengan error handling
            $readings['L_07'] = method_exists($this->ireadingB, 'forBL07') ? $this->ireadingB->forBL07() : [];
            $readings['L_08'] = method_exists($this->ireadingB, 'forBL08') ? $this->ireadingB->forBL08() : [];
            $readings['L_09'] = method_exists($this->ireadingB, 'forBL09') ? $this->ireadingB->forBL09() : [];
            
            return $readings;
        } catch (\Exception $e) {
            log_message('error', 'Error in getInitialReadingsB: ' . $e->getMessage());
            return [
                'L_07' => [],
                'L_08' => [],
                'L_09' => []
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
                'l07' => $this->getDataL07(),
                'l08' => $this->getDataL08(),
                'l09' => $this->getDataL09(),
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
                'l07' => [],
                'l08' => [],
                'l09' => [],
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
        
        // L07
        $sampleL07 = $this->perhitunganL07Model->first();
        $debugData['L07_fields'] = $sampleL07 ? array_keys($sampleL07) : 'No data';
        
        // L08
        $sampleL08 = $this->perhitunganL08Model->first();
        $debugData['L08_fields'] = $sampleL08 ? array_keys($sampleL08) : 'No data';
        
        // L09
        $sampleL09 = $this->perhitunganL09Model->first();
        $debugData['L09_fields'] = $sampleL09 ? array_keys($sampleL09) : 'No data';
        
        // Pembacaan L07
        $samplePembacaanL07 = $this->pembacaanL07Model->first();
        $debugData['PembacaanL07_fields'] = $samplePembacaanL07 ? array_keys($samplePembacaanL07) : 'No data';
        
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