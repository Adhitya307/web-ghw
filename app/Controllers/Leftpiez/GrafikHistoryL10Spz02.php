<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL10Spz02 extends BaseController
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
            'title' => 'Grafik History L10 & SPZ-02',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L10 & SPZ-02' => current_url()
            ],
            'pengukuran' => $dataPengukuran,
            'dataL10' => $this->getDataForPiezometer('L10'),
            'dataSpz02' => $this->getDataForPiezometer('SPZ02'),
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
                
                // Ambil data pembacaan untuk L10 dan SPZ-02 dari tabel tunggal
                $pembacaanL10 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L10');
                $pembacaanSpz02 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'SPZ02');
                
                // Ambil data perhitungan untuk L10 dan SPZ-02 dari tabel tunggal
                $perhitunganL10 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L10');
                $perhitunganSpz02 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'SPZ02');
                
                // Hitung nilai t_psmetrik jika belum ada
                if (!$perhitunganL10) {
                    $t_psmetrik_L10 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L10');
                    $perhitunganL10 = ['t_psmetrik' => $t_psmetrik_L10];
                }
                
                if (!$perhitunganSpz02) {
                    $t_psmetrik_SPZ02 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'SPZ02');
                    $perhitunganSpz02 = ['t_psmetrik' => $t_psmetrik_SPZ02];
                }
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_10' => $pembacaanL10 ?? ['feet' => 0, 'inch' => 0],
                        'SPZ_02' => $pembacaanSpz02 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l10' => $perhitunganL10 ?? ['t_psmetrik' => 0],
                    'perhitungan_spz02' => $perhitunganSpz02 ?? ['t_psmetrik' => 0]
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
            
            // Ambil data untuk L10 dan SPZ-02 dari IreadingA
            $readings['L_10'] = $this->ireadingA->getByTitik('L_10', $idPengukuran);
            $readings['SPZ_02'] = $this->ireadingA->getByTitik('SPZ_02', $idPengukuran);
            
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
     * Get initial readings from IreadingB
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil semua data untuk pengukuran terbaru
            $latestPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->first();
            $idPengukuran = $latestPengukuran['id_pengukuran'] ?? null;
            
            // Ambil data untuk L10 dan SPZ-02 dari IreadingB
            $readings['L_10'] = $this->ireadingB->getByTitik('L_10', $idPengukuran);
            $readings['SPZ_02'] = $this->ireadingB->getByTitik('SPZ_02', $idPengukuran);
            
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
                'l10' => $this->getDataForPiezometer('L10'),
                'spz02' => $this->getDataForPiezometer('SPZ02'),
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
}