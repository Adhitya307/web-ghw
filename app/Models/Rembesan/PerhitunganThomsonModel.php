<?php
namespace App\Models\Rembesan;

use CodeIgniter\Model;

class PerhitunganThomsonModel extends Model
{
    protected $table            = 'p_thomson_weir';
    protected $primaryKey       = 'id';           // pakai id karena itu PK asli
    protected $useAutoIncrement = true;           // karena id auto increment

    protected $returnType       = 'array';
    protected $allowedFields    = [
        'pengukuran_id',
        'a1_r',
        'a1_l',
        'b1',
        'b3',
        'b5'
    ];

    // Validasi
    protected $validationRules = [
        'pengukuran_id' => 'required|numeric|is_not_unique[t_data_pengukuran.id]',
        'a1_r'          => 'permit_empty|numeric',
        'a1_l'          => 'permit_empty|numeric',
        'b1'            => 'permit_empty|numeric',
        'b3'            => 'permit_empty|numeric',
        'b5'            => 'permit_empty|numeric'
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

        public function getAllWithPengukuran()
    {
        return $this->select("p_thomson_weir.*, CONCAT(t_data_pengukuran.tanggal, ' - ', t_data_pengukuran.periode) AS nama_pengukuran")
                    ->join('t_data_pengukuran', 't_data_pengukuran.id = p_thomson_weir.pengukuran_id')
                    ->orderBy('p_thomson_weir.id', 'DESC')
                    ->findAll();
    }
}
