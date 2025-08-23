<?php

namespace App\Controllers\Rembesan;

use App\Controllers\BaseController;
use App\Models\Rembesan\DataGabunganModel;
use App\Models\Rembesan\PerhitunganThomsonModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ThomsonController extends BaseController
{
    protected $dataGabunganModel;
    protected $thomsonModel;

    public function __construct()
    {
        $this->dataGabunganModel = new DataGabunganModel();
        $this->thomsonModel      = new PerhitunganThomsonModel();
        helper(['Rembesan/thomson']);
    }

    /**
     * Hitung Thomson untuk pengukuran_id tertentu
     * Bisa dipanggil via URL (routes) atau internal (RumusRembesan)
     */
    public function hitung($pengukuran_id, $returnArray = false)
    {
        log_message('debug', "[ThomsonController] Mulai perhitungan untuk ID: {$pengukuran_id}");

        // 🔹 Ambil data gabungan
        $data = $this->dataGabunganModel->getDataById($pengukuran_id);
        if (!$data) {
            $msg = "❌ Data tidak ditemukan untuk ID: {$pengukuran_id}";
            log_message('error', "[ThomsonController] {$msg}");
            return $returnArray ? ['success' => false, 'message' => $msg] 
                                : $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        if (empty($data['thomson']) || !is_array($data['thomson'])) {
            $msg = "❌ Data Thomson kosong/tidak valid untuk ID: {$pengukuran_id}";
            log_message('error', "[ThomsonController] {$msg}");
            return $returnArray ? ['success' => false, 'message' => $msg] 
                                : $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        // 🔹 Load Excel
        $thomsonPath = FCPATH . 'assets/excel/tabel_thomson.xlsx';
        if (!file_exists($thomsonPath)) {
            $msg = "❌ File Excel Thomson tidak ditemukan: {$thomsonPath}";
            log_message('error', "[ThomsonController] {$msg}");
            return $returnArray ? ['success' => false, 'message' => $msg] 
                                : $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        try {
            $spreadsheet = IOFactory::load($thomsonPath);
            $sheet       = $spreadsheet->getSheetByName('Tabel Thomson');
            if (!$sheet) {
                $msg = "❌ Sheet 'Tabel Thomson' tidak ditemukan";
                log_message('error', "[ThomsonController] {$msg}");
                return $returnArray ? ['success' => false, 'message' => $msg] 
                                    : $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
        } catch (\Exception $e) {
            $msg = "❌ Error load Excel Thomson: " . $e->getMessage();
            log_message('error', "[ThomsonController] {$msg}");
            return $returnArray ? ['success' => false, 'message' => $msg] 
                                : $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        // 🔹 Hitung pakai helper
        $thomson = [
            'r'  => !empty($data['thomson']['a1_r']) ? perhitunganQ_thomson($data['thomson']['a1_r'], $sheet) : 0,
            'l'  => !empty($data['thomson']['a1_l']) ? perhitunganQ_thomson($data['thomson']['a1_l'], $sheet) : 0,
            'b1' => !empty($data['thomson']['b1'])   ? perhitunganQ_thomson($data['thomson']['b1'], $sheet) : 0,
            'b3' => !empty($data['thomson']['b3'])   ? perhitunganQ_thomson($data['thomson']['b3'], $sheet) : 0,
            'b5' => !empty($data['thomson']['b5'])   ? perhitunganQ_thomson($data['thomson']['b5'], $sheet) : 0,
        ];

        foreach ($thomson as $k => $v) {
            if ($v === null) $thomson[$k] = 0;
        }
        log_message('debug', "[ThomsonController] Hasil Thomson: " . json_encode($thomson));

        // 🔹 Simpan ke DB
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
            if ($cek) {
                $this->thomsonModel->update($cek['id'], $dataThomson);
                log_message('debug', "[ThomsonController] Updated Thomson ID: {$pengukuran_id}");
            } else {
                $this->thomsonModel->insert($dataThomson);
                log_message('debug', "[ThomsonController] Inserted Thomson ID: {$pengukuran_id}");
            }
        } catch (\Exception $e) {
            $msg = "❌ Error simpan ke DB: " . $e->getMessage();
            log_message('error', "[ThomsonController] {$msg}");
            return $returnArray ? ['success' => false, 'message' => $msg] 
                                : $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        $result = [
            'success' => true,
            'message' => "Perhitungan Thomson selesai untuk ID {$pengukuran_id}",
            'thomson' => $dataThomson
        ];

        return $returnArray ? $result : $this->response->setJSON($result);
    }
}
