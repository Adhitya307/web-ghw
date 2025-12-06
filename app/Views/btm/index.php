<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bubble Tilt Meter - PT Indonesia Power' ?></title>

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
            min-width: 1400px;
        }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 600px;
            overflow: auto;
        }
        
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
        
        /* WARNA BACKGROUND HEADER */
        .bg-reading { 
            background-color: #e8f4fd !important; 
            color: #2c3e50 !important; 
            font-weight: 600;
        }
        
        .bg-calculation { 
            background-color: #f0f9eb !important; 
            color: #2c3e50 !important; 
            font-weight: 600;
        }
        
        .bg-result { 
            background-color: #e6f7ff !important; 
            color: #2c3e50 !important; 
            font-weight: 600;
        }
        
        .bg-action { 
            background-color: #e3f2fd !important;
            color: #2c3e50 !important; 
            font-weight: 600;
        }
        
        .bg-scatter { 
            background-color: #fff2cc !important; 
            color: #2c3e50 !important; 
            font-weight: 600;
        }
        
        .btn-bt {
            min-width: 60px;
        }
        
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .btn-group .btn {
            white-space: nowrap;
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
            <i class="fas fa-mountain me-2"></i>Bubble Tilt Meter (BTM) - <?= strtoupper($currentBt ?? 'BT-1') ?>
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('btm/bt1') ?>" class="btn <?= ($currentBt ?? 'bt1') == 'bt1' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">
                <i class="fas fa-table"></i> BT-1
            </a>
            <a href="<?= base_url('btm/bt2') ?>" class="btn <?= ($currentBt ?? '') == 'bt2' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">BT-2</a>
            <a href="<?= base_url('btm/bt3') ?>" class="btn <?= ($currentBt ?? '') == 'bt3' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">BT-3</a>
            <a href="<?= base_url('btm/bt4') ?>" class="btn <?= ($currentBt ?? '') == 'bt4' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">BT-4</a>
            <a href="<?= base_url('btm/bt6') ?>" class="btn <?= ($currentBt ?? '') == 'bt6' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">BT-6</a>
            <a href="<?= base_url('btm/bt7') ?>" class="btn <?= ($currentBt ?? '') == 'bt7' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">BT-7</a>
            <a href="<?= base_url('btm/bt8') ?>" class="btn <?= ($currentBt ?? '') == 'bt8' ? 'btn-primary' : 'btn-outline-primary' ?> btn-bt">BT-8</a>
            
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
                            <p class="mb-2">✅ Tabel BTM yang didukung:</p>
                            <div class="row">
                                <div class="col-6">
                                    <ul class="mb-1">
                                        <li>Data Pengukuran</li>
                                        <li>BT-1, BT-2, BT-3</li>
                                        <li>BT-4, BT-6, BT-7</li>
                                        <li>BT-8</li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="mb-1">
                                        <li>Data Bacaan</li>
                                        <li>Data Perhitungan</li>
                                        <li>Data Scatter</li>
                                    </ul>
                                </div>
                            </div>
                            <p class="mb-0 text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Data BT-5 akan diabaikan
                            </p>
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
                        <i class="fas fa-shield-alt me-2"></i>Pengaturan Akses BTM
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
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data BTM</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Mengakses semua BT (1-8)</li>
                            <li><i class="fas fa-check"></i> Melihat grafik scatter data</li>
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
                    <th rowspan="4" class="sticky">TAHUN</th>
                    <th rowspan="4" class="sticky-2">PERIODE</th>
                    <th rowspan="4" class="sticky-3">TANGGAL</th>
                    
                    <!-- BACAAN BT -->
                    <th colspan="4" class="bg-reading">BACAAN <?= strtoupper($currentBt ?? 'BT-1') ?></th>
                    
                    <!-- PERHITUNGAN BT -->
                    <th colspan="2" class="bg-calculation">UTARA-SELATAN</th>
                    <th colspan="2" class="bg-calculation">TIMUR-BARAT</th>
                    <th colspan="2" rowspan="3" class="bg-result">INCLINED ANGLE-C</th>
                    <th colspan="3" rowspan="3" class="bg-result">DIPPED DIRECTION OF-C</th>
                    <th colspan="4" rowspan="3" class="bg-scatter">SCATTER</th>
                    
                    <!-- KOLOM AKSI -->
                    <?php if($isAdmin): ?>
                    <th rowspan="4" class="action-header bg-action">AKSI</th>
                    <?php else: ?>
                    <th rowspan="4" class="action-header bg-action">AKSI</th>
                    <?php endif; ?>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- BACAAN BT Sub Headers -->
                    <th colspan="2" class="bg-reading">UTARA-SELATAN</th>
                    <th colspan="2" class="bg-reading">TIMUR-BARAT</th>
                    <!-- PERHITUNGAN BT Sub Headers -->
                    <th rowspan="2" colspan="2" class="bg-calculation">INCLINED ANGLE-A</th>
                    <th rowspan="2" colspan="2" class="bg-calculation">INCLINED ANGLE-B</th>
                </tr>
                
                <!-- Row 3: Measurement Headers -->
                <tr>
                    <th colspan="2" class="bg-reading">GP & ARAH</th>
                    <th colspan="2" class="bg-reading">GP & ARAH</th>
                </tr>

                <!-- Row 4: Column Headers -->
                <tr>
                    <!-- BACAAN BT Headers -->
                    <th class="bg-reading">US GP</th>
                    <th class="bg-reading">US Arah</th>
                    <th class="bg-reading">TB GP</th>
                    <th class="bg-reading">TB Arah</th>

                    <!-- PERHITUNGAN BT Headers -->
                    <th class="bg-calculation">A_sec</th>
                    <th class="bg-calculation">sin_A_rad</th>
                    <th class="bg-calculation">B_sec</th>
                    <th class="bg-calculation">sin_B_rad</th>
                    <th class="bg-result">sin_C_rad</th>
                    <th class="bg-result">sin_C_deg</th>
                    <th class="bg-result">Cos α</th>
                    <th class="bg-result">α (Rad)</th>
                    <th class="bg-result">DMS</th>
                    
                    <!-- SCATTER Headers -->
                    <th class="bg-scatter">Y (U-S)</th>
                    <th class="bg-scatter">X (T-B)</th>
                    <th class="bg-scatter">Y (cum)</th>
                    <th class="bg-scatter">X (cum)</th>
                    
                    <!-- HEADER KOLOM AKSI -->
                    <?php if($isAdmin): ?>
                    <th class="bg-action"></th>
                    <?php endif; ?>
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
                <p>Apakah Anda yakin ingin menghapus data BTM ini?</p>
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
let currentBt = '<?= $currentBt ?? "bt1" ?>';
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
let deleteId = null;
let originalTableHTML = null; // Untuk menyimpan struktur tabel asli

// Variabel global untuk modal hak akses
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

// Variabel untuk filter
let tahunFilter, periodeFilter, searchInput, resetFilter;

// ============ FUNGSI HAK AKSES ============
function showAccessWarning(actionType, tahun = null, periode = null, tanggal = null) {
    let title = '';
    let message = '';
    
    switch(actionType) {
        case 'add':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penambahan data BTM tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'edit':
            title = 'Akses Tidak Tersedia';
            message = `Fitur pengeditan data BTM tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'delete':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penghapusan data BTM tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'import':
            title = 'Akses Tidak Tersedia';
            message = `Fitur import database BTM tidak dapat diakses dengan level pengguna saat ini.`;
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
    
    if (Math.abs(number) < 0.0001 && number != 0) {
        return '<span class="scientific-notation">' + number.toExponential(8) + '</span>';
    }
    
    const formatted = number.toFixed(9);
    return formatted.replace(/(\.0*|(?<=\.\d*?)0+)$/, '');
}

// ============ FUNGSI PERHITUNGAN ============
function calculateSinRad(seconds) {
    if (seconds === null || seconds === '') {
        return null;
    }
    
    const radians = (seconds * Math.PI) / (180 * 3600);
    const sinValue = Math.sin(radians);
    return sinValue;
}

function calculateSinCRad(sinA, sinB) {
    if (sinA === null || sinB === null) {
        return null;
    }
    
    const sinC = Math.sqrt(Math.pow(sinA, 2) + Math.pow(sinB, 2));
    return sinC;
}

function radToDeg(radians) {
    if (radians === null) {
        return null;
    }
    
    const degrees = radians * (180 / Math.PI);
    return degrees;
}

// ============ FUNGSI FILTER YANG DIPERBAIKI ============
function initializeFilter() {
    tahunFilter = document.getElementById('tahunFilter');
    periodeFilter = document.getElementById('periodeFilter');
    searchInput = document.getElementById('searchInput');
    resetFilter = document.getElementById('resetFilter');
    
    // Event listeners untuk filter
    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    resetFilter.addEventListener('click', resetAllFilters);
}

function filterTable() {
    const tahunValue = tahunFilter.value.toLowerCase();
    const periodeValue = periodeFilter.value.toLowerCase();
    const searchValue = searchInput.value.toLowerCase();
    
    const rows = document.querySelectorAll('#dataTableBody tr:not(.no-data)');
    const yearGroups = {};
    let currentYear = null;
    let yearStartRow = null;
    
    // Fase 1: Kelompokkan baris berdasarkan tahun
    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        
        const tahunCell = row.querySelector('td.sticky');
        if (tahunCell) {
            // Ini adalah baris pertama dari grup tahun
            currentYear = tahunCell.textContent.toLowerCase();
            yearStartRow = row;
            
            if (!yearGroups[currentYear]) {
                yearGroups[currentYear] = {
                    rows: [],
                    yearCell: tahunCell,
                    visibleCount: 0
                };
            }
        }
        
        if (currentYear && yearGroups[currentYear]) {
            yearGroups[currentYear].rows.push(row);
        }
    });
    
    // Fase 2: Hitung baris yang visible untuk setiap tahun
    Object.keys(yearGroups).forEach(tahun => {
        const group = yearGroups[tahun];
        group.visibleCount = 0;
        
        group.rows.forEach(row => {
            // Ambil data dari baris untuk filtering
            const periode = row.cells[1]?.textContent.toLowerCase() || '';
            const rowText = row.textContent.toLowerCase();
            
            const tahunMatch = !tahunValue || tahun === tahunValue;
            const periodeMatch = !periodeValue || periode === periodeValue;
            const searchMatch = !searchValue || rowText.includes(searchValue);
            
            const isVisible = tahunMatch && periodeMatch && searchMatch;
            
            // Set tampilan baris
            if (isVisible) {
                row.style.display = '';
                group.visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Fase 3: Update rowspan untuk header tahun
        const yearCell = group.yearCell;
        const visibleCount = group.visibleCount;
        
        if (tahunValue && tahun !== tahunValue) {
            // Jika filter tahun aktif dan tahun ini tidak cocok
            yearCell.style.display = 'none';
            yearCell.removeAttribute('rowspan');
        } else if (visibleCount > 0) {
            // Tampilkan header tahun dengan rowspan yang sesuai
            yearCell.style.display = '';
            yearCell.setAttribute('rowspan', visibleCount);
            
            // Sembunyikan duplikat header tahun di baris berikutnya
            for (let i = 1; i < group.rows.length; i++) {
                const duplicateCell = group.rows[i].querySelector('td.sticky');
                if (duplicateCell) {
                    duplicateCell.style.display = 'none';
                    duplicateCell.removeAttribute('rowspan');
                }
            }
        } else {
            // Sembunyikan header tahun jika tidak ada baris yang visible
            yearCell.style.display = 'none';
            yearCell.removeAttribute('rowspan');
        }
    });
}

function resetAllFilters() {
    tahunFilter.value = '';
    periodeFilter.value = '';
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
                <td colspan="${isAdmin ? 22 : 21}" class="text-center py-4">
                    <i class="fas fa-database fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data BTM yang tersedia</p>
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
    
    // Render data dengan rowspan untuk tahun yang sama
    Object.keys(groupedByYear).forEach(year => {
        const yearData = groupedByYear[year];
        const rowspan = yearData.length;
        
        yearData.forEach((data, indexInYear) => {
            const item = data.item;
            const p = item['pengukuran'];
            const bacaan = item['bacaan'] || {};
            const perhitungan = item['perhitungan'] || {};
            const scatter = item['scatter'] || {};
            
            // Ambil data untuk current BT
            const currentBtKey = currentBt;
            const Y_US = scatter[currentBtKey]?.['Y_US'] ?? null;
            const X_TB = scatter[currentBtKey]?.['X_TB'] ?? null;
            const Y_cum = scatter[currentBtKey]?.['Y_cum'] ?? null;
            const X_cum = scatter[currentBtKey]?.['X_cum'] ?? null;

            // PERHITUNGAN REAL-TIME
            const btBacaan = bacaan[currentBtKey] || {};
            const btPerhitungan = perhitungan[currentBtKey] || {};
            
            const A_sec = btPerhitungan['A_sec'] ?? null;
            const B_sec = btPerhitungan['B_sec'] ?? null;
            
            const sin_A_rad_calculated = calculateSinRad(A_sec);
            const sin_B_rad_calculated = calculateSinRad(B_sec);
            const sin_C_rad_calculated = calculateSinCRad(sin_A_rad_calculated, sin_B_rad_calculated);
            const sin_C_deg_calculated = sin_C_rad_calculated !== null ? radToDeg(sin_C_rad_calculated) : null;

            // Gunakan nilai yang sudah dihitung jika tersedia
            const sin_A_rad_display = sin_A_rad_calculated ?? btPerhitungan['sin_A_rad'] ?? null;
            const sin_B_rad_display = sin_B_rad_calculated ?? btPerhitungan['sin_B_rad'] ?? null;
            const sin_C_rad_display = sin_C_rad_calculated ?? btPerhitungan['sin_C_rad'] ?? null;
            const sin_C_deg_display = sin_C_deg_calculated ?? btPerhitungan['sin_C_deg'] ?? null;
            
            // Format date
            const displayDate = p['tanggal'] ? new Date(p['tanggal']).toLocaleDateString('id-ID') : '-';
            
            html += `
                <tr data-pid="${p['id_pengukuran']}" data-year="${year}" data-periode="${p['periode'] || ''}">
                    ${indexInYear === 0 ? `
                    <td class="sticky" rowspan="${rowspan}" data-year-header="true">${year}</td>
                    ` : ''}
                    <td class="sticky-2">${p['periode'] || '-'}</td>
                    <td class="sticky-3">${displayDate}</td>
                    
                    <!-- BACAAN DATA BT -->
                    <td class="number-cell">${btBacaan['US_GP'] !== null && btBacaan['US_GP'] !== undefined ? formatNumber(parseFloat(btBacaan['US_GP'])) : '-'}</td>
                    <td>${btBacaan['US_Arah'] || '-'}</td>
                    <td class="number-cell">${btBacaan['TB_GP'] !== null && btBacaan['TB_GP'] !== undefined ? formatNumber(parseFloat(btBacaan['TB_GP'])) : '-'}</td>
                    <td>${btBacaan['TB_Arah'] || '-'}</td>

                    <!-- PERHITUNGAN DATA BT -->
                    <td class="number-cell">${A_sec !== null && A_sec !== undefined ? formatNumber(parseFloat(A_sec)) : '-'}</td>
                    <td class="number-cell">${sin_A_rad_display !== null ? formatNumber(parseFloat(sin_A_rad_display)) : '-'}</td>
                    <td class="number-cell">${B_sec !== null && B_sec !== undefined ? formatNumber(parseFloat(B_sec)) : '-'}</td>
                    <td class="number-cell">${sin_B_rad_display !== null ? formatNumber(parseFloat(sin_B_rad_display)) : '-'}</td>
                    <td class="number-cell">${sin_C_rad_display !== null ? formatNumber(parseFloat(sin_C_rad_display)) : '-'}</td>
                    <td class="number-cell">${sin_C_deg_display !== null ? formatNumber(parseFloat(sin_C_deg_display)) : '-'}</td>
                    <td class="number-cell">${btPerhitungan['Cosa'] !== null && btPerhitungan['Cosa'] !== undefined ? formatNumber(parseFloat(btPerhitungan['Cosa'])) : '-'}</td>
                    <td class="number-cell">${btPerhitungan['a_rad'] !== null && btPerhitungan['a_rad'] !== undefined ? formatNumber(parseFloat(btPerhitungan['a_rad'])) : '-'}</td>
                    <td>${btPerhitungan['DMS'] || '-'}</td>
                    
                    <!-- SCATTER DATA -->
                    <td class="number-cell bg-scatter">${Y_US !== null && Y_US !== undefined ? formatNumber(parseFloat(Y_US)) : '-'}</td>
                    <td class="number-cell bg-scatter">${X_TB !== null && X_TB !== undefined ? formatNumber(parseFloat(X_TB)) : '-'}</td>
                    <td class="number-cell bg-scatter">${Y_cum !== null && Y_cum !== undefined ? formatNumber(parseFloat(Y_cum)) : '-'}</td>
                    <td class="number-cell bg-scatter">${X_cum !== null && X_cum !== undefined ? formatNumber(parseFloat(X_cum)) : '-'}</td>
                    
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
                window.location.href = '<?= base_url('btm/create') ?>';
            } else {
                showAccessWarning('add');
            }
        });
    }
    
    if (addDataEmptyBtn) {
        addDataEmptyBtn.addEventListener('click', function() {
            if (isAdmin) {
                window.location.href = '<?= base_url('btm/create') ?>';
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
                window.location.href = '<?= base_url('btm/edit') ?>/' + id;
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
                fetch('<?= base_url('btm/delete') ?>/' + deleteId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data');
                })
                .finally(() => {
                    deleteModal.hide();
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
                const wb = XLSX.utils.table_to_book(table, {sheet: `Data ${currentBt.toUpperCase()}`});
                
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const filename = `BTM_${currentBt.toUpperCase()}_Export_${timestamp}.xlsx`;
                
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
        console.log('[BTM IMPORT] Tombol Import diklik.');

        const sqlFileInput = document.getElementById('sqlFile');
        const importProgress = document.getElementById('importProgress');
        const importStatus = document.getElementById('importStatus');
        const btnImport = this;

        importStatus.style.display = 'none';

        // === Validasi file ===
        if (!sqlFileInput.files || sqlFileInput.files.length === 0) {
            console.warn('[BTM IMPORT] Tidak ada file dipilih.');
            showImportStatus('❌ Pilih file SQL terlebih dahulu', 'danger');
            return;
        }

        const file = sqlFileInput.files[0];
        console.log('[BTM IMPORT] File terpilih:', file.name, '-', (file.size / 1024).toFixed(2), 'KB');

        if (!file.name.toLowerCase().endsWith('.sql')) {
            console.warn('[BTM IMPORT] File bukan .sql');
            showImportStatus('❌ File harus berformat .sql', 'danger');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            console.warn('[BTM IMPORT] File lebih dari 50MB');
            showImportStatus('❌ Ukuran file maksimal 50MB', 'danger');
            return;
        }

        if (file.size === 0) {
            console.warn('[BTM IMPORT] File kosong');
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
        console.log('[BTM IMPORT] Memulai upload ke server...');

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
        fetch('<?= base_url('btm/import-sql') ?>', {
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
            console.log('[BTM IMPORT] Response diterima:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('[BTM IMPORT] Response JSON:', data);

            if (data.success) {
                console.log('[BTM IMPORT] Import SQL sukses');
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
                    console.log('[BTM IMPORT] Reload halaman setelah sukses.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                }, 3000);

            } else {
                console.error('[BTM IMPORT] Import gagal:', data.message);
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
            console.error('[BTM IMPORT ERROR]', error);
            showImportStatus('❌ Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            console.log('[BTM IMPORT] Proses import selesai (finally).');
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