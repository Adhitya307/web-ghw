<?php

namespace App\Controllers\Inclino;

use App\Controllers\BaseController;
use App\Models\Inclino\PembacaanInclinoModel;
use App\Models\Inclino\Ireadingmodel;
use App\Models\Inclino\ProfilAModel;
use App\Models\Inclino\ProfilBModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class ImportController extends BaseController
{
    use ResponseTrait;

    protected $pembacaanModel;
    protected $ireadingModel;
    protected $profilAModel;
    protected $profilBModel;

    public function __construct()
    {
        $this->pembacaanModel = new PembacaanInclinoModel();
        $this->ireadingModel = new Ireadingmodel();
        $this->profilAModel = new ProfilAModel();
        $this->profilBModel = new ProfilBModel();
        helper(['form', 'url', 'text']);
    }

    /**
     * Tampilkan form upload CSV
     */
    public function index()
    {
        $data = [
            'title' => 'Import Data InclinoMeter - PT Indonesia Power',
            'boreholes' => $this->pembacaanModel->getBoreholeList()
        ];

        return view('inclino/import', $data);
    }

    /**
     * Proses upload CSV file
     */
    public function uploadCSV()
    {
        log_message('debug', '=== START UPLOAD CSV PROCESS ===');
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'Upload CSV: Request bukan AJAX');
            return $this->fail('Invalid request method', 400);
        }

        log_message('debug', 'Upload CSV: Request adalah AJAX');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'csv_file' => [
                'rules' => 'uploaded[csv_file]|max_size[csv_file,5120]|ext_in[csv_file,csv,txt]',
                'errors' => [
                    'uploaded' => 'Pilih file CSV terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'Hanya file CSV yang diperbolehkan'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            log_message('error', 'Upload CSV: Validation failed - ' . print_r($errors, true));
            return $this->fail($errors, 400);
        }

        log_message('debug', 'Upload CSV: File validation passed');

        $file = $this->request->getFile('csv_file');
        
        if (!$file->isValid()) {
            $error = $file->getErrorString();
            log_message('error', 'Upload CSV: File upload gagal - ' . $error);
            return $this->fail('File upload gagal: ' . $error, 400);
        }

        log_message('debug', 'Upload CSV: File is valid - ' . $file->getName() . ' (' . $file->getSize() . ' bytes)');

        try {
            log_message('debug', 'Upload CSV: Starting processCSVFile');
            $result = $this->processCSVFile($file);
            
            log_message('debug', 'Upload CSV: Starting automatic data generation process');
            
            // 1. GENERATE INITIAL READING DULU
            log_message('debug', 'Upload CSV: Step 1 - Generating initial readings');
            $initialResult = $this->generateInitialReadings($result['id_pengukuran']);
            
            if ($initialResult) {
                log_message('debug', "Upload CSV: Successfully generated $initialResult initial readings");
                $result['initial_generated'] = $initialResult;
                
                // **PERUBAHAN: Gunakan method yang diperbaiki untuk Profil A**
                log_message('debug', 'Upload CSV: Step 2 - Generating profil A with CORRECT formula');
                $profilAResult = $this->profilAModel->generateProfilAWithFormulaCorrect($result['id_pengukuran'], $result['reading_date']);
                
                if ($profilAResult === false) {
                    // Jika rumus kompleks gagal, coba versi simple
                    log_message('debug', 'Upload CSV: Trying SIMPLE version for profil A');
                    $profilAResult = $this->profilAModel->generateProfilASimple($result['id_pengukuran'], $result['reading_date']);
                }
                
                if ($profilAResult) {
                    log_message('debug', "Upload CSV: Successfully generated $profilAResult profil A records");
                    $result['profil_a_generated'] = $profilAResult;
                } else {
                    log_message('warning', 'Upload CSV: Failed to generate profil A');
                    $result['profil_a_generated'] = 0;
                }
                
                // **PERUBAHAN: Gunakan method yang diperbaiki untuk Profil B**
                log_message('debug', 'Upload CSV: Step 3 - Generating profil B with CORRECT formula');
                $profilBResult = $this->profilBModel->generateProfilBWithFormulaCorrect($result['id_pengukuran'], $result['reading_date']);
                
                if ($profilBResult === false) {
                    // Jika rumus kompleks gagal, coba versi simple
                    log_message('debug', 'Upload CSV: Trying SIMPLE version for profil B');
                    $profilBResult = $this->profilBModel->generateProfilBSimple($result['id_pengukuran'], $result['reading_date']);
                }
                
                if ($profilBResult) {
                    log_message('debug', "Upload CSV: Successfully generated $profilBResult profil B records");
                    $result['profil_b_generated'] = $profilBResult;
                } else {
                    log_message('warning', 'Upload CSV: Failed to generate profil B');
                    $result['profil_b_generated'] = 0;
                }
            } else {
                log_message('warning', 'Upload CSV: Failed to generate initial readings');
                $result['initial_generated'] = 0;
                $result['profil_a_generated'] = 0;
                $result['profil_b_generated'] = 0;
            }
            
            log_message('debug', 'Upload CSV: Process completed successfully - ' . $result['imported'] . ' records imported');
            
            return $this->respond([
                'status' => 'success',
                'message' => "✅ Berhasil mengimport {$result['imported']} data dari {$result['borehole']}" . 
                            ($initialResult ? " dan generate $initialResult initial readings" : "") . 
                            (isset($result['profil_a_generated']) && $result['profil_a_generated'] > 0 ? ", {$result['profil_a_generated']} profil A" : "") . 
                            (isset($result['profil_b_generated']) && $result['profil_b_generated'] > 0 ? ", {$result['profil_b_generated']} profil B" : ""),
                'data' => $result
            ]);

        } catch (\Exception $e) {
            log_message('error', 'CSV Import Error: ' . $e->getMessage());
            log_message('error', 'CSV Import Trace: ' . $e->getTraceAsString());
            return $this->fail('Import gagal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate initial readings otomatis setelah data diimport
     */
    private function generateInitialReadings($id_pengukuran)
    {
        try {
            log_message('debug', "Generating initial readings for id_pengukuran: $id_pengukuran");
            
            $result = $this->ireadingModel->generateInitialReadings($id_pengukuran);
            
            if ($result === false) {
                log_message('error', "Failed to generate initial readings for id_pengukuran: $id_pengukuran");
                return false;
            }
            
            log_message('debug', "Successfully generated $result initial readings for id_pengukuran: $id_pengukuran");
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'generateInitialReadings ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process CSV file data
     */
    private function processCSVFile($file)
    {
        log_message('debug', 'processCSVFile: Starting with file - ' . $file->getName());
        
        $filePath = $file->getTempName();
        log_message('debug', 'processCSVFile: Temporary file path - ' . $filePath);
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        log_message('debug', 'processCSVFile: Total lines read - ' . count($lines));
        
        if (empty($lines)) {
            throw new \Exception('File CSV kosong');
        }

        $lastId = $this->pembacaanModel->getLastMeasurementId();
        $id_pengukuran = $lastId + 1;
        log_message('debug', "Generated id_pengukuran: $id_pengukuran untuk file " . $file->getName());

        for ($i = 0; $i < min(5, count($lines)); $i++) {
            log_message('debug', "processCSVFile: Line $i - " . substr($lines[$i], 0, 100));
        }

        $metadata = [
            'reading_date' => '',
            'borehole_name' => '',
            'probe_serial' => '',
            'reel_serial' => '',
            'operator' => '',
            'site' => '',
            'depth_units' => 'meters',
            'reading_units' => 'meters'
        ];

        $dataStarted = false;
        $importData = [];
        $importedCount = 0;
        $skippedCount = 0;
        $currentLine = 0;

        foreach ($lines as $lineNumber => $line) {
            $currentLine = $lineNumber + 1;
            
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $data = str_getcsv($line);
            log_message('debug', "processCSVFile: Line $currentLine parsed into " . count($data) . " columns");
            
            if (empty(array_filter($data, function($value) { 
                return $value !== null && $value !== ''; 
            }))) {
                log_message('debug', "processCSVFile: Line $currentLine skipped (empty)");
                continue;
            }

            $this->extractMetadata($data, $metadata);

            if (isset($data[0]) && trim($data[0]) === 'Depth' && 
                isset($data[1]) && strpos(trim($data[1]), 'Face A+') !== false) {
                $dataStarted = true;
                log_message('debug', "processCSVFile: Data started at line $currentLine");
                continue;
            }

            if ($dataStarted && isset($data[0]) && is_numeric(trim($data[0]))) {
                
                if (count($data) < 5) {
                    $skippedCount++;
                    log_message('debug', "processCSVFile: Data tidak lengkap pada baris $currentLine - hanya " . count($data) . " kolom: " . implode(',', $data));
                    continue;
                }

                $depth = floatval(trim($data[0]));
                $faceAPlus = !empty(trim($data[1])) ? floatval(trim($data[1])) : 0;
                $faceAMinus = !empty(trim($data[2])) ? floatval(trim($data[2])) : 0;
                $faceBPlus = !empty(trim($data[3])) ? floatval(trim($data[3])) : 0;
                $faceBMinus = !empty(trim($data[4])) ? floatval(trim($data[4])) : 0;

                log_message('debug', "processCSVFile: Parsed data - Depth: $depth, A+: $faceAPlus, A-: $faceAMinus, B+: $faceBPlus, B-: $faceBMinus");

                if (empty($metadata['reading_date']) || empty($metadata['borehole_name'])) {
                    log_message('error', "processCSVFile: Metadata tidak lengkap - Date: {$metadata['reading_date']}, Borehole: {$metadata['borehole_name']}");
                    throw new \Exception("Metadata tidak lengkap (tanggal atau nama borehole tidak ditemukan). Pastikan file CSV memiliki metadata yang lengkap.");
                }

                if (!$this->isValidDate($metadata['reading_date'])) {
                    log_message('error', "processCSVFile: Format tanggal tidak valid - {$metadata['reading_date']}");
                    throw new \Exception("Format tanggal tidak valid: {$metadata['reading_date']}");
                }

                if ($this->pembacaanModel->isDataExists($metadata['borehole_name'], $metadata['reading_date'], $depth)) {
                    $skippedCount++;
                    log_message('debug', "processCSVFile: Data duplikat skipped - Borehole: {$metadata['borehole_name']}, Date: {$metadata['reading_date']}, Depth: $depth");
                    continue;
                }

                $record = [
                    'id_pengukuran' => $id_pengukuran,
                    'depth' => $depth,
                    'face_a_plus' => $faceAPlus,
                    'face_a_minus' => $faceAMinus,
                    'face_b_plus' => $faceBPlus,
                    'face_b_minus' => $faceBMinus,
                    'reading_date' => $metadata['reading_date'],
                    'borehole_name' => $metadata['borehole_name'],
                    'file_name' => $file->getName(),
                    'probe_serial' => $metadata['probe_serial'] ?? null,
                    'reel_serial' => $metadata['reel_serial'] ?? null,
                    'operator' => $metadata['operator'] ?? null
                ];
                
                $importData[] = $record;
                $importedCount++;
                
                log_message('debug', "processCSVFile: Data prepared for import - " . json_encode($record));

                if (count($importData) >= 50) {
                    log_message('debug', "processCSVFile: Executing batch insert with " . count($importData) . " records");
                    
                    $insertResult = $this->insertBatchData($importData);
                    log_message('debug', "processCSVFile: Batch insert successful - $insertResult records inserted");
                    $importData = [];
                }
            }
        }

        if (!empty($importData)) {
            log_message('debug', "processCSVFile: Executing final batch insert with " . count($importData) . " records");
            
            $insertResult = $this->insertBatchData($importData);
            log_message('debug', "processCSVFile: Final batch insert successful - $insertResult records inserted");
        }

        if ($importedCount === 0) {
            if ($skippedCount > 0) {
                log_message('debug', "processCSVFile: No data imported - all $skippedCount records were duplicates");
                throw new \Exception("Semua data ($skippedCount records) sudah ada di database (duplikat)");
            } else {
                log_message('debug', "processCSVFile: No valid data found in CSV file");
                throw new \Exception('Tidak ada data yang valid ditemukan dalam file CSV. Pastikan format file sesuai template.');
            }
        }

        log_message('debug', "processCSVFile: Import completed - Imported: $importedCount, Skipped: $skippedCount");

        return [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
            'borehole' => $metadata['borehole_name'] ?? 'Unknown',
            'reading_date' => $metadata['reading_date'] ?? 'Unknown',
            'probe_serial' => $metadata['probe_serial'] ?? 'Unknown',
            'reel_serial' => $metadata['reel_serial'] ?? 'Unknown',
            'id_pengukuran' => $id_pengukuran,
            'total_lines' => count($lines),
            'file_name' => $file->getName()
        ];
    }

    /**
     * Insert batch data
     */
    private function insertBatchData($data)
    {
        try {
            log_message('debug', 'insertBatchData: Starting with ' . count($data) . ' records');
            
            $result = $this->pembacaanModel->insertBatchInclinometer($data);
            
            if ($result === false) {
                $errors = $this->pembacaanModel->errors();
                log_message('error', "insertBatchData failed: " . print_r($errors, true));
                throw new \Exception('Gagal menyimpan data ke database');
            }
            
            log_message('debug', 'insertBatchData: Successfully inserted ' . $result . ' records');
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'insertBatchData Error: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan data batch: ' . $e->getMessage());
        }
    }

    /**
     * Extract metadata from CSV
     */
    private function extractMetadata($data, &$metadata)
    {
        if (!isset($data[0]) || !isset($data[1])) {
            return;
        }

        $key = trim($data[0]);
        $value = trim($data[1]);

        log_message('debug', "extractMetadata: Processing key '$key' with value '$value'");

        switch ($key) {
            case 'Reading Date(m/d/y)':
                $time = isset($data[2]) ? trim($data[2]) : '00:00:00';
                $dateString = $value . ' ' . $time;
                
                log_message('debug', "extractMetadata: Date string - '$dateString'");
                
                $timestamp = strtotime($dateString);
                if ($timestamp !== false) {
                    $metadata['reading_date'] = date('Y-m-d', $timestamp);
                    log_message('debug', "extractMetadata: Date parsed successfully - '{$metadata['reading_date']}'");
                } else {
                    log_message('warning', "extractMetadata: Invalid date format: '$dateString'");
                }
                break;

            case 'Borehole':
                $metadata['borehole_name'] = $value;
                log_message('debug', "extractMetadata: Borehole set to '$value'");
                break;

            case 'Probe Serial#':
                $metadata['probe_serial'] = $value;
                log_message('debug', "extractMetadata: Probe serial set to '$value'");
                break;

            case 'Reel Serial#':
                $metadata['reel_serial'] = $value;
                log_message('debug', "extractMetadata: Reel serial set to '$value'");
                break;

            case 'Operator':
                $metadata['operator'] = $value;
                log_message('debug', "extractMetadata: Operator set to '$value'");
                break;

            case 'Site':
                $metadata['site'] = $value;
                log_message('debug', "extractMetadata: Site set to '$value'");
                break;

            case 'Depth Units':
                $metadata['depth_units'] = $value;
                log_message('debug', "extractMetadata: Depth units set to '$value'");
                break;

            case 'Reading Units':
                $metadata['reading_units'] = $value;
                log_message('debug', "extractMetadata: Reading units set to '$value'");
                break;
        }
    }

    /**
     * Validate date format
     */
    private function isValidDate($date)
    {
        if (empty($date)) {
            log_message('debug', "isValidDate: Date is empty");
            return false;
        }
        
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        $isValid = $d && $d->format('Y-m-d') === $date;
        
        log_message('debug', "isValidDate: '$date' is " . ($isValid ? 'valid' : 'invalid'));
        return $isValid;
    }

    /**
     * Get reading dates by borehole
     */
    public function getReadingDates($boreholeName = null)
    {
        if (!$boreholeName) {
            $boreholeName = $this->request->getGet('borehole');
        }

        if (empty($boreholeName)) {
            return $this->fail('Nama borehole harus diisi', 400);
        }

        try {
            $dates = $this->pembacaanModel->getReadingDates($boreholeName);
            
            return $this->respond([
                'status' => 'success',
                'data' => $dates,
                'borehole' => $boreholeName,
                'total' => count($dates)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getReadingDates Error: ' . $e->getMessage());
            return $this->fail('Gagal mengambil data tanggal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete data by borehole and date
     */
    public function deleteData()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 400);
        }

        $borehole = $this->request->getPost('borehole');
        $readingDate = $this->request->getPost('reading_date');

        if (empty($borehole) || empty($readingDate)) {
            return $this->fail('Borehole dan tanggal harus diisi', 400);
        }

        try {
            $deleted = $this->pembacaanModel->deleteByBoreholeAndDate($borehole, $readingDate);
            
            if ($deleted === false) {
                throw new \Exception('Gagal menghapus data dari database');
            }
            
            return $this->respond([
                'status' => 'success',
                'message' => "✅ Berhasil menghapus {$deleted} data dari {$borehole} tanggal {$readingDate}",
                'deleted_count' => $deleted
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Delete Data Error: ' . $e->getMessage());
            return $this->fail('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Download template CSV
     */
    public function downloadTemplate()
    {
        $template = "RST Digital Inclinometer Data,,,,
File Version,2.2,,,
File Type,Digital Inclinometer,,,
Site,SPI (DAM Left Bank SGL),,,
Borehole,SPI-1,,,
Probe Serial#,DP16410000,,,
Reel Serial#,DR25890000,,,
Reading Date(m/d/y)," . date('m/d/Y') . "," . date('H:i:s') . ",,
Depth,-80,-0.5,,
Interval,0.5,,,
Depth Units,meters,,,
Reading Units,meters,,,
Operator,Operator Name,,,
Comment:,,,,
Comment End:,,,,
Offset Correction,0,Incline Angle,0,

Depth,Face A+,Face A-,Face B+,Face B-
-0.5,0.00326897,-0.003402,0.00618664,-0.00613713
-1.0,0.00793087,-0.00795268,0.00528233,-0.00529054
-1.5,0.00961580,-0.00972739,0.00474084,-0.00472146
-2.0,0.00864190,-0.00854393,0.00466951,-0.00467556";

        return $this->response->download(
            'inclinometer_template_' . date('Ymd_His') . '.csv', 
            $template
        )->setContentType('text/csv');
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        try {
            $totalRecords = $this->pembacaanModel->getTotalRecords();
            $boreholes = $this->pembacaanModel->getBoreholeList();
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'total_records' => $totalRecords,
                    'total_boreholes' => count($boreholes),
                    'boreholes' => $boreholes
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getStatistics Error: ' . $e->getMessage());
            return $this->fail('Gagal mengambil statistik: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test connection and table
     */
    public function testConnection()
    {
        try {
            $db = \Config\Database::connect('db_inclino');
            $tables = $db->listTables();
            
            $tableExists = in_array('inclinometer_readings', $tables);
            $recordCount = $this->pembacaanModel->countAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'database_connected' => true,
                    'table_exists' => $tableExists,
                    'total_records' => $recordCount,
                    'tables' => $tables
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'testConnection Error: ' . $e->getMessage());
            return $this->fail('Database error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test koneksi database khusus
     */
    public function testKoneksi()
    {
        try {
            echo "<h3>Testing Koneksi Database db_inclino</h3>";
            
            $db = \Config\Database::connect('db_inclino');
            $db->initialize();
            
            if ($db->connect()) {
                echo "✅ <strong>Koneksi database db_inclino BERHASIL</strong><br>";
                
                $result = $db->query('SHOW TABLES LIKE "inclinometer_readings"');
                if ($result->getNumRows() > 0) {
                    echo "✅ Tabel 'inclinometer_readings' ADA<br>";
                } else {
                    echo "❌ Tabel 'inclinometer_readings' TIDAK ADA<br>";
                }
                
                $result2 = $db->query('SELECT COUNT(*) as total FROM information_schema.tables WHERE table_schema = "db_inclino"');
                $row = $result2->getRow();
                echo "📊 Total tabel di db_inclino: " . $row->total . "<br>";
                
            } else {
                echo "❌ <strong>Koneksi database GAGAL</strong>";
            }
            
        } catch (\Exception $e) {
            echo "❌ <strong>Error:</strong> " . $e->getMessage();
            echo "<br>⚠️ <strong>Detail:</strong> " . $e->getFile() . " pada baris " . $e->getLine();
        }
    }

    /**
     * Test database connection khusus untuk API
     */
    public function testDbConnection()
    {
        try {
            $db = \Config\Database::connect('db_inclino');
            
            $query = $db->query('SELECT COUNT(*) as count FROM inclinometer_readings');
            $count = $query->getRow()->count;
            
            $tables = $db->listTables();
            $tableExists = in_array('inclinometer_readings', $tables);
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'database_connected' => true,
                    'table_exists' => $tableExists,
                    'current_records' => $count,
                    'connection_group' => 'db_inclino'
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'testDbConnection Error: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * **TAMBAHAN: Test method untuk debugging Profil A & B**
     */
    public function testProfilCalculation($id_pengukuran = null)
    {
        try {
            if (!$id_pengukuran) {
                $id_pengukuran = $this->request->getGet('id_pengukuran') ?? 1;
            }
            
            log_message('debug', "Testing profil calculation for id_pengukuran: $id_pengukuran");
            
            // Test Profil A
            $testA = $this->profilAModel->testFormulaCalculationCorrect($id_pengukuran);
            
            // Test Profil B
            $testB = $this->profilBModel->testFormulaCalculationCorrect($id_pengukuran);
            
            // Check initial reading data
            $initialCheck = $this->profilAModel->checkInitialReadingData($id_pengukuran);
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'id_pengukuran' => $id_pengukuran,
                    'initial_reading_check' => $initialCheck,
                    'profil_a_test' => $testA,
                    'profil_b_test' => $testB
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'testProfilCalculation ERROR: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * **TAMBAHAN: Regenerate Profil A & B jika ada masalah**
     */
    public function regenerateProfil($id_pengukuran)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->fail('Invalid request method', 400);
            }
            
            log_message('debug', "Regenerating profil for id_pengukuran: $id_pengukuran");
            
            // Cek apakah data initial reading ada
            $initialCheck = $this->profilAModel->checkInitialReadingData($id_pengukuran);
            
            if (!$initialCheck || $initialCheck['total'] == 0) {
                return $this->fail('Data initial reading tidak ditemukan', 400);
            }
            
            // Ambil reading_date
            $inclinoData = $this->pembacaanModel->db->table('inclinometer_readings')
                                ->select('DISTINCT reading_date')
                                ->where('id_pengukuran', $id_pengukuran)
                                ->get()
                                ->getRow();
            
            if (!$inclinoData) {
                return $this->fail('Data pengukuran tidak ditemukan', 400);
            }
            
            $reading_date = $inclinoData->reading_date;
            
            // Regenerate Profil A
            $profilAResult = $this->profilAModel->generateProfilAWithFormulaCorrect($id_pengukuran, $reading_date);
            if ($profilAResult === false) {
                $profilAResult = $this->profilAModel->generateProfilASimple($id_pengukuran, $reading_date);
            }
            
            // Regenerate Profil B
            $profilBResult = $this->profilBModel->generateProfilBWithFormulaCorrect($id_pengukuran, $reading_date);
            if ($profilBResult === false) {
                $profilBResult = $this->profilBModel->generateProfilBSimple($id_pengukuran, $reading_date);
            }
            
            return $this->respond([
                'status' => 'success',
                'message' => "✅ Berhasil regenerate profil" . 
                            ($profilAResult ? " A ($profilAResult records)" : "") . 
                            ($profilBResult ? " B ($profilBResult records)" : ""),
                'data' => [
                    'id_pengukuran' => $id_pengukuran,
                    'profil_a_regenerated' => $profilAResult,
                    'profil_b_regenerated' => $profilBResult,
                    'initial_data_check' => $initialCheck
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'regenerateProfil ERROR: ' . $e->getMessage());
            return $this->fail('Regenerate failed: ' . $e->getMessage(), 500);
        }
    }
}