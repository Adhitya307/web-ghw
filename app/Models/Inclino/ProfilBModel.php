<?php

namespace App\Models\Inclino;

use CodeIgniter\Model;

class ProfilBModel extends Model
{
    protected $table            = 'profil_b';
    protected $primaryKey       = 'id_profil_b';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pengukuran',
        'depth',
        'reading_date',
        'nilai_profil_b',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('db_inclino');
    }

    /**
     * Get profil B by id_pengukuran
     */
    public function getByPengukuran($id_pengukuran)
    {
        try {
            return $this->where('id_pengukuran', $id_pengukuran)
                        ->orderBy('depth', 'ASC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'ProfilBModel getByPengukuran ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get displace_profile_b dari tabel initial_reading dengan LOGGING
     */
    public function getDisplaceProfilesB($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilBModel] getDisplaceProfilesB START for id_pengukuran: $id_pengukuran");
            
            $query = $this->db->table('initial_reading')
                            ->select('depth, displace_profile_b')
                            ->where('id_pengukuran', $id_pengukuran)
                            ->orderBy('depth', 'ASC')
                            ->get();
            
            if (!$query) {
                log_message('error', "[ProfilBModel] Query failed for id_pengukuran: $id_pengukuran");
                return [];
            }
            
            $results = [];
            $count = 0;
            
            foreach ($query->getResultArray() as $row) {
                $results[$row['depth']] = (float)$row['displace_profile_b'];
                $count++;
                
                // Log first 5 records
                if ($count <= 5) {
                    log_message('debug', "[ProfilBModel] Row $count: depth={$row['depth']}, displace_profile_b={$row['displace_profile_b']}");
                }
            }
            
            log_message('debug', "[ProfilBModel] Total displace_profile_b records found: $count");
            
            if (empty($results)) {
                log_message('warning', "[ProfilBModel] No displace_profile_b found for id_pengukuran: $id_pengukuran");
            }
            
            return $results;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilBModel] getDisplaceProfilesB ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate dan simpan profil B dengan rumus Excel - VERSI DIPERBAIKI
     */
    public function generateProfilBWithFormulaCorrect($id_pengukuran, $reading_date)
    {
        try {
            log_message('debug', "[ProfilBModel] ===========================================");
            log_message('debug', "[ProfilBModel] START generateProfilBWithFormulaCorrect");
            log_message('debug', "[ProfilBModel] id_pengukuran: $id_pengukuran");
            log_message('debug', "[ProfilBModel] reading_date: $reading_date");
            log_message('debug', "[ProfilBModel] ===========================================");
            
            // 1. Ambil displace_profile_b dari initial_reading
            log_message('debug', "[ProfilBModel] Step 1: Getting displace_profile_b data...");
            $displaceProfiles = $this->getDisplaceProfilesB($id_pengukuran);
            
            if (empty($displaceProfiles)) {
                log_message('error', "[ProfilBModel] ERROR: displaceProfiles is EMPTY!");
                throw new \Exception("Displace profile B tidak ditemukan untuk id_pengukuran: $id_pengukuran");
            }
            
            log_message('debug', "[ProfilBModel] Step 1 COMPLETE: Found " . count($displaceProfiles) . " displace profiles");
            
            // DEBUG: Log data displace_profile_b
            $first10 = array_slice($displaceProfiles, 0, 10, true);
            log_message('debug', "[ProfilBModel] First 10 displace_profile_b: " . json_encode($first10));
            
            // 2. Ambil semua depth dari displaceProfiles
            $depths = array_keys($displaceProfiles);
            log_message('debug', "[ProfilBModel] Step 2: Extracted " . count($depths) . " depths");
            
            // 3. Hitung semua nilai_profil_b (versi simple - langsung copy)
            log_message('debug', "[ProfilBModel] Step 3: Calculating nilai_profil_b (SIMPLE COPY)...");
            $nilaiProfilB = [];
            foreach ($displaceProfiles as $depth => $value) {
                $nilaiProfilB[$depth] = $value; // Simple copy
            }
            
            // DEBUG: Log hasil perhitungan
            $first5Results = array_slice($nilaiProfilB, 0, 5, true);
            log_message('debug', "[ProfilBModel] First 5 nilai_profil_b (after calculation): " . json_encode($first5Results));
            
            // 4. Persiapkan data untuk insert
            log_message('debug', "[ProfilBModel] Step 4: Preparing data for insert...");
            $profilBData = [];
            $dataCount = 0;
            
            foreach ($nilaiProfilB as $depth => $nilai) {
                $profilBData[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'depth' => $depth,
                    'reading_date' => $reading_date,
                    'nilai_profil_b' => $nilai,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $dataCount++;
                
                // Log first 3 records
                if ($dataCount <= 3) {
                    log_message('debug', "[ProfilBModel] Prepared record $dataCount: depth=$depth, nilai_profil_b=$nilai");
                }
            }
            
            log_message('debug', "[ProfilBModel] Step 4 COMPLETE: Prepared $dataCount records");
            
            // 5. Hapus data lama jika ada
            log_message('debug', "[ProfilBModel] Step 5: Deleting old data if exists...");
            $deleted = $this->deleteByPengukuran($id_pengukuran);
            log_message('debug', "[ProfilBModel] Step 5 COMPLETE: Deleted old data");
            
            // 6. Insert data baru
            log_message('debug', "[ProfilBModel] Step 6: Inserting new data...");
            $result = $this->insertBatchProfilB($profilBData);
            
            if ($result === false) {
                log_message('error', "[ProfilBModel] ERROR: insertBatchProfilB returned FALSE!");
                
                // Try to get error info
                $error = $this->db->error();
                log_message('error', "[ProfilBModel] Database error: " . json_encode($error));
                
                throw new \Exception("Gagal insert data profil B: " . json_encode($error));
            }
            
            log_message('debug', "[ProfilBModel] Step 6 COMPLETE: Inserted $result records");
            log_message('debug', "[ProfilBModel] ===========================================");
            log_message('debug', "[ProfilBModel] SUCCESS: Generated $result profil B records");
            log_message('debug', "[ProfilBModel] ===========================================");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilBModel] generateProfilBWithFormulaCorrect ERROR: ' . $e->getMessage());
            log_message('error', '[ProfilBModel] Error trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * ALTERNATIVE: Simple copy displace_profile_b ke nilai_profil_b
     */
    public function generateProfilBSimple($id_pengukuran, $reading_date)
    {
        try {
            log_message('debug', "[ProfilBModel] START generateProfilBSimple for id_pengukuran: $id_pengukuran");
            
            // 1. Ambil displace_profile_b dari initial_reading
            $displaceProfiles = $this->getDisplaceProfilesB($id_pengukuran);
            
            if (empty($displaceProfiles)) {
                throw new \Exception("Displace profile B tidak ditemukan");
            }
            
            // 2. Persiapkan data (langsung copy displace_profile_b ke nilai_profil_b)
            $profilBData = [];
            foreach ($displaceProfiles as $depth => $nilai) {
                $profilBData[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'depth' => $depth,
                    'reading_date' => $reading_date,
                    'nilai_profil_b' => $nilai,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            
            // 3. Hapus data lama jika ada
            $this->deleteByPengukuran($id_pengukuran);
            
            // 4. Insert data baru
            $result = $this->insertBatchProfilB($profilBData);
            
            log_message('debug', "[ProfilBModel] SUCCESS: Generated $result profil B records (SIMPLE VERSION)");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilBModel] generateProfilBSimple ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert batch profil B dengan LOGGING detail
     */
    public function insertBatchProfilB(array $data)
    {
        try {
            log_message('debug', "[ProfilBModel] insertBatchProfilB START: " . count($data) . " records");
            
            // Start transaction
            $this->db->transStart();
            
            // Check if table exists
            $tableExists = $this->db->tableExists($this->table);
            log_message('debug', "[ProfilBModel] Table '{$this->table}' exists: " . ($tableExists ? 'YES' : 'NO'));
            
            if (!$tableExists) {
                throw new \Exception("Table '{$this->table}' does not exist!");
            }
            
            // Check table structure
            $fields = $this->db->getFieldData($this->table);
            $fieldNames = array_column($fields, 'name');
            log_message('debug', "[ProfilBModel] Table fields: " . implode(', ', $fieldNames));
            
            // Validate data
            if (empty($data)) {
                throw new \Exception("Data array is empty!");
            }
            
            // Check first record
            $firstRecord = $data[0];
            log_message('debug', "[ProfilBModel] First record sample: " . json_encode($firstRecord));
            
            // Insert batch
            $result = $this->db->table($this->table)->insertBatch($data);
            
            if ($result === false) {
                $error = $this->db->error();
                log_message('error', "[ProfilBModel] insertBatch failed: " . json_encode($error));
                throw new \Exception("Insert batch failed: " . json_encode($error));
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                $error = $this->db->error();
                log_message('error', "[ProfilBModel] Transaction failed: " . json_encode($error));
                throw new \Exception("Transaction failed: " . json_encode($error));
            }
            
            log_message('debug', "[ProfilBModel] insertBatchProfilB SUCCESS: Inserted $result records");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilBModel] insertBatchProfilB ERROR: ' . $e->getMessage());
            log_message('error', '[ProfilBModel] Error trace: ' . $e->getTraceAsString());
            
            // Rollback transaction if still active
            if ($this->db->transStatus()) {
                $this->db->transRollback();
            }
            
            return false;
        }
    }

    /**
     * Delete profil B by id_pengukuran
     */
    public function deleteByPengukuran($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilBModel] deleteByPengukuran for id_pengukuran: $id_pengukuran");
            
            $result = $this->where('id_pengukuran', $id_pengukuran)->delete();
            
            if ($result === false) {
                $error = $this->db->error();
                log_message('error', "[ProfilBModel] deleteByPengukuran failed: " . json_encode($error));
            } else {
                log_message('debug', "[ProfilBModel] deleteByPengukuran deleted $result records");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilBModel] deleteByPengukuran ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test perhitungan rumus untuk debug
     */
    public function testFormulaCalculationCorrect($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilBModel] testFormulaCalculationCorrect for id_pengukuran: $id_pengukuran");
            
            $displaceProfiles = $this->getDisplaceProfilesB($id_pengukuran);
            
            if (empty($displaceProfiles)) {
                return ['error' => 'No displace profiles found'];
            }
            
            $depths = array_keys($displaceProfiles);
            $results = [];
            
            // Test untuk beberapa depth pertama
            $testDepths = array_slice($depths, 0, 10);
            
            foreach ($testDepths as $index => $depth) {
                // Rumus simple copy
                $nilaiBaru = $displaceProfiles[$depth] ?? 0;
                
                $results[] = [
                    'depth' => $depth,
                    'depth_index' => $index,
                    'displace_profile_b' => $displaceProfiles[$depth] ?? 0,
                    'nilai_baru' => $nilaiBaru,
                    'formula' => 'Simple copy (displace_profile_b -> nilai_profil_b)'
                ];
            }
            
            return [
                'total_displace_profiles' => count($displaceProfiles),
                'total_depths' => count($depths),
                'sample_data' => $results,
                'first_5_displace' => array_slice($displaceProfiles, 0, 5, true)
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'testFormulaCalculationCorrect ERROR: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Cek apakah ada data di initial_reading
     */
    public function checkInitialReadingData($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilBModel] checkInitialReadingData for id_pengukuran: $id_pengukuran");
            
            $query = $this->db->table('initial_reading')
                            ->select('COUNT(*) as total, MIN(depth) as min_depth, MAX(depth) as max_depth')
                            ->where('id_pengukuran', $id_pengukuran)
                            ->get();
            
            if (!$query) {
                log_message('error', "[ProfilBModel] checkInitialReadingData query failed");
                return null;
            }
            
            $result = $query->getRowArray();
            log_message('debug', "[ProfilBModel] checkInitialReadingData result: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'checkInitialReadingData ERROR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Debug: Check table structure
     */
    public function checkTableStructure()
    {
        try {
            log_message('debug', "[ProfilBModel] Checking table structure for '{$this->table}'");
            
            $tables = $this->db->listTables();
            $tableExists = in_array($this->table, $tables);
            
            $result = [
                'table_name' => $this->table,
                'table_exists' => $tableExists,
                'all_tables' => $tables
            ];
            
            if ($tableExists) {
                $fields = $this->db->getFieldData($this->table);
                $result['fields'] = $fields;
                
                // Count records
                $countQuery = $this->db->table($this->table)->countAllResults();
                $result['record_count'] = $countQuery;
            }
            
            log_message('debug', "[ProfilBModel] Table structure: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilBModel] checkTableStructure ERROR: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }


}