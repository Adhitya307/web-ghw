<?php
namespace App\Models\HDM;

use CodeIgniter\Model;

class MPergerakanElv625 extends Model
{
    protected $DBGroup = 'hdm';
    protected $table = 't_pergerakan_elv625';
    protected $primaryKey = 'id_pergerakan';
    protected $allowedFields = [
        'id_pengukuran', 
        'hv_1', 
        'hv_2', 
        'hv_3', 
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Hitung dan insert data pergerakan
     */
    public function hitungPergerakan($pengukuran_id)
{
    try {
        // Ambil data pembacaan terbaru
        $pembacaanModel = new MPembacaanElv625();
        $pembacaan = $pembacaanModel->where('id_pengukuran', $pengukuran_id)->first();
        
        if (!$pembacaan) {
            throw new \Exception('Data pembacaan tidak ditemukan');
        }

        // Ambil initial reading
        $initialModel = new MInitialReadingElv625();
        $initial = $initialModel->where('id_pengukuran', $pengukuran_id)->first();
        
        if (!$initial) {
            throw new \Exception('Data initial reading tidak ditemukan');
        }

        // Hitung pergerakan
        $pergerakanData = [
            'id_pengukuran' => $pengukuran_id,
            'hv_1' => $pembacaan['hv_1'] - $initial['hv_1'],
            'hv_2' => $pembacaan['hv_2'] - $initial['hv_2'],
            'hv_3' => $pembacaan['hv_3'] - $initial['hv_3']
        ];

        // Cek apakah data pergerakan sudah ada
        $existing = $this->where('id_pengukuran', $pengukuran_id)->first();
        
        if ($existing) {
            // Update data yang sudah ada
            $this->update($existing['id_pergerakan'], $pergerakanData);
        } else {
            // Insert data baru
            $this->insert($pergerakanData);
        }

        return true;
    } catch (\Exception $e) {
        throw new \Exception('Gagal menghitung pergerakan: ' . $e->getMessage());
    }
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