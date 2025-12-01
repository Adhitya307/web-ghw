<?php

namespace App\Models\Inclino;

use CodeIgniter\Model;

class Ireadingmodel extends Model
{
    protected $table = 'initial_reading';
    protected $primaryKey = 'id_initial';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_pengukuran',
        'depth',
        'face_a',
        'face_b',
        'mean_cum_deviation_a',
        'mean_cum_deviation_b',
        'basereading_a',
        'basereading_b',
        'displace_profile_a',
        'displace_profile_b',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // DATA BASEREADING DEFAULT A - SEMUA KEYS STRING
    private $default_basereading_a = [
        '-0.5' => 0.003, '-1.0' => 0.007, '-1.5' => 0.010, '-2.0' => 0.009,
        '-2.5' => 0.006, '-3.0' => 0.005, '-3.5' => 0.003, '-4.0' => 0.002,
        '-4.5' => 0.000, '-5.0' => -0.001, '-5.5' => 0.000, '-6.0' => -0.001,
        '-6.5' => -0.003, '-7.0' => -0.004, '-7.5' => -0.004, '-8.0' => -0.006,
        '-8.5' => -0.007, '-9.0' => -0.008, '-9.5' => -0.009, '-10.0' => -0.010,
        '-10.5' => -0.011, '-11.0' => -0.012, '-11.5' => -0.012, '-12.0' => -0.012,
        '-12.5' => -0.013, '-13.0' => -0.013, '-13.5' => -0.012, '-14.0' => -0.012,
        '-14.5' => -0.012, '-15.0' => -0.012, '-15.5' => -0.013, '-16.0' => -0.013,
        '-16.5' => -0.014, '-17.0' => -0.014, '-17.5' => -0.014, '-18.0' => -0.015,
        '-18.5' => -0.015, '-19.0' => -0.016, '-19.5' => -0.018, '-20.0' => -0.018,
        '-20.5' => -0.019, '-21.0' => -0.020, '-21.5' => -0.020, '-22.0' => -0.019,
        '-22.5' => -0.019, '-23.0' => -0.019, '-23.5' => -0.019, '-24.0' => -0.020,
        '-24.5' => -0.020, '-25.0' => -0.021, '-25.5' => -0.021, '-26.0' => -0.022,
        '-26.5' => -0.022, '-27.0' => -0.022, '-27.5' => -0.023, '-28.0' => -0.023,
        '-28.5' => -0.024, '-29.0' => -0.024, '-29.5' => -0.024, '-30.0' => -0.024,
        '-30.5' => -0.024, '-31.0' => -0.023, '-31.5' => -0.022, '-32.0' => -0.022,
        '-32.5' => -0.021, '-33.0' => -0.021, '-33.5' => -0.021, '-34.0' => -0.021,
        '-34.5' => -0.022, '-35.0' => -0.022, '-35.5' => -0.023, '-36.0' => -0.023,
        '-36.5' => -0.023, '-37.0' => -0.023, '-37.5' => -0.024, '-38.0' => -0.024,
        '-38.5' => -0.024, '-39.0' => -0.024, '-39.5' => -0.024, '-40.0' => -0.024,
        '-40.5' => -0.024, '-41.0' => -0.024, '-41.5' => -0.024, '-42.0' => -0.024,
        '-42.5' => -0.024, '-43.0' => -0.024, '-43.5' => -0.025, '-44.0' => -0.025,
        '-44.5' => -0.025, '-45.0' => -0.026, '-45.5' => -0.026, '-46.0' => -0.026,
        '-46.5' => -0.026, '-47.0' => -0.027, '-47.5' => -0.027, '-48.0' => -0.027,
        '-48.5' => -0.028, '-49.0' => -0.027, '-49.5' => -0.027, '-50.0' => -0.027,
        '-50.5' => -0.027, '-51.0' => -0.027, '-51.5' => -0.027, '-52.0' => -0.028,
        '-52.5' => -0.028, '-53.0' => -0.028, '-53.5' => -0.028, '-54.0' => -0.028,
        '-54.5' => -0.029, '-55.0' => -0.028, '-55.5' => -0.028, '-56.0' => -0.028,
        '-56.5' => -0.028, '-57.0' => -0.027, '-57.5' => -0.027, '-58.0' => -0.027,
        '-58.5' => -0.027, '-59.0' => -0.028, '-59.5' => -0.029, '-60.0' => -0.029,
        '-60.5' => -0.030, '-61.0' => -0.029, '-61.5' => -0.029, '-62.0' => -0.029,
        '-62.5' => -0.028, '-63.0' => -0.027, '-63.5' => -0.026, '-64.0' => -0.026,
        '-64.5' => -0.028, '-65.0' => -0.029, '-65.5' => -0.030, '-66.0' => -0.030,
        '-66.5' => -0.031, '-67.0' => -0.031, '-67.5' => -0.031, '-68.0' => -0.030,
        '-68.5' => -0.030, '-69.0' => -0.029, '-69.5' => -0.029, '-70.0' => -0.029,
        '-70.5' => -0.028, '-71.0' => -0.028, '-71.5' => -0.027, '-72.0' => -0.027,
        '-72.5' => -0.027, '-73.0' => -0.027, '-73.5' => -0.028, '-74.0' => -0.029,
        '-74.5' => -0.030, '-75.0' => -0.031, '-75.5' => -0.032, '-76.0' => -0.030,
        '-76.5' => -0.030, '-77.0' => -0.029, '-77.5' => -0.029, '-78.0' => -0.028,
        '-78.5' => -0.027, '-79.0' => -0.026, '-79.5' => -0.027, '-80.0' => -0.028
    ];

