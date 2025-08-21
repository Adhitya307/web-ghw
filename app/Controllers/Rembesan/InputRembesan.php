<?php

namespace App\Controllers\Rembesan;

use CodeIgniter\Controller;
use Config\Database;

class InputRembesan extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();

        // Support CORS untuk testing Android / Postman
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    }

    private function getVal($key, $data)
    {
        return isset($data[$key]) && trim($data[$key]) !== '' ? $data[$key] : null;
    }

    public function index()
    {
        // Ambil raw input untuk handle JSON
        $rawInput = $this->request->getBody();
        $data = json_decode($rawInput, true);
        
        // Jika JSON decode gagal, coba dari form data
        if (!$data || json_last_error() !== JSON_ERROR_NONE) {
            $data = $this->request->getPost();
        }

        if (!$data) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Tidak ada data dikirim!"
            ]);
        }

        $mode = $this->getVal("mode", $data);
        $pengukuran_id = $this->getVal("pengukuran_id", $data);
        $temp_id = $this->getVal("temp_id", $data);

        if (!$mode) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Parameter mode wajib dikirim!"
            ]);
        }

        // Mode pengukuran langsung simpan
        if ($mode === "pengukuran") {
            return $this->savePengukuran($data, $temp_id);
        }

        // Ambil pengukuran_id dari temp_id jika belum ada
        if ((!$pengukuran_id || !is_numeric($pengukuran_id)) && $temp_id) {
            $row = $this->db->table("t_data_pengukuran")->where("temp_id", $temp_id)->get()->getRow();
            if ($row) $pengukuran_id = $row->id;
            else return $this->response->setJSON([
                "status" => "error",
                "message" => "Tidak ditemukan pengukuran dari temp_id: " . $temp_id
            ]);
        }

        if (!$pengukuran_id || !is_numeric($pengukuran_id)) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "pengukuran_id wajib dikirim atau ditemukan dari temp_id"
            ]);
        }

        switch ($mode) {
            case "thomson":
                return $this->saveThomson($data, $pengukuran_id);
            case "sr":
                return $this->saveSr($data, $pengukuran_id);
            case "bocoran":
                return $this->saveBocoran($data, $pengukuran_id);
            default:
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Mode tidak dikenali: $mode"
                ]);
        }
    }

    private function savePengukuran($data, $temp_id)
    {
        $tahun   = $this->getVal('tahun', $data);
        $bulan   = $this->getVal('bulan', $data);
        $periode = $this->getVal('periode', $data);
        $tanggal = $this->getVal('tanggal', $data);
        $tma     = $this->getVal('tma_waduk', $data);
        $curah   = $this->getVal('curah_hujan', $data);

        if (!$tahun || !$tanggal) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Tahun dan Tanggal wajib diisi!"
            ]);
        }

        // Cek duplikasi berdasarkan tahun, bulan, periode
        $check = $this->db->table("t_data_pengukuran")
            ->where("tahun", $tahun)
            ->where("bulan", $bulan)
            ->where("periode", $periode)
            ->get()
            ->getRow();
            
        if ($check) {
            return $this->response->setJSON([
                "status" => "success",
                "message" => "Data sudah ada.",
                "pengukuran_id" => $check->id
            ]);
        }

        $this->db->table("t_data_pengukuran")->insert([
            "tahun" => $tahun,
            "bulan" => $bulan,
            "periode" => $periode,
            "tanggal" => $tanggal,
            "tma_waduk" => $tma,
            "curah_hujan" => $curah,
            "temp_id" => $temp_id
        ]);

        return $this->response->setJSON([
            "status" => "success",
            "message" => "Data pengukuran berhasil disimpan.",
            "pengukuran_id" => $this->db->insertID()
        ]);
    }

    private function saveThomson($data, $pengukuran_id)
    {
        $check = $this->db->table("t_thomson_weir")->where("pengukuran_id", $pengukuran_id)->get()->getRow();
        if ($check) return $this->response->setJSON([
            "status" => "success",
            "message" => "Data Thomson Weir sudah ada."
        ]);

        $this->db->table("t_thomson_weir")->insert([
            "pengukuran_id" => $pengukuran_id,
            "a1_r" => $this->getVal('a1_r', $data),
            "a1_l" => $this->getVal('a1_l', $data),
            "b1"   => $this->getVal('b1', $data),
            "b3"   => $this->getVal('b3', $data),
            "b5"   => $this->getVal('b5', $data),
        ]);

        return $this->response->setJSON([
            "status" => "success",
            "message" => "Data Thomson Weir berhasil disimpan."
        ]);
    }

    private function saveSr($data, $pengukuran_id)
    {
        $check = $this->db->table("t_sr")->where("pengukuran_id", $pengukuran_id)->get()->getRow();
        if ($check) return $this->response->setJSON([
            "status" => "success",
            "message" => "Data SR sudah ada."
        ]);

        $fields = [1,40,66,68,70,79,81,83,85,92,94,96,98,100,102,104,106];
        $insert = ["pengukuran_id" => $pengukuran_id];

        foreach ($fields as $kode) {
            $insert["sr_{$kode}_kode"] = $this->getVal("sr_{$kode}_kode", $data) ?? '';
            $insert["sr_{$kode}_nilai"] = floatval($this->getVal("sr_{$kode}_nilai", $data) ?? 0);
        }

        $this->db->table("t_sr")->insert($insert);

        return $this->response->setJSON([
            "status" => "success",
            "message" => "Data SR berhasil disimpan."
        ]);
    }

    private function saveBocoran($data, $pengukuran_id)
    {
        $check = $this->db->table("t_bocoran_baru")->where("pengukuran_id", $pengukuran_id)->get()->getRow();
        if ($check) return $this->response->setJSON([
            "status" => "success",
            "message" => "Data bocoran sudah ada."
        ]);

        $this->db->table("t_bocoran_baru")->insert([
            "pengukuran_id" => $pengukuran_id,
            "elv_624_t1" => $this->getVal('elv_624_t1', $data),
            "elv_624_t1_kode" => $this->getVal('elv_624_t1_kode', $data),
            "elv_615_t2" => $this->getVal('elv_615_t2', $data),
            "elv_615_t2_kode" => $this->getVal('elv_615_t2_kode', $data),
            "pipa_p1" => $this->getVal('pipa_p1', $data),
            "pipa_p1_kode" => $this->getVal('pipa_p1_kode', $data),
        ]);

        return $this->response->setJSON([
            "status" => "success",
            "message" => "Data bocoran berhasil disimpan."
        ]);
    }
}