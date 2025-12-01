<?php

namespace App\Models\Inclino;

use CodeIgniter\Model;

class ProfilAModel extends Model
{
    protected $table            = 'profil_a';
    protected $primaryKey       = 'id_profil_a';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pengukuran',
        'depth',
        'reading_date',
        'nilai_profil_a',
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
     * Get profil A by id_pengukuran
     */
    public function getByPengukuran($id_pengukuran)
    {
        try {
            return $this->where('id_pengukuran', $id_pengukuran)
                        ->orderBy('depth', 'ASC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'ProfilAModel getByPengukuran ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get displace_profile_a dari tabel initial_reading dengan LOGGING
     */
    public function getDisplaceProfilesA($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilAModel] getDisplaceProfilesA START for id_pengukuran: $id_pengukuran");
            
            $query = $this->db->table('initial_reading')
                            ->select('depth, displace_profile_a')
                            ->where('id_pengukuran', $id_pengukuran)
                            ->orderBy('depth', 'ASC')
                            ->get();
            
            if (!$query) {
                log_message('error', "[ProfilAModel] Query failed for id_pengukuran: $id_pengukuran");
                return [];
            }
            
            $results = [];
            $count = 0;
            
            foreach ($query->getResultArray() as $row) {
                $results[$row['depth']] = (float)$row['displace_profile_a'];
                $count++;
                
                // Log first 5 records
                if ($count <= 5) {
                    log_message('debug', "[ProfilAModel] Row $count: depth={$row['depth']}, displace_profile_a={$row['displace_profile_a']}");
                }
            }
            
            log_message('debug', "[ProfilAModel] Total displace_profile_a records found: $count");
            
            if (empty($results)) {
                log_message('warning', "[ProfilAModel] No displace_profile_a found for id_pengukuran: $id_pengukuran");
            }
            
            return $results;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilAModel] getDisplaceProfilesA ERROR: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate dan simpan profil A dengan rumus Excel - VERSI DIPERBAIKI
     */
    public function generateProfilAWithFormulaCorrect($id_pengukuran, $reading_date)
    {
        try {
            log_message('debug', "[ProfilAModel] ===========================================");
            log_message('debug', "[ProfilAModel] START generateProfilAWithFormulaCorrect");
            log_message('debug', "[ProfilAModel] id_pengukuran: $id_pengukuran");
            log_message('debug', "[ProfilAModel] reading_date: $reading_date");
            log_message('debug', "[ProfilAModel] ===========================================");
            
            // 1. Ambil displace_profile_a dari initial_reading
            log_message('debug', "[ProfilAModel] Step 1: Getting displace_profile_a data...");
            $displaceProfiles = $this->getDisplaceProfilesA($id_pengukuran);
            
            if (empty($displaceProfiles)) {
                log_message('error', "[ProfilAModel] ERROR: displaceProfiles is EMPTY!");
                throw new \Exception("Displace profile A tidak ditemukan untuk id_pengukuran: $id_pengukuran");
            }
            
            log_message('debug', "[ProfilAModel] Step 1 COMPLETE: Found " . count($displaceProfiles) . " displace profiles");
            
            // DEBUG: Log data displace_profile_a
            $first10 = array_slice($displaceProfiles, 0, 10, true);
            log_message('debug', "[ProfilAModel] First 10 displace_profile_a: " . json_encode($first10));
            
            // 2. Ambil semua depth dari displaceProfiles
            $depths = array_keys($displaceProfiles);
            log_message('debug', "[ProfilAModel] Step 2: Extracted " . count($depths) . " depths");
            
            // 3. Hitung semua nilai_profil_a (versi simple - langsung copy)
            log_message('debug', "[ProfilAModel] Step 3: Calculating nilai_profil_a (SIMPLE COPY)...");
            $nilaiProfilA = [];
            foreach ($displaceProfiles as $depth => $value) {
                $nilaiProfilA[$depth] = $value; // Simple copy
            }
            
            // DEBUG: Log hasil perhitungan
            $first5Results = array_slice($nilaiProfilA, 0, 5, true);
            log_message('debug', "[ProfilAModel] First 5 nilai_profil_a (after calculation): " . json_encode($first5Results));
            
            // 4. Persiapkan data untuk insert
            log_message('debug', "[ProfilAModel] Step 4: Preparing data for insert...");
            $profilAData = [];
            $dataCount = 0;
            
            foreach ($nilaiProfilA as $depth => $nilai) {
                $profilAData[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'depth' => $depth,
                    'reading_date' => $reading_date,
                    'nilai_profil_a' => $nilai,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $dataCount++;
                
                // Log first 3 records
                if ($dataCount <= 3) {
                    log_message('debug', "[ProfilAModel] Prepared record $dataCount: depth=$depth, nilai_profil_a=$nilai");
                }
            }
            
            log_message('debug', "[ProfilAModel] Step 4 COMPLETE: Prepared $dataCount records");
            
            // 5. Hapus data lama jika ada
            log_message('debug', "[ProfilAModel] Step 5: Deleting old data if exists...");
            $deleted = $this->deleteByPengukuran($id_pengukuran);
            log_message('debug', "[ProfilAModel] Step 5 COMPLETE: Deleted old data");
            
            // 6. Insert data baru
            log_message('debug', "[ProfilAModel] Step 6: Inserting new data...");
            $result = $this->insertBatchProfilA($profilAData);
            
            if ($result === false) {
                log_message('error', "[ProfilAModel] ERROR: insertBatchProfilA returned FALSE!");
                
                // Try to get error info
                $error = $this->db->error();
                log_message('error', "[ProfilAModel] Database error: " . json_encode($error));
                
                throw new \Exception("Gagal insert data profil A: " . json_encode($error));
            }
            
            log_message('debug', "[ProfilAModel] Step 6 COMPLETE: Inserted $result records");
            log_message('debug', "[ProfilAModel] ===========================================");
            log_message('debug', "[ProfilAModel] SUCCESS: Generated $result profil A records");
            log_message('debug', "[ProfilAModel] ===========================================");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilAModel] generateProfilAWithFormulaCorrect ERROR: ' . $e->getMessage());
            log_message('error', '[ProfilAModel] Error trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * ALTERNATIVE: Simple copy displace_profile_a ke nilai_profil_a
     */
    public function generateProfilASimple($id_pengukuran, $reading_date)
    {
        try {
            log_message('debug', "[ProfilAModel] START generateProfilASimple for id_pengukuran: $id_pengukuran");
            
            // 1. Ambil displace_profile_a dari initial_reading
            $displaceProfiles = $this->getDisplaceProfilesA($id_pengukuran);
            
            if (empty($displaceProfiles)) {
                throw new \Exception("Displace profile A tidak ditemukan");
            }
            
            // 2. Persiapkan data (langsung copy displace_profile_a ke nilai_profil_a)
            $profilAData = [];
            foreach ($displaceProfiles as $depth => $nilai) {
                $profilAData[] = [
                    'id_pengukuran' => $id_pengukuran,
                    'depth' => $depth,
                    'reading_date' => $reading_date,
                    'nilai_profil_a' => $nilai,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            
            // 3. Hapus data lama jika ada
            $this->deleteByPengukuran($id_pengukuran);
            
            // 4. Insert data baru
            $result = $this->insertBatchProfilA($profilAData);
            
            log_message('debug', "[ProfilAModel] SUCCESS: Generated $result profil A records (SIMPLE VERSION)");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilAModel] generateProfilASimple ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert batch profil A dengan LOGGING detail
     */
    public function insertBatchProfilA(array $data)
    {
        try {
            log_message('debug', "[ProfilAModel] insertBatchProfilA START: " . count($data) . " records");
            
            // Start transaction
            $this->db->transStart();
            
            // Check if table exists
            $tableExists = $this->db->tableExists($this->table);
            log_message('debug', "[ProfilAModel] Table '{$this->table}' exists: " . ($tableExists ? 'YES' : 'NO'));
            
            if (!$tableExists) {
                throw new \Exception("Table '{$this->table}' does not exist!");
            }
            
            // Check table structure
            $fields = $this->db->getFieldData($this->table);
            $fieldNames = array_column($fields, 'name');
            log_message('debug', "[ProfilAModel] Table fields: " . implode(', ', $fieldNames));
            
            // Validate data
            if (empty($data)) {
                throw new \Exception("Data array is empty!");
            }
            
            // Check first record
            $firstRecord = $data[0];
            log_message('debug', "[ProfilAModel] First record sample: " . json_encode($firstRecord));
            
            // Insert batch
            $result = $this->db->table($this->table)->insertBatch($data);
            
            if ($result === false) {
                $error = $this->db->error();
                log_message('error', "[ProfilAModel] insertBatch failed: " . json_encode($error));
                throw new \Exception("Insert batch failed: " . json_encode($error));
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                $error = $this->db->error();
                log_message('error', "[ProfilAModel] Transaction failed: " . json_encode($error));
                throw new \Exception("Transaction failed: " . json_encode($error));
            }
            
            log_message('debug', "[ProfilAModel] insertBatchProfilA SUCCESS: Inserted $result records");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilAModel] insertBatchProfilA ERROR: ' . $e->getMessage());
            log_message('error', '[ProfilAModel] Error trace: ' . $e->getTraceAsString());
            
            // Rollback transaction if still active
            if ($this->db->transStatus()) {
                $this->db->transRollback();
            }
            
            return false;
        }
    }

    /**
     * Delete profil A by id_pengukuran
     */
    public function deleteByPengukuran($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilAModel] deleteByPengukuran for id_pengukuran: $id_pengukuran");
            
            $result = $this->where('id_pengukuran', $id_pengukuran)->delete();
            
            if ($result === false) {
                $error = $this->db->error();
                log_message('error', "[ProfilAModel] deleteByPengukuran failed: " . json_encode($error));
            } else {
                log_message('debug', "[ProfilAModel] deleteByPengukuran deleted $result records");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilAModel] deleteByPengukuran ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test perhitungan rumus untuk debug
     */
    public function testFormulaCalculationCorrect($id_pengukuran)
    {
        try {
            log_message('debug', "[ProfilAModel] testFormulaCalculationCorrect for id_pengukuran: $id_pengukuran");
            
            $displaceProfiles = $this->getDisplaceProfilesA($id_pengukuran);
            
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
                    'displace_profile_a' => $displaceProfiles[$depth] ?? 0,
                    'nilai_baru' => $nilaiBaru,
                    'formula' => 'Simple copy (displace_profile_a -> nilai_profil_a)'
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
            log_message('debug', "[ProfilAModel] checkInitialReadingData for id_pengukuran: $id_pengukuran");
            
            $query = $this->db->table('initial_reading')
                            ->select('COUNT(*) as total, MIN(depth) as min_depth, MAX(depth) as max_depth')
                            ->where('id_pengukuran', $id_pengukuran)
                            ->get();
            
            if (!$query) {
                log_message('error', "[ProfilAModel] checkInitialReadingData query failed");
                return null;
            }
            
            $result = $query->getRowArray();
            log_message('debug', "[ProfilAModel] checkInitialReadingData result: " . json_encode($result));
            
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
            log_message('debug', "[ProfilAModel] Checking table structure for '{$this->table}'");
            
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
            
            log_message('debug', "[ProfilAModel] Table structure: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[ProfilAModel] checkTableStructure ERROR: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }


}