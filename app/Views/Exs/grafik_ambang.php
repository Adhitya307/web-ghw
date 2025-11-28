<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Grafik & Ambang Batas - PT Indonesia Power' ?></title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.3rem;
            font-size: 0.75rem;
            white-space: nowrap;
            text-align: center;
        }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 600px;
            overflow: auto;
        }
        
        .table-header {
            background: white;
            padding: 1rem;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }
        
        .table-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .bg-hijau { background-color: #d4edda !important; }
        .bg-kuning { background-color: #fff3cd !important; }
        .bg-merah { background-color: #f8d7da !important; }
        
        .bg-status-hijau { background-color: #d4edda !important; color: #155724; font-weight: bold; }
        .bg-status-kuning { background-color: #fff3cd !important; color: #856404; font-weight: bold; }
        .bg-status-merah { background-color: #f8d7da !important; color: #721c24; font-weight: bold; }
        
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        /* Warna biru untuk header utama */
        .main-header-blue {
            background-color: #e6f2ff !important;
            font-weight: bold;
            color: #2c3e50;
        }
        
        /* Warna untuk ambang batas */
        .ambang-hijau { background-color: #d4edda !important; }
        .ambang-kuning { background-color: #fff3cd !important; }
        .ambang-merah { background-color: #f8d7da !important; }
        
        /* Sticky untuk header utama saja */
        .sticky-main-header {
            position: sticky;
            left: 0;
            background: #e6f2ff !important;
            z-index: 20;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            font-weight: bold;
        }
        
        /* Sticky untuk data rows */
        .sticky-col-data {
            position: sticky;
            left: 0;
            background: white;
            z-index: 15;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-ruler-combined me-2"></i>Extensometer Monitoring System
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('extenso/ex1') ?>" class="btn btn-primary btn-ex">
                <i class="fas fa-table"></i> Input Data
            </a>
            <a href="<?= base_url('extenso/grafik-ambang') ?>" class="btn btn-outline-warning">
                <i class="fas fa-chart-bar me-1"></i> Grafik & Ambang
            </a>
            <a href="<?= base_url('extenso/create') ?>" class="btn btn-outline-success">
                <i class="fas fa-plus me-1"></i> Add Data
            </a>
            
            <button type="button" class="btn btn-outline-info" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>

            <button type="button" class="btn btn-outline-warning" onclick="showImportModal()">
                <i class="fas fa-database me-1"></i> Import SQL
            </button>
        </div>

        <div class="table-controls">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h5><i class="fas fa-filter me-2"></i>Filter Data</h5>
        <div class="filter-group">
            <!-- Tahun -->
            <div class="filter-item">
                <label for="tahunFilter" class="form-label">Tahun</label>
                <select id="tahunFilter" class="form-select">
                    <option value="">Semua Tahun</option>
                    <?php
                    if (!empty($pengukuran)):
                        $uniqueYears = [];
                        foreach ($pengukuran as $item) {
                            $year = $item['pengukuran']['tahun'] ?? '-';
                            if ($year !== '-' && !in_array($year, $uniqueYears)) {
                                $uniqueYears[] = $year;
                            }
                        }
                        rsort($uniqueYears);
                        foreach ($uniqueYears as $year):
                    ?>
                        <option value="<?= esc($year) ?>"><?= esc($year) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <!-- Periode -->
            <div class="filter-item">
                <label for="periodeFilter" class="form-label">Periode</label>
                <select id="periodeFilter" class="form-select">
                    <option value="">Semua Periode</option>
                    <?php
                    if (!empty($pengukuran)):
                        $uniquePeriods = [];
                        foreach ($pengukuran as $item) {
                            $period = $item['pengukuran']['periode'] ?? '-';
                            if ($period !== '-' && !in_array($period, $uniquePeriods)) {
                                $uniquePeriods[] = $period;
                            }
                        }
                        sort($uniquePeriods);
                        foreach ($uniquePeriods as $period):
                    ?>
                        <option value="<?= esc($period) ?>"><?= esc($period) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <!-- Reset -->
            <div class="filter-item" style="align-self: flex-end;">
                <button id="resetFilter" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <!-- Row 1: Main Headers -->
                <tr class="sticky-header">
                    <th class="sticky-main-header">Rod Extensometer No.</th>
                    <td colspan="15" class="main-header-blue">EX-1</td>
                    <td colspan="15" class="main-header-blue">EX-2</td>
                    <td colspan="15" class="main-header-blue">EX-3</td>
                    <td colspan="15" class="main-header-blue">EX-4</td>
                </tr>
                
                <!-- Row 2: Zona -->
                <tr class="sticky-header">
                    <th class="sticky-main-header main-header-blue">Zona</th>
                    <td colspan="15" class="main-header-blue">SPILLWAY</td>
                    <td colspan="15" class="main-header-blue">SPILLWAY</td>
                    <td colspan="15" class="main-header-blue">SPILLWAY</td>
                    <td colspan="15" class="main-header-blue">SPILLWAY</td>
                </tr>
                
                <!-- Row 3: Kedalaman -->
                <tr class="sticky-header">
                    <th class="sticky-main-header main-header-blue">Kedalaman (m)</th>
                    <td colspan="5" class="main-header-blue">10</td>
                    <td colspan="5" class="main-header-blue">20</td>
                    <td colspan="5" class="main-header-blue">30</td>

                    <td colspan="5" class="main-header-blue">10</td>
                    <td colspan="5" class="main-header-blue">20</td>
                    <td colspan="5" class="main-header-blue">30</td>

                    <td colspan="5" class="main-header-blue">10</td>
                    <td colspan="5" class="main-header-blue">20</td>
                    <td colspan="5" class="main-header-blue">30</td>

                    <td colspan="5" class="main-header-blue">10</td>
                    <td colspan="5" class="main-header-blue">20</td>
                    <td colspan="5" class="main-header-blue">30</td>
                </tr>

                <!-- Row 4: Pembacaan Awal -->
                <tr class="sticky-header">
                    <th class="sticky-main-header main-header-blue">Pemb.Awal (mm)</th>
                    <td class="main-header-blue number-cell">35.00</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>

                    <td class="main-header-blue number-cell">40.95</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>

                    <td class="main-header-blue number-cell">29.80</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                    
                    <td class="main-header-blue number-cell">22.60</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">23.70</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">30.75</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">37.75</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">39.15</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">41.40</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">33.80</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">29.30</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                   
                    <td class="main-header-blue number-cell">48.95</td>
                    <td colspan="4" class="main-header-blue">Ambang Batas</td>
                </tr>
                
                <!-- Row 5: Koordinat -->
                <tr class="sticky-header">
                    <th class="sticky-main-header main-header-blue">Koordinat</th>
                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>

                    <td class="main-header-blue"></td>
                    <td colspan="4" class="main-header-blue">-</td>
                </tr>
                
                <!-- Row 6: Header Bacaan & Ambang Batas -->
                <tr class="sticky-header">
                    <th class="sticky-main-header main-header-blue">Tanggal</th>
                
                    <!-- EX-1 10m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-1 20m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-1 30m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>

                    <!-- EX-2 10m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-2 20m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-2 30m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>

                    <!-- EX-3 10m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-3 20m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-3 30m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>

                    <!-- EX-4 10m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-4 20m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                    
                    <!-- EX-4 30m -->
                    <td class="main-header-blue">Bacaan (mm)</td>
                    <th class="ambang-hijau">Hijau (mm)</th>
                    <th class="ambang-kuning">Kuning (mm)</th>
                    <th class="ambang-merah">Merah (mm)</th>
                    <td class="main-header-blue">Bacaan (mm)</td>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Nilai default ambang batas untuk setiap EX
                $ambangBatas = [
                    'ex1' => [
                        'hijau' => 80.10,
                        'kuning' => 104.00,
                        'merah' => 110.90
                    ],
                    'ex2' => [
                        'hijau' => 46.00,
                        'kuning' => 80.00,
                        'merah' => 81.00
                    ],
                    'ex3' => [
                        'hijau' => 80.10,
                        'kuning' => 104.00,
                        'merah' => 110.90
                    ],
                    'ex4' => [
                        'hijau' => 46.00,
                        'kuning' => 80.00,
                        'merah' => 81.00
                    ]
                ];

                // Fungsi untuk menentukan status berdasarkan ambang batas
                function getStatusClass($bacaan, $ambang) {
                    if ($bacaan === null || $bacaan === '') {
                        return '';
                    }
                    
                    if ($bacaan <= $ambang['hijau']) {
                        return 'bg-status-hijau';
                    } elseif ($bacaan <= $ambang['kuning']) {
                        return 'bg-status-kuning';
                    } else {
                        return 'bg-status-merah';
                    }
                }
                
                // Fungsi format number
                function formatNumber($number) {
                    if ($number === null || $number === '') return '-';
                    return number_format($number, 2, '.', '');
                }
                ?>
                
                <?php if(empty($pengukuran)): ?>
                    <tr>
                        <td colspan="91" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data monitoring yang tersedia</p>
                            <a href="<?= base_url('extenso/create') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($pengukuran as $item): 
                        $p = $item['pengukuran'];
                        $ex1 = $item['ex1'];
                        $ex2 = $item['ex2'];
                        $ex3 = $item['ex3'];
                        $ex4 = $item['ex4'];
                        
                        // Data untuk setiap kedalaman EX-1
                        $bacaan_ex1_10m = $ex1['pembacaan']['pembacaan_10'] ?? null;
                        $bacaan_ex1_20m = $ex1['pembacaan']['pembacaan_20'] ?? null;
                        $bacaan_ex1_30m = $ex1['pembacaan']['pembacaan_30'] ?? null;
                        
                        // Data untuk setiap kedalaman EX-2
                        $bacaan_ex2_10m = $ex2['pembacaan']['pembacaan_10'] ?? null;
                        $bacaan_ex2_20m = $ex2['pembacaan']['pembacaan_20'] ?? null;
                        $bacaan_ex2_30m = $ex2['pembacaan']['pembacaan_30'] ?? null;
                        
                        // Data untuk setiap kedalaman EX-3
                        $bacaan_ex3_10m = $ex3['pembacaan']['pembacaan_10'] ?? null;
                        $bacaan_ex3_20m = $ex3['pembacaan']['pembacaan_20'] ?? null;
                        $bacaan_ex3_30m = $ex3['pembacaan']['pembacaan_30'] ?? null;
                        
                        // Data untuk setiap kedalaman EX-4
                        $bacaan_ex4_10m = $ex4['pembacaan']['pembacaan_10'] ?? null;
                        $bacaan_ex4_20m = $ex4['pembacaan']['pembacaan_20'] ?? null;
                        $bacaan_ex4_30m = $ex4['pembacaan']['pembacaan_30'] ?? null;
                        
                        // Hitung status untuk setiap kedalaman semua EX
                        $status_class_ex1_10m = getStatusClass($bacaan_ex1_10m, $ambangBatas['ex1']);
                        $status_class_ex1_20m = getStatusClass($bacaan_ex1_20m, $ambangBatas['ex1']);
                        $status_class_ex1_30m = getStatusClass($bacaan_ex1_30m, $ambangBatas['ex1']);
                        
                        $status_class_ex2_10m = getStatusClass($bacaan_ex2_10m, $ambangBatas['ex2']);
                        $status_class_ex2_20m = getStatusClass($bacaan_ex2_20m, $ambangBatas['ex2']);
                        $status_class_ex2_30m = getStatusClass($bacaan_ex2_30m, $ambangBatas['ex2']);
                        
                        $status_class_ex3_10m = getStatusClass($bacaan_ex3_10m, $ambangBatas['ex3']);
                        $status_class_ex3_20m = getStatusClass($bacaan_ex3_20m, $ambangBatas['ex3']);
                        $status_class_ex3_30m = getStatusClass($bacaan_ex3_30m, $ambangBatas['ex3']);
                        
                        $status_class_ex4_10m = getStatusClass($bacaan_ex4_10m, $ambangBatas['ex4']);
                        $status_class_ex4_20m = getStatusClass($bacaan_ex4_20m, $ambangBatas['ex4']);
                        $status_class_ex4_30m = getStatusClass($bacaan_ex4_30m, $ambangBatas['ex4']);
                    ?>
                    <tr>
                        <!-- Sticky Columns untuk data -->
                        <td class="sticky-col-data"><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                        
                        <!-- EX-1 Data -->
                        <!-- 10m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex1_10m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex1']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex1']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex1']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex1_10m ?>"><?= formatNumber($bacaan_ex1_10m) ?></td>
                        
                        <!-- 20m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex1_20m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex1']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex1']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex1']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex1_20m ?>"><?= formatNumber($bacaan_ex1_20m) ?></td>
                        
                        <!-- 30m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex1_30m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex1']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex1']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex1']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex1_30m ?>"><?= formatNumber($bacaan_ex1_30m) ?></td>

                        <!-- EX-2 Data -->
                        <!-- 10m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex2_10m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex2']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex2']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex2']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex2_10m ?>"><?= formatNumber($bacaan_ex2_10m) ?></td>
                        
                        <!-- 20m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex2_20m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex2']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex2']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex2']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex2_20m ?>"><?= formatNumber($bacaan_ex2_20m) ?></td>
                        
                        <!-- 30m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex2_30m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex2']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex2']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex2']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex2_30m ?>"><?= formatNumber($bacaan_ex2_30m) ?></td>

                        <!-- EX-3 Data -->
                        <!-- 10m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex3_10m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex3']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex3']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex3']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex3_10m ?>"><?= formatNumber($bacaan_ex3_10m) ?></td>
                        
                        <!-- 20m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex3_20m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex3']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex3']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex3']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex3_20m ?>"><?= formatNumber($bacaan_ex3_20m) ?></td>
                        
                        <!-- 30m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex3_30m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex3']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex3']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex3']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex3_30m ?>"><?= formatNumber($bacaan_ex3_30m) ?></td>

                        <!-- EX-4 Data -->
                        <!-- 10m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex4_10m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex4']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex4']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex4']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex4_10m ?>"><?= formatNumber($bacaan_ex4_10m) ?></td>
                        
                        <!-- 20m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex4_20m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex4']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex4']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex4']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex4_20m ?>"><?= formatNumber($bacaan_ex4_20m) ?></td>
                        
                        <!-- 30m -->
                        <td class="number-cell"><?= formatNumber($bacaan_ex4_30m) ?></td>
                        <td class="ambang-hijau number-cell"><?= formatNumber($ambangBatas['ex4']['hijau']) ?></td>
                        <td class="ambang-kuning number-cell"><?= formatNumber($ambangBatas['ex4']['kuning']) ?></td>
                        <td class="ambang-merah number-cell"><?= formatNumber($ambangBatas['ex4']['merah']) ?></td>
                        <td class="number-cell <?= $status_class_ex4_30m ?>"><?= formatNumber($bacaan_ex4_30m) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>