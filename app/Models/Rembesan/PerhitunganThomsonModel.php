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
}
