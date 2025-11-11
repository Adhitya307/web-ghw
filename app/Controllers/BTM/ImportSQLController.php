<?php

namespace App\Controllers\BTM;

use App\Controllers\BaseController;
use App\Models\Btm\PengukuranBtmModel;
use App\Models\Btm\BacaanBt1Model;
use App\Models\Btm\BacaanBt2Model;
use App\Models\Btm\BacaanBt3Model;
use App\Models\Btm\BacaanBt4Model;
use App\Models\Btm\BacaanBt6Model;
use App\Models\Btm\BacaanBt7Model;
use App\Models\Btm\BacaanBt8Model;
use App\Models\Btm\PerhitunganBt1Model;
use App\Models\Btm\PerhitunganBt2Model;
use App\Models\Btm\PerhitunganBt3Model;
use App\Models\Btm\PerhitunganBt4Model;
use App\Models\Btm\PerhitunganBt6Model;
use App\Models\Btm\PerhitunganBt7Model;
use App\Models\Btm\PerhitunganBt8Model;
use App\Models\Btm\ScatterBt1Model;
use App\Models\Btm\ScatterBt2Model;
use App\Models\Btm\ScatterBt3Model;
use App\Models\Btm\ScatterBt4Model;
use App\Models\Btm\ScatterBt6Model;
use App\Models\Btm\ScatterBt7Model;
use App\Models\Btm\ScatterBt8Model;

class ImportSQLController extends BaseController
{
    protected $pengukuranModel;
    protected $bacaanModels;
    protected $perhitunganModels;
    protected $scatterModels;

    public function __construct()
    {
        $this->pengukuranModel = new PengukuranBtmModel();
        
        // Inisialisasi semua model bacaan (TANPA BT-5)
        $this->bacaanModels = [
            'bt1' => new BacaanBt1Model(),
            'bt2' => new BacaanBt2Model(),
            'bt3' => new BacaanBt3Model(),
            'bt4' => new BacaanBt4Model(),
            'bt6' => new BacaanBt6Model(),
            'bt7' => new BacaanBt7Model(),
            'bt8' => new BacaanBt8Model()
        ];
        
        // Inisialisasi semua model perhitungan (TANPA BT-5)
        $this->perhitunganModels = [
            'bt1' => new PerhitunganBt1Model(),
            'bt2' => new PerhitunganBt2Model(),
            'bt3' => new PerhitunganBt3Model(),
            'bt4' => new PerhitunganBt4Model(),
            'bt6' => new PerhitunganBt6Model(),
            'bt7' => new PerhitunganBt7Model(),
            'bt8' => new PerhitunganBt8Model()
        ];
        
        // Inisialisasi semua model scatter (TANPA BT-5)
        $this->scatterModels = [
            'bt1' => new ScatterBt1Model(),
            'bt2' => new ScatterBt2Model(),
            'bt3' => new ScatterBt3Model(),
            'bt4' => new ScatterBt4Model(),
            'bt6' => new ScatterBt6Model(),
            'bt7' => new ScatterBt7Model(),
            'bt8' => new ScatterBt8Model()
        ];
        
        helper(['text', 'number']);
    }

