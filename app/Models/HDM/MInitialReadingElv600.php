<?php
namespace App\Models\HDM;

use CodeIgniter\Model;

class MInitialReadingElv600 extends Model
{
    protected $DBGroup = 'hdm';
    protected $table = 'm_initial_reading_elv_600';
    protected $primaryKey = 'id_initial_reading';
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

    public function insertReading($pengukuran_id)
    {
        return $this->insert([
            'id_pengukuran' => $pengukuran_id,
            'hv_1' => 26.60,
            'hv_2' => 25.50,
            'hv_3' => 24.50,
            'hv_4' => 23.40,
            'hv_5' => 23.60
        ]);
    }

    public function getByPengukuran($pengukuran_id)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->first();
    }

    public function updateByPengukuran($pengukuran_id, $data)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->set($data)->update();
    }

    public function deleteByPengukuran($pengukuran_id)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->delete();
    }

    public function getAll()
    {
        return $this->findAll();
    }

    public function getById($id)
    {
        return $this->find($id);
    }
}