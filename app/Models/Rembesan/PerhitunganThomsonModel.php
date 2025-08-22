<?php
namespace App\Models\Rembesan;

use CodeIgniter\Model;

class PerhitunganThomsonModel extends Model
{
    protected $table = 'perhitungan_q_thomson';
    protected $primaryKey = 'id';
    protected $allowedFields = ['a1_r', 'a1_l', 'b1', 'b3', 'b5', 'pengukuran_id'];
    
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

    public function getAllWithPengukuran()
    {
        return $this->select('perhitungan_q_thomson.*, t_data_pengukuran.nama AS nama_pengukuran')
                    ->join('t_data_pengukuran', 't_data_pengukuran.id = perhitungan_q_thomson.pengukuran_id')
                    ->findAll();
    }
}