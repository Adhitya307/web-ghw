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
        .action-cell {
            position: sticky;
            right: 0;
            background: white;
            z-index: 10;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            padding: 8px 5px;
            min-width: 80px;
        }
        
        .action-header {
            position: sticky;
            right: 0;
            background-color: #e3f2fd !important;
            border: 1px solid #dee2e6;
            z-index: 10;
            color: #2c3e50 !important;
        }
        
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
            margin: 0 2px;
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
        
        .btn-disabled {
            color: #6c757d;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            cursor: pointer;
        }
        
        .btn-disabled:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            transform: translateY(0);
        }
        
        .tooltip-inner {
            font-size: 12px;
            padding: 4px 8px;
        }
        
        /* Modal peringatan hak akses */
        .modal-access .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .modal-access .modal-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding: 20px 30px;
            position: relative;
        }
        
        .modal-access .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #3498db 0%, #2c3e50 100%);
        }
        
        .modal-access .modal-header .btn-close {
            color: #6c757d;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .modal-access .modal-header .btn-close:hover {
            opacity: 1;
        }
        
        .modal-access .modal-body {
            padding: 30px;
            text-align: center;
        }
        
        .modal-access .access-icon-container {
            margin-bottom: 25px;
        }
        
        .modal-access .access-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2);
        }
        
        .modal-access .access-icon i {
            font-size: 28px;
            color: white;
        }
        
        .modal-access .access-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .modal-access .access-message {
            color: #5d6d7e;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 25px;
        }
        
        .modal-access .user-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .modal-access .user-role-badge i {
            color: #3498db;
        }
        
        .modal-access .access-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: left;
            border-left: 4px solid #3498db;
        }
        
        .modal-access .access-details h6 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 15px;
        }
        
        .modal-access .access-details ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .modal-access .access-details li {
            padding: 6px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: #5d6d7e;
        }
        
        .modal-access .access-details li i {
            color: #27ae60;
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .modal-access .access-note {
            color: #7f8c8d;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .modal-access .access-note i {
            color: #e74c3c;
            margin-right: 5px;
        }
        
        .modal-access .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 20px 30px;
            background: #f8f9fa;
        }
        
        .modal-access .btn-understand {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 140px;
        }
        
        .modal-access .btn-understand:hover {
            background: linear-gradient(135deg, #2980b9 0%, #2c3e50 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        }
        
        /* STICKY COLUMNS */
        .sticky { 
            position: sticky; 
            left: 0; 
            background: white; 
            z-index: 5; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky-2 { 
            position: sticky; 
            left: 80px; 
            background: white; 
            z-index: 5; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky-3 { 
            position: sticky; 
            left: 160px; 
            background: white; 
            z-index: 5; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky-4 { 
            position: sticky; 
            left: 240px; 
            background: white; 
            z-index: 5; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky-5 { 
            position: sticky; 
            left: 320px; 
            background: white; 
            z-index: 5; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        /* HEADER TABLE */
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
            position: relative;
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
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 600px;
            overflow: auto;
        }
        
        /* Warna Header */
        .bg-reading { background-color: #e8f4fd !important; color: #2c3e50 !important; font-weight: 600; }
        .bg-calculation { background-color: #f0f9eb !important; color: #2c3e50 !important; font-weight: 600; }
        .bg-initial { background-color: #e6ffed !important; color: #2c3e50 !important; font-weight: 600; }
        .bg-action { background-color: #e3f2fd !important; color: #2c3e50 !important; font-weight: 600; }
        .bg-metrik { background-color: #fff2cc !important; color: #2c3e50 !important; font-weight: 600; }
        .bg-info-column { background-color: #e7f1ff !important; color: #2c3e50 !important; font-weight: 600; }
        
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
            z-index: 1000;
            display: none;
        }
        
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .scientific-notation {
            font-size: 0.7rem;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .user-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #0d6efd;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }
        
        /* CSS untuk filter rowspan */
        .year-header {
            vertical-align: top;
        }
        
        .data-row {
            transition: all 0.3s ease;
        }
        
        .data-row.hidden {
            display: none;
        }
        
        /* Perbaikan untuk sticky columns saat di-scroll */
        .table-responsive {
            position: relative;
        }
        
        /* Perbaikan border untuk konsistensi */
        .data-table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .data-table td,
        .data-table th {
            border: 1px solid #dee2e6 !important;
        }
        
        /* Untuk filter */
        .hidden-row {
            display: none !important;
        }
        
        .visible-row {
            display: table-row !important;
        }
        
        .filtered-year {
            display: table-cell !important;
        }
    </style>
</head>
<body>
<?php
// Cek session dan role
$session = session();
$isLoggedIn = $session->get('isLoggedIn');
$role = $session->get('role');
$isAdmin = $role == 'admin';
$username = $session->get('username');
$fullName = $session->get('fullName');
?>

<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- User Info -->
    <div class="user-info mb-3 p-3 rounded">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user-circle me-2"></i>
                <strong><?= esc($fullName ?? $username) ?></strong>
                <span class="badge <?= $isAdmin ? 'badge-admin' : 'badge-user' ?> ms-2">
                    <?= $isAdmin ? 'Administrator' : 'User' ?>
                </span>
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-id-card me-1"></i>Username: <?= esc($username) ?>
                </small>
            </div>
            <div>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <?= date('d F Y H:i:s') ?>
                </small>
            </div>
        </div>
    </div>

    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-water me-2"></i>Piezometer - Left Bank
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('left-piez') ?>" class="btn btn-primary btn-piez">
                <i class="fas fa-table"></i> Left Bank
            </a>
            
            <!-- Tombol Grafik History -->
            <a href="<?= base_url('left_piez/grafik-history-l1-l3') ?>" class="btn btn-outline-primary btn-piez">Grafik History L1-L3</a>
            <a href="<?= base_url('left_piez/grafik-history-l4-l6') ?>" class="btn btn-outline-primary btn-piez">Grafik History L4-L6</a>
            <a href="<?= base_url('left_piez/grafik-history-l7-l9') ?>" class="btn btn-outline-primary btn-piez">Grafik History L7-L9</a>
            <a href="<?= base_url('left_piez/grafik-history-l10-spz02') ?>" class="btn btn-outline-primary btn-piez">Grafik History L10-SPZ02</a>
            
            <?php if ($isAdmin): ?>
                <button type="button" class="btn btn-outline-success" id="addData">
                    <i class="fas fa-plus me-1"></i> Add Data
                </button>
                
                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-database me-1"></i> Import SQL
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-outline-success btn-disabled" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Klik untuk melihat informasi hak akses"
                       onclick="showAccessWarning('add')">
                    <i class="fas fa-plus me-1"></i> Add Data
                </button>
                
                <button type="button" class="btn btn-outline-warning btn-disabled"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Klik untuk melihat informasi hak akses"
                       onclick="showAccessWarning('import')">
                    <i class="fas fa-database me-1"></i> Import SQL
                </button>
            <?php endif; ?>
            
            <button type="button" class="btn btn-outline-info" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
        </div>

        <div class="table-controls">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
            </div>
        </div>
    </div>

    <!-- Modal Import SQL (Hanya untuk Admin) -->
    <?php if ($isAdmin): ?>
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-database me-2"></i>Import Database SQL
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Upload file SQL yang telah diexport dari aplikasi Android. File akan diproses dan data akan diimpor ke database.
                    </div>
                    
                    <div class="mb-3">
                        <label for="sqlFile" class="form-label">Pilih File SQL</label>
                        <input class="form-control" type="file" id="sqlFile" accept=".sql">
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
                                        <li>Data Pembacaan</li>
                                        <li>Data Metrik</li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="mb-1">
                                        <li>Initial Reading A</li>
                                        <li>Initial Reading B</li>
                                        <li>Data Perhitungan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnImportSQL">
                        <i class="fas fa-upload me-1"></i> Import
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal Peringatan Hak Akses -->
    <div class="modal fade modal-access" id="accessWarningModal" tabindex="-1" aria-labelledby="accessWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accessWarningModalLabel">
                        <i class="fas fa-shield-alt me-2"></i>Pengaturan Akses Piezometer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="access-icon-container">
                        <div class="access-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    
                    <h3 class="access-title" id="warningTitle"></h3>
                    
                    <p class="access-message" id="warningMessage"></p>
                    
                    <div class="user-role-badge">
                        <i class="fas fa-user-tag"></i>
                        <span>Level Akses: <strong><?= $isAdmin ? 'Administrator' : 'Pengguna Biasa' ?></strong></span>
                    </div>
                    
                    <div class="access-details">
                        <h6>Hak Akses yang Tersedia:</h6>
                        <ul>
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data Piezometer</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Mengakses semua titik (L-01 s/d SPZ-02)</li>
                            <li><i class="fas fa-check"></i> Melihat grafik history data</li>
                        </ul>
                    </div>
                    
                    <div class="access-note">
                        <i class="fas fa-info-circle"></i>
                        Untuk meminta akses tambahan, silakan hubungi Administrator sistem.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-understand" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i> Mengerti
                    </button>
                </div>
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
                        sort($uniqueYears); // ASC untuk tahun
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

            <!-- DMA -->
            <div class="filter-item">
                <label for="dmaFilter" class="form-label">DMA</label>
                <select id="dmaFilter" class="form-select">
                    <option value="">Semua DMA</option>
                    <?php
                    if (!empty($pengukuran)):
                        $uniqueDMA = [];
                        foreach ($pengukuran as $item) {
                            $dma = $item['pengukuran']['dma'] ?? '-';
                            if ($dma !== '-' && !in_array($dma, $uniqueDMA)) {
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

            <!-- Reset -->
            <div class="filter-item" style="align-self: flex-end;">
                <button id="resetFilter" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-1"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat data...</p>
    </div>

    <!-- Main Table -->
    <div class="table-responsive" id="tableContainer">
        <table class="data-table table table-bordered table-hover" id="exportTable">
            <thead>
                <!-- Row 1: Main Header -->
                <tr>
                    <th rowspan="7" class="sticky year-header">TAHUN</th>
                    <th rowspan="7" class="sticky-2 year-header">PERIODE</th>
                    <th rowspan="7" class="sticky-3 year-header">TANGGAL</th>
                    <th rowspan="7" class="sticky-4 bg-info-column">DMA</th>
                    <th rowspan="7" class="sticky-5 bg-info-column">CH BULANAN</th>
                    
                    <!-- BACAAN METRIK -->
                    <th colspan="22" class="bg-metrik">BACAAN METRIK</th>

                    <!-- KONVERSI -->
                    <th colspan="2" class="bg-calculation">KONVERSI</th>

                    <!-- BACAAN PIEZOMETER METRIK -->
                    <th colspan="11" class="bg-reading">BACAAN PIEZOMETER METRIK</th>
                    
                    <!-- PERHITUNGAN -->
                    <th colspan="12" class="bg-calculation">PERHITUNGAN PIEZOMETER</th>

                    <!-- INITIAL READINGS -->
                    <th colspan="12" class="bg-initial">INITIAL READINGS A</th>

                    <!-- INITIAL READINGS -->
                    <th colspan="12" class="bg-initial">INITIAL READINGS B</th>
                    
                    <!-- KOLOM AKSI -->
                    <?php if($isAdmin): ?>
                    <th rowspan="7" class="action-header bg-action">AKSI</th>
                    <?php else: ?>
                    <th rowspan="7" class="action-header bg-action">AKSI</th>
                    <?php endif; ?>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- BACAAN METRIK Sub Headers -->
                    <th colspan="2" class="bg-metrik">L-01</th>
                    <th colspan="2" class="bg-metrik">L-02</th>
                    <th colspan="2" class="bg-metrik">L-03</th>
                    <th colspan="2" class="bg-metrik">L-04</th>
                    <th colspan="2" class="bg-metrik">L-05</th>
                    <th colspan="2" class="bg-metrik">L-06</th>
                    <th colspan="2" class="bg-metrik">L-07</th>
                    <th colspan="2" class="bg-metrik">L-08</th>
                    <th colspan="2" class="bg-metrik">L-09</th>
                    <th colspan="2" class="bg-metrik">L-10</th>
                    <th colspan="2" class="bg-metrik">SPZ-02</th>
                    
                    <!-- KONVERSI Sub Headers -->
                    <th rowspan="6" class="bg-calculation">FEET → M</th>
                    <th rowspan="6" class="bg-calculation">INCH → M</th>
                    
                    <!-- BACAAN PIEZOMETER METRIK Sub Headers -->
                    <th rowspan="6" class="bg-reading">L-01</th>
                    <th rowspan="6" class="bg-reading">L-02</th>
                    <th rowspan="6" class="bg-reading">L-03</th>
                    <th rowspan="6" class="bg-reading">L-04</th>
                    <th rowspan="6" class="bg-reading">L-05</th>
                    <th rowspan="6" class="bg-reading">L-06</th>
                    <th rowspan="6" class="bg-reading">L-07</th>
                    <th rowspan="6" class="bg-reading">L-08</th>
                    <th rowspan="6" class="bg-reading">L-09</th>
                    <th rowspan="6" class="bg-reading">L-10</th>
                    <th rowspan="6" class="bg-reading">SPZ-02</th>

                    <!-- PERHITUNGAN Sub Headers -->
                    <th class="bg-calculation">No.Titik</th>
                    <th class="bg-calculation">L-01</th>
                    <th class="bg-calculation">L-02</th>
                    <th class="bg-calculation">L-03</th>
                    <th class="bg-calculation">L-04</th>
                    <th class="bg-calculation">L-05</th>
                    <th class="bg-calculation">L-06</th>
                    <th class="bg-calculation">L-07</th>
                    <th class="bg-calculation">L-08</th>
                    <th class="bg-calculation">L-09</th>
                    <th class="bg-calculation">L-10</th>
                    <th class="bg-calculation">SPZ-02</th>

                    <!-- INITIAL READINGS A Sub Headers -->
                    <th class="bg-initial">No.Titik</th>
                    <th class="bg-initial">L-01</th>
                    <th class="bg-initial">L-02</th>
                    <th class="bg-initial">L-03</th>
                    <th class="bg-initial">L-04</th>
                    <th class="bg-initial">L-05</th>
                    <th class="bg-initial">L-06</th>
                    <th class="bg-initial">L-07</th>
                    <th class="bg-initial">L-08</th>
                    <th class="bg-initial">L-09</th>
                    <th class="bg-initial">L-10</th>
                    <th class="bg-initial">SPZ-02</th>

                    <!-- INITIAL READINGS B Sub Headers -->
                    <th class="bg-initial">No.Titik</th>
                    <th class="bg-initial">L-01</th>
                    <th class="bg-initial">L-02</th>
                    <th class="bg-initial">L-03</th>
                    <th class="bg-initial">L-04</th>
                    <th class="bg-initial">L-05</th>
                    <th class="bg-initial">L-06</th>
                    <th class="bg-initial">L-07</th>
                    <th class="bg-initial">L-08</th>
                    <th class="bg-initial">L-09</th>
                    <th class="bg-initial">L-10</th>
                    <th class="bg-initial">SPZ-02</th>
                </tr>

                <!-- Row 3: Column Headers -->
                <tr>
                    <!-- BACAAN METRIK Headers -->
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    <th rowspan="5" class="bg-metrik">Feet</th>
                    <th rowspan="5" class="bg-metrik">Inch</th>
                    
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation">Elev.Piez</th>
                    <th class="bg-calculation">650.64</th>
                    <th class="bg-calculation">650.66</th>
                    <th class="bg-calculation">616.55</th>
                    <th class="bg-calculation">580.26</th>
                    <th class="bg-calculation">700.76</th>
                    <th class="bg-calculation">690.09</th>
                    <th class="bg-calculation">653.36</th>
                    <th class="bg-calculation">659.14</th>
                    <th class="bg-calculation">622.45</th>
                    <th class="bg-calculation">580.36</th>
                    <th class="bg-calculation">700.08</th>

                    <!-- INITIAL READINGS A Headers -->
                    <th rowspan="5" class="bg-initial">Elev.Piez</th>
                    <th rowspan="5" class="bg-initial">650.64</th>
                    <th rowspan="5" class="bg-initial">650.6</th>
                    <th rowspan="5" class="bg-initial">616.55</th>
                    <th rowspan="5" class="bg-initial">580.26</th>
                    <th rowspan="5" class="bg-initial">700.76</th>
                    <th rowspan="5" class="bg-initial">690.09</th>
                    <th rowspan="5" class="bg-initial">653.36</th>
                    <th rowspan="5" class="bg-initial">659.14</th>
                    <th rowspan="5" class="bg-initial">622.45</th>
                    <th rowspan="5" class="bg-initial">580.36</th>
                    <th rowspan="5" class="bg-initial">700.08</th>

                    <!-- INITIAL READINGS B Headers -->
                    <th rowspan="5" class="bg-initial">Elev.Piez</th>
                    <th rowspan="5" class="bg-initial">71.5</th>
                    <th rowspan="5" class="bg-initial">73</th>
                    <th rowspan="5" class="bg-initial">59</th>
                    <th rowspan="5" class="bg-initial">50</th>
                    <th rowspan="5" class="bg-initial">62</th>
                    <th rowspan="5" class="bg-initial">62</th>
                    <th rowspan="5" class="bg-initial">40</th>
                    <th rowspan="5" class="bg-initial">55.5</th>
                    <th rowspan="5" class="bg-initial">57</th>
                    <th rowspan="5" class="bg-initial">51.5</th>
                    <th rowspan="5" class="bg-initial">70</th>
                </tr>

                <!-- Row 4: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation">Kedalaman</th>
                    <th class="bg-calculation">71.5</th>
                    <th class="bg-calculation">73</th>
                    <th class="bg-calculation">59</th>
                    <th class="bg-calculation">50</th>
                    <th class="bg-calculation">62</th>
                    <th class="bg-calculation">62</th>
                    <th class="bg-calculation">40</th>
                    <th class="bg-calculation">55.5</th>
                    <th class="bg-calculation">57</th>
                    <th class="bg-calculation">51.5</th>
                    <th class="bg-calculation">70</th>
                </tr>

                <!-- Row 5: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th rowspan="3" class="bg-calculation">Record Max/Min</th>
                    <th class="bg-calculation">636.21</th>
                    <th class="bg-calculation">624.41</th>
                    <th class="bg-calculation">603.77</th>
                    <th class="bg-calculation">571.01</th>
                    <th class="bg-calculation">667.89</th>
                    <th class="bg-calculation">635.53</th>
                    <th class="bg-calculation">624.96</th>
                    <th class="bg-calculation">607.32</th>
                    <th class="bg-calculation">582.61</th>
                    <th class="bg-calculation">562.11</th>
                    <th class="bg-calculation">671.18</th>
                </tr>

                <!-- Row 6: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation">638.72</th>
                    <th class="bg-calculation">625.37</th>
                    <th class="bg-calculation">609.03</th>
                    <th class="bg-calculation">578.76</th>
                    <th class="bg-calculation">669.80</th>
                    <th class="bg-calculation">658.30</th>
                    <th class="bg-calculation">638.21</th>
                    <th class="bg-calculation">607.70</th>
                    <th class="bg-calculation">585.89</th>
                    <th class="bg-calculation">563.26</th>
                    <th class="bg-calculation">671.18</th>
                </tr>

                <!-- Row 7: Column Headers -->
                <tr>
                    <!-- PERHITUNGAN Headers -->
                    <th class="bg-calculation">634.65</th>
                    <th class="bg-calculation">618.29</th>
                    <th class="bg-calculation">602.06</th>
                    <th class="bg-calculation">562.76</th>
                    <th class="bg-calculation">666.86</th>
                    <th class="bg-calculation">628.09</th>
                    <th class="bg-calculation">613.36</th>
                    <th class="bg-calculation">603.64</th>
                    <th class="bg-calculation">565.45</th>
                    <th class="bg-calculation">561.07</th>
                    <th class="bg-calculation">630.08</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal (Hanya untuk Admin) -->
<?php if ($isAdmin): ?>
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
<?php endif; ?>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
// Data dan state management
let allData = <?= json_encode($pengukuran ?? []) ?>;
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
let deleteId = null;
let originalTableHTML = null; // Untuk menyimpan struktur tabel asli

// Variabel global untuk modal hak akses
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

// Variabel untuk filter
let tahunFilter, periodeFilter, dmaFilter, searchInput, resetFilter;

// ============ FUNGSI HAK AKSES ============
function showAccessWarning(actionType, tahun = null, periode = null, tanggal = null) {
    let title = '';
    let message = '';
    
    switch(actionType) {
        case 'add':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penambahan data Piezometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'edit':
            title = 'Akses Tidak Tersedia';
            message = `Fitur pengeditan data Piezometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'delete':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penghapusan data Piezometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'import':
            title = 'Akses Tidak Tersedia';
            message = `Fitur import database Piezometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        default:
            title = 'Akses Tidak Tersedia';
            message = `Fitur ini tidak dapat diakses dengan level pengguna saat ini.`;
    }
    
    warningTitle.textContent = title;
    warningMessage.innerHTML = message;
    accessWarningModal.show();
}

// ============ FUNGSI FORMAT ANGKA ============
function formatNumber(number) {
    if (number === null || number === '' || number === undefined) {
        return '-';
    }
    
    if (typeof number === 'string') {
        number = parseFloat(number);
    }
    
    if (isNaN(number)) {
        return '-';
    }
    
    // Tampilkan dengan format yang sesuai
    if (Math.abs(number) < 0.0001 && number != 0) {
        return '<span class="scientific-notation">' + number.toExponential(4) + '</span>';
    }
    
    // Untuk angka desimal, tampilkan maksimal 4 digit desimal
    const formatted = Number.isInteger(number) ? number.toString() : number.toFixed(4);
    return formatted.replace(/(\.0*|(?<=\.\d*?)0+)$/, '');
}

// ============ FUNGSI TAMPILKAN DATA MENTAH ============
function displayRawData(value) {
    if (value === null || value === '' || value === undefined || value === '-') {
        return '-';
    }
    
    if (typeof value === 'string') {
        // Coba konversi ke number jika mungkin
        const numValue = parseFloat(value);
        if (!isNaN(numValue)) {
            value = numValue;
        }
    }
    
    if (typeof value === 'number') {
        // Hilangkan trailing zeros yang tidak perlu
        const strValue = value.toString();
        if (strValue.includes('.')) {
            return strValue.replace(/(\.0*|(?<=\.\d*?)0+)$/, '');
        }
        return strValue;
    }
    
    return value;
}

// ============ FUNGSI FILTER YANG DIPERBAIKI ============
function initializeFilter() {
    tahunFilter = document.getElementById('tahunFilter');
    periodeFilter = document.getElementById('periodeFilter');
    dmaFilter = document.getElementById('dmaFilter');
    searchInput = document.getElementById('searchInput');
    resetFilter = document.getElementById('resetFilter');
    
    // Event listeners untuk filter
    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    dmaFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    resetFilter.addEventListener('click', resetAllFilters);
}

function filterTable() {
    const tahunValue = tahunFilter.value.toLowerCase();
    const periodeValue = periodeFilter.value.toLowerCase();
    const dmaValue = dmaFilter.value.toLowerCase();
    const searchValue = searchInput.value.toLowerCase();
    
    const rows = document.querySelectorAll('#dataTableBody tr:not(.no-data)');
    const yearGroups = {};
    let currentYear = null;
    let yearStartRow = null;
    let yearGroupIndex = 0;
    
    // Fase 1: Kelompokkan baris berdasarkan tahun
    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        
        const tahunCell = row.querySelector('td.sticky');
        if (tahunCell) {
            // Ini adalah baris pertama dari grup tahun
            currentYear = tahunCell.textContent.toLowerCase();
            yearStartRow = row;
            yearGroupIndex++;
            
            if (!yearGroups[currentYear]) {
                yearGroups[currentYear] = {
                    rows: [],
                    yearCells: [],
                    visibleCount: 0
                };
            }
        }
        
        if (currentYear && yearGroups[currentYear]) {
            yearGroups[currentYear].rows.push(row);
            if (tahunCell) {
                yearGroups[currentYear].yearCells.push(tahunCell);
            }
        }
    });
    
    // Fase 2: Hitung baris yang visible untuk setiap tahun
    Object.keys(yearGroups).forEach(tahun => {
        const group = yearGroups[tahun];
        group.visibleCount = 0;
        
        group.rows.forEach(row => {
            // Ambil data dari baris untuk filtering
            const tahunCell = row.querySelector('td.sticky');
            const tahunRow = tahunCell ? tahunCell.textContent.toLowerCase() : tahun;
            const periode = row.cells[1]?.textContent.toLowerCase() || '';
            const dma = row.cells[3]?.textContent.toLowerCase() || '';
            const rowText = row.textContent.toLowerCase();
            
            const tahunMatch = !tahunValue || tahunRow === tahunValue;
            const periodeMatch = !periodeValue || periode === periodeValue;
            const dmaMatch = !dmaValue || dma === dmaValue;
            const searchMatch = !searchValue || rowText.includes(searchValue);
            
            const isVisible = tahunMatch && periodeMatch && dmaMatch && searchMatch;
            
            // Set tampilan baris
            if (isVisible) {
                row.style.display = '';
                group.visibleCount++;
                
                // Tampilkan sel tahun jika ini baris pertama grup yang visible
                const tahunCell = row.querySelector('td.sticky');
                if (tahunCell && group.rows[0] === row) {
                    tahunCell.style.display = '';
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Fase 3: Update rowspan untuk header tahun
        const visibleCount = group.visibleCount;
        
        // Atur semua sel tahun dalam grup
        group.yearCells.forEach((yearCell, index) => {
            if (index === 0 && visibleCount > 0) {
                // Baris pertama: tampilkan dengan rowspan
                yearCell.style.display = '';
                yearCell.setAttribute('rowspan', visibleCount);
                yearCell.classList.add('filtered-year');
            } else if (index === 0 && visibleCount === 0) {
                // Jika tidak ada baris yang visible, sembunyikan sel tahun
                yearCell.style.display = 'none';
                yearCell.removeAttribute('rowspan');
                yearCell.classList.remove('filtered-year');
            } else if (index > 0) {
                // Baris berikutnya: selalu sembunyikan
                yearCell.style.display = 'none';
                yearCell.removeAttribute('rowspan');
                yearCell.classList.remove('filtered-year');
            }
        });
    });
    
    // Tampilkan pesan jika tidak ada data yang cocok
    const visibleRows = document.querySelectorAll('#dataTableBody tr:not(.no-data)[style=""]').length;
    const noDataRow = document.querySelector('#dataTableBody tr.no-data');
    
    if (visibleRows === 0 && rows.length > 0) {
        if (!noDataRow) {
            const tbody = document.getElementById('dataTableBody');
            const newRow = document.createElement('tr');
            newRow.className = 'no-data';
            newRow.innerHTML = `
                <td colspan="105" class="text-center py-4">
                    <i class="fas fa-search fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data yang cocok dengan filter</p>
                    <button id="resetFilterBtn" class="btn btn-primary mt-2">
                        <i class="fas fa-sync-alt me-1"></i> Reset Filter
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            
            // Tambahkan event listener untuk tombol reset
            document.getElementById('resetFilterBtn').addEventListener('click', resetAllFilters);
        }
    } else if (noDataRow) {
        noDataRow.remove();
    }
}

function resetAllFilters() {
    tahunFilter.value = '';
    periodeFilter.value = '';
    dmaFilter.value = '';
    searchInput.value = '';
    
    // Render ulang tabel dari data asli
    renderTableData();
    
    // Re-initialize filter
    initializeFilter();
}

// ============ RENDER TABLE DATA ============
function renderTableData() {
    const tbody = document.getElementById('dataTableBody');
    
    if (allData.length === 0) {
        tbody.innerHTML = `
            <tr class="no-data">
                <td colspan="105" class="text-center py-4">
                    <i class="fas fa-database fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data Piezometer yang tersedia</p>
                    ${isAdmin ? `
                    <button type="button" class="btn btn-primary mt-2" id="addDataEmpty">
                        <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                    </button>
                    ` : ''}
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    
    // Kelompokkan data berdasarkan tahun
    const groupedByYear = {};
    allData.forEach((item, index) => {
        const year = item['pengukuran']['tahun'] || '-';
        if (!groupedByYear[year]) {
            groupedByYear[year] = [];
        }
        groupedByYear[year].push({ item, index });
    });
    
    // Daftar titik dalam format view (L_01, L_02, ..., SPZ_02)
    const titikListView = ['L_01', 'L_02', 'L_03', 'L_04', 'L_05', 'L_06', 'L_07', 'L_08', 'L_09', 'L_10', 'SPZ_02'];
    // Daftar titik dalam format database (L01, L02, ..., SPZ02)
    const titikListDB = ['L01', 'L02', 'L03', 'L04', 'L05', 'L06', 'L07', 'L08', 'L09', 'L10', 'SPZ02'];
    
    // Render data dengan rowspan untuk tahun yang sama
    Object.keys(groupedByYear).forEach(year => {
        const yearData = groupedByYear[year];
        const rowspan = yearData.length;
        
        yearData.forEach((data, indexInYear) => {
            const item = data.item;
            const p = item['pengukuran'];
            const metrik = item['metrik'] || {};
            const initialA = item['initial_a'] || {};
            const initialB = item['initial_b'] || {};
            const perhitungan = item['perhitungan'] || {};
            const pembacaan = item['pembacaan'] || {};
            
            // Format date
            const displayDate = p['tanggal'] ? new Date(p['tanggal']).toLocaleDateString('id-ID') : '-';
            
            html += `
                <tr data-pid="${p['id_pengukuran']}" data-year="${year}" data-periode="${p['periode'] || ''}" data-dma="${p['dma'] || ''}">
                    ${indexInYear === 0 ? `
                    <td class="sticky" rowspan="${rowspan}" data-year-header="true">${year}</td>
                    ` : ''}
                    <td class="sticky-2">${p['periode'] || '-'}</td>
                    <td class="sticky-3">${displayDate}</td>
                    <td class="sticky-4 bg-info-column">${displayRawData(p['dma'] ?? '-')}</td>
                    <td class="sticky-5 bg-info-column">${displayRawData(p['temp_id'] ?? '-')}</td>
                    
                    <!-- BACAAN METRIK - Feet & Inch (dari tabel t_pembacaan) -->
                    ${titikListView.map(titik => {
                        const bacaanData = pembacaan[titik] || {};
                        const feet = bacaanData['feet'] ?? null;
                        const inch = bacaanData['inch'] ?? null;
                        return `
                            <td class="number-cell bg-metrik">${displayRawData(feet)}</td>
                            <td class="number-cell bg-metrik">${displayRawData(inch)}</td>
                        `;
                    }).join('')}
                    
                    <!-- KONVERSI STATIS -->
                    <td class="number-cell bg-calculation">0.3048</td>
                    <td class="number-cell bg-calculation">0.0254</td>
                    
                    <!-- BACAAN PIEZOMETER METRIK (dari tabel b_piezo_metrik) -->
                    ${titikListView.map(titik => {
                        const meter = metrik[titik] ?? null;
                        return `<td class="number-cell bg-reading">${displayRawData(meter)}</td>`;
                    }).join('')}
                    
                    <!-- PERHITUNGAN PIEZOMETER - 12 kolom -->
                    <td class="number-cell bg-calculation">Elev.Piez</td>
                    ${titikListDB.map(tipeDB => {
                        const perhitunganData = perhitungan[tipeDB] || {};
                        const tPsMetrik = perhitunganData['t_psmetrik'] ?? null;
                        return `<td class="number-cell bg-calculation">${displayRawData(tPsMetrik)}</td>`;
                    }).join('')}
                    
                    <!-- INITIAL READINGS A - 12 kolom -->
                    <td class="number-cell bg-initial">Elev.Piez</td>
                    ${titikListView.map(titik => {
                        const initialAData = initialA[titik] || {};
                        const elvPiezA = initialAData['Elv_Piez'] ?? null;
                        return `<td class="number-cell bg-initial">${displayRawData(elvPiezA)}</td>`;
                    }).join('')}
                    
                    <!-- INITIAL READINGS B - 12 kolom -->
                    <td class="number-cell bg-initial">Elev.Piez</td>
                    ${titikListView.map(titik => {
                        const initialBData = initialB[titik] || {};
                        const elvPiezB = initialBData['Elv_Piez'] ?? null;
                        return `<td class="number-cell bg-initial">${displayRawData(elvPiezB)}</td>`;
                    }).join('')}
                    
                    <!-- Action Buttons -->
                    ${isAdmin ? `
                    <td class="action-cell">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn-action btn-edit edit-data" 
                                   data-id="${p['id_pengukuran']}" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top" 
                                   title="Edit Data">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn-action btn-delete delete-data" 
                                    data-id="${p['id_pengukuran']}" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    ` : `
                    <td class="action-cell">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn-action btn-disabled" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Klik untuk melihat informasi hak akses"
                                   onclick="showAccessWarning('edit', '${p['tahun']}', '${p['periode']}', '${displayDate}')">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn-action btn-disabled"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="Klik untuk melihat informasi hak akses"
                                   onclick="showAccessWarning('delete', '${p['tahun']}', '${p['periode']}', '${displayDate}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    `}
                </tr>
            `;
        });
    });
    
    tbody.innerHTML = html;
    
    // Simpan HTML asli untuk reset
    originalTableHTML = tbody.innerHTML;
    
    attachEventListeners();
}

// ============ ATTACH EVENT LISTENERS ============
function attachEventListeners() {
    // Add Data button
    const addDataBtn = document.getElementById('addData');
    const addDataEmptyBtn = document.getElementById('addDataEmpty');
    
    if (addDataBtn) {
        addDataBtn.addEventListener('click', function() {
            if (isAdmin) {
                window.location.href = '<?= base_url('left-piez/create') ?>';
            } else {
                showAccessWarning('add');
            }
        });
    }
    
    if (addDataEmptyBtn) {
        addDataEmptyBtn.addEventListener('click', function() {
            if (isAdmin) {
                window.location.href = '<?= base_url('left-piez/create') ?>';
            } else {
                showAccessWarning('add');
            }
        });
    }
    
    // Edit Data - hanya untuk admin
    if (isAdmin) {
        document.querySelectorAll('.edit-data').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                window.location.href = '<?= base_url('left-piez/edit') ?>/' + id;
            });
        });
    }
    
    // Delete Data - hanya untuk admin
    if (isAdmin) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        document.querySelectorAll('.delete-data').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
                deleteModal.show();
            });
        });
        
        // Confirm Delete
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
    }
    
    // Re-initialize tooltips
    const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    newTooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// ============ EXPORT EXCEL FUNCTIONALITY ============
function setupExportExcel() {
    document.getElementById('exportExcel').addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
        this.disabled = true;

        setTimeout(() => {
            try {
                const table = document.getElementById('exportTable');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Data Piezometer Left Bank"});
                
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const filename = `Piezometer_Left_Bank_Export_${timestamp}.xlsx`;
                
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
}

// ============ SCROLL INDICATOR ============
function setupScrollIndicator() {
    const scrollIndicator = document.getElementById('scrollIndicator');
    const tableContainer = document.getElementById('tableContainer');
    
    let scrollTimeout;
    if (tableContainer) {
        tableContainer.addEventListener('scroll', function() {
            const { scrollLeft, scrollWidth, clientWidth } = this;
            const showHorizontal = scrollLeft > 0 || scrollLeft + clientWidth < scrollWidth;
            
            if (showHorizontal && scrollIndicator) {
                scrollIndicator.style.display = 'block';
            } else if (scrollIndicator) {
                scrollIndicator.style.display = 'none';
            }

            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                if (scrollIndicator) {
                    scrollIndicator.style.display = 'none';
                }
            }, 2000);
        });
    }
}

// ============ TOAST NOTIFICATION ============
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

// ============ INITIALIZATION ============
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Render data tabel
    renderTableData();
    
    // Setup filter
    initializeFilter();
    
    // Setup export Excel
    setupExportExcel();
    
    // Setup scroll indicator
    setupScrollIndicator();
});

// Import SQL functionality (hanya untuk admin)
<?php if ($isAdmin): ?>
document.addEventListener('DOMContentLoaded', function() {
    const btnImportSQL = document.getElementById('btnImportSQL');
    if (!btnImportSQL) return;
    
    btnImportSQL.addEventListener('click', function() {
        console.log('[PIEZOMETER IMPORT] Tombol Import diklik.');

        const sqlFileInput = document.getElementById('sqlFile');
        const importProgress = document.getElementById('importProgress');
        const importStatus = document.getElementById('importStatus');
        const btnImport = this;

        importStatus.style.display = 'none';

        // === Validasi file ===
        if (!sqlFileInput.files || sqlFileInput.files.length === 0) {
            console.warn('[PIEZOMETER IMPORT] Tidak ada file dipilih.');
            showImportStatus('❌ Pilih file SQL terlebih dahulu', 'danger');
            return;
        }

        const file = sqlFileInput.files[0];
        console.log('[PIEZOMETER IMPORT] File terpilih:', file.name, '-', (file.size / 1024).toFixed(2), 'KB');

        if (!file.name.toLowerCase().endsWith('.sql')) {
            console.warn('[PIEZOMETER IMPORT] File bukan .sql');
            showImportStatus('❌ File harus berformat .sql', 'danger');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            console.warn('[PIEZOMETER IMPORT] File lebih dari 50MB');
            showImportStatus('❌ Ukuran file maksimal 50MB', 'danger');
            return;
        }

        if (file.size === 0) {
            console.warn('[PIEZOMETER IMPORT] File kosong');
            showImportStatus('❌ File kosong', 'danger');
            return;
        }

        // === Progress Bar ===
        importProgress.style.display = 'block';
        const progressBar = importProgress.querySelector('.progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        btnImport.disabled = true;
        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
        console.log('[PIEZOMETER IMPORT] Memulai upload ke server...');

        const formData = new FormData();
        formData.append('sql_file', file);

        // Simulasi progress sementara
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 2;
            if (progress <= 80) {
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
            }
        }, 100);

        // === Fetch API ===
        fetch('<?= base_url('left-piez/import-sql') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            console.log('[PIEZOMETER IMPORT] Response diterima:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('[PIEZOMETER IMPORT] Response JSON:', data);

            if (data.success) {
                console.log('[PIEZOMETER IMPORT] Import SQL sukses');
                showImportStatus('✅ ' + data.message, 'success');

                if (data.stats) {
                    const s = data.stats;
                    let detailHtml = `
                        <div class="mt-3 p-2 bg-light rounded">
                            <h6 class="mb-2">📊 Detail Import:</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small>Total Query: <strong>${s.total}</strong></small><br>
                                    <small>Berhasil: <strong class="text-success">${s.success}</strong></small>
                                </div>
                                <div class="col-6">
                                    <small>Gagal: <strong class="text-danger">${s.failed}</strong></small><br>
                                    <small>Affected Rows: <strong>${s.affected_rows || 0}</strong></small>
                                </div>
                            </div>
                    `;
                    
                    if (s.tables && Object.keys(s.tables).length > 0) {
                        detailHtml += `<div class="mt-2"><h6 class="mb-1">📋 Tabel yang Diimpor:</h6>`;
                        detailHtml += `<div class="small">`;
                        Object.entries(s.tables).forEach(([table, count]) => {
                            detailHtml += `<div>${table}: ${count} records</div>`;
                        });
                        detailHtml += `</div></div>`;
                    }
                    
                    if (data.error_display) {
                        detailHtml += `
                            <div class="mt-2">
                                <h6 class="mb-1">❌ Error Details:</h6>
                                <div class="bg-white p-2 rounded small text-danger" style="max-height:100px;overflow-y:auto;">
                                    ${data.error_display.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        `;
                    }
                    detailHtml += `</div>`;
                    importStatus.innerHTML += detailHtml;
                }

                // Auto-refresh 3 detik setelah sukses
                setTimeout(() => {
                    console.log('[PIEZOMETER IMPORT] Reload halaman setelah sukses.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                }, 3000);

            } else {
                console.error('[PIEZOMETER IMPORT] Import gagal:', data.message);
                showImportStatus('❌ ' + data.message, 'danger');
                if (data.error_display) {
                    importStatus.innerHTML += `
                        <div class="mt-2 p-2 bg-white rounded border">
                            <strong>Detail Error:</strong><br>
                            <div class="small text-danger">${data.error_display.replace(/\n/g, '<br>')}</div>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('[PIEZOMETER IMPORT ERROR]', error);
            showImportStatus('❌ Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            console.log('[PIEZOMETER IMPORT] Proses import selesai (finally).');
            setTimeout(() => {
                btnImport.disabled = false;
                btnImport.innerHTML = '<i class="fas fa-upload me-1"></i> Import';
            }, 2000);
        });

        // === Helper: tampilkan status ===
        function showImportStatus(message, type) {
            importStatus.style.display = 'block';
            importStatus.className = `alert alert-${type} alert-dismissible fade show`;
            importStatus.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
        }
    });

    // Reset form ketika modal ditutup
    const importModal = document.getElementById('importModal');
    if (importModal) {
        importModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('sqlFile').value = '';
            document.getElementById('importProgress').style.display = 'none';
            document.getElementById('importStatus').style.display = 'none';
            const progressBar = document.getElementById('importProgress')?.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
            }
        });
    }

    // Validasi file ketika dipilih
    const sqlFileInput = document.getElementById('sqlFile');
    if (sqlFileInput) {
        sqlFileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            const importStatus = document.getElementById('importStatus');
            
            if (file && importStatus) {
                if (!file.name.toLowerCase().endsWith('.sql')) {
                    importStatus.style.display = 'block';
                    importStatus.className = 'alert alert-warning';
                    importStatus.innerHTML = '⚠️ File harus berekstensi .sql';
                    this.value = '';
                } else {
                    importStatus.style.display = 'none';
                }
            }
        });
    }
});
<?php endif; ?>
</script>
</body>
</html>