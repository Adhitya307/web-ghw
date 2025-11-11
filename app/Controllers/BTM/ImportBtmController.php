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
use CodeIgniter\API\ResponseTrait;

class ImportBtmController extends BaseController
{
    use ResponseTrait;

    protected $pengukuranModel;
    protected $bacaanModels;
    protected $perhitunganModels;
    protected $scatterModels;
    protected $btmDB;

    public function __construct()
    {
        // Gunakan koneksi database BTM
        $this->btmDB = \Config\Database::connect('btm');
        
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
        
        helper(['text', 'number', 'form']);
    }

    public function importSQL()
    {
        log_message('debug', '[IMPORT BTM SQL] Request received: ' . $this->request->getMethod());

        if (!$this->request->is('post')) {
            return $this->respond(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $sqlStatements = [];
        $sourceType = 'unknown';

        // Method 1: Check for uploaded file
        if ($file = $this->request->getFile('sql_file')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $sourceType = 'file_upload';
                $sqlContent = file_get_contents($file->getTempName());
                $sqlStatements = $this->parseSQLString($sqlContent);
                log_message('debug', '[IMPORT BTM SQL] Processing uploaded file: ' . $file->getName());
            }
        }
        // Method 2: Check for raw SQL text in POST data
        elseif ($sqlText = $this->request->getPost('sql_text')) {
            $sourceType = 'post_text';
            $sqlStatements = $this->parseSQLString($sqlText);
            log_message('debug', '[IMPORT BTM SQL] Processing SQL text from POST');
        }
        // Method 3: Check for JSON data
        else {
            try {
                $jsonData = $this->request->getJSON(true);
                $sourceType = 'json';
                
                if (isset($jsonData['sql']) && is_array($jsonData['sql'])) {
                    $sqlStatements = $jsonData['sql'];
                } elseif (isset($jsonData['sql_string'])) {
                    $sqlStatements = $this->parseSQLString($jsonData['sql_string']);
                }
                log_message('debug', '[IMPORT BTM SQL] Processing JSON data');
            } catch (\Exception $e) {
                log_message('error', '[IMPORT BTM SQL] JSON parse error: ' . $e->getMessage());
            }
        }

        if (empty($sqlStatements)) {
            return $this->respond([
                'success' => false, 
                'message' => 'No SQL statements found',
                'debug' => [
                    'source_type' => $sourceType,
                    'content_type' => $this->request->getHeaderLine('Content-Type'),
                    'has_files' => !empty($_FILES),
                    'post_keys' => array_keys($this->request->getPost())
                ]
            ], 400);
        }

        log_message('debug', "[IMPORT BTM SQL] Processing $sourceType - Found " . count($sqlStatements) . " SQL statements");

        try {
            // Gunakan koneksi BTM, bukan default
            $db = $this->btmDB;
            $executed = 0;
            $imported = 0;
            $skipped = 0;
            $errors = [];

            // Daftar tabel yang didukung
            $supportedTables = [
                't_pengukuran_btm',
                't_bacaan_bt_1', 't_bacaan_bt_2', 't_bacaan_bt_3', 't_bacaan_bt_4',
                't_bacaan_bt_6', 't_bacaan_bt_7', 't_bacaan_bt_8',
                'p_bt_1', 'p_bt_2', 'p_bt_3', 'p_bt_4', 'p_bt_6', 'p_bt_7', 'p_bt_8',
                'p_scatter_bt_1', 'p_scatter_bt_2', 'p_scatter_bt_3', 'p_scatter_bt_4',
                'p_scatter_bt_6', 'p_scatter_bt_7', 'p_scatter_bt_8'
            ];

            log_message('debug', '[IMPORT BTM SQL] Supported tables: ' . implode(', ', $supportedTables));

            // Non-aktifkan FOREIGN KEY CHECKS sementara
            $db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Group statements berdasarkan jenis tabel
            $groupedStatements = [
                'pengukuran' => [],
                'bacaan' => [],
                'perhitungan' => [],
                'scatter' => []
            ];

            foreach ($sqlStatements as $statement) {
                $originalStatement = trim($statement);
                
                if (empty($originalStatement)) {
                    $skipped++;
                    continue;
                }

                // Skip komentar dan system statements
                if (strpos($originalStatement, '--') === 0 ||
                    stripos($originalStatement, 'android_metadata') !== false ||
                    stripos($originalStatement, 'sqlite_sequence') !== false ||
                    stripos($originalStatement, 'PRAGMA') !== false ||
                    stripos($originalStatement, 'BEGIN TRANSACTION') !== false ||
                    stripos($originalStatement, 'COMMIT') !== false ||
                    stripos($originalStatement, '-- =') !== false || 
                    stripos($originalStatement, '-- Data untuk') !== false ||
                    stripos($originalStatement, '-- Database:') !== false ||
                    stripos($originalStatement, '-- BTM Database Export:') !== false ||
                    stripos($originalStatement, '-- Total rows:') !== false) {
                    $skipped++;
                    continue;
                }

                // Hapus ; di akhir jika ada
                $originalStatement = rtrim($originalStatement, ";");

                // Cari tabel yang sesuai
                $tableFound = false;
                foreach ($supportedTables as $tableName) {
                    if (stripos($originalStatement, "INSERT INTO $tableName") === 0 || 
                        stripos($originalStatement, "INSERT INTO `$tableName`") === 0) {
                        $tableFound = true;
                        
                        // Group berdasarkan jenis tabel
                        if ($tableName === 't_pengukuran_btm') {
                            $groupedStatements['pengukuran'][] = $originalStatement;
                        } elseif (strpos($tableName, 't_bacaan_bt_') === 0) {
                            $groupedStatements['bacaan'][] = $originalStatement;
                        } elseif (strpos($tableName, 'p_bt_') === 0) {
                            $groupedStatements['perhitungan'][] = $originalStatement;
                        } elseif (strpos($tableName, 'p_scatter_bt_') === 0) {
                            $groupedStatements['scatter'][] = $originalStatement;
                        }
                        break;
                    }
                }

                if (!$tableFound) {
                    $skipped++;
                    log_message('debug', '[IMPORT BTM SQL] Unsupported table in: ' . substr($originalStatement, 0, 100));
                }
            }

            log_message('debug', '[IMPORT BTM SQL] Grouped statements - Pengukuran: ' . count($groupedStatements['pengukuran']) . 
                                ', Bacaan: ' . count($groupedStatements['bacaan']) . 
                                ', Perhitungan: ' . count($groupedStatements['perhitungan']) . 
                                ', Scatter: ' . count($groupedStatements['scatter']));

            // Execute dalam urutan yang benar
            // 1. Pengukuran dulu
            foreach ($groupedStatements['pengukuran'] as $statement) {
                $this->executeSimpleStatement($db, $statement, $executed, $imported, $errors);
            }

            // 2. Bacaan
            foreach ($groupedStatements['bacaan'] as $statement) {
                $this->executeSimpleStatement($db, $statement, $executed, $imported, $errors);
            }

            // 3. Perhitungan
            foreach ($groupedStatements['perhitungan'] as $statement) {
                $this->executeSimpleStatement($db, $statement, $executed, $imported, $errors);
            }

            // 4. Scatter
            foreach ($groupedStatements['scatter'] as $statement) {
                $this->executeSimpleStatement($db, $statement, $executed, $imported, $errors);
            }

            // Aktifkan kembali FOREIGN KEY CHECKS
            $db->query('SET FOREIGN_KEY_CHECKS = 1');

            $response = [
                'success' => true,
                'message' => "Import BTM selesai. $executed statement dieksekusi, $imported baris terpengaruh, $skipped di-skip",
                'executed' => $executed,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'has_error' => count($errors) > 0,
                'source_type' => $sourceType,
                'summary' => [
                    'pengukuran' => count($groupedStatements['pengukuran']),
                    'bacaan' => count($groupedStatements['bacaan']),
                    'perhitungan' => count($groupedStatements['perhitungan']),
                    'scatter' => count($groupedStatements['scatter'])
                ]
            ];

            return $this->respond($response);

        } catch (\Exception $e) {
            // Pastikan FOREIGN KEY CHECKS diaktifkan kembali meski ada error
            try {
                $this->btmDB->query('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Exception $e2) {
                // Ignore error saat mengaktifkan foreign key checks
            }

            log_message('critical', '[IMPORT BTM SQL] Fatal error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Error importing BTM data',
                'error' => $e->getMessage(),
                'source_type' => $sourceType
            ], 500);
        }
    }

    /**
     * Simple method untuk execute statement tanpa parsing kompleks
     */
    protected function executeSimpleStatement($db, $statement, &$executed, &$imported, &$errors)
    {
        try {
            // Eksekusi statement langsung tanpa modifikasi
            $result = $db->query($statement);
            $executed++;
            $affected = $db->affectedRows();
            if ($affected > 0) {
                $imported += $affected;
            }
            
            // Extract table name untuk logging
            $tableName = 'unknown';
            if (preg_match('/INSERT\s+INTO\s+`?(\w+)`?/i', $statement, $matches)) {
                $tableName = $matches[1];
            }
            
            log_message('debug', "[IMPORT BTM SQL] Success: $tableName - Rows affected: $affected");
            
        } catch (\Exception $e) {
            // Extract table name untuk error reporting
            $tableName = 'unknown';
            if (preg_match('/INSERT\s+INTO\s+`?(\w+)`?/i', $statement, $matches)) {
                $tableName = $matches[1];
            }
            
            $errors[] = [
                'statement' => substr($statement, 0, 150) . '...',
                'error' => $e->getMessage(),
                'table' => $tableName
            ];
            log_message('error', "[IMPORT BTM SQL] Error: $tableName - " . $e->getMessage());
        }
    }

    /**
     * Parse SQL string into individual statements - Versi Sederhana
     */
    protected function parseSQLString($sqlString)
    {
        if (empty($sqlString)) {
            return [];
        }

        // Remove BOM if exists
        $sqlString = preg_replace('/^\xEF\xBB\xBF/', '', $sqlString);
        
        // Normalize line endings
        $sqlString = str_replace(["\r\n", "\r"], "\n", $sqlString);
        
        // Split by semicolon, handle basic cases
        $statements = [];
        $current = '';
        
        $lines = explode("\n", $sqlString);
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Skip comment lines
            if (strpos($trimmedLine, '--') === 0) {
                continue;
            }
            
            $current .= ' ' . $trimmedLine;
            
            // Jika line berakhir dengan semicolon, itu adalah statement lengkap
            if (substr($trimmedLine, -1) === ';') {
                $statements[] = trim($current);
                $current = '';
            }
        }
        
        // Add any remaining content
        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }
        
