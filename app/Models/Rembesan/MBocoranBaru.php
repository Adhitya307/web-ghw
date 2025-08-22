<?php
namespace App\Models\Rembesan;
use CodeIgniter\Model;

class MBocoranBaru extends Model
{
    protected $table = 't_bocoran_baru';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pengukuran_id',
        'elv_624_t1', 'elv_624_t1_kode',
        'elv_615_t2', 'elv_615_t2_kode',
        'pipa_p1', 'pipa_p1_kode'
    ];
    
    protected $validationRules = [
        'pengukuran_id' => 'required|numeric|is_not_unique[t_data_pengukuran.id]',
        'elv_624_t1' => 'permit_empty|numeric',
        'elv_615_t2' => 'permit_empty|numeric',
        'pipa_p1' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'pengukuran_id' => [
            'required' => 'pengukuran_id harus diisi',
            'numeric' => 'pengukuran_id harus berupa angka',
            'is_not_unique' => 'Data pengukuran dengan ID {value} tidak ditemukan'
        ]
    ];
}