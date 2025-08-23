<?php

namespace App\Controllers\Rembesan;

use App\Controllers\BaseController;
use App\Models\Rembesan\MSR;
use App\Models\Rembesan\PerhitunganSRModel;

class SRController extends BaseController
{
    protected $msr;
    protected $perhitungan;

    public function __construct()
    {
        $this->msr = new MSR();
        $this->perhitungan = new PerhitunganSRModel();
    }

    /**
     * Hitung SR berdasarkan pengukuran_id
     */
    public function hitung($pengukuran_id, $silent = false)
    {
        try {
            log_message('debug', "[SR] Mulai hitung untuk pengukuran_id={$pengukuran_id}");

            // ambil data mentah
            $data = $this->msr->where('pengukuran_id', $pengukuran_id)->first();

            if (!$data) {
                log_message('error', "[SR] Data tidak ditemukan untuk pengukuran_id={$pengukuran_id}");
                return ['status' => 'error', 'msg' => 'Data tidak ditemukan'];
            }

            helper('rumus/sr');

            // daftar field SR
            $fields = [
                'sr_1', 'sr_40', 'sr_66', 'sr_68', 'sr_70',
                'sr_79', 'sr_81', 'sr_83', 'sr_85',
                'sr_92', 'sr_94', 'sr_96', 'sr_98',
                'sr_100', 'sr_102', 'sr_104', 'sr_106'
            ];

            $hasil = ['pengukuran_id' => $pengukuran_id];

            foreach ($fields as $f) {
                $kode = $data[$f . '_kode'] ?? null;
                $nilai = $data[$f . '_nilai'] ?? null;

                $q = perhitunganQ_sr($nilai, $kode);

                $hasil[$f . '_q'] = $q;

                log_message('debug', "[SR] {$f} => kode={$kode}, nilai={$nilai}, q={$q}");
            }

            // simpan ke tabel hasil perhitungan
            $existing = $this->perhitungan->where('pengukuran_id', $pengukuran_id)->first();

            if ($existing) {
                $this->perhitungan->update($existing['id'], $hasil);
                log_message('debug', "[SR] Data hasil diperbarui untuk pengukuran_id={$pengukuran_id}");
            } else {
                $this->perhitungan->insert($hasil);
                log_message('debug', "[SR] Data hasil disimpan baru untuk pengukuran_id={$pengukuran_id}");
            }

            return ['status' => 'success', 'data' => $hasil];

        } catch (\Throwable $e) {
            log_message('error', "[SR] Error hitung SR: " . $e->getMessage());
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }
}
