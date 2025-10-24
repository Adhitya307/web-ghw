<?php

namespace App\Models\HDM;

use CodeIgniter\Model;

class MInitialReadingElv625 extends Model
{
    protected $DBGroup = 'hdm';
    protected $table = 'm_initial_reading_elv_625';
    protected $primaryKey = 'id_initial_reading';
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
     * Insert data dengan nilai FIXED: 36.00, 35.50, 35.00
     */
    public function insertReading($pengukuran_id)
    {
        return $this->insert([
            'id_pengukuran' => $pengukuran_id,
            'hv_1' => 36.00,
            'hv_2' => 35.50,
            'hv_3' => 35.00
        ]);
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

    /**
     * Get data by id_initial_reading
     */
    public function getById($id)
    {
        return $this->find($id);
    }
}