    // DATA BASEREADING DEFAULT B - SEMUA KEYS STRING
    private $default_basereading_b = [
        '-0.5' => 0.006, '-1.0' => 0.005, '-1.5' => 0.005, '-2.0' => 0.005,
        '-2.5' => 0.005, '-3.0' => 0.004, '-3.5' => 0.004, '-4.0' => 0.004,
        '-4.5' => 0.003, '-5.0' => 0.001, '-5.5' => 0.000, '-6.0' => -0.001,
        '-6.5' => -0.001, '-7.0' => -0.002, '-7.5' => -0.002, '-8.0' => -0.003,
        '-8.5' => -0.004, '-9.0' => -0.005, '-9.5' => -0.006, '-10.0' => -0.009,
        '-10.5' => -0.010, '-11.0' => -0.010, '-11.5' => -0.010, '-12.0' => -0.010,
        '-12.5' => -0.009, '-13.0' => -0.008, '-13.5' => -0.008, '-14.0' => -0.008,
        '-14.5' => -0.007, '-15.0' => -0.006, '-15.5' => -0.006, '-16.0' => -0.006,
        '-16.5' => -0.006, '-17.0' => -0.006, '-17.5' => -0.006, '-18.0' => -0.005,
        '-18.5' => -0.005, '-19.0' => -0.005, '-19.5' => -0.005, '-20.0' => -0.005,
        '-20.5' => -0.005, '-21.0' => -0.003, '-21.5' => -0.003, '-22.0' => -0.003,
        '-22.5' => -0.004, '-23.0' => -0.005, '-23.5' => -0.006, '-24.0' => -0.004,
        '-24.5' => -0.004, '-25.0' => -0.003, '-25.5' => -0.002, '-26.0' => -0.002,
        '-26.5' => -0.001, '-27.0' => -0.001, '-27.5' => -0.001, '-28.0' => -0.002,
        '-28.5' => -0.003, '-29.0' => -0.004, '-29.5' => -0.005, '-30.0' => -0.005,
        '-30.5' => -0.005, '-31.0' => -0.005, '-31.5' => -0.005, '-32.0' => -0.005,
        '-32.5' => -0.004, '-33.0' => -0.004, '-33.5' => -0.004, '-34.0' => -0.005,
        '-34.5' => -0.005, '-35.0' => -0.005, '-35.5' => -0.005, '-36.0' => -0.005,
        '-36.5' => -0.005, '-37.0' => -0.006, '-37.5' => -0.006, '-38.0' => -0.006,
        '-38.5' => -0.007, '-39.0' => -0.007, '-39.5' => -0.007, '-40.0' => -0.007,
        '-40.5' => -0.007, '-41.0' => -0.007, '-41.5' => -0.007, '-42.0' => -0.007,
        '-42.5' => -0.007, '-43.0' => -0.007, '-43.5' => -0.007, '-44.0' => -0.007,
        '-44.5' => -0.007, '-45.0' => -0.008, '-45.5' => -0.007, '-46.0' => -0.007,
        '-46.5' => -0.007, '-47.0' => -0.007, '-47.5' => -0.007, '-48.0' => -0.007,
        '-48.5' => -0.006, '-49.0' => -0.005, '-49.5' => -0.005, '-50.0' => -0.004,
        '-50.5' => -0.003, '-51.0' => -0.003, '-51.5' => -0.003, '-52.0' => -0.003,
        '-52.5' => -0.003, '-53.0' => -0.002, '-53.5' => -0.002, '-54.0' => -0.002,
        '-54.5' => -0.002, '-55.0' => -0.002, '-55.5' => -0.003, '-56.0' => -0.003,
        '-56.5' => -0.004, '-57.0' => -0.005, '-57.5' => -0.005, '-58.0' => -0.005,
        '-58.5' => -0.004, '-59.0' => -0.003, '-59.5' => -0.003, '-60.0' => -0.003,
        '-60.5' => -0.004, '-61.0' => -0.004, '-61.5' => -0.004, '-62.0' => -0.004,
        '-62.5' => -0.004, '-63.0' => -0.004, '-63.5' => -0.005, '-64.0' => -0.005,
        '-64.5' => -0.004, '-65.0' => -0.004, '-65.5' => -0.004, '-66.0' => -0.003,
        '-66.5' => -0.004, '-67.0' => -0.004, '-67.5' => -0.005, '-68.0' => -0.005,
        '-68.5' => -0.006, '-69.0' => -0.006, '-69.5' => -0.005, '-70.0' => -0.006,
        '-70.5' => -0.007, '-71.0' => -0.008, '-71.5' => -0.008, '-72.0' => -0.009,
        '-72.5' => -0.008, '-73.0' => -0.007, '-73.5' => -0.007, '-74.0' => -0.007,
        '-74.5' => -0.006, '-75.0' => -0.005, '-75.5' => -0.004, '-76.0' => -0.003,
        '-76.5' => -0.003, '-77.0' => -0.002, '-77.5' => -0.001, '-78.0' => -0.001,
        '-78.5' => 0.001, '-79.0' => 0.001, '-79.5' => 0.001, '-80.0' => 0.001
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('db_inclino');
    }

