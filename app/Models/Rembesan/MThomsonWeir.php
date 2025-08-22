<?php
namespace App\Models\Rembesan;
use CodeIgniter\Model;

class MThomsonWeir extends Model
{
    protected $table = 't_thomson_weir';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pengukuran_id', 'a1_r', 'a1_l', 'b1', 'b3', 'b5'];
    
    protected $validationRules = [
        'pengukuran_id' => 'required|numeric|is_not_unique[t_data_pengukuran.id]',
        'a1_r' => 'permit_empty|numeric',
        'a1_l' => 'permit_empty|numeric',
        'b1' => 'permit_empty|numeric',
        'b3' => 'permit_empty|numeric',
        'b5' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'pengukuran_id' => [
            'required' => 'pengukuran_id harus diisi',
            'numeric' => 'pengukuran_id harus berupa angka',
            'is_not_unique' => 'Data pengukuran dengan ID {value} tidak ditemukan'
        ]
    ];
}