    /**
     * Process SQL file import for BTM
     */
    public function processImport()
    {
        // Check if it's AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        // Get uploaded file
        $sqlFile = $this->request->getFile('sql_file');

        if (!$sqlFile || !$sqlFile->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid atau tidak ditemukan'
            ]);
        }

        // DEBUG: Log informasi file
        log_message('debug', 'Import SQL - File name: ' . $sqlFile->getName());
        log_message('debug', 'Import SQL - Client name: ' . $sqlFile->getClientName());
        log_message('debug', 'Import SQL - Extension: ' . $sqlFile->getExtension());
        log_message('debug', 'Import SQL - Size: ' . $sqlFile->getSize());

        // VALIDASI EKSTENSI YANG LEBIH BAIK - DIPERBAIKI
        $originalName = $sqlFile->getName();
        $clientName = $sqlFile->getClientName();
        $serverExtension = strtolower($sqlFile->getExtension());
        $clientExtension = strtolower(pathinfo($clientName, PATHINFO_EXTENSION));
        
        // Validasi multiple approach
        if ($serverExtension !== 'sql' && $clientExtension !== 'sql') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File harus berformat .sql. Ekstensi terdeteksi: .' . $clientExtension . ' (dari: ' . $clientName . ')'
            ]);
        }

        // Validate file size (max 50MB)
        if ($sqlFile->getSize() > 50 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ukuran file maksimal 50MB. File saat ini: ' . round($sqlFile->getSize() / (1024 * 1024), 2) . 'MB'
            ]);
        }

        // Validasi file tidak kosong
        if ($sqlFile->getSize() === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File kosong'
            ]);
        }

        try {
            // Read SQL file content
            $sqlContent = file_get_contents($sqlFile->getTempName());
            
            if (empty($sqlContent)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File SQL kosong atau tidak dapat dibaca'
                ]);
            }

            // Log success file reading
            log_message('debug', 'Import SQL - File berhasil dibaca, size: ' . strlen($sqlContent) . ' bytes');

            // Process SQL content
            $result = $this->processSQLContent($sqlContent);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Import SQL BTM berhasil',
                'stats' => $result['stats'],
                'error_display' => $result['error_display']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Import SQL BTM Error: ' . $e->getMessage());
            log_message('error', 'Import SQL BTM Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process SQL content and import data
     */
    private function processSQLContent($sqlContent)
    {
        $stats = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'affected_rows' => 0,
            'tables' => [],
            'skipped_bt5' => 0
        ];

        $errorMessages = [];
        $db = \Config\Database::connect('btm');

        // Split SQL content into individual queries
        $queries = $this->splitSQLQueries($sqlContent);
        $stats['total'] = count($queries);

        log_message('debug', 'Import SQL - Total queries found: ' . $stats['total']);

        foreach ($queries as $index => $query) {
            if (empty(trim($query))) {
                continue;
            }

            try {
                // Skip DROP and CREATE TABLE statements for safety
                if (preg_match('/^\s*(DROP|CREATE|ALTER)\s+TABLE/i', $query)) {
                    log_message('debug', "Import SQL - Skip DROP/CREATE query: " . substr($query, 0, 100));
                    continue;
                }

                // Extract table name for statistics
                $tableName = $this->extractTableName($query);
                
                // Skip BT-5 tables if they exist in the SQL file
                if ($tableName && (strpos($tableName, 'bt_5') !== false || strpos($tableName, 'bt5') !== false)) {
                    $stats['skipped_bt5']++;
                    log_message('debug', "Import SQL - Skip BT-5 table: " . $tableName);
                    continue;
                }

                // Process INSERT statements
                if (preg_match('/^\s*INSERT\s+INTO/i', $query)) {
                    $result = $db->query($query);
                    
                    if ($result) {
                        $stats['success']++;
                        $affected = $db->affectedRows();
                        $stats['affected_rows'] += $affected;
                        
                        // Update table statistics
                        if ($tableName) {
                            if (!isset($stats['tables'][$tableName])) {
                                $stats['tables'][$tableName] = 0;
                            }
                            $stats['tables'][$tableName] += $affected;
                        }
                        
                        log_message('debug', "Import SQL - Query {$index} success, affected: {$affected}");
                    } else {
                        $stats['failed']++;
                        $errorMsg = $db->error();
                        $errorMessages[] = "Query {$index} gagal: " . $errorMsg;
                        log_message('error', "Import SQL - Query {$index} failed: " . $errorMsg);
                    }
                } else {
                    // Execute other queries (UPDATE, DELETE, etc.)
                    $result = $db->query($query);
                    if ($result) {
                        $stats['success']++;
                        $affected = $db->affectedRows();
                        $stats['affected_rows'] += $affected;
                        log_message('debug', "Import SQL - Non-INSERT query {$index} success, affected: {$affected}");
                    } else {
                        $stats['failed']++;
                        $errorMsg = $db->error();
                        $errorMessages[] = "Query {$index} gagal: " . $errorMsg;
                        log_message('error', "Import SQL - Non-INSERT query {$index} failed: " . $errorMsg);
                    }
                }

            } catch (\Exception $e) {
                $stats['failed']++;
                $errorMsg = "Error pada query {$index}: " . $e->getMessage();
                $errorMessages[] = $errorMsg;
                
                // Log detailed error for debugging
                log_message('error', "SQL Import Error - Query {$index}: " . $e->getMessage());
                log_message('debug', "Failed Query: " . substr($query, 0, 500) . "...");
            }
        }

        // Prepare error display
        $errorDisplay = '';
        if (!empty($errorMessages)) {
            $errorDisplay = implode("\n", array_slice($errorMessages, 0, 10)); // Show first 10 errors
            if (count($errorMessages) > 10) {
                $errorDisplay .= "\n... dan " . (count($errorMessages) - 10) . " error lainnya";
            }
        }

        // Log final statistics
        log_message('debug', 'Import SQL - Final stats: ' . json_encode($stats));

        return [
            'stats' => $stats,
            'error_display' => $errorDisplay
        ];
    }

    /**
     * Extract table name from SQL query
     */
    private function extractTableName($query)
    {
        if (preg_match('/INSERT\s+INTO\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?/i', $query, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Split SQL content into individual queries
     */
    private function splitSQLQueries($sqlContent)
    {
        // Remove comments
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);
        
        // Split by semicolon, but ignore semicolons in quotes
        $queries = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($sqlContent); $i++) {
            $char = $sqlContent[$i];
            
            if (($char === "'" || $char === '"') && ($i === 0 || $sqlContent[$i-1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                }
            }
            
            if ($char === ';' && !$inString) {
                $queries[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        // Add the last query if any
        if (!empty(trim($current))) {
            $queries[] = trim($current);
        }
        
        return array_filter($queries);
    }

    /**
     * Calculate all BTM data after import
     */
    public function calculateAllBTM()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $result = $this->calculateAllBTMData();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Perhitungan semua data BTM berhasil',
                'stats' => $result
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Calculate All BTM Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate all BTM data
     */
    private function calculateAllBTMData()
    {
        $stats = [
            'total_pengukuran' => 0,
            'calculated' => 0,
            'bt_types' => [],
            'errors' => []
        ];

        // Get all pengukuran data
        $allPengukuran = $this->pengukuranModel->getAllPengukuran();
        $stats['total_pengukuran'] = count($allPengukuran);

        log_message('debug', 'Calculate All BTM - Total pengukuran: ' . $stats['total_pengukuran']);

        foreach ($allPengukuran as $index => $pengukuran) {
            try {
                $id_pengukuran = $pengukuran['id_pengukuran'];
                
                // Calculate for each BT type (TANPA BT-5)
                foreach ($this->bacaanModels as $btType => $bacaanModel) {
                    $this->calculateForBTType($id_pengukuran, $btType, $pengukuran['tanggal']);
                    
                    // Track calculated BT types
                    if (!isset($stats['bt_types'][$btType])) {
                        $stats['bt_types'][$btType] = 0;
                    }
                    $stats['bt_types'][$btType]++;
                }
                
                $stats['calculated']++;
                
            } catch (\Exception $e) {
                $errorMsg = "Error pada pengukuran {$id_pengukuran}: " . $e->getMessage();
                $stats['errors'][] = $errorMsg;
                log_message('error', $errorMsg);
            }
        }

        return $stats;
    }

    /**
     * Calculate for specific BT type - DIPERBAIKI
     */
    private function calculateForBTType($id_pengukuran, $btType, $tanggal)
    {
        $bacaanModel = $this->bacaanModels[$btType];
        $perhitunganModel = $this->perhitunganModels[$btType];
        $scatterModel = $this->scatterModels[$btType];

        // Get current bacaan
        $bacaan_sekarang = $bacaanModel->getByPengukuran($id_pengukuran);
        if (!$bacaan_sekarang) {
            log_message('debug', "Calculate {$btType} - No bacaan data for id_pengukuran: {$id_pengukuran}");
            return;
        }

        // Get previous bacaan
        $pengukuran_sebelumnya = $this->pengukuranModel->getPengukuranSebelumnya($tanggal);
        $bacaan_sebelumnya = null;
        
        if ($pengukuran_sebelumnya) {
            $bacaan_sebelumnya = $bacaanModel->getByPengukuran($pengukuran_sebelumnya['id_pengukuran']);
        }

        // Calculate perhitungan
        $methodName = 'hitungRumus' . ucfirst($btType);
        if (method_exists($perhitunganModel, $methodName)) {
            $perhitunganData = $perhitunganModel->$methodName(
                $id_pengukuran, 
                $bacaanModel, 
                $bacaan_sebelumnya
            );

            // Save perhitungan
            $existingPerhitungan = $perhitunganModel->getByPengukuran($id_pengukuran);
            if ($existingPerhitungan) {
                $perhitunganModel->update($existingPerhitungan['id_perhitungan'], $perhitunganData);
            } else {
                $perhitunganModel->insert($perhitunganData);
            }

            // Calculate scatter - DIPERBAIKI dengan error handling
            if (isset($perhitunganData['A_sec']) && isset($perhitunganData['B_sec'])) {
                try {
                    // Cek apakah scatter model memiliki method calculateScatterData
                    if (method_exists($scatterModel, 'calculateScatterData')) {
                        $scatterModel->calculateScatterData(
                            $id_pengukuran,
                            $perhitunganData['A_sec'],
                            $perhitunganData['B_sec'],
                            $bacaan_sekarang['US_Arah'],
                            $bacaan_sekarang['TB_Arah']
                        );
                    } else {
                        log_message('debug', "Scatter model {$btType} doesn't have calculateScatterData method");
                    }
                } catch (\Exception $e) {
                    log_message('error', "Error calculating scatter for {$btType}, id_pengukuran: {$id_pengukuran}: " . $e->getMessage());
                }
            }
        } else {
            log_message('error', "Method {$methodName} not found in " . get_class($perhitunganModel));
        }
    }

    /**
     * Get import status
     */
    public function getImportStatus()
    {
        $db = \Config\Database::connect('btm');
        
        // Get counts from all tables (TANPA BT-5)
        $tables = [
            'pengukuran' => 't_pengukuran_btm',
            'bacaan_bt1' => 't_bacaan_bt_1',
            'bacaan_bt2' => 't_bacaan_bt_2',
            'bacaan_bt3' => 't_bacaan_bt_3',
            'bacaan_bt4' => 't_bacaan_bt_4',
            'bacaan_bt6' => 't_bacaan_bt_6',
            'bacaan_bt7' => 't_bacaan_bt_7',
            'bacaan_bt8' => 't_bacaan_bt_8'
        ];

        $counts = [];
        foreach ($tables as $key => $table) {
            try {
                $counts[$key] = $db->table($table)->countAll();
            } catch (\Exception $e) {
                $counts[$key] = 0;
                log_message('error', "Error counting table {$table}: " . $e->getMessage());
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'last_import' => date('Y-m-d H:i:s'),
                'counts' => $counts,
                'status' => 'ready'
            ]
        ]);
    }
}