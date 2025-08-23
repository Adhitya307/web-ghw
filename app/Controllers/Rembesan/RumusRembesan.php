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
use App\Models\Rembesan\PerhitunganThomsonModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RumusRembesan extends BaseController
{
    protected $srModel;
    protected $bocoranModel;
    protected $intiGaleryModel;
    protected $spillwayModel;
    protected $tebingModel;
    protected $totalBocoranModel;
    protected $dataGabunganModel;
    protected $thomsonModel;

    public function __construct()
    {
        $this->srModel           = new PerhitunganSRModel();
        $this->bocoranModel      = new PerhitunganBocoranModel();
        $this->intiGaleryModel   = new PerhitunganIntiGaleryModel();
        $this->spillwayModel     = new PerhitunganSpillwayModel();
        $this->tebingModel       = new TebingKananModel();
        $this->totalBocoranModel = new TotalBocoranModel();
        $this->dataGabunganModel = new DataGabunganModel();
        $this->thomsonModel      = new PerhitunganThomsonModel();

        helper([
            'Rembesan/thomson',
            'Rembesan/sr',
            'Rembesan/bocoran',
            'Rembesan/ambang',
            'Rembesan/spillway',
            'Rembesan/tebingkanan',
            'Rembesan/totalBocoran',
            'Rembesan/BatasMaksimal'
        ]);

        if (!function_exists('perhitunganQ_thomson')) {
            log_message('error', '❌ Thomson helper NOT loaded!');
        } else {
            log_message('debug', '✅ Thomson helper loaded successfully');
        }

        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function inputDataForId($pengukuran_id)
    {
        log_message('debug', "=== inputDataForId() STARTED for ID: {$pengukuran_id} ===");

        // 🔹 Ambil data gabungan
        $data = $this->dataGabunganModel->getDataById($pengukuran_id);

        if (!$data) {
            log_message('debug', "❌ Data tidak ditemukan untuk ID: {$pengukuran_id}");
            return ['success' => false, 'message' => 'Data tidak ditemukan'];
        }

        log_message('debug', "Data ditemukan untuk ID: {$pengukuran_id}");
        log_message('debug', "Thomson data: " . json_encode($data['thomson'] ?? []));
        log_message('debug', "Pengukuran data: " . json_encode($data['pengukuran'] ?? []));

        // 🔹 Validasi data thomson
        if (empty($data['thomson']) || !is_array($data['thomson'])) {
            log_message('debug', "❌ Data Thomson tidak valid/kosong untuk ID: {$pengukuran_id}");
            return ['success' => false, 'message' => 'Data Thomson tidak valid'];
        }

        // 🔹 Load Excel Thomson
        $thomsonPath = FCPATH . 'assets/excel/tabel_thomson.xlsx';
        if (!file_exists($thomsonPath)) {
            log_message('error', '❌ File Thomson tidak ditemukan: ' . $thomsonPath);
            return ['success' => false, 'message' => 'File Excel Thomson tidak ditemukan'];
        }

        try {
            $spreadsheetThomson = IOFactory::load($thomsonPath);
            $sheetThomson       = $spreadsheetThomson->getSheetByName('Tabel Thomson');
            if (!$sheetThomson) {
                log_message('error', '❌ Sheet "Tabel Thomson" tidak ditemukan');
                return ['success' => false, 'message' => 'Sheet Thomson tidak ditemukan'];
            }
            // Debug test read
            log_message('debug', "Excel test A4: " . $sheetThomson->getCell('A4')->getValue() .
                ", C4: " . $sheetThomson->getCell('C4')->getValue());
        } catch (\Exception $e) {
            log_message('error', '❌ Error load Thomson Excel: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error load file Excel: ' . $e->getMessage()];
        }

        // 🔹 Perhitungan Thomson
        $thomson = [
            'r'  => !empty($data['thomson']['a1_r']) ? perhitunganQ_thomson($data['thomson']['a1_r'], $sheetThomson) : 0,
            'l'  => !empty($data['thomson']['a1_l']) ? perhitunganQ_thomson($data['thomson']['a1_l'], $sheetThomson) : 0,
            'b1' => !empty($data['thomson']['b1'])   ? perhitunganQ_thomson($data['thomson']['b1'], $sheetThomson) : 0,
            'b3' => !empty($data['thomson']['b3'])   ? perhitunganQ_thomson($data['thomson']['b3'], $sheetThomson) : 0,
            'b5' => !empty($data['thomson']['b5'])   ? perhitunganQ_thomson($data['thomson']['b5'], $sheetThomson) : 0,
        ];

        // Null safety
        foreach ($thomson as $k => $v) {
            if ($v === null) $thomson[$k] = 0;
        }
        log_message('debug', 'Hasil Thomson: ' . json_encode($thomson));

        // 🔹 Simpan Thomson ke DB
        $dataThomson = [
            'pengukuran_id' => $pengukuran_id,
            'a1_r' => $thomson['r'],
            'a1_l' => $thomson['l'],
            'b1'   => $thomson['b1'],
            'b3'   => $thomson['b3'],
            'b5'   => $thomson['b5'],
        ];

        try {
            $cek = $this->thomsonModel->where('pengukuran_id', $pengukuran_id)->first();
            $cek ? $this->thomsonModel->update($cek['id'], $dataThomson)
                 : $this->thomsonModel->insert($dataThomson);
            log_message('debug', ($cek ? 'Updated' : 'Inserted') . " Thomson ID: {$pengukuran_id}");
        } catch (\Exception $e) {
            log_message('error', '❌ Error save Thomson: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error save Thomson: ' . $e->getMessage()];
        }

        // 🔹 Perhitungan Inti Galeri
        $tma = (float)($data['pengukuran']['tma_waduk'] ?? 0);
        $a1  = $thomson['r'] + $thomson['l'];
        $ambang_a1 = null;

        if ($tma > 0) {
            $ambangPath = FCPATH . 'assets/excel/tabel_ambang.xlsx';
            if (file_exists($ambangPath)) {
                try {
                    $spreadsheetAmbang = IOFactory::load($ambangPath);
                    $sheetAmbang       = $spreadsheetAmbang->getSheetByName('AMBANG TIAP CM') ?: $spreadsheetAmbang->getActiveSheet();
                    $ambangData        = getAmbangData($sheetAmbang);
                    $ambang_a1         = cariAmbangArray($tma, $ambangData);
                } catch (\Exception $e) {
                    log_message('error', '❌ Error load Ambang Excel: ' . $e->getMessage());
                }
            } else {
                log_message('error', "❌ File Ambang tidak ditemukan: {$ambangPath}");
            }
        } else {
            log_message('debug', "TMA kosong/null → skip ambang, tetap simpan A1");
        }
        log_message('debug', "Ambang A1: " . ($ambang_a1 ?? 'NULL'));

        $perhitunganInti = [
            'pengukuran_id' => $pengukuran_id,
            'a1'            => $a1,
            'ambang_a1'     => $ambang_a1
        ];

        try {
            $cekInti = $this->intiGaleryModel->where('pengukuran_id', $pengukuran_id)->first();
            $cekInti ? $this->intiGaleryModel->update($cekInti['id'], $perhitunganInti)
                     : $this->intiGaleryModel->insert($perhitunganInti);
            log_message('debug', ($cekInti ? 'Updated' : 'Inserted') . " Inti Galeri ID: {$pengukuran_id}");
        } catch (\Exception $e) {
            log_message('error', '❌ Error save Inti Galeri: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error save Inti Galeri: ' . $e->getMessage()];
        }

        log_message('debug', "=== inputDataForId() COMPLETED for ID: {$pengukuran_id} ===");

        return [
            'thomson'     => $thomson,
            'inti_galeri' => $perhitunganInti,
            'success'     => true,
            'message'     => 'Perhitungan berhasil diselesaikan'
        ];
    }
}
