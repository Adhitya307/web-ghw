<?php

namespace App\Controllers\Leftpiez;

use App\Controllers\BaseController;
use App\Models\LeftPiez\PerhitunganLeftPiezModel;
use App\Models\LeftPiez\IreadingA;
use App\Models\LeftPiez\IreadingB;
use App\Models\LeftPiez\TPembacaanLeftPiezModel;
use App\Models\LeftPiez\TPengukuranLeftpiezModel;

class GrafikHistoryL4L6 extends BaseController
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
            'title' => 'Grafik History L4-L6',
            'breadcrumb' => [
                'Dashboard' => base_url('dashboard'),
                'Left Piezometer' => base_url('left-piez'),
                'Grafik History L4-L6' => current_url()
            ],
            'pengukuran' => $dataPengukuran,
            'dataL04' => $this->getDataForPiezometer('L04'),
            'dataL05' => $this->getDataForPiezometer('L05'),
            'dataL06' => $this->getDataForPiezometer('L06'),
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
                
                // Ambil data pembacaan untuk setiap titik dari tabel tunggal
                $pembacaanL04 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L04');
                $pembacaanL05 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L05');
                $pembacaanL06 = $this->pembacaanModel->getByPengukuranDanTipe($id_pengukuran, 'L06');
                
                // Ambil data perhitungan untuk setiap titik dari tabel tunggal
                $perhitunganL04 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L04');
                $perhitunganL05 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L05');
                $perhitunganL06 = $this->perhitunganModel->getByPengukuranDanTipe($id_pengukuran, 'L06');
                
                // Hitung nilai t_psmetrik jika belum ada
                if (!$perhitunganL04) {
                    $t_psmetrik_L04 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L04');
                    $perhitunganL04 = ['t_psmetrik' => $t_psmetrik_L04];
                }
                
                if (!$perhitunganL05) {
                    $t_psmetrik_L05 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L05');
                    $perhitunganL05 = ['t_psmetrik' => $t_psmetrik_L05];
                }
                
                if (!$perhitunganL06) {
                    $t_psmetrik_L06 = $this->perhitunganModel->hitungNilai($id_pengukuran, 'L06');
                    $perhitunganL06 = ['t_psmetrik' => $t_psmetrik_L06];
                }
                
                $data[] = [
                    'pengukuran' => $pengukuran,
                    'pembacaan' => [
                        'L_04' => $pembacaanL04 ?? ['feet' => 0, 'inch' => 0],
                        'L_05' => $pembacaanL05 ?? ['feet' => 0, 'inch' => 0],
                        'L_06' => $pembacaanL06 ?? ['feet' => 0, 'inch' => 0]
                    ],
                    'perhitungan_l04' => $perhitunganL04 ?? ['t_psmetrik' => 0],
                    'perhitungan_l05' => $perhitunganL05 ?? ['t_psmetrik' => 0],
                    'perhitungan_l06' => $perhitunganL06 ?? ['t_psmetrik' => 0]
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
            
            // Ambil data untuk L04, L05, L06 dari IreadingA
            $readings['L_04'] = $this->ireadingA->getByTitik('L_04', $idPengukuran);
            $readings['L_05'] = $this->ireadingA->getByTitik('L_05', $idPengukuran);
            $readings['L_06'] = $this->ireadingA->getByTitik('L_06', $idPengukuran);
            
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
     * Get initial readings from IreadingB
     */
    private function getInitialReadingsB()
    {
        try {
            $readings = [];
            
            // Ambil semua data untuk pengukuran terbaru
            $latestPengukuran = $this->pengukuranModel->orderBy('tanggal', 'DESC')->first();
            $idPengukuran = $latestPengukuran['id_pengukuran'] ?? null;
            
            // Ambil data untuk L04, L05, L06 dari IreadingB
            $readings['L_04'] = $this->ireadingB->getByTitik('L_04', $idPengukuran);
            $readings['L_05'] = $this->ireadingB->getByTitik('L_05', $idPengukuran);
            $readings['L_06'] = $this->ireadingB->getByTitik('L_06', $idPengukuran);
            
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
                'l04' => $this->getDataForPiezometer('L04'),
                'l05' => $this->getDataForPiezometer('L05'),
                'l06' => $this->getDataForPiezometer('L06'),
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
}