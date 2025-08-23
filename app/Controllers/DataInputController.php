<?php 
namespace App\Controllers;

use App\Models\Rembesan\DataGabunganModel;
use App\Models\Rembesan\MAmbangBatas;
use App\Models\Rembesan\MBocoranBaru;
use App\Models\Rembesan\MDataPengukuran;
use App\Models\Rembesan\MSR;
use App\Models\Rembesan\MThomsonWeir;
use App\Models\Rembesan\PerhitunganBocoranModel;
use App\Models\Rembesan\PerhitunganIntiGaleryModel;
use App\Models\Rembesan\PerhitunganSpillwayModel;
use App\Models\Rembesan\PerhitunganSRModel;
use App\Models\Rembesan\PerhitunganThomsonModel;
use App\Models\Rembesan\PerhitunganBatasMaksimalModel;
use App\Models\Rembesan\TebingKananModel;
use App\Models\Rembesan\TotalBocoranModel;

class DataInputController extends BaseController
{
    public function rembesan()
    {
        $data = [
            'gabungan'               => (new DataGabunganModel())->findAll(),
            'ambang'                 => (new MAmbangBatas())->findAll(),
            'bocoran'                => (new MBocoranBaru())->findAll(),
            'pengukuran'             => (new MDataPengukuran())->findAll(),
            'sr'                     => (new MSR())->findAll(),
            'thomson'                => (new MThomsonWeir())->findAll(),
            'perhitungan_bocoran'    => (new PerhitunganBocoranModel())->findAll(),
            'perhitungan_ig'         => (new PerhitunganIntiGaleryModel())->findAll(),
            'perhitungan_spillway'   => (new PerhitunganSpillwayModel())->findAll(),
            'perhitungan_sr'         => (new PerhitunganSRModel())->findAll(),
            'perhitungan_thomson'    => (new PerhitunganThomsonModel())->getAllWithPengukuran(),
            'perhitungan_batas'      => (new PerhitunganBatasMaksimalModel())->getAllWithPengukuran(),
            'tebing_kanan'           => (new TebingKananModel())->findAll(),
            'total_bocoran'          => (new TotalBocoranModel())->findAll(),
        ];

        return view('Data/data_rembesan', $data);
    }

    public function streamRembesan()
{
    // Set headers SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    $modelPengukuran      = new MDataPengukuran();
    $modelThomson         = new MThomsonWeir();
    $modelSR              = new MSR();
    $modelBocoran         = new MBocoranBaru();

    // Looping untuk push data terus
    while (true) {
        $pengukuran = $modelPengukuran->findAll();
        $thomson    = $modelThomson->findAll();
        $sr         = $modelSR->findAll();
        $bocoran    = $modelBocoran->findAll();

        // Index data by pengukuran_id untuk mudah mapping
        $indexById = fn($arr) => array_reduce($arr, function($carry, $item) {
            if (isset($item['pengukuran_id'])) $carry[$item['pengukuran_id']] = $item;
            return $carry;
        }, []);

        $thomsonBy = $thomson ? $indexById($thomson) : [];
        $srBy      = $sr ? $indexById($sr) : [];
        $bocoranBy = $bocoran ? $indexById($bocoran) : [];

        $dataToSend = [];

        foreach ($pengukuran as $p) {
            $pid = $p['id'];
            $dataToSend[] = [
                'pengukuran' => $p,
                'thomson'    => $thomsonBy[$pid] ?? [],
                'sr'         => $srBy[$pid] ?? [],
                'bocoran'    => $bocoranBy[$pid] ?? []
            ];
        }

        echo "event: update\n";
        echo 'data: ' . json_encode($dataToSend) . "\n\n";

        // Flush output
        ob_flush();
        flush();

        // Delay 2 detik
        sleep(2);
    }
}

}