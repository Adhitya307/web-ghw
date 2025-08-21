<?php
namespace App\Controllers\Rembesan;

use App\Controllers\BaseController;
use CodeIgniter\Database\Config;

class CheckConnection extends BaseController
{
    public function index()
    {
        try {
            $db = Config::connect(); 
            $db->connect(); // paksa koneksi

            return $this->response->setJSON([
                "status"  => "success",
                "message" => "Koneksi ke database berhasil!"
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => $e->getMessage() // tampilkan error asli
            ]);
        }
    }
}
