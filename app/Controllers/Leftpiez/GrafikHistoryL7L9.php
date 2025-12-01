<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL7L9 extends BaseController
{
    protected $perhitunganModel;
    protected $ireadingA;
    protected $ireadingB;
    protected $pembacaanModel;
    protected $pengukuranModel;

    public function __construct()
    {
        $this->perhitunganModel = new PerhitunganLeftPiezModel();
        $this->ireadingA = new IreadingA();
        $this->ireadingB = new IreadingB();
        $this->pembacaanModel = new TPembacaanLeftPiezModel();
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
            'pengukuran' => $dataPengukuran,
            'dataL07' => $this->getDataForPiezometer('L07'),
            'dataL08' => $this->getDataForPiezometer('L08'),
            'dataL09' => $this->getDataForPiezometer('L09'),
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
                
                // Ambil data pembacaan untuk setiap titik dari tabel tunggal
                $pembacaanL07 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L07');
                $pembacaanL08 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L08');
                $pembacaanL09 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L09');
                
                // Ambil data perhitungan untuk setiap titik dari tabel tunggal
                $perhitunganL07 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L07');
                $perhitunganL08 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L08');
                $perhitunganL09 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L09');
                
                // Hitung nilai t_psmetrik jika belum ada
                if (!$perhitunganL07) {
                    $t_psmetrik_L07 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L07');
                    $perhitunganL07 = ['t_psmetrik' => $t_psmetrik_L07];
                }
                
                if (!$perhitunganL08) {
                    $t_psmetrik_L08 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L08');
                    $perhitunganL08 = ['t_psmetrik' => $t_psmetrik_L08];
                }
                
                if (!$perhitunganL09) {
                    $t_psmetrik_L09 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L09');
                    $perhitunganL09 = ['t_psmetrik' => $t_psmetrik_L09];
                }
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_07' => $pembacaanL07 ?? ['feet' => 0, 'inch' => 0],
                        'L_08' => $pembacaanL08 ?? ['feet' => 0, 'inch' => 0],
                        'L_09' => $pembacaanL09 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l07' => $perhitunganL07 ?? ['t_psmetrik' => 0],
                    'perhitungan_l08' => $perhitunganL08 ?? ['t_psmetrik' => 0],
                    'perhitungan_l09' => $perhitunganL09 ?? ['t_psmetrik' => 0]
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataForTable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for specific piezometer type
     */
    private function getDataForPiezometer($tipePiezometer)
    {
        try {
            // Ambil semua data perhitungan untuk tipe piezometer tertentu
            $perhitunganData = $this->perhitunganModel->getByTipe($tipePiezometer);
            
            $data = [];
            foreach ($perhitunganData as $item) {
                // Ambil default values
                $defaults = $this->perhitunganModel->defaultsByType[$tipePiezometer] ?? [];
                
                $data[] = [
                    'id_pengukuran' => $item['id_pengukuran'] ?? null,
                    'elv_piez' => $item['elv_piez'] ?? $defaults['elv_piez'] ?? null,
                    'Elv_Piez' => $item['elv_piez'] ?? $defaults['elv_piez'] ?? null,
                    'kedalaman' => $item['kedalaman'] ?? $defaults['kedalaman'] ?? null,
                    't_psmetrik' => $item['t_psmetrik'] ?? null,
                    'record_max' => $item['record_max'] ?? null,
                    'record_min' => $item['record_min'] ?? null,
                    'koordinat_x' => $item['koordinat_x'] ?? null,
                    'koordinat_y' => $item['koordinat_y'] ?? null
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataForPiezometer(' . $tipePiezometer . '): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get initial readings from IreadingA
     */
    private function getInitialReadingsA()
    {
        try {
            $readings = [];
            
            // Ambil semua data untuk pengukuran terbaru
            $latestPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->first();
            $idPengukuran = $latestPengukuran['id_pengukuran'] ?? null;
            
            // Ambil data untuk L07, L08, L09 dari IreadingA
            $readings['L_07'] = $this->ireadingA->getByTitik('L_07', $idPengukuran);
            $readings['L_08'] = $this->ireadingA->getByTitik('L_08', $idPengukuran);
            $readings['L_09'] = $this->ireadingA->getByTitik('L_09', $idPengukuran);
            
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
     * Get initial readings from IreadingB
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil semua data untuk pengukuran terbaru
            $latestPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->first();
            $idPengukuran = $latestPengukuran['id_pengukuran'] ?? null;
            
            // Ambil data untuk L07, L08, L09 dari IreadingB
            $readings['L_07'] = $this->ireadingB->getByTitik('L_07', $idPengukuran);
            $readings['L_08'] = $this->ireadingB->getByTitik('L_08', $idPengukuran);
            $readings['L_09'] = $this->ireadingB->getByTitik('L_09', $idPengukuran);
            
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
                'l07' => $this->getDataForPiezometer('L07'),
                'l08' => $this->getDataForPiezometer('L08'),
                'l09' => $this->getDataForPiezometer('L09'),
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
}