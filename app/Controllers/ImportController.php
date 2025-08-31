<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class ImportController extends Controller
{
    use ResponseTrait;

    public function importSQL()
    {
        log_message('debug', '[IMPORT SQL] Request received: ' . $this->request->getMethod());

        if (!$this->request->is('post')) {
            return $this->respond(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $sqlData = $this->request->getJSON(true);
        if (!isset($sqlData['sql']) || empty($sqlData['sql'])) {
            return $this->respond(['success' => false, 'message' => 'No SQL data provided'], 400);
        }

        try {
            $db        = \Config\Database::connect();
            $executed  = 0;
            $imported  = 0;
            $skipped   = 0;
            $errors    = [];

            // daftar tabel yang perlu auto-fix
            $tablesToFix = [
                't_data_pengukuran',
                't_thomson_weir',
                't_sr',
                't_bocoran_baru'
            ];

            foreach ($sqlData['sql'] as $statement) {
                $statement = trim($statement);
                if (empty($statement)) continue;

                // ✅ buang ; di akhir biar regex bisa jalan
                $statement = rtrim($statement, ";");

                // ✅ skip query bawaan SQLite
                if (
                    stripos($statement, 'android_metadata') !== false ||
                    stripos($statement, 'sqlite_sequence') !== false ||
                    stripos($statement, 'PRAGMA') !== false
                ) {
                    $skipped++;
                    log_message('warning', '[IMPORT SQL] Skipped: ' . substr($statement, 0, 120));
                    continue;
                }

                // ✅ cek apakah insert ke tabel yg harus difix
                foreach ($tablesToFix as $table) {
                    if (preg_match('/INSERT\s+INTO\s+' . $table . '\s+VALUES\s*\((.+)\)/is', $statement, $matches)) {
                        $values = $matches[1];
                        $parts  = array_map('trim', explode(',', $values));

                        // hitung jumlah kolom di MySQL
                        $fields      = $db->getFieldNames($table);
                        $fieldCount  = count($fields);

                        if (count($parts) > $fieldCount) {
                            $parts = array_slice($parts, 0, $fieldCount);
                            $statement = "INSERT INTO $table VALUES(" . implode(', ', $parts) . ")";
                            log_message('debug', "[IMPORT SQL] Fixed INSERT mismatch for $table → trimmed to $fieldCount values");
                        }
                        break;
                    }
                }

                try {
                    $db->query($statement);
                    $executed++;
                    $affected = $db->affectedRows();
                    if ($affected > 0) {
                        $imported += $affected;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'statement' => substr($statement, 0, 150) . '...',
                        'error'     => $e->getMessage()
                    ];
                    log_message('error', "[IMPORT SQL] Error on statement: " . substr($statement, 0, 120) . " | " . $e->getMessage());
                }
            }

            return $this->respond([
                'success'   => true,
                'message'   => "Import selesai. $executed statement dieksekusi, $imported baris terpengaruh, $skipped di-skip",
                'executed'  => $executed,
                'imported'  => $imported,
                'skipped'   => $skipped,
                'errors'    => $errors,
                'has_error' => count($errors) > 0
            ]);

        } catch (\Exception $e) {
            log_message('critical', '[IMPORT SQL] Fatal error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Error importing data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
