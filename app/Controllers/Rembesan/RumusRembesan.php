<?php

namespace App\Controllers\Rembesan;

use App\Controllers\BaseController;
use App\Models\Rembesan\DataGabunganModel;
use App\Models\Rembesan\PerhitunganSRModel;
use App\Models\Rembesan\PerhitunganBocoranModel;
use App\Models\Rembesan\PerhitunganIntiGaleryModel;
use App\Models\Rembesan\PerhitunganSpillwayModel;
use App\Models\Rembesan\TebingKananModel;
use App\Models\Rembesan\TotalBocoranModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RumusRembesan extends BaseController
{
    public function index()
    {
        return $this->response->setJSON([
            'status' => 'API Rembesan aktif',
            'message' => 'Gunakan /getRembesanData atau /inputData'
        ]);
    }

    public function inputData()
    {
        helper([
            'Rembesan/thomson', 
            'Rembesan/sr', 
            'Rembesan/bocoran', 
            'Rembesan/ambang',
            'Rembesan/spillway', 
            'Rembesan/tebing', 
            'Rembesan/totalBocoran', 
            'Rembesan/batasMaksimal'
        ]);

        $model = new DataGabunganModel();
        $dataGabungan = $model->getDataGabungan();

        $sheetThomson = IOFactory::load(FCPATH . 'assets/excel/tabel_thomson.xlsx')
                                 ->getSheetByName('Tabel Thomson');
        $sheetAmbang = IOFactory::load(FCPATH . 'assets/excel/tabel_ambang.xlsx')
                                 ->getSheetByName('AMBANG TIAP CM');

        $ambangData = getAmbangData($sheetAmbang);
        $ambangDataTebing = getAmbangTebingKanan(FCPATH . 'assets/excel/tabel_ambang.xlsx', 'AMBANG TIAP CM');
        $spillwayDataArray = loadAmbangSpillway(FCPATH . 'assets/excel/tabel_ambang.xlsx', 'AMBANG TIAP CM');

        $sr_fields = [1,40,66,68,70,79,81,83,85,92,94,96,98,100,102,104,106];

        foreach ($dataGabungan as &$data) {
            $tma = (float)($data['pengukuran']['tma_waduk'] ?? 0);

            // Thomson
            $thomson = [
                'r' => perhitunganQ_thomson($data['thomson']['a1_r'] ?? 0, $sheetThomson),
                'l' => perhitunganQ_thomson($data['thomson']['a1_l'] ?? 0, $sheetThomson),
                'b1'=> perhitunganQ_thomson($data['thomson']['b1'] ?? 0, $sheetThomson),
                'b3'=> perhitunganQ_thomson($data['thomson']['b3'] ?? 0, $sheetThomson),
                'b5'=> perhitunganQ_thomson($data['thomson']['b5'] ?? 0, $sheetThomson),
            ];
            $data['perhitungan_thomson'] = $thomson;

            // SR
            $perhitunganSR = [];
            foreach($sr_fields as $field){
                $nilai = $data['sr']["sr_{$field}_nilai"] ?? 0;
                $kode = $data['sr']["sr_{$field}_kode"] ?? '';
                $perhitunganSR["sr_{$field}_q"] = perhitunganQ_sr($nilai, $kode);
            }

            // Bocoran
            $perhitunganBocoran = [
                'talang1'=> perhitunganQ_bocoran($data['bocoran']['elv_624_t1'] ?? 0, $data['bocoran']['elv_624_t1_kode'] ?? ''),
                'talang2'=> perhitunganQ_bocoran($data['bocoran']['elv_615_t2'] ?? 0, $data['bocoran']['elv_615_t2_kode'] ?? ''),
                'pipa'=> perhitunganQ_bocoran($data['bocoran']['pipa_p1'] ?? 0, $data['bocoran']['pipa_p1_kode'] ?? '')
            ];

            // Inti Galeri
            $a1 = $thomson['r'] + $thomson['l'];
            $ambang_a1 = ($tma>0)?cariAmbangArray($tma,$ambangData):null;
            $perhitunganInti = ['pengukuran_id'=>$data['pengukuran_id']??null,'a1'=>$a1,'ambang_a1'=>$ambang_a1];

            // Spillway
            $B3 = hitungSpillway($thomson['b1'],$thomson['b3']);
            $spillwayData = ['pengukuran_id'=>$data['pengukuran_id']??null,'B3'=>$B3,'ambang'=>($tma>0)?cariAmbangSpillway($tma,$spillwayDataArray):null];

            // Tebing
            $sr_tebing = hitungSrTebingKanan($data['sr'] ?? [], $sr_fields);
            $perhitunganTebing = ['sr'=>$sr_tebing,'ambang'=>($tma>0)?cariAmbangTebingKanan($tma,$ambangDataTebing):null,'pengukuran_id'=>$data['pengukuran_id']??null,'b5'=>$thomson['b5']];

            // Total Bocoran
            $r1 = hitungTotalBocoran($perhitunganInti['a1'],$spillwayData['B3'],$sr_tebing);

            $data['perhitungan_sr'] = $perhitunganSR;
            $data['perhitungan_bocoran'] = $perhitunganBocoran;
            $data['perhitungan_inti'] = $perhitunganInti;
            $data['perhitungan_spillway'] = $spillwayData;
            $data['perhitungan_tebing_kanan'] = $perhitunganTebing;
            $data['perhitungan_total_bocoran'] = ['r1'=>$r1];
        }

        return $this->response->setJSON($dataGabungan);
    }
}
