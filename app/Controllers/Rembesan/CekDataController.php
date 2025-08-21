<?php

namespace App\Controllers\Rembesan;

use CodeIgniter\Controller;
use Config\Database;

class CekDataController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        
        // Support CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }
    }

    public function index()
    {
        $pengukuran_id = $this->request->getGet('pengukuran_id', FILTER_VALIDATE_INT);

        $response = [
            "status" => "success",
            "data" => [
                "pengukuran_ada" => false,
                "thomson_ada" => false,
                "sr_ada" => false,
                "bocoran_ada" => false
            ]
        ];

        if (!$pengukuran_id || $pengukuran_id <= 0) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Parameter pengukuran_id tidak valid"
            ]);
        }

        try {
            // Gunakan query builder untuk keamanan
            $builder = $this->db->table('t_data_pengukuran');
            $response['data']['pengukuran_ada'] = $builder->where('id', $pengukuran_id)->countAllResults() > 0;

            $builder = $this->db->table('t_thomson_weir');
            $response['data']['thomson_ada'] = $builder->where('pengukuran_id', $pengukuran_id)->countAllResults() > 0;

            $builder = $this->db->table('t_sr');
            $response['data']['sr_ada'] = $builder->where('pengukuran_id', $pengukuran_id)->countAllResults() > 0;

            $builder = $this->db->table('t_bocoran_baru');
            $response['data']['bocoran_ada'] = $builder->where('pengukuran_id', $pengukuran_id)->countAllResults() > 0;

        } catch (\Exception $e) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }

        return $this->response->setJSON($response);
    }
}