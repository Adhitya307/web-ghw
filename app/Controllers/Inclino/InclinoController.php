<?php

namespace App\Controllers\Inclino;

use App\Controllers\BaseController;
use App\Models\Inclino\PembacaanInclinoModel;
use App\Models\Inclino\Ireadingmodel;
use App\Models\Inclino\ProfilAModel;
use App\Models\Inclino\ProfilBModel;

class InclinoController extends BaseController
{
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
    }
    
    public function index()
    {
        return $this->view();
    }
    
    public function view()
    {
        // Get filter data
        $years = $this->getAvailableYears();
        $boreholes = $this->pembacaanModel->getBoreholeList();
        
        $data = [
            'title' => 'InclinoMeter Monitoring - PT Indonesia Power',
            'years' => $years,
            'boreholes' => $boreholes
        ];
        
        return view('inclino/view', $data);
    }
    
    public function create()
    {
        return view('inclino/create');
    }
    
    public function edit($id)
    {
        $data['id'] = $id;
        return view('inclino/edit', $data);
    }
    
    /**
     * Get data by filter - AJAX
     */
    public function getDataByFilter()
    {
        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $day = $this->request->getGet('day');
        $borehole = $this->request->getGet('borehole');
        
        try {
            $data = $this->getFilteredData($year, $month, $day, $borehole);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getDataByFilter Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get months for selected year - AJAX
     */
    public function getMonthsByYear()
    {
        $year = $this->request->getGet('year');
        $borehole = $this->request->getGet('borehole');
        
        if (empty($year)) {
            return $this->response->setJSON([
                'status' => 'success',
                'months' => [],
                'message' => 'Pilih tahun terlebih dahulu'
            ]);
        }
        
        try {
            $months = $this->getAvailableMonths($year, $borehole);
            
            return $this->response->setJSON([
                'status' => 'success',
                'months' => $months
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getMonthsByYear Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil bulan: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get days for selected year and month - AJAX
     */
    public function getDaysByMonth()
    {
        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $borehole = $this->request->getGet('borehole');
        
        if (empty($year) || empty($month)) {
            return $this->response->setJSON([
                'status' => 'success',
                'days' => [],
                'message' => 'Pilih tahun dan bulan terlebih dahulu'
            ]);
        }
        
        try {
            $days = $this->getAvailableDays($year, $month, $borehole);
            
            return $this->response->setJSON([
                'status' => 'success',
                'days' => $days
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getDaysByMonth Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil hari: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get available years from database
     */
    private function getAvailableYears()
    {
        try {
            $query = $this->pembacaanModel->db->query("
                SELECT DISTINCT YEAR(reading_date) as year 
                FROM inclinometer_readings 
                WHERE reading_date IS NOT NULL 
                ORDER BY year DESC
            ");
            
            $years = [];
            foreach ($query->getResultArray() as $row) {
                $years[] = $row['year'];
            }
            
            return $years;
            
        } catch (\Exception $e) {
            log_message('error', 'getAvailableYears Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get available months for selected year
     */
    private function getAvailableMonths($year, $borehole = null)
    {
        try {
            $sql = "SELECT DISTINCT MONTH(reading_date) as month 
                    FROM inclinometer_readings 
                    WHERE YEAR(reading_date) = ?";
            
            $params = [$year];
            
            if (!empty($borehole)) {
                $sql .= " AND borehole_name = ?";
                $params[] = $borehole;
            }
            
            $sql .= " ORDER BY month DESC";
            
            $query = $this->pembacaanModel->db->query($sql, $params);
            
            $months = [];
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            foreach ($query->getResultArray() as $row) {
                $monthNum = (int)$row['month'];
                if (isset($monthNames[$monthNum])) {
                    $months[] = [
                        'value' => $monthNum,
                        'name' => $monthNames[$monthNum]
                    ];
                }
            }
            
            return $months;
            
        } catch (\Exception $e) {
            log_message('error', 'getAvailableMonths Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get available days for selected year and month
     */
    private function getAvailableDays($year, $month, $borehole = null)
    {
        try {
            $sql = "SELECT DISTINCT DAY(reading_date) as day 
                    FROM inclinometer_readings 
                    WHERE YEAR(reading_date) = ? 
                    AND MONTH(reading_date) = ?";
            
            $params = [$year, $month];
            
            if (!empty($borehole)) {
                $sql .= " AND borehole_name = ?";
                $params[] = $borehole;
            }
            
            $sql .= " ORDER BY day DESC";
            
            $query = $this->pembacaanModel->db->query($sql, $params);
            
            $days = [];
            foreach ($query->getResultArray() as $row) {
                $days[] = (int)$row['day'];
            }
            
            return $days;
            
        } catch (\Exception $e) {
            log_message('error', 'getAvailableDays Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get filtered data
     */
    /**
 * Get filtered data
 */
/**
 * Get filtered data
 */
private function getFilteredData($year, $month, $day, $borehole)
{
    try {
        $builder = $this->pembacaanModel->db->table('inclinometer_readings ir');
        
        // PERBAIKAN: Tulis query dengan format yang benar
        $builder->select("
            ir.id_pengukuran,
            ir.depth,
            ir.face_a_plus,
            ir.face_a_minus,
            ir.face_b_plus,
            ir.face_b_minus,
            ir.reading_date,
            ir.borehole_name,
            ir.probe_serial,
            ir.reel_serial,
            ir.operator,
            ia.face_a as mean_deviation_a,
            ia.face_b as mean_deviation_b,
            ia.mean_cum_deviation_a,
            ia.mean_cum_deviation_b,
            ia.basereading_a,
            ia.basereading_b,
            ia.displace_profile_a,
            ia.displace_profile_b,
            pa.nilai_profil_a,
            pb.nilai_profil_b
        ")
        ->join('initial_reading ia', 'ir.id_pengukuran = ia.id_pengukuran AND ir.depth = ia.depth', 'left')
        ->join('profil_a pa', 'ir.id_pengukuran = pa.id_pengukuran AND ir.depth = pa.depth', 'left')
        ->join('profil_b pb', 'ir.id_pengukuran = pb.id_pengukuran AND ir.depth = pb.depth', 'left');
        
        // Atau gunakan cara alternatif yang lebih aman:
        // $builder->select('ir.id_pengukuran, ir.depth, ir.face_a_plus, ir.face_a_minus, ir.face_b_plus, ir.face_b_minus, ir.reading_date, ir.borehole_name, ir.probe_serial, ir.reel_serial, ir.operator, ia.face_a as mean_deviation_a, ia.face_b as mean_deviation_b, ia.mean_cum_deviation_a, ia.mean_cum_deviation_b, ia.basereading_a, ia.basereading_b, ia.displace_profile_a, ia.displace_profile_b, pa.nilai_profil_a, pb.nilai_profil_b');
        
        // Apply filters
        if (!empty($year)) {
            $builder->where('YEAR(ir.reading_date)', $year);
        }
        
        if (!empty($month)) {
            $builder->where('MONTH(ir.reading_date)', $month);
        }
        
        if (!empty($day)) {
            $builder->where('DAY(ir.reading_date)', $day);
        }
        
        if (!empty($borehole)) {
            $builder->where('ir.borehole_name', $borehole);
        }
        
        $builder->orderBy('ir.depth', 'ASC');
        
        $rawData = $builder->get()->getResultArray();
        
        // Debug: Log query untuk melihat error
        log_message('debug', 'SQL Query: ' . $this->pembacaanModel->db->getLastQuery());
        
        if (empty($rawData)) {
            return [
                'header' => [],
                'data' => [],
                'metadata' => []
            ];
        }
        
        // Process data for table
        $processedData = $this->processTableData($rawData);
        
        return [
            'header' => $this->getTableHeader(),
            'data' => $processedData,
            'metadata' => $this->getMetadata($rawData)
        ];
        
    } catch (\Exception $e) {
        log_message('error', 'getFilteredData Error: ' . $e->getMessage());
        log_message('error', 'SQL Error: ' . $this->pembacaanModel->db->error());
        throw $e;
    }
}
    /**
     * Get table header structure
     */
    private function getTableHeader()
    {
        return [
            'row1' => [
                ['text' => 'No', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-info-column sticky'],
                ['text' => 'Depth (m)', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-info-column sticky-2'],
                ['text' => 'FACE A (A0°)', 'colspan' => 7, 'rowspan' => 1, 'class' => 'point-header'],
                ['text' => 'FACE B (A90°)', 'colspan' => 7, 'rowspan' => 1, 'class' => 'point-header'],
                ['text' => 'Profil A', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-reading'],
                ['text' => 'Profil B', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-reading'],
                ['text' => 'Aksi', 'colspan' => 1, 'rowspan' => 4, 'class' => 'bg-action action-cell']
            ],
            'row2' => [
                ['text' => 'Pembacaan', 'colspan' => 2, 'rowspan' => 1, 'class' => 'initial-header'],
                ['text' => 'Rata-rata (Face A)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Base Reading', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-initial'],
                ['text' => 'Selisih (A-AR)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Mean Cum Dev', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Displace Profil', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-result'],
                ['text' => 'Pembacaan', 'colspan' => 2, 'rowspan' => 1, 'class' => 'initial-header'],
                ['text' => 'Rata-rata (Face B)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Base Reading', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-initial'],
                ['text' => 'Selisih (B-BR)', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Mean Cum Dev', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-calculation'],
                ['text' => 'Displace Profil', 'colspan' => 1, 'rowspan' => 3, 'class' => 'bg-result']
            ],
            'row3' => [
                ['text' => 'Face A+', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => 'Face A-', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => 'Face B+', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => 'Face B-', 'colspan' => 1, 'rowspan' => 2, 'class' => 'bg-reading'],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => ''],
                ['text' => '', 'colspan' => 0, 'rowspan' => 0, 'class' => '']
            ],
            'row4' => [
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(m)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '(mm)', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik'],
                ['text' => '', 'colspan' => 1, 'rowspan' => 1, 'class' => 'bg-metrik']
            ]
        ];
    }
    
    /**
     * Process data for table display
     */
    /**
 * Process data for table display - PERBAIKAN: Gunakan data dari database
 */
private function processTableData($rawData)
{
    $processedData = [];
    $counter = 1;
    
    foreach ($rawData as $row) {
        // Ambil data dari database, bukan hitung ulang
        $face_a_avg = isset($row['mean_deviation_a']) ? $row['mean_deviation_a'] : (($row['face_a_plus'] + $row['face_a_minus']) / 2);
        $face_b_avg = isset($row['mean_deviation_b']) ? $row['mean_deviation_b'] : (($row['face_b_plus'] + $row['face_b_minus']) / 2);
        
        // Ambil basereading dari database jika ada, jika tidak gunakan default
        $baseReadingA = isset($row['basereading_a']) ? $row['basereading_a'] : $this->getBaseReadingA($row['depth']);
        $baseReadingB = isset($row['basereading_b']) ? $row['basereading_b'] : $this->getBaseReadingB($row['depth']);
        
        // Hitung differences jika diperlukan
        $diff_a = $face_a_avg - $baseReadingA;
        $diff_b = $face_b_avg - $baseReadingB;
        
        // Gunakan mean_cum_deviation dari database jika ada
        $mean_cum_dev_a = isset($row['mean_cum_deviation_a']) ? $row['mean_cum_deviation_a'] : ((500 * $diff_a) / 0.5);
        $mean_cum_dev_b = isset($row['mean_cum_deviation_b']) ? $row['mean_cum_deviation_b'] : ((500 * $diff_b) / 0.5);
        
        $processedData[] = [
            'no' => $counter++,
            'depth' => number_format($row['depth'], 1),
            'face_a_plus' => number_format($row['face_a_plus'], 6),
            'face_a_minus' => number_format($row['face_a_minus'], 6),
            'face_a_avg' => number_format($face_a_avg, 6),
            'basereading_a' => number_format($baseReadingA, 6),
            'diff_a' => number_format($diff_a, 6),
            'mean_cum_deviation_a' => number_format($mean_cum_dev_a, 6),
            'displace_profile_a' => isset($row['displace_profile_a']) ? number_format($row['displace_profile_a'], 6) : '0.000000',
            'face_b_plus' => number_format($row['face_b_plus'], 6),
            'face_b_minus' => number_format($row['face_b_minus'], 6),
            'face_b_avg' => number_format($face_b_avg, 6),
            'basereading_b' => number_format($baseReadingB, 6),
            'diff_b' => number_format($diff_b, 6),
            'mean_cum_deviation_b' => number_format($mean_cum_dev_b, 6),
            'displace_profile_b' => isset($row['displace_profile_b']) ? number_format($row['displace_profile_b'], 6) : '0.000000',
            'profil_a' => isset($row['nilai_profil_a']) ? number_format($row['nilai_profil_a'], 6) : '0.000000',
            'profil_b' => isset($row['nilai_profil_b']) ? number_format($row['nilai_profil_b'], 6) : '0.000000',
            'id_pengukuran' => $row['id_pengukuran']
        ];
    }
    
    return $processedData;
}
    
    /**
     * Get base reading A
     */
    private function getBaseReadingA($depth)
    {
        $key = number_format($depth, 1, '.', '');
        
        $defaultValues = [
            '-0.5' => 0.003, '-1.0' => 0.007, '-1.5' => 0.010, '-2.0' => 0.009,
            '-2.5' => 0.006, '-3.0' => 0.005, '-3.5' => 0.003, '-4.0' => 0.002,
            '-4.5' => 0.000, '-5.0' => -0.001
        ];
        
        return isset($defaultValues[$key]) ? $defaultValues[$key] : 0.000000;
    }
    
    /**
     * Get base reading B
     */
    private function getBaseReadingB($depth)
    {
        $key = number_format($depth, 1, '.', '');
        
        $defaultValues = [
            '-0.5' => 0.006, '-1.0' => 0.005, '-1.5' => 0.005, '-2.0' => 0.005,
            '-2.5' => 0.005, '-3.0' => 0.004, '-3.5' => 0.004, '-4.0' => 0.004,
            '-4.5' => 0.003, '-5.0' => 0.001
        ];
        
        return isset($defaultValues[$key]) ? $defaultValues[$key] : 0.000000;
    }
    
    /**
     * Get metadata
     */
    private function getMetadata($rawData)
    {
        if (empty($rawData)) {
            return null;
        }
        
        $firstRow = $rawData[0];
        
        // Format date
        $readingDate = isset($firstRow['reading_date']) ? date('d-m-Y', strtotime($firstRow['reading_date'])) : 'Unknown';
        
        return [
            'borehole_name' => $firstRow['borehole_name'] ?? 'Unknown',
            'reading_date' => $readingDate,
            'probe_serial' => $firstRow['probe_serial'] ?? 'Unknown',
            'reel_serial' => $firstRow['reel_serial'] ?? 'Unknown',
            'operator' => $firstRow['operator'] ?? 'Unknown',
            'total_records' => count($rawData),
            'min_depth' => min(array_column($rawData, 'depth')),
            'max_depth' => max(array_column($rawData, 'depth'))
        ];
    }
}