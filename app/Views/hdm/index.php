<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horizontal Displacement Meter - PT Indonesia Power</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/data.css') ?>">
    
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    
    <style>
        /* Styling khusus untuk aksi tabel */
        .action-cell {
            position: sticky;
            right: 0;
            background: white;
            z-index: 10;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            padding: 8px 5px;
            min-width: 60px;
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
        
        /* Modal peringatan hak akses - MODERN & FORMAL */
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
        
        /* Styling untuk tabel HDM */
        .sticky { position: sticky; left: 0; background: white; z-index: 5; }
        .sticky-2 { position: sticky; left: 60px; background: white; z-index: 5; }
        .sticky-3 { position: sticky; left: 120px; background: white; z-index: 5; }
        .sticky-4 { position: sticky; left: 180px; background: white; z-index: 5; }
        
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.5rem;
        }
        
        .data-table {
            font-size: 0.875rem;
            min-width: 1200px;
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
        
        .bg-light {
            background-color: #e9ecef !important;
        }
        
        /* Perbaikan layout tombol */
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .btn-group .btn {
            white-space: nowrap;
        }

        /* Pagination styling */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
        }
        
        .pagination-info {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .page-size-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .page-size-selector select {
            width: auto;
        }
        
        /* Scroll indicator */
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
        
        /* Loading spinner */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        /* Warna background untuk header sections */
        .bg-info { background-color: #0dcaf0 !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-success { background-color: #198754 !important; }
        .bg-secondary { background-color: #6c757d !important; }
        .bg-primary { background-color: #0d6efd !important; }
        .bg-danger { background-color: #dc3545 !important; }

        /* User info */
        .user-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #0d6efd;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .badge-user {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
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
            <i class="fas fa-arrow-left-right me-2"></i>Data Horizontal Displacement Meter
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('horizontal-displacement') ?>" class="btn btn-outline-primary active">
                <i class="fas fa-table"></i> Tabel Data HDM
            </a>
            <a href="#" class="btn btn-outline-info"
            onclick="window.location.href='<?= base_url('hdm625') ?>'">
            <i class="fas fa-database"></i> HDM 625
            </a>

            <a href="<?= base_url('hdm600') ?>" class="btn btn-outline-info">
                <i class="fas fa-arrow-right-arrow-left"></i> HDM 600
            </a>
            
            <?php if ($isAdmin): ?>
                <!-- Tombol untuk Admin -->
                <button type="button" class="btn btn-outline-primary" id="addData">
                    <i class="fas fa-plus me-1"></i> Add Data
                </button>
                
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-database"></i> Import SQL
                </button>
            <?php else: ?>
                <!-- Tombol untuk User Biasa dengan modal peringatan -->
                <button type="button" class="btn btn-outline-primary btn-disabled" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Klik untuk melihat informasi hak akses"
                       onclick="showAccessWarning('add')">
                    <i class="fas fa-plus me-1"></i> Add Data
                </button>
                
                <button type="button" class="btn btn-outline-info btn-disabled"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Klik untuk melihat informasi hak akses"
                       onclick="showAccessWarning('import')">
                    <i class="fas fa-database"></i> Import SQL
                </button>
            <?php endif; ?>
            
            <button type="button" class="btn btn-outline-success" id="exportExcel">
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

    <!-- Modal Peringatan Hak Akses Modern & Formal -->
    <div class="modal fade modal-access" id="accessWarningModal" tabindex="-1" aria-labelledby="accessWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accessWarningModalLabel">
                        <i class="fas fa-shield-alt me-2"></i>Pengaturan Akses
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="access-icon-container">
                        <div class="access-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    
                    <h3 class="access-title" id="warningTitle">
                        <!-- Judul akan diisi oleh JavaScript -->
                    </h3>
                    
                    <p class="access-message" id="warningMessage">
                        <!-- Pesan akan diisi oleh JavaScript -->
                    </p>
                    
                    <div class="user-role-badge">
                        <i class="fas fa-user-tag"></i>
                        <span>Level Akses: <strong><?= $isAdmin ? 'Administrator' : 'Pengguna Biasa' ?></strong></span>
                    </div>
                    
                    <div class="access-details">
                        <h6>Hak Akses yang Tersedia:</h6>
                        <ul>
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data HDM</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Mengakses data lengkap HDM</li>
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
                        $uniqueYears = array_unique(array_map(fn($p) => $p['tahun'] ?? '-', $pengukuran));
                        rsort($uniqueYears);
                        foreach ($uniqueYears as $year):
                            if ($year === '-') continue;
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
                        $uniquePeriods = array_unique(array_map(fn($p) => $p['periode'] ?? '-', $pengukuran));
                        sort($uniquePeriods);
                        foreach ($uniquePeriods as $period):
                            if ($period === '-') continue;
                    ?>
                        <option value="<?= esc($period) ?>"><?= esc($period) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <!-- DAM -->
            <div class="filter-item">
                <label for="damFilter" class="form-label">DMA</label>
                <select id="damFilter" class="form-select">
                    <option value="">Semua DMA</option>
                    <?php
                    if (!empty($pengukuran)):
                        $uniqueDMA = array_unique(array_map(fn($p) => $p['dma'] ?? '-', $pengukuran));
                        sort($uniqueDMA);
                        foreach ($uniqueDMA as $dma):
                            if ($dma === '-') continue;
                    ?>
                        <option value="<?= esc($dma) ?>"><?= esc($dma) ?></option>
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
                    <th rowspan="3" class="sticky">TAHUN</th>
                    <th rowspan="3" class="sticky-2">PERIODE</th>
                    <th rowspan="3" class="sticky-3">TANGGAL</th>
                    <th rowspan="3" class="sticky-4">DAM</th>
                    
                    <!-- PEMBACAAN HDM -->
                    <th colspan="8" class="bg-info text-white">PEMBACAAN HDM</th>
                    
                    <!-- DEPTH (S) -->
                    <th colspan="8" class="bg-primary text-white">DEPTH (S)</th>
                  
                    <!-- READINGS (S) -->
                    <th colspan="8" class="bg-success text-white">READINGS (S)</th>

                    <!-- PERGERAKAN (CM) -->
                    <th colspan="8" class="bg-warning text-dark">PERGERAKAN (CM)</th>
                    
                    <th rowspan="3" class="action-cell">Aksi</th>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- PEMBACAAN HDM Sub Headers -->
                    <th colspan="3" class="bg-info text-white">ELV.625</th>
                    <th colspan="5" class="bg-info text-white">ELV.600</th>
                    

                    <!-- DEPTH (S) Sub Headers -->
                    <th colspan="3" class="bg-primary text-white">ELV.625</th>
                    <th colspan="5" class="bg-primary text-white">ELV.600</th>
    

                    <!-- READINGS (S) Sub Headers -->
                    <th colspan="3" class="bg-success text-white">ELV.625</th>
                    <th colspan="5" class="bg-success text-white">ELV.600</th>

                    <!-- PERGERAKAN (CM) Sub Headers -->
                    <th colspan="3" class="bg-warning text-dark">ELV.625</th>
                    <th colspan="5" class="bg-warning text-dark">ELV.600</th>
                    
                </tr>
                
                <!-- Row 3: Measurement Headers -->
                <tr>
                    <!-- PEMBACAAN HDM - ELV 625 -->
                    <th class="bg-info text-white">HV-1</th>
                    <th class="bg-info text-white">HV-2</th>
                    <th class="bg-info text-white">HV-3</th>
                    
                    <!-- PEMBACAAN HDM - ELV 600 -->
                    <th class="bg-info text-white">HV-1</th>
                    <th class="bg-info text-white">HV-2</th>
                    <th class="bg-info text-white">HV-3</th>
                    <th class="bg-info text-white">HV-4</th>
                    <th class="bg-info text-white">HV-5</th>
                    
                    <!-- DEPTH (S) - ELV 625 -->
                    <th class="bg-primary text-white">HV-1</th>
                    <th class="bg-primary text-white">HV-2</th>
                    <th class="bg-primary text-white">HV-3</th>
                    
                    <!-- DEPTH (S) - ELV 600 -->
                    <th class="bg-primary text-white">HV-1</th>
                    <th class="bg-primary text-white">HV-2</th>
                    <th class="bg-primary text-white">HV-3</th>
                    <th class="bg-primary text-white">HV-4</th>
                    <th class="bg-primary text-white">HV-5</th>
                    
                    <!-- READINGS (S) - ELV 625 -->
                    <th class="bg-success text-white">HV-1</th>
                    <th class="bg-success text-white">HV-2</th>
                    <th class="bg-success text-white">HV-3</th>
                    
                    <!-- READINGS (S) - ELV 600 -->
                    <th class="bg-success text-white">HV-1</th>
                    <th class="bg-success text-white">HV-2</th>
                    <th class="bg-success text-white">HV-3</th>
                    <th class="bg-success text-white">HV-4</th>
                    <th class="bg-success text-white">HV-5</th>
                    
                    <!-- PERGERAKAN (CM) - ELV 625 -->
                    <th class="bg-warning text-dark">HV-1</th>
                    <th class="bg-warning text-dark">HV-2</th>
                    <th class="bg-warning text-dark">HV-3</th>
                  
                    <!-- PERGERAKAN (CM) - ELV 600 -->
                    <th class="bg-warning text-dark">HV-1</th>
                    <th class="bg-warning text-dark">HV-2</th>
                    <th class="bg-warning text-dark">HV-3</th>
                    <th class="bg-warning text-dark">HV-4</th>
                    <th class="bg-warning text-dark">HV-5</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <!-- Data akan dimuat melalui JavaScript -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Controls -->
    <div class="pagination-container">
        <div class="pagination-info" id="paginationInfo">
            Menampilkan 0 dari 0 data
        </div>
        
        <div class="pagination-controls">
            <div class="page-size-selector">
                <label for="pageSize" class="form-label mb-0">Tampilkan:</label>
                <select id="pageSize" class="form-select form-select-sm">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination">
                    <!-- Pagination akan di-generate oleh JavaScript -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Hapus HDM (Hanya untuk Admin) -->
<?php if ($isAdmin): ?>
<?= $this->include('hdm/modal_hapus') ?>
<?php endif; ?>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Data dan state management
let allData = <?= json_encode($dataWithReadings ?? []) ?>;
let currentPage = 1;
let pageSize = 10;
let filteredData = [];
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;

// Variabel global untuk modal hak akses
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

// Fungsi untuk mengurutkan data berdasarkan tanggal (terlama ke terbaru)
function sortDataByDate(data) {
    return data.sort((a, b) => {
        const dateA = new Date(a.pengukuran?.tanggal || 0);
        const dateB = new Date(b.pengukuran?.tanggal || 0);
        return dateA - dateB; // Urutkan dari tanggal terlama ke terbaru
    });
}

// ============ FUNGSI HAK AKSES ============

// Fungsi untuk menampilkan modal peringatan hak akses
function showAccessWarning(actionType, tahun = null, periode = null, tanggal = null) {
    let title = '';
    let message = '';
    
    switch(actionType) {
        case 'add':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penambahan data tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'edit':
            title = 'Akses Tidak Tersedia';
            message = `Fitur pengeditan data tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'delete':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penghapusan data tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'import':
            title = 'Akses Tidak Tersedia';
            message = `Fitur import database tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        default:
            title = 'Akses Tidak Tersedia';
            message = `Fitur ini tidak dapat diakses dengan level pengguna saat ini.`;
    }
    
    // Update judul dan pesan
    warningTitle.textContent = title;
    warningMessage.innerHTML = message;
    
    // Tampilkan modal
    accessWarningModal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // ============ PAGINATION & DATA MANAGEMENT ============
    const pageSizeSelect = document.getElementById('pageSize');
    const paginationElement = document.getElementById('pagination');
    
    // Inisialisasi data dengan pengurutan tanggal
    allData = sortDataByDate(allData);
    filteredData = [...allData];
    renderTable();
    
    // Event listener untuk perubahan page size
    pageSizeSelect.addEventListener('change', function() {
        pageSize = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });
    
    // Fungsi render tabel dengan pagination
    function renderTable() {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableContainer = document.getElementById('tableContainer');
        
        // Show loading
        loadingSpinner.style.display = 'block';
        tableContainer.style.display = 'none';
        
        // Simulasi loading (bisa dihapus di production)
        setTimeout(() => {
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const pageData = filteredData.slice(startIndex, endIndex);
            
            renderTableBody(pageData);
            renderPagination();
            
            // Hide loading
            loadingSpinner.style.display = 'none';
            tableContainer.style.display = 'block';
        }, 300);
    }
    
    // Fungsi render body tabel
function renderTableBody(data) {
    const tbody = document.getElementById('dataTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="34" class="text-center py-4">
                    <i class="fas fa-database fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data HDM yang tersedia</p>
                    <a href="<?= base_url('horizontal-displacement') ?>" class="btn btn-primary mt-2">
                        <i class="fas fa-refresh me-1"></i> Refresh
                    </a>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    
    // Group data by tahun untuk rowspan
    const tahunGroups = {};
    data.forEach(item => {
        const tahun = item.pengukuran?.tahun ?? '-';
        if (!tahunGroups[tahun]) {
            tahunGroups[tahun] = [];
        }
        tahunGroups[tahun].push(item);
    });
    
    // Render data dengan grouping tahun yang benar
    Object.keys(tahunGroups).sort().forEach(tahun => { // Sort tahun ascending
        const itemsInYear = tahunGroups[tahun];
        const rowCount = itemsInYear.length;
        
        itemsInYear.forEach((item, index) => {
            const p = item.pengukuran || {};
            const pembacaanElv600 = item.pembacaan_elv600 || {};
            const pembacaanElv625 = item.pembacaan_elv625 || {};
            const depthElv600 = item.depth_elv600 || {};
            const depthElv625 = item.depth_elv625 || {};
            const initialReadingElv600 = item.initial_reading_elv600 || {};
            const initialReadingElv625 = item.initial_reading_elv625 || {};
            const pergerakanElv600 = item.pergerakan_elv600 || {};
            const pergerakanElv625 = item.pergerakan_elv625 || {};
            
            const periode = p.periode ?? '-';
            const dma = p.dma ?? '-';
            const pid = p.id_pengukuran ?? null;
            
            const isFirstInYear = index === 0;
            
            // Format tanggal untuk tampilan
            const displayDate = p.tanggal ? new Date(p.tanggal).toLocaleDateString('id-ID') : '-';
            
            html += `
                <tr data-tahun="${tahun}" data-periode="${periode}" data-dma="${dma}" data-pid="${pid}">
                    ${isFirstInYear ? `<td rowspan="${rowCount}" class="sticky">${tahun}</td>` : ''}
                    <td class="sticky-2">${periode}</td>
                    <td class="sticky-3">${displayDate}</td>
                    <td class="sticky-4">${dma}</td>
                    
                    <!-- PEMBACAAN HDM - ELV 625 -->
                    <td>${pembacaanElv625.hv_1 || '-'}</td>
                    <td>${pembacaanElv625.hv_2 || '-'}</td>
                    <td>${pembacaanElv625.hv_3 || '-'}</td>

                    <!-- PEMBACAAN HDM - ELV 600 -->
                    <td>${pembacaanElv600.hv_1 || '-'}</td>
                    <td>${pembacaanElv600.hv_2 || '-'}</td>
                    <td>${pembacaanElv600.hv_3 || '-'}</td>
                    <td>${pembacaanElv600.hv_4 || '-'}</td>
                    <td>${pembacaanElv600.hv_5 || '-'}</td>
                    
                    <!-- DEPTH (S) - ELV 625 -->
                    <td>${depthElv625.hv_1 || '-'}</td>
                    <td>${depthElv625.hv_2 || '-'}</td>
                    <td>${depthElv625.hv_3 || '-'}</td>

                    <!-- DEPTH (S) - ELV 600 -->
                    <td>${depthElv600.hv_1 || '-'}</td>
                    <td>${depthElv600.hv_2 || '-'}</td>
                    <td>${depthElv600.hv_3 || '-'}</td>
                    <td>${depthElv600.hv_4 || '-'}</td>
                    <td>${depthElv600.hv_5 || '-'}</td>
                    
                    <!-- READINGS (S) - ELV 625 -->
                    <td>${initialReadingElv625.hv_1 || '-'}</td>
                    <td>${initialReadingElv625.hv_2 || '-'}</td>
                    <td>${initialReadingElv625.hv_3 || '-'}</td>

                    <!-- READINGS (S) - ELV 600 -->
                    <td>${initialReadingElv600.hv_1 || '-'}</td>
                    <td>${initialReadingElv600.hv_2 || '-'}</td>
                    <td>${initialReadingElv600.hv_3 || '-'}</td>
                    <td>${initialReadingElv600.hv_4 || '-'}</td>
                    <td>${initialReadingElv600.hv_5 || '-'}</td>
                
                    <!-- PERGERAKAN (CM) - ELV 625 -->
                    <td>${pergerakanElv625.hv_1 || '-'}</td>
                    <td>${pergerakanElv625.hv_2 || '-'}</td>
                    <td>${pergerakanElv625.hv_3 || '-'}</td>

                    <!-- PERGERAKAN (CM) - ELV 600 -->
                    <td>${pergerakanElv600.hv_1 || '-'}</td>
                    <td>${pergerakanElv600.hv_2 || '-'}</td>
                    <td>${pergerakanElv600.hv_3 || '-'}</td>
                    <td>${pergerakanElv600.hv_4 || '-'}</td>
                    <td>${pergerakanElv600.hv_5 || '-'}</td>

                    <td class="action-cell">
                        <div class="d-flex justify-content-center">
                            <?php if ($isAdmin): ?>
                                <!-- Tombol untuk Admin -->
                                <button type="button" class="btn-action btn-edit edit-data" 
                                       data-id="${pid}" data-bs-toggle="tooltip" 
                                       data-bs-placement="top" title="Edit Data">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn-action btn-delete delete-data" 
                                        data-id="${pid}" data-bs-toggle="tooltip" 
                                        data-bs-placement="top" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <!-- Tombol untuk User Biasa dengan modal peringatan -->
                                <button type="button" class="btn-action btn-disabled" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Klik untuk melihat informasi hak akses"
                                       onclick="showAccessWarning('edit', '${tahun}', '${periode}', '${displayDate}')">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn-action btn-disabled"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Klik untuk melihat informasi hak akses"
                                       onclick="showAccessWarning('delete', '${tahun}', '${periode}', '${displayDate}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            `;
        });
    });
    
    tbody.innerHTML = html;
    attachEventListeners();
}
    
    // Fungsi render pagination
    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const paginationInfo = document.getElementById('paginationInfo');
        
        const startItem = (currentPage - 1) * pageSize + 1;
        const endItem = Math.min(currentPage * pageSize, filteredData.length);
        
        paginationInfo.textContent = `Menampilkan ${startItem}-${endItem} dari ${filteredData.length} data`;
        
        if (totalPages <= 1) {
            paginationElement.innerHTML = '';
            return;
        }
        
        let html = '';
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        
        // First page
        if (startPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
                ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
            `;
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page
        if (endPage < totalPages) {
            html += `
                ${endPage < totalPages - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `;
        }
        
        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationElement.innerHTML = html;
        
        // Attach pagination event listeners
        paginationElement.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page >= 1 && page <= totalPages && page !== currentPage) {
                    currentPage = page;
                    renderTable();
                    
                    // Scroll ke atas tabel
                    document.getElementById('tableContainer').scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }
    
    // ============ FILTER FUNCTIONALITY ============
    const tahunFilter = document.getElementById('tahunFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const damFilter = document.getElementById('damFilter');
    const resetFilter = document.getElementById('resetFilter');
    const searchInput = document.getElementById('searchInput');
    
    // Fungsi filter data
    function filterData() {
        const tVal = tahunFilter.value;
        const pVal = periodeFilter.value;
        const dVal = damFilter.value;
        const searchVal = searchInput.value.toLowerCase();
        
        filteredData = allData.filter(item => {
            const p = item.pengukuran || {};
            const tahun = p.tahun ?? '-';
            const periode = p.periode ?? '-';
            const dma = p.dma ?? '-';
            
            const tahunMatch = !tVal || tahun === tVal;
            const periodeMatch = !pVal || periode === pVal;
            const dmaMatch = !dVal || dma.toString() === dVal;
            
            let searchMatch = true;
            if (searchVal) {
                const searchText = Object.values(p).join(' ').toLowerCase() +
                                 Object.values(item.pembacaan_elv600 || {}).join(' ').toLowerCase() +
                                 Object.values(item.pembacaan_elv625 || {}).join(' ').toLowerCase() +
                                 Object.values(item.depth_elv600 || {}).join(' ').toLowerCase() +
                                 Object.values(item.depth_elv625 || {}).join(' ').toLowerCase() +
                                 Object.values(item.initial_reading_elv600 || {}).join(' ').toLowerCase() +
                                 Object.values(item.initial_reading_elv625 || {}).join(' ').toLowerCase() +
                                 Object.values(item.pergerakan_elv600 || {}).join(' ').toLowerCase() +
                                 Object.values(item.pergerakan_elv625 || {}).join(' ').toLowerCase();
                searchMatch = searchText.includes(searchVal);
            }
            
            return tahunMatch && periodeMatch && dmaMatch && searchMatch;
        });
        
        filteredData = sortDataByDate(filteredData);
        currentPage = 1;
        renderTable();
    }
    
    // Event listeners untuk filter
    tahunFilter.addEventListener('change', filterData);
    periodeFilter.addEventListener('change', filterData);
    damFilter.addEventListener('change', filterData);
    searchInput.addEventListener('input', filterData);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
        periodeFilter.value = '';
        damFilter.value = '';
        searchInput.value = '';
        filterData();
    });
    
    // ============ EVENT LISTENERS ATTACHMENT ============
    function attachEventListeners() {
        // Edit Data
        document.querySelectorAll('.edit-data').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                window.location.href = '<?= base_url('horizontal-displacement/edit') ?>/' + id;
            });
        });
        
        // Delete Data - hanya untuk admin
        if (isAdmin) {
            document.querySelectorAll('.delete-data').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    // Trigger click event yang akan ditangkap oleh modal_hapus.php
                    this.dispatchEvent(new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    }));
                });
            });
        }
        
        // Re-initialize tooltips
        const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        newTooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // ============ EXPORT EXCEL FUNCTIONALITY (SERVER-SIDE) ============
    document.getElementById('exportExcel').addEventListener('click', function() {
        // Show loading
        const originalText = this.innerHTML;
        const originalState = this.disabled;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
        this.disabled = true;

        try {
            // Buat filter data yang akan dikirim ke server
            const tahun = document.getElementById('tahunFilter').value || '';
            const periode = document.getElementById('periodeFilter').value || '';
            const dma = document.getElementById('damFilter').value || '';
            
            // Build URL dengan parameter filter
            let url = '<?= base_url('hdm/export-excel/export') ?>';
            const params = [];
            
            if (tahun) params.push(`tahun=${encodeURIComponent(tahun)}`);
            if (periode) params.push(`periode=${encodeURIComponent(periode)}`);
            if (dma) params.push(`dma=${encodeURIComponent(dma)}`);
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            // Redirect ke URL export
            window.location.href = url;
            
            // Reset button setelah 2 detik
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = originalState;
            }, 2000);

        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Terjadi kesalahan saat mengexport: ' + error.message);
            
            // Restore button state
            this.innerHTML = originalText;
            this.disabled = originalState;
        }
    });
    
    // Add Data button
    const addDataBtn = document.getElementById('addData');
    if (addDataBtn) {
        addDataBtn.addEventListener('click', function() {
            if (isAdmin) {
                window.location.href = '<?= base_url('horizontal-displacement/create') ?>';
            } else {
                showAccessWarning('add');
            }
        });
    }
});

// Import SQL functionality (hanya untuk admin)
<?php if ($isAdmin): ?>
document.getElementById('btnImportSQL').addEventListener('click', function() {
    console.log('[IMPORT] Tombol Import diklik.');

    const sqlFileInput = document.getElementById('sqlFile');
    const importProgress = document.getElementById('importProgress');
    const importStatus = document.getElementById('importStatus');
    const btnImport = this;

    importStatus.style.display = 'none';

    // === Validasi file ===
    if (!sqlFileInput.files || sqlFileInput.files.length === 0) {
        console.warn('[IMPORT] Tidak ada file dipilih.');
        showImportStatus('❌ Pilih file SQL terlebih dahulu', 'danger');
        return;
    }

    const file = sqlFileInput.files[0];
    console.log('[IMPORT] File terpilih:', file.name, '-', (file.size / 1024).toFixed(2), 'KB');

    if (!file.name.toLowerCase().endsWith('.sql')) {
        console.warn('[IMPORT] File bukan .sql');
        showImportStatus('❌ File harus berformat .sql', 'danger');
        return;
    }

    if (file.size > 50 * 1024 * 1024) {
        console.warn('[IMPORT] File lebih dari 50MB');
        showImportStatus('❌ Ukuran file maksimal 50MB', 'danger');
        return;
    }

    if (file.size === 0) {
        console.warn('[IMPORT] File kosong');
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
    console.log('[IMPORT] Memulai upload ke server...');

    const formData = new FormData();
    formData.append('sql_file', file);

    // Simulasi progress sementara menunggu response server
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 2;
        if (progress <= 80) {
            progressBar.style.width = progress + '%';
            progressBar.textContent = progress + '%';
        }
    }, 100);

   // === Fetch API ===
fetch('<?= base_url('horizontal-displacement/importSQL') ?>', {
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
        console.log('[IMPORT] Response diterima:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('[IMPORT] Response JSON:', data);

        if (data.success) {
            console.log('[IMPORT] Import SQL sukses');
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
                console.log('[IMPORT] Reload halaman setelah sukses.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                if (modal) modal.hide();
                window.location.reload();
            }, 3000);

        } else {
            console.error('[IMPORT] Import gagal:', data.message);
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
        console.error('[IMPORT ERROR]', error);
        showImportStatus('❌ Terjadi kesalahan: ' + error.message, 'danger');
    })
    .finally(() => {
        console.log('[IMPORT] Proses import selesai (finally).');
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
document.getElementById('importModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('sqlFile').value = '';
    document.getElementById('importProgress').style.display = 'none';
    document.getElementById('importStatus').style.display = 'none';
    document.getElementById('importProgress').querySelector('.progress-bar').style.width = '0%';
    document.getElementById('importProgress').querySelector('.progress-bar').textContent = '0%';
});

// Validasi file ketika dipilih
document.getElementById('sqlFile').addEventListener('change', function(e) {
    const file = this.files[0];
    const importStatus = document.getElementById('importStatus');
    
    if (file) {
        // Validasi ekstensi
        if (!file.name.toLowerCase().endsWith('.sql')) {
            importStatus.style.display = 'block';
            importStatus.className = 'alert alert-warning';
            importStatus.innerHTML = '⚠️ File harus berekstensi .sql';
            this.value = ''; // Reset input file
        } else {
            importStatus.style.display = 'none';
        }
    }
});
<?php endif; ?>

</script>
</body>
</html>