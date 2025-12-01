<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL1L3 extends BaseController
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
            'title' => 'Grafik History L1-L3',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L1-L3' => current_url()
            ],
            'pengukuran' => $dataPengukuran,
            'dataL01' => $this->getDataForPiezometer('L01'),
            'dataL02' => $this->getDataForPiezometer('L02'),
            'dataL03' => $this->getDataForPiezometer('L03'),
            'initialReadingsA' => $this->getInitialReadingsA(),
            'initialReadingsB' => $this->getInitialReadingsB()
        ];

        return view('left_piez/grafik-history-l1-l3', $data);
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
                $pembacaanL01 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L01');
                $pembacaanL02 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L02');
                $pembacaanL03 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L03');
                
                // Ambil data perhitungan untuk setiap titik dari tabel tunggal
                $perhitunganL01 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L01');
                $perhitunganL02 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L02');
                $perhitunganL03 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L03');
                
                // Hitung nilai t_psmetrik jika belum ada
                if (!$perhitunganL01) {
                    $t_psmetrik_L01 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L01');
                    $perhitunganL01 = ['t_psmetrik' => $t_psmetrik_L01];
                }
                
                if (!$perhitunganL02) {
                    $t_psmetrik_L02 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L02');
                    $perhitunganL02 = ['t_psmetrik' => $t_psmetrik_L02];
                }
                
                if (!$perhitunganL03) {
                    $t_psmetrik_L03 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L03');
                    $perhitunganL03 = ['t_psmetrik' => $t_psmetrik_L03];
                }
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_01' => $pembacaanL01 ?? ['feet' => 0, 'inch' => 0],
                        'L_02' => $pembacaanL02 ?? ['feet' => 0, 'inch' => 0],
                        'L_03' => $pembacaanL03 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l01' => $perhitunganL01 ?? ['t_psmetrik' => 0],
                    'perhitungan_l02' => $perhitunganL02 ?? ['t_psmetrik' => 0],
                    'perhitungan_l03' => $perhitunganL03 ?? ['t_psmetrik' => 0]
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
            
            // Ambil data untuk L01, L02, L03 dari IreadingA
            $readings['L_01'] = $this->ireadingA->getByTitik('L_01', $idPengukuran);
            $readings['L_02'] = $this->ireadingA->getByTitik('L_02', $idPengukuran);
            $readings['L_03'] = $this->ireadingA->getByTitik('L_03', $idPengukuran);
            
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
     * Get initial readings from IreadingB
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil semua data untuk pengukuran terbaru
            $latestPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->first();
            $idPengukuran = $latestPengukuran['id_pengukuran'] ?? null;
            
            // Ambil data untuk L01, L02, L03 dari IreadingB
            $readings['L_01'] = $this->ireadingB->getByTitik('L_01', $idPengukuran);
            $readings['L_02'] = $this->ireadingB->getByTitik('L_02', $idPengukuran);
            $readings['L_03'] = $this->ireadingB->getByTitik('L_03', $idPengukuran);
            
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
                'l01' => $this->getDataForPiezometer('L01'),
                'l02' => $this->getDataForPiezometer('L02'),
                'l03' => $this->getDataForPiezometer('L03'),
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
}