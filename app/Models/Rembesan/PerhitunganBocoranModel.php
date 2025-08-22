<?php
namespace App\Models\Rembesan;

use CodeIgniter\Model;

class PerhitunganBocoranModel extends Model
{
    protected $table      = 'p_bocoran_baru';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pengukuran_id',
        'talang1',
        'talang2',
        'pipa'
    ];
    
    protected $validationRules = [
        'pengukuran_id' => 'required|numeric|is_not_unique[t_data_pengukuran.id]',
        'talang1' => 'permit_empty|numeric',
        'talang2' => 'permit_empty|numeric',
        'pipa' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'pengukuran_id' => [
            'required' => 'pengukuran_id harus diisi',
            'numeric' => 'pengukuran_id harus berupa angka',
            'is_not_unique' => 'Data pengukuran dengan ID {value} tidak ditemukan'
        ]
    ];
    
    protected $useTimestamps = true;
}