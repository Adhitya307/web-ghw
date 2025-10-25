<?php
namespace App\Models\HDM;

use CodeIgniter\Model;

class AmbangBatas625H3Model extends Model
{
    protected $DBGroup = 'hdm';
    protected $table = 'ambang_batas_625_h3';
    protected $primaryKey = 'id_ambang_batas';
    protected $allowedFields = [
        'id_pengukuran', 
        'aman', 
        'peringatan', 
        'bahaya', 
        'pergerakan',
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get data by id_pengukuran
     */
    public function getByPengukuran($pengukuran_id)
    {
        return $this->where('id_pengukuran', $pengukuran_id)->first();
    }

    /**
     * Insert data default
     */
    public function insertDefault($pengukuran_id)
    {
        return $this->insert([
            'id_pengukuran' => $pengukuran_id,
            'aman' => -5.94,
            'peringatan' => -6.85,
            'bahaya' => -8.10,
            'pergerakan' => null
        ]);
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
     * Update pergerakan from t_pergerakan_elv625 hv_3 * 10
     */
    public function updatePergerakanFromHv3($pengukuran_id)
    {
        $pergerakanModel = new MPergerakanElv625();
        $pergerakan = $pergerakanModel->getByPengukuran($pengukuran_id);
        
        if ($pergerakan && isset($pergerakan['hv_3'])) {
            $pergerakan_value = $pergerakan['hv_3'] * 10;
            return $this->where('id_pengukuran', $pengukuran_id)
                        ->set('pergerakan', $pergerakan_value)
                        ->update();
        }
        
        return false;
    }
}