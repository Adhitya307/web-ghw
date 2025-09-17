<?php
namespace App\Models\Rembesan;
use CodeIgniter\Model;

class MDataPengukuran extends Model
{
    protected $table = 't_data_pengukuran';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tahun', 'bulan', 'periode', 'tanggal', 
        'tma_waduk', 'curah_hujan', 'temp_id' // ADDED temp_id
    ];
    
    protected $validationRules = [
        'tahun' => 'required',
        'bulan' => 'permit_empty',
        'periode' => 'permit_empty',
        'tanggal' => 'required|valid_date',
        'tma_waduk' => 'permit_empty|numeric',
        'curah_hujan' => 'permit_empty|numeric',
        'temp_id' => 'permit_empty|string'
    ];
    
    protected $validationMessages = [
        'tahun' => [
            'required' => 'Tahun harus diisi',
            'numeric' => 'Tahun harus berupa angka'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];

    public function getAllOrdered()
{
    return $this->orderBy('bulan', 'ASC')
                ->orderBy('tanggal', 'ASC')
                ->findAll();
}

}