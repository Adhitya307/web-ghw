<?php
namespace App\Models\HDM;

use CodeIgniter\Model;

class MPergerakanElv600 extends Model
{
    protected $DBGroup = 'hdm';
    protected $table = 't_pergerakan_elv600';
    protected $primaryKey = 'id_pergerakan';
    protected $allowedFields = [
        'id_pengukuran', 
        'hv_1', 
        'hv_2', 
        'hv_3', 
        'hv_4', 
        'hv_5', 
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Hitung dan insert data pergerakan
     */
    public function hitungPergerakan($id_pengukuran)
    {
        // Load model yang diperlukan
        $pembacaanModel = new \App\Models\HDM\MPembacaanElv600();
        $initialModel = new \App\Models\HDM\MInitialReadingElv600();
        
        // Ambil data pembacaan
        $pembacaan = $pembacaanModel->getByPengukuran($id_pengukuran);
        // Ambil data initial reading
        $initial = $initialModel->getByPengukuran($id_pengukuran);
        
        if (!$pembacaan || !$initial) {
            return false; // Data tidak lengkap
        }
        
        // Hitung pergerakan (Pembacaan - Initial Reading)
        $hv_1 = $pembacaan['hv_1'] - $initial['hv_1'];
        $hv_2 = $pembacaan['hv_2'] - $initial['hv_2'];
        $hv_3 = $pembacaan['hv_3'] - $initial['hv_3'];
        $hv_4 = $pembacaan['hv_4'] - $initial['hv_4'];
        $hv_5 = $pembacaan['hv_5'] - $initial['hv_5'];
        
        // Insert data pergerakan
        return $this->insert([
            'id_pengukuran' => $id_pengukuran,
            'hv_1' => $hv_1,
            'hv_2' => $hv_2,
            'hv_3' => $hv_3,
            'hv_4' => $hv_4,
            'hv_5' => $hv_5
        ]);
    }

    /**
     * Hitung pergerakan untuk semua pengukuran
     */
    public function hitungSemuaPergerakan()
    {
        $pengukuranModel = new \App\Models\HDM\MPengukuranHdm();
        $pengukurans = $pengukuranModel->findAll();
        
        $results = [];
        foreach ($pengukurans as $pengukuran) {
            $result = $this->hitungPergerakan($pengukuran['id_pengukuran']);
            $results[] = [
                'id_pengukuran' => $pengukuran['id_pengukuran'],
                'success' => $result !== false
            ];
        }
        
        return $results;
    }

    /**
     * Get data by id_pengukuran
     */
    public function getByPengukuran($pengukuran_id)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->first();
    }

    /**
     * Update data by id_pengukuran
     */
    public function updateByPengukuran($pengukuran_id, $data)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->set($data)->update();
    }

    /**
     * Delete data by id_pengukuran
     */
    public function deleteByPengukuran($pengukuran_id)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->delete();
    }

    /**
     * Get all data
     */
    public function getAll()
    {
        return $this->findAll();
    }
}