    /**
     * Get default basereading A untuk depth tertentu - PERBAIKAN
     */
    private function getDefaultBasereadingA($depth)
    {
        // Convert depth to string dengan format yang sama
        $key = number_format($depth, 1, '.', '');
        return isset($this->default_basereading_a[$key]) 
            ? $this->default_basereading_a[$key] 
            : 0.0;
    }

    /**
     * Get default basereading B untuk depth tertentu - PERBAIKAN
     */
    private function getDefaultBasereadingB($depth)
    {
        // Convert depth to string dengan format yang sama
        $key = number_format($depth, 1, '.', '');
        return isset($this->default_basereading_b[$key]) 
            ? $this->default_basereading_b[$key] 
            : 0.0;
    }

    /**
     * METHOD UTAMA - GENERATE INITIAL READINGS
     */
    public function generateInitialReadings($id_pengukuran)
    {
        try {
            log_message('debug', "[Ireadingmodel] START generateInitialReadings for id_pengukuran: $id_pengukuran");

            // 1. GET RAW DATA
            $rawData = $this->db->table('inclinometer_readings')
                ->where('id_pengukuran', $id_pengukuran)
                ->orderBy('depth', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($rawData)) {
                throw new \Exception("Tidak ada data raw ditemukan!");
            }

            // 2. CALCULATE ALL READINGS
            $initialReadings = [];
            $interval = 0.5;
            
            foreach ($rawData as $index => $data) {
                $depth = $data['depth'];
                
                // Hitung face_a dan face_b
                $face_a = ($data['face_a_plus'] - $data['face_a_minus']) / 2;
                $face_b = ($data['face_b_plus'] - $data['face_b_minus']) / 2;
                
                // Ambil basereading
                $basereading_a = $this->getDefaultBasereadingA($depth);
                $basereading_b = $this->getDefaultBasereadingB($depth);
                
                // Hitung mean cumulative deviation
                $difference_a = $face_a - $basereading_a;
                $difference_b = $face_b - $basereading_b;
                
                $mean_cum_deviation_a = 1000 * $difference_a; // 500/0.5 = 1000
                $mean_cum_deviation_b = 1000 * $difference_b;
                
                // Log untuk verifikasi
                if ($index < 3) {
                    log_message('debug', sprintf(
                        "[Ireadingmodel] Depth %.2f: face_a=%.6f, base_a=%.6f, diff=%.6f, mean_cum=%.6f",
                        $depth, $face_a, $basereading_a, $difference_a, $mean_cum_deviation_a
                    ));
                }
                
                $initialReadings[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'depth' => $depth,
                    'face_a' => round($face_a, 6),
                    'face_b' => round($face_b, 6),
                    'mean_cum_deviation_a' => round($mean_cum_deviation_a, 6),
                    'mean_cum_deviation_b' => round($mean_cum_deviation_b, 6),
                    'basereading_a' => $basereading_a,
                    'basereading_b' => $basereading_b,
                    'displace_profile_a' => 0,
                    'displace_profile_b' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }

            // 3. CALCULATE DISPLACEMENT PROFILES
            $reversedReadings = array_reverse($initialReadings);
            
            $cumulative_a = 0;
            $cumulative_b = 0;
            
            foreach ($reversedReadings as &$reading) {
                $cumulative_a += $reading['mean_cum_deviation_a'];
                $cumulative_b += $reading['mean_cum_deviation_b'];
                
                $reading['displace_profile_a'] = round($cumulative_a, 6);
                $reading['displace_profile_b'] = round($cumulative_b, 6);
            }
            
            $initialReadings = array_reverse($reversedReadings);

            // 4. DELETE OLD AND INSERT NEW
            $this->deleteByPengukuran($id_pengukuran);
            
            $result = $this->insertBatchInitial($initialReadings);
            
            if ($result === false) {
                throw new \Exception("Insert data gagal!");
            }
            
            log_message('debug', "[Ireadingmodel] SUCCESS: Generated $result initial readings");
            
            return $result;

        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] generateInitialReadings ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert batch
     */
    public function insertBatchInitial(array $data)
    {
        try {
            $this->db->transStart();
            $result = $this->db->table($this->table)->insertBatch($data);
            $this->db->transComplete();
            
            return $this->db->transStatus() ? $result : false;
            
        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] insertBatchInitial ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete by id_pengukuran
     */
    public function deleteByPengukuran($id_pengukuran)
    {
        try {
            return $this->where('id_pengukuran', $id_pengukuran)->delete();
        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] deleteByPengukuran ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Debug method
     */
    public function debugBasereading($depth = -0.5)
    {
        $key = number_format($depth, 1, '.', '');
        return [
            'depth' => $depth,
            'key_used' => $key,
            'basereading_a' => $this->getDefaultBasereadingA($depth),
            'basereading_b' => $this->getDefaultBasereadingB($depth),
            'in_array_a' => isset($this->default_basereading_a[$key]),
            'in_array_b' => isset($this->default_basereading_b[$key])
        ];
    }
}