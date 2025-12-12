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
        'updated_at',
        'reading_date'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // DATA BASEREADING DEFAULT A - DARI DATA YANG ANDA BERIKAN
    private $default_basereading_a = [
        '-0.5' => 0.00339594,
        '-1.0' => 0.00749072,
        '-1.5' => 0.009989775,
        '-2.0' => 0.00859667,
        '-2.5' => 0.00634877,
        '-3.0' => 0.00471846,
        '-3.5' => 0.003271915,
        '-4.0' => 0.00152667,
        '-4.5' => -0.000407655,
        '-5.0' => -0.0010585,
        '-5.5' => -0.000331275,
        '-6.0' => -0.00143603,
        '-6.5' => -0.003169715,
        '-7.0' => -0.004078765,
        '-7.5' => -0.004494635,
        '-8.0' => -0.00576767,
        '-8.5' => -0.006674515,
        '-9.0' => -0.007597325,
        '-9.5' => -0.009147505,
        '-10.0' => -0.0101757,
        '-10.5' => -0.011128935,
        '-11.0' => -0.011573845,
        '-11.5' => -0.01187834,
        '-12.0' => -0.01242018,
        '-12.5' => -0.013155455,
        '-13.0' => -0.012728235,
        '-13.5' => -0.01243112,
        '-14.0' => -0.012194415,
        '-14.5' => -0.011875195,
        '-15.0' => -0.011950975,
        '-15.5' => -0.01279671,
        '-16.0' => -0.013285445,
        '-16.5' => -0.01362125,
        '-17.0' => -0.01387557,
        '-17.5' => -0.01434434,
        '-18.0' => -0.01479585,
        '-18.5' => -0.015377595,
        '-19.0' => -0.01608875,
        '-19.5' => -0.01757433,
        '-20.0' => -0.01828679,
        '-20.5' => -0.01910031,
        '-21.0' => -0.019776755,
        '-21.5' => -0.019509135,
        '-22.0' => -0.01932121,
        '-22.5' => -0.01897897,
        '-23.0' => -0.019018365,
        '-23.5' => -0.019102695,
        '-24.0' => -0.01952315,
        '-24.5' => -0.02018302,
        '-25.0' => -0.020759405,
        '-25.5' => -0.021364095,
        '-26.0' => -0.02176867,
        '-26.5' => -0.02186134,
        '-27.0' => -0.02213765,
        '-27.5' => -0.02295381,
        '-28.0' => -0.02344139,
        '-28.5' => -0.02361273,
        '-29.0' => -0.02357255,
        '-29.5' => -0.023620145,
        '-30.0' => -0.023740365,
        '-30.5' => -0.023672445,
        '-31.0' => -0.02315514,
        '-31.5' => -0.022450075,
        '-32.0' => -0.02171486,
        '-32.5' => -0.02116252,
        '-33.0' => -0.02100237,
        '-33.5' => -0.021081145,
        '-34.0' => -0.021371005,
        '-34.5' => -0.021930895,
        '-35.0' => -0.02224533,
        '-35.5' => -0.02265771,
        '-36.0' => -0.02307484,
        '-36.5' => -0.023202705,
        '-37.0' => -0.023489135,
        '-37.5' => -0.02357729,
        '-38.0' => -0.02376833,
        '-38.5' => -0.023977665,
        '-39.0' => -0.024046215,
        '-39.5' => -0.024241335,
        '-40.0' => -0.02410974,
        '-40.5' => -0.02393811,
        '-41.0' => -0.02380355,
        '-41.5' => -0.023692065,
        '-42.0' => -0.023904635,
        '-42.5' => -0.02424498,
        '-43.0' => -0.0251046,
        '-43.5' => -0.025484615,
        '-44.0' => -0.025478935,
        '-44.5' => -0.026051555,
        '-45.0' => -0.02636838,
        '-45.5' => -0.025730905,
        '-46.0' => -0.02597715,
        '-46.5' => -0.026551655,
        '-47.0' => -0.026889665,
        '-47.5' => -0.027235315,
        '-48.0' => -0.02786305,
        '-48.5' => -0.027380025,
        '-49.0' => -0.027253525,
        '-49.5' => -0.027198145,
        '-50.0' => -0.02698937,
        '-50.5' => -0.02674837,
        '-51.0' => -0.027018375,
        '-51.5' => -0.02751944,
        '-52.0' => -0.0280241,
        '-52.5' => -0.028110075,
        '-53.0' => -0.02821578,
        '-53.5' => -0.028370535,
        '-54.0' => -0.02873373,
        '-54.5' => -0.02843327,
        '-55.0' => -0.028075925,
        '-55.5' => -0.027702695,
        '-56.0' => -0.02755388,
        '-56.5' => -0.02706861,
        '-57.0' => -0.02655408,
        '-57.5' => -0.02698101,
        '-58.0' => -0.027498465,
        '-58.5' => -0.02809203,
        '-59.0' => -0.02851462,
        '-59.5' => -0.029411585,
        '-60.0' => -0.030055305,
        '-60.5' => -0.02941726,
        '-61.0' => -0.02916905,
        '-61.5' => -0.028653245,
        '-62.0' => -0.028074845,
        '-62.5' => -0.02724188,
        '-63.0' => -0.026436375,
        '-63.5' => -0.02637916,
        '-64.0' => -0.027794765,
        '-64.5' => -0.0288422,
        '-65.0' => -0.02964385,
        '-65.5' => -0.03042903,
        '-66.0' => -0.030872095,
        '-66.5' => -0.03119404,
        '-67.0' => -0.030738195,
        '-67.5' => -0.030475845,
        '-68.0' => -0.030144995,
        '-68.5' => -0.02945578,
        '-69.0' => -0.02905893,
        '-69.5' => -0.02869452,
        '-70.0' => -0.028284805,
        '-70.5' => -0.027697735,
        '-71.0' => -0.027314525,
        '-71.5' => -0.02712185,
        '-72.0' => -0.02715627,
        '-72.5' => -0.027096765,
        '-73.0' => -0.028292765,
        '-73.5' => -0.029272095,
        '-74.0' => -0.030148195,
        '-74.5' => -0.03109294,
        '-75.0' => -0.031650885,
        '-75.5' => -0.03043698,
        '-76.0' => -0.02980893,
        '-76.5' => -0.029254975,
        '-77.0' => -0.028537775,
        '-77.5' => -0.02782688,
        '-78.0' => -0.027173655,
        '-78.5' => -0.02555582,
        '-79.0' => -0.02650604,
        '-79.5' => -0.027917395,
        '-80.0' => -0.029508455
    ];

    // DATA BASEREADING DEFAULT B - DARI DATA YANG ANDA BERIKAN
    private $default_basereading_b = [
        '-0.5' => 0.00613261,
        '-1.0' => 0.00536812,
        '-1.5' => 0.004734645,
        '-2.0' => 0.00455951,
        '-2.5' => 0.004772455,
        '-3.0' => 0.004306485,
        '-3.5' => 0.003864595,
        '-4.0' => 0.003544825,
        '-4.5' => 0.002500595,
        '-5.0' => 0.00125591,
        '-5.5' => 0.000433515,
        '-6.0' => -0.001097935,
        '-6.5' => -0.001065625,
        '-7.0' => -0.00159564,
        '-7.5' => -0.001980975,
        '-8.0' => -0.002638675,
        '-8.5' => -0.00357086,
        '-9.0' => -0.0048129,
        '-9.5' => -0.006374875,
        '-10.0' => -0.008760805,
        '-10.5' => -0.010263055,
        '-11.0' => -0.010056025,
        '-11.5' => -0.009802295,
        '-12.0' => -0.00971173,
        '-12.5' => -0.00915451,
        '-13.0' => -0.008422745,
        '-13.5' => -0.008018185,
        '-14.0' => -0.00753901,
        '-14.5' => -0.006920315,
        '-15.0' => -0.00622722,
        '-15.5' => -0.00612255,
        '-16.0' => -0.00628996,
        '-16.5' => -0.006353715,
        '-17.0' => -0.006364015,
        '-17.5' => -0.006381095,
        '-18.0' => -0.00593767,
        '-18.5' => -0.00501461,
        '-19.0' => -0.00490282,
        '-19.5' => -0.0048341,
        '-20.0' => -0.004741845,
        '-20.5' => -0.004829565,
        '-21.0' => -0.00456871,
        '-21.5' => -0.002931095,
        '-22.0' => -0.003136865,
        '-22.5' => -0.003485595,
        '-23.0' => -0.004226945,
        '-23.5' => -0.00511149,
        '-24.0' => -0.005712865,
        '-24.5' => -0.00444419,
        '-25.0' => -0.003752235,
        '-25.5' => -0.003172415,
        '-26.0' => -0.002359765,
        '-26.5' => -0.001560055,
        '-27.0' => -0.001111565,
        '-27.5' => -0.000632775,
        '-28.0' => -0.001135805,
        '-28.5' => -0.00216646,
        '-29.0' => -0.00315606,
        '-29.5' => -0.004362495,
        '-30.0' => -0.005089925,
        '-30.5' => -0.00461643,
        '-31.0' => -0.004560425,
        '-31.5' => -0.004559155,
        '-32.0' => -0.00454338,
        '-32.5' => -0.00453918,
        '-33.0' => -0.00413924,
        '-33.5' => -0.004142305,
        '-34.0' => -0.00445094,
        '-34.5' => -0.00483225,
        '-35.0' => -0.005055565,
        '-35.5' => -0.005185445,
        '-36.0' => -0.005242595,
        '-36.5' => -0.00518194,
        '-37.0' => -0.00545662,
        '-37.5' => -0.00575957,
        '-38.0' => -0.00618571,
        '-38.5' => -0.006490425,
        '-39.0' => -0.006771915,
        '-39.5' => -0.00695668,
        '-40.0' => -0.00695764,
        '-40.5' => -0.00696947,
        '-41.0' => -0.00687035,
        '-41.5' => -0.00687372,
        '-42.0' => -0.006758375,
        '-42.5' => -0.006540025,
        '-43.0' => -0.00667445,
        '-43.5' => -0.00677214,
        '-44.0' => -0.00683856,
        '-44.5' => -0.007192285,
        '-45.0' => -0.007753865,
        '-45.5' => -0.0073612,
        '-46.0' => -0.00698609,
        '-46.5' => -0.006740175,
        '-47.0' => -0.006631615,
        '-47.5' => -0.00654887,
        '-48.0' => -0.00663117,
        '-48.5' => -0.006380515,
        '-49.0' => -0.005464535,
        '-49.5' => -0.005072,
        '-50.0' => -0.00390934,
        '-50.5' => -0.00320617,
        '-51.0' => -0.00279413,
        '-51.5' => -0.00349606,
        '-52.0' => -0.00303874,
        '-52.5' => -0.002770815,
        '-53.0' => -0.0024942,
        '-53.5' => -0.001983235,
        '-54.0' => -0.001842325,
        '-54.5' => -0.001558405,
        '-55.0' => -0.002104305,
        '-55.5' => -0.00265237,
        '-56.0' => -0.003257035,
        '-56.5' => -0.003655795,
        '-57.0' => -0.00473026,
        '-57.5' => -0.00535305,
        '-58.0' => -0.00466924,
        '-58.5' => -0.004015125,
        '-59.0' => -0.003189075,
        '-59.5' => -0.00278217,
        '-60.0' => -0.00280962,
        '-60.5' => -0.003535535,
        '-61.0' => -0.00357416,
        '-61.5' => -0.003681885,
        '-62.0' => -0.00365718,
        '-62.5' => -0.0037286,
        '-63.0' => -0.004144695,
        '-63.5' => -0.00460962,
        '-64.0' => -0.004847185,
        '-64.5' => -0.00444582,
        '-65.0' => -0.003966385,
        '-65.5' => -0.003700485,
        '-66.0' => -0.00325588,
        '-66.5' => -0.00371453,
        '-67.0' => -0.00413173,
        '-67.5' => -0.004950965,
        '-68.0' => -0.00541802,
        '-68.5' => -0.005972545,
        '-69.0' => -0.006224445,
        '-69.5' => -0.005374085,
        '-70.0' => -0.006091635,
        '-70.5' => -0.00679345,
        '-71.0' => -0.007659575,
        '-71.5' => -0.008444525,
        '-72.0' => -0.00877878,
        '-72.5' => -0.00769891,
        '-73.0' => -0.00743035,
        '-73.5' => -0.0069062,
        '-74.0' => -0.006586785,
        '-74.5' => -0.006046805,
        '-75.0' => -0.005352415,
        '-75.5' => -0.003777575,
        '-76.0' => -0.00324855,
        '-76.5' => -0.002988815,
        '-77.0' => -0.002110185,
        '-77.5' => -0.00134871,
        '-78.0' => -0.000711965,
        '-78.5' => 0.00140333,
        '-79.0' => 0.001492165,
        '-79.5' => 0.00131874,
        '-80.0' => 0.00086354
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('db_inclino');
    }

    /**
     * Get default basereading A untuk depth tertentu
     */
    private function getDefaultBasereadingA($depth)
    {
        // Convert depth to string dengan format yang sama
        $key = number_format($depth, 1, '.', '');
        $value = isset($this->default_basereading_a[$key]) 
            ? $this->default_basereading_a[$key] 
            : 0.0;
        
        // Log untuk debugging
        if (in_array($depth, [-0.5, -1.0, -1.5]) || $depth == -80.0) {
            log_message('debug', "[Ireadingmodel] getDefaultBasereadingA - Depth: $depth, Key: $key, Value: $value");
        }
        
        return $value;
    }

    /**
     * Get default basereading B untuk depth tertentu
     */
    private function getDefaultBasereadingB($depth)
    {
        // Convert depth to string dengan format yang sama
        $key = number_format($depth, 1, '.', '');
        $value = isset($this->default_basereading_b[$key]) 
            ? $this->default_basereading_b[$key] 
            : 0.0;
        
        // Log untuk debugging
        if (in_array($depth, [-0.5, -1.0, -1.5]) || $depth == -80.0) {
            log_message('debug', "[Ireadingmodel] getDefaultBasereadingB - Depth: $depth, Key: $key, Value: $value");
        }
        
        return $value;
    }

    /**
     * METHOD UTAMA - GENERATE INITIAL READINGS
     */
    /**
     * METHOD UTAMA - GENERATE INITIAL READINGS
     */
    public function generateInitialReadings($id_pengukuran, $targetDate = null)
    {
        try {
            log_message('debug', "[Ireadingmodel] ======= START generateInitialReadings =======");
            log_message('debug', "[Ireadingmodel] Parameters - id_pengukuran: $id_pengukuran, targetDate: " . ($targetDate ? $targetDate : 'ALL'));
            
            // 1. LOG BASELINE INFO
            log_message('debug', "[Ireadingmodel] Step 1: Using STATIC DEFAULT baseline values");
            log_message('debug', "[Ireadingmodel] Baseline A count: " . count($this->default_basereading_a));
            log_message('debug', "[Ireadingmodel] Baseline B count: " . count($this->default_basereading_b));
            log_message('debug', "[Ireadingmodel] Baseline samples A: [-0.5 => " . $this->default_basereading_a['-0.5'] . 
                              ", -80.0 => " . $this->default_basereading_a['-80.0'] . "]");
            log_message('debug', "[Ireadingmodel] Baseline samples B: [-0.5 => " . $this->default_basereading_b['-0.5'] . 
                              ", -80.0 => " . $this->default_basereading_b['-80.0'] . "]");
            
            // 2. GET RAW DATA dari database
            log_message('debug', "[Ireadingmodel] Step 2: Getting raw data from database");
            
            $query = $this->db->table('inclinometer_readings')
                ->where('id_pengukuran', $id_pengukuran);
            
            // Jika ada targetDate spesifik, filter berdasarkan tanggal
            if ($targetDate) {
                $query->where('reading_date', $targetDate);
                log_message('debug', "[Ireadingmodel] Filtering by date: $targetDate");
            } else {
                log_message('debug', "[Ireadingmodel] Getting all dates for id_pengukuran: $id_pengukuran");
            }
            
            $rawData = $query->orderBy('reading_date', 'ASC')
                ->orderBy('depth', 'ASC')
                ->get()
                ->getResultArray();
            
            if (empty($rawData)) {
                log_message('error', "[Ireadingmodel] ERROR: No raw data found!");
                throw new \Exception("Tidak ada data raw ditemukan untuk id_pengukuran: $id_pengukuran!");
            }
            
            // LOG: Tampilkan summary raw data
            $uniqueDates = array_unique(array_column($rawData, 'reading_date'));
            $depths = array_column($rawData, 'depth');
            log_message('debug', "[Ireadingmodel] Raw data loaded: " . count($rawData) . " rows");
            log_message('debug', "[Ireadingmodel] Dates in raw data: " . implode(', ', $uniqueDates));
            log_message('debug', "[Ireadingmodel] Depth range: " . min($depths) . " to " . max($depths));
            log_message('debug', "[Ireadingmodel] Unique depths: " . count(array_unique($depths)));
            
            // 3. CALCULATE ALL READINGS
            log_message('debug', "[Ireadingmodel] Step 3: Calculating initial readings");
            log_message('debug', "[Ireadingmodel] Formula: mean_cum_deviation = (face_current - face_baseline) * 1000");
            
            $initialReadings = [];
            $baselineUsedCount = 0;
            $baselineNotFoundCount = 0;
            $processedDates = [];
            
            foreach ($rawData as $index => $data) {
                $depth = $data['depth'];
                $readingDate = $data['reading_date'];
                
                // Track tanggal yang diproses
                if (!in_array($readingDate, $processedDates)) {
                    $processedDates[] = $readingDate;
                }
                
                // Hitung face_a dan face_b dari data current
                $face_a = ($data['face_a_plus'] - $data['face_a_minus']) / 2;
                $face_b = ($data['face_b_plus'] - $data['face_b_minus']) / 2;
                
                // Ambil basereading dari DEFAULT VALUES
                $basereading_a = $this->getDefaultBasereadingA($depth);
                $basereading_b = $this->getDefaultBasereadingB($depth);
                
                // Statistik penggunaan baseline
                $key = number_format($depth, 1, '.', '');
                if (isset($this->default_basereading_a[$key]) && isset($this->default_basereading_b[$key])) {
                    $baselineUsedCount++;
                } else {
                    $baselineNotFoundCount++;
                }
                
                // Hitung mean cumulative deviation
                $difference_a = $face_a - $basereading_a;
                $difference_b = $face_b - $basereading_b;
                
                $mean_cum_deviation_a = 1000 * $difference_a; // 500/0.5 = 1000
                $mean_cum_deviation_b = 1000 * $difference_b;
                
                // LOG DETAIL untuk beberapa data pertama dan terakhir
                if ($index < 3 || $index >= count($rawData) - 3) {
                    $position = $index < 3 ? "first " . ($index + 1) : "last " . (count($rawData) - $index);
                    log_message('debug', "[Ireadingmodel] Calculation ($position):");
                    log_message('debug', "[Ireadingmodel]   Depth: $depth, Date: $readingDate");
                    log_message('debug', "[Ireadingmodel]   Face A (current): $face_a, Baseline A: $basereading_a, Diff: $difference_a");
                    log_message('debug', "[Ireadingmodel]   Face B (current): $face_b, Baseline B: $basereading_b, Diff: $difference_b");
                    log_message('debug', "[Ireadingmodel]   Mean Cum A: $mean_cum_deviation_a, Mean Cum B: $mean_cum_deviation_b");
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
                    'displace_profile_a' => 0, // Akan dihitung dengan rumus yang benar
                    'displace_profile_b' => 0, // Akan dihitung dengan rumus yang benar
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            
            // LOG: Tampilkan statistik
            log_message('debug', "[Ireadingmodel] Baseline Usage Statistics:");
            log_message('debug', "[Ireadingmodel]   Total calculations: " . count($rawData));
            log_message('debug', "[Ireadingmodel]   Baseline found and used: $baselineUsedCount");
            log_message('debug', "[Ireadingmodel]   Baseline not found (used 0.0): $baselineNotFoundCount");
            log_message('debug', "[Ireadingmodel]   Dates processed: " . implode(', ', $processedDates));
            
// 4. CORRECTED CALCULATION OF DISPLACEMENT PROFILES
log_message('debug', "[Ireadingmodel] Step 4: Calculating displacement profiles WITH CORRECT FORMULA");
log_message('debug', "[Ireadingmodel] Formula: Displace_n = MeanDev_n + Displace_(n+1)");
log_message('debug', "[Ireadingmodel] Direction: FROM BOTTOM (-80) TO TOP (-0.5)");


// ---------------------------------
// Group by date (tanpa key float!)
// ---------------------------------
$groupedByDate = [];

foreach ($initialReadings as $reading) {

    $groupKey = $reading['reading_date'] ?? 'all';

    // jangan gunakan depth sebagai key — harus push biasa
    $reading['depth'] = floatval($reading['depth']);

    $groupedByDate[$groupKey][] = $reading;
}


// ---------------------------------
// Hitung displacement
// ---------------------------------
foreach ($groupedByDate as $dateKey => &$readings) {

    // Sort ASC: depth paling dalam (-80) → ke atas (-0.5)
    usort($readings, fn($a,$b) => $a['depth'] <=> $b['depth']);

    $prevA = 0.0;
    $prevB = 0.0;

    foreach ($readings as $i => $reading) {

        $meanA = floatval($reading['mean_cum_deviation_a']);
        $meanB = floatval($reading['mean_cum_deviation_b']);

        // displacement = mean + previous displacement
        $dispA = $meanA + $prevA;
        $dispB = $meanB + $prevB;

        $readings[$i]['displace_profile_a'] = $dispA;
        $readings[$i]['displace_profile_b'] = $dispB;

        // update previous
        $prevA = $dispA;
        $prevB = $dispB;
    }

    // Sort kembali ASC untuk display
    usort($readings, fn($a,$b) => $a['depth'] <=> $b['depth']);
}

// Flatten array
$finalReadings = [];
foreach ($groupedByDate as $readings) {
    $finalReadings = array_merge($finalReadings, $readings);
}

log_message('debug', "[Ireadingmodel] Verification of displacement calculation:");

// Depth yang ingin dicek
$checkDepths = ['-80', '-79.5', '-0.5'];

$checkResults = [];

// Ambil data untuk depth yang dicek
foreach ($finalReadings as $r) {

    $depthKey = (string)$r['depth'];

    if (in_array($depthKey, $checkDepths, true)) {
        $checkResults[$depthKey] = [
            'mean_a' => $r['mean_cum_deviation_a'],
            'displace_a' => $r['displace_profile_a'],
            'mean_b' => $r['mean_cum_deviation_b'],
            'displace_b' => $r['displace_profile_b']
        ];
    }
}

// Mulai verifikasi
foreach ($checkDepths as $depth) {

    if (!isset($checkResults[$depth])) {
        log_message('warning', "[Ireadingmodel]   Depth $depth: Not found in results!");
        continue;
    }

    $data = $checkResults[$depth];

    // Depth pertama -80
    if ($depth === '-80') {

        log_message(
            'debug',
            "[Ireadingmodel]   Depth $depth: Displace = MeanDev({$data['mean_a']}) + 0 = {$data['displace_a']}"
        );

        continue;
    }

for ($i = 0; $i < count($checkDepths); $i++) {

    $depth = $checkDepths[$i];

    if (!isset($checkResults[$depth])) {
        log_message('warning', "[Ireadingmodel]   Depth $depth: Not found in results!");
        continue;
    }

    $data = $checkResults[$depth];

    // Depth pertama (-80)
    if ($i === 0) {
        log_message(
            'debug',
            "[Ireadingmodel]   Depth $depth: Displace = MeanDev({$data['mean_a']}) + 0 = {$data['displace_a']}"
        );
        continue;
    }

    // Depth sebelumnya mengikuti urutan array
    $prevDepth = $checkDepths[$i - 1];

    if (!isset($checkResults[$prevDepth])) {
        log_message('warning', "[Ireadingmodel]   Depth $depth: Cannot verify - previous depth $prevDepth not found");
        continue;
    }

    $prevDisplace = $checkResults[$prevDepth]['displace_a'];

    $expected = round($data['mean_a'] + $prevDisplace, 6);

    log_message(
        'debug',
        "[Ireadingmodel]   Depth $depth: Displace = MeanDev({$data['mean_a']}) + Displace($prevDepth)($prevDisplace) = {$data['displace_a']}"
    );
    log_message(
        'debug',
        "[Ireadingmodel]   Expected: {$data['mean_a']} + $prevDisplace = $expected"
    );
    log_message(
        'debug',
        "[Ireadingmodel]   Calculation is " . (abs($data['displace_a'] - $expected) < 0.0001 ? "CORRECT" : "WRONG")
    );
}

}

            
            // LOG: Tampilkan sample displacement
            log_message('debug', "[Ireadingmodel] Displacement Samples:");
            $sampleCount = min(10, count($finalReadings));
            for ($i = 0; $i < $sampleCount; $i++) {
                $r = $finalReadings[$i];
                log_message('debug', "[Ireadingmodel]   Depth {$r['depth']}: MeanDev A = {$r['mean_cum_deviation_a']}, Displace A = {$r['displace_profile_a']}");
            }
            
            // 5. DELETE OLD AND INSERT NEW
            log_message('debug', "[Ireadingmodel] Step 5: Saving to database");
            
            // Delete semua data untuk id_pengukuran ini
            log_message('debug', "[Ireadingmodel] Deleting ALL old data for id_pengukuran: $id_pengukuran");
            $deleteResult = $this->deleteByPengukuran($id_pengukuran);
            log_message('debug', "[Ireadingmodel] Delete result: " . ($deleteResult ? 'SUCCESS' : 'FAILED'));
            
            // Insert data baru
            log_message('debug', "[Ireadingmodel] Inserting " . count($finalReadings) . " new records");
            $result = $this->insertBatchInitial($finalReadings);
            
            if ($result === false) {
                log_message('error', "[Ireadingmodel] ERROR: Insert data failed!");
                throw new \Exception("Insert data gagal!");
            }
            
            log_message('debug', "[Ireadingmodel] SUCCESS: Generated $result initial readings");
            log_message('debug', "[Ireadingmodel] Displacement calculation formula: Displace_n = MeanDev_n + Displace_(n+1)");
            log_message('debug', "[Ireadingmodel] Calculation direction: Bottom (-80) to Top (-0.5)");
            log_message('debug', "[Ireadingmodel] Baseline source: STATIC DEFAULT VALUES (from code)");
            log_message('debug', "[Ireadingmodel] ======= END generateInitialReadings =======");
            
            return $result;

        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] generateInitialReadings ERROR: ' . $e->getMessage());
            log_message('error', '[Ireadingmodel] Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * METHOD ALTERNATIF - Generate untuk tanggal spesifik
     */
    public function generateInitialReadingsForDate($id_pengukuran, $targetDate)
    {
        log_message('debug', "[Ireadingmodel] generateInitialReadingsForDate called - Date: $targetDate");
        return $this->generateInitialReadings($id_pengukuran, $targetDate);
    }

    /**
     * METHOD ALTERNATIF - Generate untuk semua tanggal
     */
    public function generateAllInitialReadings($id_pengukuran)
    {
        try {
            log_message('debug', "[Ireadingmodel] ======= START generateAllInitialReadings =======");
            
            // Cukup panggil generateInitialReadings tanpa filter tanggal
            $result = $this->generateInitialReadings($id_pengukuran);
            
            log_message('debug', "[Ireadingmodel] ======= END generateAllInitialReadings =======");
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] generateAllInitialReadings ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert batch dengan logging
     */
    public function insertBatchInitial(array $data)
    {
        try {
            log_message('debug', "[Ireadingmodel] insertBatchInitial - Starting transaction");
            log_message('debug', "[Ireadingmodel] Records to insert: " . count($data));
            
            $this->db->transStart();
            $result = $this->db->table($this->table)->insertBatch($data);
            $this->db->transComplete();
            
            $status = $this->db->transStatus();
            log_message('debug', "[Ireadingmodel] insertBatchInitial - Transaction status: " . ($status ? 'SUCCESS' : 'FAILED'));
            log_message('debug', "[Ireadingmodel] insertBatchInitial - Result: " . ($result !== false ? $result : 'FALSE'));
            
            // Log sample data yang diinsert
            if (!empty($data) && $status) {
                $sample = $data[0];
                log_message('debug', "[Ireadingmodel] Sample inserted record:");
                log_message('debug', "[Ireadingmodel]   Depth: {$sample['depth']}");
                log_message('debug', "[Ireadingmodel]   Face A: {$sample['face_a']}, Baseline A: {$sample['basereading_a']}");
                log_message('debug', "[Ireadingmodel]   Mean Cum A: {$sample['mean_cum_deviation_a']}");
                log_message('debug', "[Ireadingmodel]   Displace A: {$sample['displace_profile_a']}");
            }
            
            return $status ? $result : false;
            
        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] insertBatchInitial ERROR: ' . $e->getMessage());
            if (!empty($data)) {
                log_message('error', '[Ireadingmodel] First record: ' . json_encode($data[0]));
            }
            return false;
        }
    }

    /**
     * Delete by id_pengukuran dengan logging
     */
    public function deleteByPengukuran($id_pengukuran)
    {
        try {
            log_message('debug', "[Ireadingmodel] deleteByPengukuran - id_pengukuran: $id_pengukuran");
            
            // Hitung dulu berapa yang akan dihapus
            $count = $this->where('id_pengukuran', $id_pengukuran)->countAllResults();
            log_message('debug', "[Ireadingmodel] deleteByPengukuran - Records to delete: $count");
            
            $result = $this->where('id_pengukuran', $id_pengukuran)->delete();
            
            log_message('debug', "[Ireadingmodel] deleteByPengukuran - Result: " . ($result ? 'SUCCESS' : 'FAILED'));
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] deleteByPengukuran ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Debug method untuk memverifikasi default values
     */
    public function debugDefaultValues()
    {
        $debugInfo = [
            'basereading_a_count' => count($this->default_basereading_a),
            'basereading_b_count' => count($this->default_basereading_b),
            'sample_depths_a' => [],
            'sample_depths_b' => [],
            'all_depths' => array_keys($this->default_basereading_a)
        ];
        
        // Ambil 10 sample dari basereading A
        $sampleKeys = array_slice(array_keys($this->default_basereading_a), 0, 10);
        foreach ($sampleKeys as $key) {
            $debugInfo['sample_depths_a'][$key] = $this->default_basereading_a[$key];
        }
        
        // Ambil 10 sample dari basereading B
        $sampleKeys = array_slice(array_keys($this->default_basereading_b), 0, 10);
        foreach ($sampleKeys as $key) {
            $debugInfo['sample_depths_b'][$key] = $this->default_basereading_b[$key];
        }
        
        // Cek apakah semua depths ada di kedua array
        $missingInB = array_diff(array_keys($this->default_basereading_a), array_keys($this->default_basereading_b));
        $missingInA = array_diff(array_keys($this->default_basereading_b), array_keys($this->default_basereading_a));
        
        $debugInfo['missing_in_b'] = array_values($missingInB);
        $debugInfo['missing_in_a'] = array_values($missingInA);
        $debugInfo['all_depths_match'] = empty($missingInA) && empty($missingInB);
        
        log_message('debug', "[Ireadingmodel] debugDefaultValues: " . json_encode($debugInfo));
        
        return $debugInfo;
    }

    /**
     * Verifikasi perhitungan untuk depth tertentu
     */
    public function verifyCalculation($id_pengukuran, $depth)
    {
        try {
            // Ambil data dari database
            $data = $this->db->table('inclinometer_readings')
                ->where('id_pengukuran', $id_pengukuran)
                ->where('depth', $depth)
                ->orderBy('reading_date', 'ASC')
                ->get()
                ->getResultArray();
            
            if (empty($data)) {
                return ["error" => "No data found for depth: $depth"];
            }
            
            $verification = [
                'depth' => $depth,
                'baseline_values' => [
                    'a' => $this->getDefaultBasereadingA($depth),
                    'b' => $this->getDefaultBasereadingB($depth)
                ],
                'calculations' => []
            ];
            
            foreach ($data as $row) {
                $face_a = ($row['face_a_plus'] - $row['face_a_minus']) / 2;
                $face_b = ($row['face_b_plus'] - $row['face_b_minus']) / 2;
                
                $diff_a = $face_a - $verification['baseline_values']['a'];
                $diff_b = $face_b - $verification['baseline_values']['b'];
                
                $mean_cum_a = 1000 * $diff_a;
                $mean_cum_b = 1000 * $diff_b;
                
                $verification['calculations'][] = [
                    'date' => $row['reading_date'],
                    'face_a' => $face_a,
                    'face_b' => $face_b,
                    'diff_a' => $diff_a,
                    'diff_b' => $diff_b,
                    'mean_cum_a' => $mean_cum_a,
                    'mean_cum_b' => $mean_cum_b
                ];
            }
            
            return $verification;
            
        } catch (\Exception $e) {
            log_message('error', '[Ireadingmodel] verifyCalculation ERROR: ' . $e->getMessage());
            return ["error" => $e->getMessage()];
        }
    }
}