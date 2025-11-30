<?php

namespace App\Controllers\Inclino;

use App\Controllers\BaseController;
use App\Models\Inclino\PembacaanInclinoModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class ImportController extends BaseController
{
    use ResponseTrait;

    protected $pembacaanModel;

    public function __construct()
    {
        $this->pembacaanModel = new PembacaanInclinoModel();
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
        
        // Validasi request
        if (!$this->request->isAJAX()) {
            log_message('error', 'Upload CSV: Request bukan AJAX');
            return $this->fail('Invalid request method', 400);
        }

        log_message('debug', 'Upload CSV: Request adalah AJAX');

        // Validasi file
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
            // Process CSV file
            log_message('debug', 'Upload CSV: Starting processCSVFile');
            $result = $this->processCSVFile($file);
            
            log_message('debug', 'Upload CSV: Process completed successfully - ' . $result['imported'] . ' records imported');
            
            return $this->respond([
                'status' => 'success',
                'message' => "✅ Berhasil mengimport {$result['imported']} data dari {$result['borehole']}",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            log_message('error', 'CSV Import Error: ' . $e->getMessage());
            log_message('error', 'CSV Import Trace: ' . $e->getTraceAsString());
            return $this->fail('Import gagal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process CSV file data - DIPERBAIKI dengan debugging lengkap
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

        // Log first few lines untuk debugging
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
            
            // Handle different line endings and encoding
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Parse CSV line dengan handling khusus untuk format RST
            $data = str_getcsv($line);
            log_message('debug', "processCSVFile: Line $currentLine parsed into " . count($data) . " columns");
            
            // Skip lines yang hanya berisi koma
            if (empty(array_filter($data, function($value) { 
                return $value !== null && $value !== ''; 
            }))) {
                log_message('debug', "processCSVFile: Line $currentLine skipped (empty)");
                continue;
            }

            // Extract metadata - DIPERBAIKI untuk format spesifik
            $this->extractMetadata($data, $metadata);

            // Detect data start - header "Depth,Face A+,Face A-,Face B+,Face B-"
            if (isset($data[0]) && trim($data[0]) === 'Depth' && 
                isset($data[1]) && strpos(trim($data[1]), 'Face A+') !== false) {
                $dataStarted = true;
                log_message('debug', "processCSVFile: Data started at line $currentLine");
                continue;
            }

            // Process reading data - angka depth dengan tanda minus
            if ($dataStarted && isset($data[0]) && is_numeric(trim($data[0]))) {
                
                // Validasi data lengkap - minimal 5 kolom
                if (count($data) < 5) {
                    $skippedCount++;
                    log_message('debug', "processCSVFile: Data tidak lengkap pada baris $currentLine - hanya " . count($data) . " kolom: " . implode(',', $data));
                    continue;
                }

                // Parse data dengan handling empty values
                $depth = floatval(trim($data[0]));
                $faceAPlus = !empty(trim($data[1])) ? floatval(trim($data[1])) : 0;
                $faceAMinus = !empty(trim($data[2])) ? floatval(trim($data[2])) : 0;
                $faceBPlus = !empty(trim($data[3])) ? floatval(trim($data[3])) : 0;
                $faceBMinus = !empty(trim($data[4])) ? floatval(trim($data[4])) : 0;

                log_message('debug', "processCSVFile: Parsed data - Depth: $depth, A+: $faceAPlus, A-: $faceAMinus, B+: $faceBPlus, B-: $faceBMinus");

                // Validate required metadata
                if (empty($metadata['reading_date']) || empty($metadata['borehole_name'])) {
                    log_message('error', "processCSVFile: Metadata tidak lengkap - Date: {$metadata['reading_date']}, Borehole: {$metadata['borehole_name']}");
                    throw new \Exception("Metadata tidak lengkap (tanggal atau nama borehole tidak ditemukan). Pastikan file CSV memiliki metadata yang lengkap.");
                }

                // Validate date format
                if (!$this->isValidDate($metadata['reading_date'])) {
                    log_message('error', "processCSVFile: Format tanggal tidak valid - {$metadata['reading_date']}");
                    throw new \Exception("Format tanggal tidak valid: {$metadata['reading_date']}");
                }

                // Check for duplicate data
                if ($this->pembacaanModel->isDataExists($metadata['borehole_name'], $metadata['reading_date'], $depth)) {
                    $skippedCount++;
                    log_message('debug', "processCSVFile: Data duplikat skipped - Borehole: {$metadata['borehole_name']}, Date: {$metadata['reading_date']}, Depth: $depth");
                    continue;
                }

                // Prepare data untuk insert
                $record = [
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

                // Insert in batches to avoid memory issues
                if (count($importData) >= 50) {
                    log_message('debug', "processCSVFile: Executing batch insert with " . count($importData) . " records");
                    
                    $insertResult = $this->insertBatchData($importData);
                    log_message('debug', "processCSVFile: Batch insert successful - $insertResult records inserted");
                    $importData = [];
                }
            }
        }

        // Insert remaining data
        if (!empty($importData)) {
            log_message('debug', "processCSVFile: Executing final batch insert with " . count($importData) . " records");
            
            $insertResult = $this->insertBatchData($importData);
            log_message('debug', "processCSVFile: Final batch insert successful - $insertResult records inserted");
        }

        // Validasi hasil import
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
            'total_lines' => count($lines),
            'file_name' => $file->getName()
        ];
    }

 /**
 * Insert batch data dengan error handling yang lebih baik
 */
private function insertBatchData($data)
{
    try {
        log_message('debug', 'insertBatchData: Starting with ' . count($data) . ' records');
        
        // Gunakan model untuk insert batch
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
     * Extract metadata from CSV - DIPERBAIKI dengan debugging
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
                
                // Handle date format: 10/13/2025 11:44:17
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
                
                // Test query untuk cek tabel
                $result = $db->query('SHOW TABLES LIKE "inclinometer_readings"');
                if ($result->getNumRows() > 0) {
                    echo "✅ Tabel 'inclinometer_readings' ADA<br>";
                } else {
                    echo "❌ Tabel 'inclinometer_readings' TIDAK ADA<br>";
                }
                
                // Hitung total tabel
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
            
            // Test connection
            $query = $db->query('SELECT COUNT(*) as count FROM inclinometer_readings');
            $count = $query->getRow()->count;
            
            // Check table structure
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
}