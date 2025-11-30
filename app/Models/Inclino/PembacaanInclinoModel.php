<?php

namespace App\Models\Inclino;

use CodeIgniter\Model;

class PembacaanInclinoModel extends Model
{
    protected $table            = 'inclinometer_readings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'depth',
        'face_a_plus',
        'face_a_minus', 
        'face_b_plus',
        'face_b_minus',
        'reading_date',
        'borehole_name',
        'file_name',
        'probe_serial',
        'reel_serial',
        'operator',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Constructor - set database connection to db_inclino
     */
    public function __construct()
    {
        parent::__construct();
        
        // Gunakan koneksi database db_inclino
        $this->db = \Config\Database::connect('db_inclino');
    }

    /**
     * Insert batch data untuk inclinometer readings - VERSI SIMPLE & FIXED
     */
    public function insertBatchInclinometer(array $data)
    {
        log_message('debug', 'InsertBatch: Starting with ' . count($data) . ' records');
        
        try {
            // **SIMPLE INSERT TANPA TEST COMPLICATED**
            $this->db->transStart();
            
            $result = $this->db->table($this->table)->insertBatch($data);
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $error = $this->db->error();
                log_message('error', 'Batch insert failed: ' . print_r($error, true));
                throw new \Exception('Database error: ' . ($error['message'] ?? 'Unknown error'));
            }

            log_message('debug', 'InsertBatch: Successfully inserted ' . $result . ' records');
            return $result;

        } catch (\Exception $e) {
            log_message('error', 'InsertBatch ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek apakah data sudah ada (duplikat)
     */
    public function isDataExists($boreholeName, $readingDate, $depth)
    {
        try {
            return $this->where('borehole_name', $boreholeName)
                        ->where('reading_date', $readingDate)
                        ->where('depth', $depth)
                        ->countAllResults() > 0;
        } catch (\Exception $e) {
            log_message('error', 'isDataExists ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get list of boreholes
     */
    public function getBoreholeList()
    {
        try {
            return $this->distinct()
                        ->select('borehole_name')
                        ->orderBy('borehole_name', 'ASC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getBoreholeList ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get reading dates by borehole
     */
    public function getReadingDates($boreholeName)
    {
        try {
            return $this->distinct()
                        ->select('reading_date')
                        ->where('borehole_name', $boreholeName)
                        ->orderBy('reading_date', 'DESC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getReadingDates ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete data by borehole and date
     */
    public function deleteByBoreholeAndDate($borehole, $readingDate)
    {
        try {
            return $this->where('borehole_name', $borehole)
                        ->where('reading_date', $readingDate)
                        ->delete();
        } catch (\Exception $e) {
            log_message('error', 'deleteByBoreholeAndDate ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total records count
     */
    public function getTotalRecords()
    {
        try {
            return $this->countAll();
        } catch (\Exception $e) {
            log_message('error', 'getTotalRecords ERROR: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get data by borehole and date
     */
    public function getByBoreholeAndDate($borehole, $readingDate)
    {
        try {
            return $this->where('borehole_name', $borehole)
                        ->where('reading_date', $readingDate)
                        ->orderBy('depth', 'ASC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByBoreholeAndDate ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Simple connection test method (optional)
     */
    public function testConnection()
    {
        try {
            $result = $this->db->query('SELECT 1 as test')->getRow();
            return $result && $result->test == 1;
        } catch (\Exception $e) {
            log_message('error', 'testConnection ERROR: ' . $e->getMessage());
            return false;
        }
    }
}