        // Filter hanya INSERT statements
        $insertStatements = [];
        foreach ($statements as $stmt) {
            if (stripos($stmt, 'INSERT INTO') === 0) {
                $insertStatements[] = $stmt;
            }
        }
        
        return $insertStatements;
    }

    /**
     * Method khusus untuk handle file upload SQL
     */
    public function importSQLFile()
    {
        if (!$this->request->is('post')) {
            return $this->respond(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        if (!$file = $this->request->getFile('sql_file')) {
            return $this->respond(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        if (!$file->isValid()) {
            return $this->respond(['success' => false, 'message' => $file->getErrorString()], 400);
        }

        // Validate file type
        $allowedTypes = ['sql', 'txt'];
        $fileExtension = $file->getClientExtension();
        if (!in_array(strtolower($fileExtension), $allowedTypes)) {
            return $this->respond(['success' => false, 'message' => 'Only SQL and TXT files are allowed'], 400);
        }

        try {
            $sqlContent = file_get_contents($file->getTempName());
            $sqlStatements = $this->parseSQLString($sqlContent);

            if (empty($sqlStatements)) {
                return $this->respond(['success' => false, 'message' => 'No valid SQL statements found in file'], 400);
            }

            // Process the import menggunakan method utama
            return $this->importSQL();

        } catch (\Exception $e) {
            log_message('error', '[IMPORT BTM FILE] Error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Error processing SQL file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data menggunakan model (alternative method)
     */
    public function importData()
    {
        if (!$this->request->is('post')) {
            return $this->respond(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $importData = $this->request->getJSON(true);
        
        if (!isset($importData['data']) || empty($importData['data'])) {
            return $this->respond(['success' => false, 'message' => 'No import data provided'], 400);
        }

        $results = [
            'success' => true,
            'imported' => 0,
            'errors' => [],
            'details' => []
        ];

        try {
            // Import data pengukuran
            if (isset($importData['data']['pengukuran'])) {
                foreach ($importData['data']['pengukuran'] as $pengukuran) {
                    try {
                        $this->pengukuranModel->insert($pengukuran);
                        $results['imported']++;
                        $results['details']['pengukuran'][] = "ID {$pengukuran['id_pengukuran']} imported";
                    } catch (\Exception $e) {
                        $results['errors'][] = "Pengukuran ID {$pengukuran['id_pengukuran']}: " . $e->getMessage();
                    }
                }
            }

            // Import data bacaan
            foreach ($this->bacaanModels as $key => $model) {
                $tableKey = 'bacaan_' . $key;
                if (isset($importData['data'][$tableKey])) {
                    foreach ($importData['data'][$tableKey] as $bacaan) {
                        try {
                            $model->insert($bacaan);
                            $results['imported']++;
                            $results['details'][$tableKey][] = "ID {$bacaan['id_bacaan']} imported";
                        } catch (\Exception $e) {
                            $results['errors'][] = "Bacaan {$key} ID {$bacaan['id_bacaan']}: " . $e->getMessage();
                        }
                    }
                }
            }

            // Import data perhitungan
            foreach ($this->perhitunganModels as $key => $model) {
                $tableKey = 'perhitungan_' . $key;
                if (isset($importData['data'][$tableKey])) {
                    foreach ($importData['data'][$tableKey] as $perhitungan) {
                        try {
                            $model->insert($perhitungan);
                            $results['imported']++;
                            $results['details'][$tableKey][] = "ID {$perhitungan['id_perhitungan']} imported";
                        } catch (\Exception $e) {
                            $results['errors'][] = "Perhitungan {$key} ID {$perhitungan['id_perhitungan']}: " . $e->getMessage();
                        }
                    }
                }
            }

            // Import data scatter
            foreach ($this->scatterModels as $key => $model) {
                $tableKey = 'scatter_' . $key;
                if (isset($importData['data'][$tableKey])) {
                    foreach ($importData['data'][$tableKey] as $scatter) {
                        try {
                            $model->insert($scatter);
                            $results['imported']++;
                            $results['details'][$tableKey][] = "ID {$scatter['id_scatter']} imported";
                        } catch (\Exception $e) {
                            $results['errors'][] = "Scatter {$key} ID {$scatter['id_scatter']}: " . $e->getMessage();
                        }
                    }
                }
            }

            if (count($results['errors']) > 0) {
                $results['success'] = false;
                $results['message'] = 'Import completed with errors';
            } else {
                $results['message'] = 'Import completed successfully';
            }

            return $this->respond($results);

        } catch (\Exception $e) {
            log_message('error', '[IMPORT BTM DATA] Error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Error during data import',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}