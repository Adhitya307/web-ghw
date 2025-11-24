<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Piezometer - PT Indonesia Power' ?></title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* PERBAIKAN UTAMA: Sistem z-index dan top position yang terstruktur */
        
        /* Kolom biasa - z-index terendah */
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
            position: sticky;
            z-index: 10;
        }
        
        /* PERBAIKAN: Sticky Header Levels dengan TOP POSITION yang BERURUTAN */
        .table th.sticky-header {
            position: sticky;
            top: 0;
            z-index: 30; /* Level 1 - tertinggi */
        }
        
        .table th.sticky-header-level-2 {
            position: sticky;
            top: 56px; /* Row 2: tinggi row 1 (56px) */
            z-index: 29;
        }
        
        .table th.sticky-header-level-3 {
            position: sticky;
            top: 112px; /* Row 3: sebelumnya (56px) + row 3 (56px) */
            z-index: 28;
        }
        
        .table th.sticky-header-level-4 {
            position: sticky;
            top: 168px; /* Row 4: sebelumnya (112px) + row 4 (56px) */
            z-index: 27;
        }
        
        .table th.sticky-header-level-5 {
            position: sticky;
            top: 224px; /* Row 5: sebelumnya (168px) + row 5 (56px) */
            z-index: 26;
        }
        
        .table th.sticky-header-level-6 {
            position: sticky;
            top: 280px; /* Row 6: sebelumnya (224px) + row 6 (56px) */
            z-index: 25;
        }
        
        .table th.sticky-header-level-7 {
            position: sticky;
            top: 336px; /* Row 7: sebelumnya (280px) + row 7 (56px) */
            z-index: 24;
        }

        /* Sticky columns kiri - z-index menengah */
        .sticky { 
            position: sticky; 
            left: 0; 
            background: white; 
            z-index: 35; /* Lebih tinggi dari header biasa */
            top: 0;
        }
        .sticky-2 { 
            position: sticky; 
            left: 80px; 
            background: white; 
            z-index: 35;
            top: 0;
        }
        .sticky-3 { 
            position: sticky; 
            left: 160px; 
            background: white; 
            z-index: 35;
            top: 0;
        }
        .sticky-4 { 
            position: sticky; 
            left: 240px; 
            background: white; 
            z-index: 35;
            top: 0;
        }
        .sticky-5 { 
            position: sticky; 
            left: 320px; 
            background: white; 
            z-index: 35;
            top: 0;
        }

        /* PERBAIKAN: Action Cell dengan z-index yang TINGGI agar tidak tertimpa */
        .action-cell {
            position: sticky;
            right: 0;
            background: white;
            z-index: 100; /* DITINGKATKAN dari 40 ke 100 */
            padding: 0.3rem;
            min-width: 80px;
            border-left: 2px solid #dee2e6 !important;
            white-space: nowrap;
            vertical-align: middle !important;
            text-align: center !important;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        }

        /* PERBAIKAN: Header Action Cell dengan z-index PALING TINGGI */
        .table th.action-cell {
            position: sticky;
            right: 0;
            z-index: 200; /* DITINGKATKAN DRASTIS - PALING TINGGI */
            background: #f8f9fa !important;
            border-left: 2px solid #dee2e6 !important;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        }

        /* PERBAIKAN: Header Action Cell dengan TOP POSITION yang BERURUTAN dan z-index TINGGI */
        .table th.action-cell.sticky-header {
            top: 0;
            right: 0;
            z-index: 200;
        }

        .table th.action-cell.sticky-header-level-2 {
            top: 56px;
            right: 0;
            z-index: 199;
        }

        .table th.action-cell.sticky-header-level-3 {
            top: 112px;
            right: 0;
            z-index: 198;
        }

        .table th.action-cell.sticky-header-level-4 {
            top: 168px;
            right: 0;
            z-index: 197;
        }

        .table th.action-cell.sticky-header-level-5 {
            top: 224px;
            right: 0;
            z-index: 196;
        }

        .table th.action-cell.sticky-header-level-6 {
            top: 280px;
            right: 0;
            z-index: 195;
        }

        .table th.action-cell.sticky-header-level-7 {
            top: 336px;
            right: 0;
            z-index: 194;
        }

        /* PERBAIKAN: Standarkan tinggi row header */
        .table thead tr {
            height: 56px;
        }

        .btn-action {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
            margin: 0 1px;
            font-size: 0.75rem;
        }
        
        .btn-edit {
            color: #fff;
            background-color: #0d6efd;
            border: 1px solid #0d6efd;
        }
        
        .btn-edit:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-1px);
        }
        
        .btn-delete {
            color: #fff;
            background-color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        .btn-delete:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
            transform: translateY(-1px);
        }
        
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.3rem;
            font-size: 0.75rem;
            white-space: nowrap;
        }
        
        .data-table {
            min-width: 2000px;
        }
        
        /* PERBAIKAN: Tinggi tabel container */
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 800px; /* DITINGKATKAN untuk memastikan data terlihat */
            overflow: auto;
        }
        
        /* Warna Header Netral */
        .point-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
        }
        
        .calculation-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
        }
        
        .initial-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
        }
        
        .conversion-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
        }
        
        /* Background Colors untuk Cells */
        .bg-reading { background-color: #e8f4fd !important; color: #2c3e50 !important; }
        .bg-calculation { background-color: #f0f9eb !important; color: #2c3e50 !important; }
        .bg-result { background-color: #e6f7ff !important; color: #2c3e50 !important; }
        .bg-action { background-color: #f8f9fa !important; color: #2c3e50 !important; }
        .bg-metrik { background-color: #fff2cc !important; color: #2c3e50 !important; }
        .bg-initial { background-color: #e6ffed !important; color: #2c3e50 !important; }
        .bg-info-column { background-color: #e7f1ff !important; color: #2c3e50 !important; }
        
        /* Filter Section */
        .filter-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 150px;
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
        
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn-piez {
            min-width: 70px;
        }
        
        .scroll-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 2000;
            display: none;
        }
        
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .text-cell {
            text-align: center;
        }
        
        .scientific-notation {
            font-size: 0.7rem;
        }
        
        /* Perbaikan alignment untuk action cell */
        .action-cell .d-flex {
            height: 100%;
            align-items: center;
            justify-content: center;
            min-height: 40px;
        }
        
        /* Pastikan kolom kosong tetap ada */
        .table td:empty::before {
            content: "-";
            color: #6c757d;
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-tachometer-alt me-2"></i>Piezometer - Left Bank
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('left-piez') ?>" class="btn btn-primary btn-piez">
                <i class="fas fa-table"></i> Left Bank
            </a>
            <a href="<?= base_url('piezometer/right') ?>" class="btn btn-outline-primary btn-piez">Right Bank</a>
            <a href="<?= base_url('left-piez/create') ?>" class="btn btn-outline-success">
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

            <!-- DMA Filter -->
            <div class="filter-item">
                <label for="dmaFilter" class="form-label">DMA</label>
                <select id="dmaFilter" class="form-select">
                    <option value="">Semua DMA</option>
                    <?php
                    if (!empty($pengukuran)):
                        $uniqueDMA = [];
                        foreach ($pengukuran as $item) {
                            $dma = $item['pengukuran']['dma'] ?? '-';
                            if ($dma !== '-' && $dma !== '' && !in_array($dma, $uniqueDMA)) {
                                $uniqueDMA[] = $dma;
                            }
                        }
                        sort($uniqueDMA);
                        foreach ($uniqueDMA as $dma):
                    ?>
                        <option value="<?= esc($dma) ?>"><?= esc($dma) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <!-- Titik Piezometer -->
            <div class="filter-item">
                <label for="titikFilter" class="form-label">Titik Piezometer</label>
                <select id="titikFilter" class="form-select">
                    <option value="">Semua Titik</option>
                    <option value="L_01">L-01</option>
                    <option value="L_02">L-02</option>
                    <option value="L_03">L-03</option>
                    <option value="L_04">L-04</option>
                    <option value="L_05">L-05</option>
                    <option value="L_06">L-06</option>
                    <option value="L_07">L-07</option>
                    <option value="L_08">L-08</option>
                    <option value="L_09">L-09</option>
                    <option value="L_10">L-10</option>
                    <option value="SPZ_02">SPZ-02</option>
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
    <div class="table-responsive" id="tableContainer">
        <table class="data-table table table-bordered table-hover" id="exportTable">
            <thead>
                <!-- Row 1: Main Header -->
                <tr>
                    <th rowspan="7" class="sticky sticky-header">TAHUN</th>
                    <th rowspan="7" class="sticky-2 sticky-header">PERIODE</th>
                    <th rowspan="7" class="sticky-3 sticky-header">TANGGAL</th>
                    <th rowspan="7" class="sticky-4 sticky-header bg-info-column">DMA</th>
                    <th rowspan="7" class="sticky-5 sticky-header bg-info-column">CH BULANAN</th>
                    
                    <!-- BACAAN METRIK -->
                    <th colspan="22" class="bg-metrik sticky-header-level-2">BACAAN METRIK</th>

                    <!-- KONVERSI -->
                    <th colspan="2" class="bg-calculation sticky-header-level-2">KONVERSI</th>

                    <!-- BACAAN PIEZOMETER METRIK -->
                    <th colspan="11" class="bg-reading sticky-header-level-2">BACAAN PIEZOMETER METRIK</th>
                    
                    <!-- PERHITUNGAN -->
                    <th colspan="12" class="bg-calculation sticky-header-level-2">PERHITUNGAN PIEZOMETER</th>

                    <!-- INITIAL READINGS -->
                    <th colspan="12" class="bg-initial sticky-header-level-2">INITIAL READINGS A</th>

                    <!-- INITIAL READINGS -->
                    <th colspan="12" class="bg-initial sticky-header-level-2">INITIAL READINGS B</th>
                    
                    <th rowspan="7" class="action-cell bg-action sticky-header">AKSI</th>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- BACAAN METRIK Sub Headers -->
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-01</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-02</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-03</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-04</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-05</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-06</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-07</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-08</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-09</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">L-10</th>
                    <th colspan="2" class="bg-metrik sticky-header-level-2">SPZ-02</th>
                    
                    <!-- KONVERSI Sub Headers -->
                    <th rowspan="6" class="bg-calculation sticky-header-level-2">FEET → M</th>
                    <th rowspan="6" class="bg-calculation sticky-header-level-2">INCH → M</th>
                    
                    <!-- BACAAN PIEZOMETER METRIK Sub Headers -->
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-01</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-02</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-03</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-04</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-05</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-06</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-07</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-08</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-09</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">L-10</th>
                    <th rowspan="6" class="bg-reading sticky-header-level-2">SPZ-02</th>

                    <!-- PERHITUNGAN Sub Headers -->
                    <th class="bg-calculation sticky-header-level-2">No.Titik</th>
                    <th class="bg-calculation sticky-header-level-2">L-01</th>
                    <th class="bg-calculation sticky-header-level-2">L-02</th>
                    <th class="bg-calculation sticky-header-level-2">L-03</th>
                    <th class="bg-calculation sticky-header-level-2">L-04</th>
                    <th class="bg-calculation sticky-header-level-2">L-05</th>
                    <th class="bg-calculation sticky-header-level-2">L-06</th>
                    <th class="bg-calculation sticky-header-level-2">L-07</th>
                    <th class="bg-calculation sticky-header-level-2">L-08</th>
                    <th class="bg-calculation sticky-header-level-2">L-09</th>
                    <th class="bg-calculation sticky-header-level-2">L-10</th>
                    <th class="bg-calculation sticky-header-level-2">SPZ-02</th>

                    <!-- INITIAL READINGS A Sub Headers -->
                    <th class="bg-initial sticky-header-level-2">No.Titik</th>
                    <th class="bg-initial sticky-header-level-2">L-01</th>
                    <th class="bg-initial sticky-header-level-2">L-02</th>
                    <th class="bg-initial sticky-header-level-2">L-03</th>
                    <th class="bg-initial sticky-header-level-2">L-04</th>
                    <th class="bg-initial sticky-header-level-2">L-05</th>
                    <th class="bg-initial sticky-header-level-2">L-06</th>
                    <th class="bg-initial sticky-header-level-2">L-07</th>
                    <th class="bg-initial sticky-header-level-2">L-08</th>
                    <th class="bg-initial sticky-header-level-2">L-09</th>
                    <th class="bg-initial sticky-header-level-2">L-10</th>
                    <th class="bg-initial sticky-header-level-2">SPZ-02</th>

                    <!-- INITIAL READINGS B Sub Headers -->
                    <th class="bg-initial sticky-header-level-2">No.Titik</th>
                    <th class="bg-initial sticky-header-level-2">L-01</th>
                    <th class="bg-initial sticky-header-level-2">L-02</th>
                    <th class="bg-initial sticky-header-level-2">L-03</th>
                    <th class="bg-initial sticky-header-level-2">L-04</th>
                    <th class="bg-initial sticky-header-level-2">L-05</th>
                    <th class="bg-initial sticky-header-level-2">L-06</th>
                    <th class="bg-initial sticky-header-level-2">L-07</th>
                    <th class="bg-initial sticky-header-level-2">L-08</th>
                    <th class="bg-initial sticky-header-level-2">L-09</th>
                    <th class="bg-initial sticky-header-level-2">L-10</th>
                    <th class="bg-initial sticky-header-level-2">SPZ-02</th>
                </tr>

                <!-- Row 3: Column Headers -->
                <tr>
                    <!-- BACAAN METRIK Headers -->
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Feet</th>
                    <th rowspan="5" class="bg-metrik sticky-header-level-3">Inch</th>
                    
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation sticky-header-level-3">Elev.Piez</th>
                    <th class="bg-calculation sticky-header-level-3">650.64</th>
                    <th class="bg-calculation sticky-header-level-3">650.66</th>
                    <th class="bg-calculation sticky-header-level-3">616.55</th>
                    <th class="bg-calculation sticky-header-level-3">580.26</th>
                    <th class="bg-calculation sticky-header-level-3">700.76</th>
                    <th class="bg-calculation sticky-header-level-3">690.09</th>
                    <th class="bg-calculation sticky-header-level-3">653.36</th>
                    <th class="bg-calculation sticky-header-level-3">659.14</th>
                    <th class="bg-calculation sticky-header-level-3">622.45</th>
                    <th class="bg-calculation sticky-header-level-3">580.36</th>
                    <th class="bg-calculation sticky-header-level-3">700.08</th>

                    <!-- INITIAL READINGS A Headers -->
                    <th rowspan="5" class="bg-initial sticky-header-level-3">Elev.Piez</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">650.64</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">650.6</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">616.55</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">580.26</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">700.67</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">690.09</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">653.36</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">659.14</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">622.45</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">580.36</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">700.08</th>

                    <!-- INITIAL READINGS B Headers -->
                    <th rowspan="5" class="bg-initial sticky-header-level-3">Elev.Piez</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">71.5</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">73</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">59</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">50</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">62</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">62</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">40</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">55.5</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">57</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">51.5</th>
                    <th rowspan="5" class="bg-initial sticky-header-level-3">70</th>
                </tr>

                <!-- Row 4: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation sticky-header-level-4">Kedalaman</th>
                    <th class="bg-calculation sticky-header-level-4">71.5</th>
                    <th class="bg-calculation sticky-header-level-4">73</th>
                    <th class="bg-calculation sticky-header-level-4">59</th>
                    <th class="bg-calculation sticky-header-level-4">50</th>
                    <th class="bg-calculation sticky-header-level-4">62</th>
                    <th class="bg-calculation sticky-header-level-4">62</th>
                    <th class="bg-calculation sticky-header-level-4">40</th>
                    <th class="bg-calculation sticky-header-level-4">55.5</th>
                    <th class="bg-calculation sticky-header-level-4">57</th>
                    <th class="bg-calculation sticky-header-level-4">51.5</th>
                    <th class="bg-calculation sticky-header-level-4">70</th>
                </tr>

                <!-- Row 5: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th rowspan="3" class="bg-calculation sticky-header-level-5">Record Max/Min</th>
                    <th class="bg-calculation sticky-header-level-5">636.21</th>
                    <th class="bg-calculation sticky-header-level-5">624.41</th>
                    <th class="bg-calculation sticky-header-level-5">603.77</th>
                    <th class="bg-calculation sticky-header-level-5">571.01</th>
                    <th class="bg-calculation sticky-header-level-5">667.89</th>
                    <th class="bg-calculation sticky-header-level-5">635.53</th>
                    <th class="bg-calculation sticky-header-level-5">624.96</th>
                    <th class="bg-calculation sticky-header-level-5">607.32</th>
                    <th class="bg-calculation sticky-header-level-5">582.61</th>
                    <th class="bg-calculation sticky-header-level-5">562.11</th>
                    <th class="bg-calculation sticky-header-level-5">671.18</th>
                </tr>

                <!-- Row 6: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation sticky-header-level-6">638.72</th>
                    <th class="bg-calculation sticky-header-level-6">625.37</th>
                    <th class="bg-calculation sticky-header-level-6">609.03</th>
                    <th class="bg-calculation sticky-header-level-6">578.76</th>
                    <th class="bg-calculation sticky-header-level-6">669.80</th>
                    <th class="bg-calculation sticky-header-level-6">658.30</th>
                    <th class="bg-calculation sticky-header-level-6">638.21</th>
                    <th class="bg-calculation sticky-header-level-6">607.70</th>
                    <th class="bg-calculation sticky-header-level-6">585.89</th>
                    <th class="bg-calculation sticky-header-level-6">563.26</th>
                    <th class="bg-calculation sticky-header-level-6">671.18</th>
                </tr>

                <!-- Row 7: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation sticky-header-level-7">634.65</th>
                    <th class="bg-calculation sticky-header-level-7">618.29</th>
                    <th class="bg-calculation sticky-header-level-7">602.06</th>
                    <th class="bg-calculation sticky-header-level-7">562.76</th>
                    <th class="bg-calculation sticky-header-level-7">666.86</th>
                    <th class="bg-calculation sticky-header-level-7">628.09</th>
                    <th class="bg-calculation sticky-header-level-7">613.36</th>
                    <th class="bg-calculation sticky-header-level-7">603.64</th>
                    <th class="bg-calculation sticky-header-level-7">565.45</th>
                    <th class="bg-calculation sticky-header-level-7">561.07</th>
                    <th class="bg-calculation sticky-header-level-7">630.08</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <?php if(empty($pengukuran)): ?>
                    <tr>
                        <td colspan="105" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data Piezometer yang tersedia</p>
                            <a href="<?= base_url('left-piez/create') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $titikList = ['L_01', 'L_02', 'L_03', 'L_04', 'L_05', 'L_06', 'L_07', 'L_08', 'L_09', 'L_10', 'SPZ_02'];
                    ?>
                    
                    <?php foreach($pengukuran as $item): 
                        $p = $item['pengukuran'];
                        $metrik = $item['metrik'] ?? [];
                        $initialA = $item['initial_a'] ?? [];
                        $initialB = $item['initial_b'] ?? [];
                        $perhitungan = $item['perhitungan'] ?? [];
                        $pembacaan = $item['pembacaan'] ?? [];
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>">
                        <!-- Basic Information -->
                        <td class="sticky"><?= esc($p['tahun'] ?? '-') ?></td>
                        <td class="sticky-2"><?= esc($p['periode'] ?? '-') ?></td>
                        <td class="sticky-3"><?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?></td>
                        <td class="sticky-4 bg-info-column"><?= esc($p['dma'] ?? '-') ?></td>
                        <td class="sticky-5 bg-info-column"><?= esc($p['temp_id'] ?? '-') ?></td>
                        
                        <!-- BACAAN METRIK - Feet & Inch (dari tabel t_pembacaan) -->
                        <?php foreach($titikList as $titik): 
                            $bacaanData = $pembacaan[$titik] ?? [];
                            $feet = $bacaanData['feet'] ?? null;
                            $inch = $bacaanData['inch'] ?? null;
                        ?>
                            <td class="number-cell bg-metrik"><?= formatNumber($feet, 0) ?></td>
                            <td class="number-cell bg-metrik"><?= formatNumber($inch) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- KONVERSI STATIS -->
                        <td class="number-cell bg-calculation">0.3048</td>
                        <td class="number-cell bg-calculation">0.0254</td>
                        
                        <!-- BACAAN PIEZOMETER METRIK (dari tabel b_piezo_metrik) -->
                        <?php foreach($titikList as $titik): 
                            $meter = $metrik[$titik] ?? null;
                        ?>
                            <td class="number-cell bg-reading"><?= formatNumber($meter) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- PERHITUNGAN PIEZOMETER - 12 kolom -->
                        <!-- Kolom 1: Elev.Piez -->
                        <td class="number-cell bg-calculation">Elev.Piez</td>
                        
                        <!-- Kolom 2-12: Data untuk L-01 sampai SPZ-02 -->
                        <?php foreach($titikList as $titik): 
                            $perhitunganData = $perhitungan[$titik] ?? [];
                            $elvPiez = $perhitunganData['Elv_Piez'] ?? '-';
                        ?>
                            <td class="number-cell bg-calculation"><?= formatNumber($elvPiez) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- INITIAL READINGS A - 12 kolom -->
                        <!-- Kolom 1: Elev.Piez -->
                        <td class="number-cell bg-initial">Elev.Piez</td>
                        
                        <!-- Kolom 2-12: Data untuk L-01 sampai SPZ-02 -->
                        <?php foreach($titikList as $titik): 
                            $initialAData = $initialA[$titik] ?? [];
                            $elvPiezA = $initialAData['Elv_Piez'] ?? '-';
                        ?>
                            <td class="number-cell bg-initial"><?= formatNumber($elvPiezA) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- INITIAL READINGS B - 12 kolom -->
                        <!-- Kolom 1: Elev.Piez -->
                        <td class="number-cell bg-initial">Elev.Piez</td>
                        
                        <!-- Kolom 2-12: Data untuk L-01 sampai SPZ-02 -->
                        <?php foreach($titikList as $titik): 
                            $initialBData = $initialB[$titik] ?? [];
                            $elvPiezB = $initialBData['Elv_Piez'] ?? '-';
                        ?>
                            <td class="number-cell bg-initial"><?= formatNumber($elvPiezB) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- ACTION BUTTONS -->
                        <td class="action-cell">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="<?= base_url('left-piez/edit/' . $p['id_pengukuran']) ?>" 
                                   class="btn-action btn-edit" title="Edit Data">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button type="button" class="btn-action btn-delete delete-data" 
                                        data-id="<?= $p['id_pengukuran'] ?>" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Scroll Indicator -->
<div class="scroll-indicator" id="scrollIndicator">
    <i class="fas fa-arrows-alt-h me-1"></i>
    <span id="scrollText">Scroll untuk melihat lebih banyak data</span>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data Piezometer ini?</p>
                <p class="text-muted small">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import SQL -->
<div class="modal fade" id="importSqlModal" tabindex="-1" aria-labelledby="importSqlModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSqlModalLabel">
                    <i class="fas fa-database me-2"></i>Import SQL Piezometer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Upload file SQL yang berisi data piezometer.
                </div>
                
                <div class="mb-3">
                    <label for="sqlFile" class="form-label">Pilih File SQL</label>
                    <input class="form-control" type="file" id="sqlFile" accept=".sql">
                    <div class="form-text">
                        Format file: .sql (Maksimal 50MB)
                    </div>
                </div>
                
                <div class="progress mb-3" style="display: none;" id="importProgress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                
                <div id="importStatus" class="alert" style="display: none;"></div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Data yang Akan Diimpor</h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">✅ Tabel Piezometer yang didukung:</p>
                        <div class="row">
                            <div class="col-6">
                                <ul class="mb-1">
                                    <li>Data Pengukuran</li>
                                    <li>Bacaan Metrik</li>
                                    <li>Initial Reading A</li>
                                    <li>Initial Reading B</li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="mb-1">
                                    <li>Perhitungan L-01 s/d L-10</li>
                                    <li>Perhitungan SPZ-02</li>
                                    <li>Data Pembacaan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnImportSQL">
                    <i class="fas fa-upload me-1"></i> Import SQL
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Fungsi untuk mengatur ulang tinggi header secara dinamis
function recalculateHeaderHeights() {
    const thead = document.querySelector('.table thead');
    if (!thead) return;
    
    const rows = thead.querySelectorAll('tr');
    let accumulatedHeight = 0;
    
    rows.forEach((row, index) => {
        const rowHeight = row.offsetHeight || 56; // Default 56px jika tidak terdeteksi
        
        // Update semua th dalam row ini
        const ths = row.querySelectorAll('th');
        ths.forEach(th => {
            if (th.classList.contains('sticky-header')) {
                th.style.top = accumulatedHeight + 'px';
            } else if (th.classList.contains('sticky-header-level-2')) {
                th.style.top = accumulatedHeight + 'px';
            } else if (th.classList.contains('sticky-header-level-3')) {
                th.style.top = accumulatedHeight + 'px';
            } else if (th.classList.contains('sticky-header-level-4')) {
                th.style.top = accumulatedHeight + 'px';
            } else if (th.classList.contains('sticky-header-level-5')) {
                th.style.top = accumulatedHeight + 'px';
            } else if (th.classList.contains('sticky-header-level-6')) {
                th.style.top = accumulatedHeight + 'px';
            } else if (th.classList.contains('sticky-header-level-7')) {
                th.style.top = accumulatedHeight + 'px';
            }
            
            // Untuk action cell khusus
            if (th.classList.contains('action-cell')) {
                if (index === 0) th.style.top = accumulatedHeight + 'px';
                else if (index === 1) th.style.top = accumulatedHeight + 'px';
                else if (index === 2) th.style.top = accumulatedHeight + 'px';
                else if (index === 3) th.style.top = accumulatedHeight + 'px';
                else if (index === 4) th.style.top = accumulatedHeight + 'px';
                else if (index === 5) th.style.top = accumulatedHeight + 'px';
                else if (index === 6) th.style.top = accumulatedHeight + 'px';
            }
        });
        
        accumulatedHeight += rowHeight;
    });
}

// Fungsi untuk memastikan kolom aksi selalu terlihat
function ensureActionColumnVisible() {
    const tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        // Scroll ke kanan untuk memastikan kolom aksi terlihat
        setTimeout(() => {
            tableContainer.scrollLeft = tableContainer.scrollWidth;
        }, 100);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Jalankan fungsi recalculate header heights
    recalculateHeaderHeights();
    
    // Recalculate ketika window di-resize
    window.addEventListener('resize', recalculateHeaderHeights);
    
    // Recalculate setelah font loading
    if (document.fonts) {
        document.fonts.ready.then(recalculateHeaderHeights);
    }

    // Juga jalankan setelah delay untuk memastikan tabel sudah render sempurna
    setTimeout(recalculateHeaderHeights, 100);
    setTimeout(recalculateHeaderHeights, 500);

    // Pastikan kolom aksi terlihat
    ensureActionColumnVisible();
    setTimeout(ensureActionColumnVisible, 300);

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
        this.disabled = true;

        setTimeout(() => {
            try {
                const table = document.getElementById('exportTable');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Data Piezometer"});
                
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const filename = `Piezometer_Data_Export_${timestamp}.xlsx`;
                
                XLSX.writeFile(wb, filename);
                
                setTimeout(() => {
                    alert('Export berhasil! File: ' + filename);
                }, 500);
                
            } catch (error) {
                console.error('Error exporting to Excel:', error);
                alert('Terjadi kesalahan saat mengexport data: ' + error.message);
            } finally {
                this.innerHTML = originalText;
                this.disabled = false;
            }
        }, 1000);
    });

    // Delete Data
    let deleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.querySelectorAll('.delete-data').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteId = this.getAttribute('data-id');
            deleteModal.show();
        });
    });

    // PERBAIKAN: Confirm Delete dengan fetch yang benar
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const deleteButton = this;
            const originalText = deleteButton.innerHTML;
            
            // Tampilkan loading state
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';

            fetch('<?= base_url('left-piez/delete') ?>/' + deleteId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Tampilkan pesan sukses
                    showToast('success', 'Data berhasil dihapus');
                    
                    // Refresh halaman setelah 1 detik
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menghapus data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan: ' + error.message);
            })
            .finally(() => {
                deleteModal.hide();
                // Reset button state setelah modal tertutup
                setTimeout(() => {
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = originalText;
                }, 500);
            });
        }
    });

    // Fungsi untuk menampilkan toast notification
    function showToast(type, message) {
        // Buat elemen toast
        const toastContainer = document.createElement('div');
        toastContainer.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed top-0 end-0 m-3`;
        toastContainer.style.zIndex = '9999';
        
        toastContainer.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toastContainer);
        
        const toast = new bootstrap.Toast(toastContainer);
        toast.show();
        
        // Hapus elemen setelah toast hilang
        toastContainer.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toastContainer);
        });
    }

    // Filter Functionality
    const tahunFilter = document.getElementById('tahunFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const dmaFilter = document.getElementById('dmaFilter');
    const titikFilter = document.getElementById('titikFilter');
    const searchInput = document.getElementById('searchInput');
    const resetFilter = document.getElementById('resetFilter');

    function filterTable() {
        const tahunValue = tahunFilter.value.toLowerCase();
        const periodeValue = periodeFilter.value.toLowerCase();
        const dmaValue = dmaFilter.value.toLowerCase();
        const titikValue = titikFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();

        const rows = document.querySelectorAll('#dataTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.text-center')) return;
            
            const tahun = row.cells[0].textContent.toLowerCase();
            const periode = row.cells[1].textContent.toLowerCase();
            const dma = row.cells[3].textContent.toLowerCase();
            const rowText = row.textContent.toLowerCase();

            const tahunMatch = !tahunValue || tahun === tahunValue;
            const periodeMatch = !periodeValue || periode === periodeValue;
            const dmaMatch = !dmaValue || dma === dmaValue;
            const titikMatch = !titikValue || rowText.includes(titikValue);
            const searchMatch = !searchValue || rowText.includes(searchValue);

            row.style.display = tahunMatch && periodeMatch && dmaMatch && titikMatch && searchMatch ? '' : 'none';
        });
    }

    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    dmaFilter.addEventListener('change', filterTable);
    titikFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
        periodeFilter.value = '';
        dmaFilter.value = '';
        titikFilter.value = '';
        searchInput.value = '';
        filterTable();
    });

    // Scroll Indicator
    const scrollIndicator = document.getElementById('scrollIndicator');
    const tableContainer = document.getElementById('tableContainer');
    
    let scrollTimeout;
    tableContainer.addEventListener('scroll', function() {
        const { scrollLeft, scrollWidth, clientWidth } = this;
        const showHorizontal = scrollLeft > 0 || scrollLeft + clientWidth < scrollWidth;
        
        if (showHorizontal) {
            document.getElementById('scrollText').textContent = 'Scroll horizontal untuk melihat lebih banyak data';
            scrollIndicator.style.display = 'block';
        } else {
            scrollIndicator.style.display = 'none';
        }

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            scrollIndicator.style.display = 'none';
        }, 2000);
    });

    // Import SQL Functionality
    document.getElementById('btnImportSQL').addEventListener('click', function() {
        const sqlFileInput = document.getElementById('sqlFile');
        const importProgress = document.getElementById('importProgress');
        const importStatus = document.getElementById('importStatus');
        const btnImport = this;

        importStatus.style.display = 'none';

        if (!sqlFileInput.files || sqlFileInput.files.length === 0) {
            showImportStatus('❌ Pilih file SQL terlebih dahulu', 'danger');
            return;
        }

        const file = sqlFileInput.files[0];
        const fileName = file.name.toLowerCase();
        
        if (!fileName.endsWith('.sql')) {
            showImportStatus('❌ File harus berformat .sql', 'danger');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            showImportStatus('❌ Ukuran file maksimal 50MB', 'danger');
            return;
        }

        // Progress Bar
        importProgress.style.display = 'block';
        const progressBar = importProgress.querySelector('.progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        btnImport.disabled = true;
        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

        const formData = new FormData();
        formData.append('sql_file', file);

        // Simulasi progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 2;
            if (progress <= 80) {
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
            }
        }, 100);

        // Fetch API
        fetch('<?= base_url('left-piez/import-sql') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            
            if (data.success) {
                showImportStatus('✅ ' + data.message, 'success');
                
                // Auto-refresh setelah sukses
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importSqlModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                }, 3000);
            } else {
                showImportStatus('❌ ' + data.message, 'danger');
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('Import error:', error);
            showImportStatus('❌ Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            setTimeout(() => {
                btnImport.disabled = false;
                btnImport.innerHTML = '<i class="fas fa-upload me-1"></i> Import';
            }, 2000);
        });

        function showImportStatus(message, type) {
            importStatus.style.display = 'block';
            importStatus.className = `alert alert-${type}`;
            importStatus.innerHTML = message;
        }
    });

    // Reset form ketika modal import ditutup
    document.getElementById('importSqlModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('sqlFile').value = '';
        document.getElementById('importProgress').style.display = 'none';
        document.getElementById('importStatus').style.display = 'none';
    });

    // Auto-scroll untuk memastikan kolom aksi terlihat
    setTimeout(() => {
        const tableContainer = document.getElementById('tableContainer');
        if (tableContainer) {
            tableContainer.scrollLeft = tableContainer.scrollWidth;
        }
    }, 100);
});

// Function untuk menampilkan modal import
function showImportModal() {
    const modal = new bootstrap.Modal(document.getElementById('importSqlModal'));
    modal.show();
}
</script>
</body>
</html>