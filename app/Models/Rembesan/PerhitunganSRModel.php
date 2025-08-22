<?php
namespace App\Models\Rembesan;
use CodeIgniter\Model;

class PerhitunganSRModel extends Model
{
    protected $table = 'p_sr';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pengukuran_id',
        'sr_1_q', 'sr_40_q', 'sr_66_q', 'sr_68_q', 'sr_70_q',
        'sr_79_q', 'sr_81_q', 'sr_83_q', 'sr_85_q', 'sr_92_q',
        'sr_94_q', 'sr_96_q', 'sr_98_q', 'sr_100_q', 'sr_102_q',
        'sr_104_q', 'sr_106_q',
        'created_at', 'updated_at'
    ];
    
    protected $validationRules = [
        'pengukuran_id' => 'required|numeric|is_not_unique[t_data_pengukuran.id]',
        'sr_1_q' => 'permit_empty|numeric',
        'sr_40_q' => 'permit_empty|numeric',
        'sr_66_q' => 'permit_empty|numeric',
        'sr_68_q' => 'permit_empty|numeric',
        'sr_70_q' => 'permit_empty|numeric',
        'sr_79_q' => 'permit_empty|numeric',
        'sr_81_q' => 'permit_empty|numeric',
        'sr_83_q' => 'permit_empty|numeric',
        'sr_85_q' => 'permit_empty|numeric',
        'sr_92_q' => 'permit_empty|numeric',
        'sr_94_q' => 'permit_empty|numeric',
        'sr_96_q' => 'permit_empty|numeric',
        'sr_98_q' => 'permit_empty|numeric',
        'sr_100_q' => 'permit_empty|numeric',
        'sr_102_q' => 'permit_empty|numeric',
        'sr_104_q' => 'permit_empty|numeric',
        'sr_106_q' => 'permit_empty|numeric'
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