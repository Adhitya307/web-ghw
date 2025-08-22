<?php
namespace App\Models\Rembesan;
use CodeIgniter\Model;

class MAmbangBatas extends Model
{
    protected $table = 't_ambang_batas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pengukuran_id', 'tma', 'ambang_a1', 'ambang_b3', 'ambang_sr', 'ambang_b5'];
    
    protected $validationRules = [
        'pengukuran_id' => 'required|numeric|is_not_unique[t_data_pengukuran.id]',
        'tma' => 'permit_empty|numeric',
        'ambang_a1' => 'permit_empty|numeric',
        'ambang_b3' => 'permit_empty|numeric',
        'ambang_sr' => 'permit_empty|numeric',
        'ambang_b5' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'pengukuran_id' => [
            'required' => 'pengukuran_id harus diisi',
            'numeric' => 'pengukuran_id harus berupa angka',
            'is_not_unique' => 'Data pengukuran dengan ID {value} tidak ditemukan'
        ]
    ];
}