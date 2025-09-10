<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Rembesan\AnalisaLookBurtModel;

class AnalisaLookBurt extends BaseController
{
    protected $analisaModel;

    public function __construct()
    {
        $this->analisaModel = new AnalisaLookBurtModel();
    }

    // Menampilkan data
    public function index()
    {
        $data['analisa'] = $this->analisaModel->getAll();
        return view('data/analisa_look_burt', $data);
    }

    // Simpan data baru
    public function save()
    {
        $rembesan = $this->request->getPost('rembesan_bendungan');
        $panjang  = $this->request->getPost('panjang_bendungan');
        $perMeter = ($panjang > 0) ? ($rembesan / $panjang) : 0;

        // logika keterangan
        if ($perMeter < 0.28) {
            $ket = "Aman";
        } elseif ($perMeter > 0.56) {
            $ket = "Tidak Aman";
        } else {
            $ket = "Waspada";
        }

        $this->analisaModel->save([
            'pengukuran_id'     => $this->request->getPost('pengukuran_id'),
            'rembesan_bendungan'=> $rembesan,
            'panjang_bendungan' => $panjang,
            'rembesan_per_m'    => $perMeter,
            'nilai_ambang_ok'   => 0.28,
            'nilai_ambang_notok'=> 0.56,
            'keterangan'        => $ket
        ]);

        return redirect()->to('/analisaLookBurt');
    }
}
