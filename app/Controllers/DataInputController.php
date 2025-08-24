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

    // Method untuk AJAX polling - DIREVISI
    public function getLatestData()
    {
        $modelPengukuran      = new MDataPengukuran();
        $modelThomson         = new MThomsonWeir();
        $modelSR              = new MSR();
        $modelBocoran         = new MBocoranBaru();
        $modelPerhitunganThomson = new PerhitunganThomsonModel();
        $modelPerhitunganSR   = new PerhitunganSRModel();
        $modelPerhitunganBocoran = new PerhitunganBocoranModel();
        $modelPerhitunganIG   = new PerhitunganIntiGaleryModel();
        $modelPerhitunganSpillway = new PerhitunganSpillwayModel();
        $modelTebingKanan     = new TebingKananModel();
        $modelTotalBocoran    = new TotalBocoranModel();
        $modelPerhitunganBatas = new PerhitunganBatasMaksimalModel();

        $pengukuran = $modelPengukuran->findAll();
        $thomson    = $modelThomson->findAll();
        $sr         = $modelSR->findAll();
        $bocoran    = $modelBocoran->findAll();
        $perhitunganThomson = $modelPerhitunganThomson->getAllWithPengukuran();
        $perhitunganSR = $modelPerhitunganSR->findAll();
        $perhitunganBocoran = $modelPerhitunganBocoran->findAll();
        $perhitunganIG = $modelPerhitunganIG->findAll();
        $perhitunganSpillway = $modelPerhitunganSpillway->findAll();
        $tebingKanan = $modelTebingKanan->findAll();
        $totalBocoran = $modelTotalBocoran->findAll();
        $perhitunganBatas = $modelPerhitunganBatas->getAllWithPengukuran();

        // Fungsi indexing yang lebih robust
        $indexBy = function(array $rows, $idField = 'pengukuran_id') {
            $result = [];
            foreach ($rows as $row) {
                // Coba beberapa kemungkinan nama field ID
                $possibleIdFields = [$idField, 'id_pengukuran', 'pengukuranId', 'pengukuran'];
                
                foreach ($possibleIdFields as $field) {
                    if (isset($row[$field])) {
                        $result[$row[$field]] = $row;
                        break;
                    }
                }
            }
            return $result;
        };

        // Index semua data dengan field yang sesuai
        $thomsonBy = $indexBy($thomson);
        $srBy = $indexBy($sr);
        $bocoranBy = $indexBy($bocoran);
        $perhitunganThomsonBy = $indexBy($perhitunganThomson);
        $perhitunganSrBy = $indexBy($perhitunganSR);
        $perhitunganBocoranBy = $indexBy($perhitunganBocoran);
        $perhitunganIgBy = $indexBy($perhitunganIG);
        $perhitunganSpillwayBy = $indexBy($perhitunganSpillway);
        $tebingKananBy = $indexBy($tebingKanan);
        $totalBocoranBy = $indexBy($totalBocoran);
        $perhitunganBatasBy = $indexBy($perhitunganBatas);

        $dataToSend = [];

        foreach ($pengukuran as $p) {
            $pid = $p['id'];
            $dataToSend[] = [
                'pengukuran' => $p,
                'thomson'    => $thomsonBy[$pid] ?? [],
                'sr'         => $srBy[$pid] ?? [],
                'bocoran'    => $bocoranBy[$pid] ?? [],
                'perhitungan_thomson' => $perhitunganThomsonBy[$pid] ?? [],
                'perhitungan_sr' => $perhitunganSrBy[$pid] ?? [],
                'perhitungan_bocoran' => $perhitunganBocoranBy[$pid] ?? [],
                'perhitungan_ig' => $perhitunganIgBy[$pid] ?? [],
                'perhitungan_spillway' => $perhitunganSpillwayBy[$pid] ?? [],
                'tebing_kanan' => $tebingKananBy[$pid] ?? [],
                'total_bocoran' => $totalBocoranBy[$pid] ?? [],
                'perhitungan_batas' => $perhitunganBatasBy[$pid] ?? []
            ];
        }

        return $this->response->setJSON($dataToSend);
    }

    // Method untuk debugging - bisa dihapus setelah fix
    public function debugData()
    {
        $modelThomson = new MThomsonWeir();
        $thomson = $modelThomson->findAll();
        
        // Lihat struktur data Thomson
        echo "<pre>";
        echo "THOMSON DATA STRUCTURE:\n";
        if (!empty($thomson)) {
            print_r($thomson[0]);
            echo "\nTHOMSON KEYS: ";
            print_r(array_keys($thomson[0]));
        } else {
            echo "No Thomson data found";
        }
        echo "</pre>";
        
        // Lihat struktur data SR
        $modelSR = new MSR();
        $sr = $modelSR->findAll();
        
        echo "<pre>";
        echo "SR DATA STRUCTURE:\n";
        if (!empty($sr)) {
            print_r($sr[0]);
            echo "\nSR KEYS: ";
            print_r(array_keys($sr[0]));
        } else {
            echo "No SR data found";
        }
        echo "</pre>";
    }
}