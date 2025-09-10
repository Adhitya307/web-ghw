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

            // Daftar semua tabel yang didukung
            $supportedTables = [
                't_data_pengukuran',
                't_thomson_weir',
                't_sr',
                't_bocoran_baru',
                'ambang',
                'p_batasmaksimal',
                'p_intigalery',
                'p_spillway',
                'p_sr',
                'p_tebingkanan',
                'p_thomson_weir',
                'p_totalbocoran',
                'thomson',
                't_ambang_batas',
                'p_bocoran_baru',
                'analisa_look_burt'
            ];

            foreach ($sqlData['sql'] as $statement) {
                $statement = trim($statement);
                if (empty($statement)) continue;

                // buang ; di akhir
                $statement = rtrim($statement, ";");

                // skip query SQLite khusus
                if (
                    stripos($statement, 'android_metadata') !== false ||
                    stripos($statement, 'sqlite_sequence') !== false ||
                    stripos($statement, 'PRAGMA') !== false
                ) {
                    $skipped++;
                    log_message('warning', '[IMPORT SQL] Skipped: ' . substr($statement, 0, 120));
                    continue;
                }

                // cek apakah insert ke tabel yang didukung
                foreach ($supportedTables as $table) {
                    if (preg_match('/INSERT\s+INTO\s+`?' . $table . '`?\s*(\([^\)]*\))?\s*VALUES\s*\((.+)\)/is', $statement, $matches)) {
                        $columns = isset($matches[1]) ? trim($matches[1], '() ') : '';
                        $values  = $matches[2];

                        $parts = array_map('trim', explode(',', $values));

                        // ambil nama field asli dari database
                        $fields = $db->getFieldNames($table);

                        if (!empty($columns)) {
                            $colArray = array_map('trim', explode(',', str_replace('`','',$columns)));

                            // skip kolom yang tidak ada di database
                            $filteredParts = [];
                            $filteredCols  = [];
                            foreach ($fields as $f) {
                                $idx = array_search($f, $colArray);
                                if ($idx !== false) {
                                    $filteredParts[] = $parts[$idx];
                                    $filteredCols[]  = $f;
                                }
                            }

                            $statement = "INSERT INTO $table (`" . implode('`,`', $filteredCols) . "`) VALUES(" . implode(',', $filteredParts) . ")";
                        } else {
                            // jika tidak ada kolom di query, sesuaikan jumlah values
                            if (count($parts) > count($fields)) {
                                $parts = array_slice($parts, 0, count($fields));
                            } elseif (count($parts) < count($fields)) {
                                while (count($parts) < count($fields)) $parts[] = 'NULL';
                            }
                            $statement = "INSERT INTO $table (`" . implode('`,`', $fields) . "`) VALUES(" . implode(',', $parts) . ")";
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
