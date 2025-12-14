<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extensometer Monitoring - PT Indonesia Power</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        
        /* Styling untuk tabel Extensometer dengan DMA */
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
        
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            position: relative;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.5rem;
        }
        
        .data-table {
            font-size: 0.875rem;
            min-width: 2200px;
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
        
        .bg-reading { 
            background-color: #e8f4fd !important; 
            color: #2c3e50 !important;
            font-weight: 600;
        }
        
        .bg-deformasi { 
            background-color: #f0f9eb !important; 
            color: #2c3e50 !important;
            font-weight: 600;
        }
        
        .bg-initial { 
            background-color: #fff2cc !important; 
            color: #2c3e50 !important;
            font-weight: 600;
        }
        
        .bg-dma {
            background-color: #f5e6ff !important;
            color: #2c3e50 !important;
            font-weight: 600;
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

        /* User info */
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
        
        /* Number cell styling */
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .scientific-notation {
            font-size: 0.7rem;
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
        
        /* Pagination */
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
        
        /* Action header */
        .action-header {
            position: sticky;
            right: 0;
            background-color: #e3f2fd !important;
            border: 1px solid #dee2e6;
            z-index: 10;
            color: #2c3e50 !important;
            font-weight: 600;
        }
        
        /* DMA cell styling */
        .dma-cell {
            text-align: center;
            font-weight: 500;
            background-color: #f8f0ff;
        }
        
        /* Filter DMA */
        .dma-filter-item {
            flex: 1;
            min-width: 120px;
        }
        
        /* Toast notification untuk export */
        .toast-export {
            position: fixed;
            bottom: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .toast-export.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .toast-export.hide {
            opacity: 0;
            transform: translateY(20px);
        }
        
        .toast-export .toast-body {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .toast-export.success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            border: none;
        }
        
        .toast-export.error {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
        }
        
        .toast-export.warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            border: none;
        }
        
        .toast-export.info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
        }
        
        .toast-export i {
            font-size: 22px;
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
            <i class="fas fa-ruler-combined me-2"></i>Extensometer Monitoring System
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('extenso') ?>" class="btn btn-outline-primary active">
                <i class="fas fa-table"></i> Tabel Data Extensometer
            </a>
            <a href="<?= base_url('extenso/grafik-ambang') ?>" class="btn btn-outline-warning">
                <i class="fas fa-chart-bar me-1"></i> Grafik & Ambang
            </a>
            
            <?php if ($isAdmin): ?>
                <!-- Tombol untuk Admin -->
                <a href="<?= base_url('extenso/create') ?>" class="btn btn-outline-success">
                    <i class="fas fa-plus me-1"></i> Add Data
                </a>
                
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-database"></i> Import SQL
                </button>
            <?php else: ?>
                <!-- Tombol untuk User Biasa dengan modal peringatan -->
                <button type="button" class="btn btn-outline-success btn-disabled" 
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
            
            <button type="button" class="btn btn-outline-success" id="exportExcelBtn">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
        </div>

        <div class="table-controls">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
            </div>
            <div class="text-muted small">
                <i class="fas fa-sort-amount-down me-1"></i> Data diurutkan berdasarkan tanggal (tua → baru)
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
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data Extensometer</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Mengakses data lengkap Extensometer</li>
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

    <!-- Toast Notification untuk Export -->
    <div id="exportToast" class="toast-export" style="display: none;">
        <div class="toast-body">
            <i class="fas fa-spinner fa-spin"></i>
            <span id="exportToastText">Mempersiapkan export...</span>
        </div>
    </div>

    <!-- Filter Section dengan DMA -->
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
            <div class="dma-filter-item">
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

    <!-- Main Table dengan DMA -->
    <div class="table-responsive" id="tableContainer">
        <table class="data-table table table-bordered table-hover" id="exportTable">
            <thead>
                <!-- Row 1: Main Header -->
                <tr>
                    <th rowspan="3" class="sticky">TAHUN</th>
                    <th rowspan="3" class="sticky-2">PERIODE</th>
                    <th rowspan="3" class="sticky-3">TANGGAL</th>
                    <th rowspan="3" class="sticky-4">DMA</th>
                    
                    <!-- PEMBACAAN (EX1-EX4) -->
                    <th colspan="12" class="bg-reading">PEMBACAAN</th>
                    
                    <!-- DEFORMASI (EX1-EX4) -->
                    <th colspan="12" class="bg-deformasi">DEFORMASI</th>
                    
                    <!-- INITIAL READINGS (EX1-EX4) -->
                    <th colspan="12" class="bg-initial">INITIAL READINGS</th>
                    
                    <!-- ACTION HEADER -->
                    <th rowspan="3" class="action-header">AKSI</th>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- PEMBACAAN Sub Headers -->
                    <th colspan="3" class="bg-reading">EX-1</th>
                    <th colspan="3" class="bg-reading">EX-2</th>
                    <th colspan="3" class="bg-reading">EX-3</th>
                    <th colspan="3" class="bg-reading">EX-4</th>
                    
                    <!-- DEFORMASI Sub Headers -->
                    <th colspan="3" class="bg-deformasi">EX-1</th>
                    <th colspan="3" class="bg-deformasi">EX-2</th>
                    <th colspan="3" class="bg-deformasi">EX-3</th>
                    <th colspan="3" class="bg-deformasi">EX-4</th>
                    
                    <!-- INITIAL READINGS Sub Headers -->
                    <th colspan="3" class="bg-initial">EX-1</th>
                    <th colspan="3" class="bg-initial">EX-2</th>
                    <th colspan="3" class="bg-initial">EX-3</th>
                    <th colspan="3" class="bg-initial">EX-4</th>
                </tr>

                <!-- Row 3: Column Headers -->
                <tr>
                    <!-- PEMBACAAN Headers -->
                    <?php for($i = 1; $i <= 4; $i++): ?>
                        <th class="bg-reading">10 m</th>
                        <th class="bg-reading">20 m</th>
                        <th class="bg-reading">30 m</th>
                    <?php endfor; ?>
                    
                    <!-- DEFORMASI Headers -->
                    <?php for($i = 1; $i <= 4; $i++): ?>
                        <th class="bg-deformasi">10 m</th>
                        <th class="bg-deformasi">20 m</th>
                        <th class="bg-deformasi">30 m</th>
                    <?php endfor; ?>
                    
                    <!-- INITIAL READINGS Headers -->
                    <?php for($i = 1; $i <= 4; $i++): ?>
                        <th class="bg-initial">10 m</th>
                        <th class="bg-initial">20 m</th>
                        <th class="bg-initial">30 m</th>
                    <?php endfor; ?>
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
                <p>Apakah Anda yakin ingin menghapus data Extensometer ini?</p>
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

<script>
// Data dan state management
let allData = <?= json_encode($pengukuran ?? []) ?>;
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
let deleteId = null;
let originalTableHTML = null;

// Variabel global untuk modal
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

// Variabel untuk filter
let tahunFilter, periodeFilter, dmaFilter, searchInput, resetFilter;

// ============ FUNGSI HAK AKSES ============
function showAccessWarning(actionType) {
    let title = '';
    let message = '';
    
    switch(actionType) {
        case 'add':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penambahan data Extensometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'edit':
            title = 'Akses Tidak Tersedia';
            message = `Fitur pengeditan data Extensometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'delete':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penghapusan data Extensometer tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'import':
            title = 'Akses Tidak Tersedia';
            message = `Fitur import database Extensometer tidak dapat diakses dengan level pengguna saat ini.`;
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
function formatNumber(value) {
    if (value === null || value === undefined || value === '' || value === '-') {
        return '-';
    }
    
    const numValue = typeof value === 'number' ? value : parseFloat(value);
    if (isNaN(numValue)) return value;
    
    if (Math.abs(numValue) < 0.0001 && numValue != 0) {
        return numValue.toExponential(4);
    }
    
    return numValue.toFixed(4);
}

// ============ FUNGSI PENGURUTAN DATA ============
function sortDataByDateAsc(data) {
    if (!data || data.length === 0) return data;
    
    return [...data].sort((a, b) => {
        const aDate = a['pengukuran']['tanggal'] || '';
        const bDate = b['pengukuran']['tanggal'] || '';
        const aYear = parseInt(a['pengukuran']['tahun']) || 0;
        const bYear = parseInt(b['pengukuran']['tahun']) || 0;
        const aPeriode = a['pengukuran']['periode'] || '';
        const bPeriode = b['pengukuran']['periode'] || '';
        
        // Urut berdasarkan tanggal (terkecil ke terbesar)
        if (aDate && bDate) {
            return new Date(aDate) - new Date(bDate);
        } else if (aDate && !bDate) {
            return -1;
        } else if (!aDate && bDate) {
            return 1;
        }
        
        // Fallback ke tahun dan periode jika tanggal sama
        if (aYear !== bYear) return aYear - bYear;
        return aPeriode.localeCompare(bPeriode);
    });
}

// ============ FUNGSI FILTER DENGAN DMA ============
function initializeFilter() {
    tahunFilter = document.getElementById('tahunFilter');
    periodeFilter = document.getElementById('periodeFilter');
    dmaFilter = document.getElementById('dmaFilter');
    searchInput = document.getElementById('searchInput');
    resetFilter = document.getElementById('resetFilter');
    
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
    
    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        
        const tahunCell = row.querySelector('td.sticky');
        if (tahunCell && tahunCell.hasAttribute('data-year-header')) {
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
    
    Object.keys(yearGroups).forEach(tahun => {
        const group = yearGroups[tahun];
        group.visibleCount = 0;
        
        group.rows.forEach(row => {
            const periode = row.cells[1]?.textContent.toLowerCase() || '';
            const dma = row.cells[3]?.textContent.toLowerCase() || '';
            const rowText = row.textContent.toLowerCase();
            
            const tahunMatch = !tahunValue || tahun === tahunValue;
            const periodeMatch = !periodeValue || periode === periodeValue;
            const dmaMatch = !dmaValue || dma === dmaValue;
            const searchMatch = !searchValue || rowText.includes(searchValue);
            
            const isVisible = tahunMatch && periodeMatch && dmaMatch && searchMatch;
            
            if (isVisible) {
                row.style.display = '';
                group.visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        const yearCell = group.yearCell;
        const visibleCount = group.visibleCount;
        
        if (tahunValue && tahun !== tahunValue) {
            yearCell.style.display = 'none';
            yearCell.removeAttribute('rowspan');
        } else if (visibleCount > 0) {
            yearCell.style.display = '';
            yearCell.setAttribute('rowspan', visibleCount);
            
            for (let i = 1; i < group.rows.length; i++) {
                const duplicateCell = group.rows[i].querySelector('td.sticky');
                if (duplicateCell) {
                    duplicateCell.style.display = 'none';
                    duplicateCell.removeAttribute('rowspan');
                }
            }
        } else {
            yearCell.style.display = 'none';
            yearCell.removeAttribute('rowspan');
        }
    });
}

function resetAllFilters() {
    tahunFilter.value = '';
    periodeFilter.value = '';
    dmaFilter.value = '';
    searchInput.value = '';
    renderTableData();
    initializeFilter();
}

// ============ RENDER TABLE DATA DENGAN DMA ============
function renderTableData() {
    const tbody = document.getElementById('dataTableBody');
    
    // Sort data berdasarkan tanggal dari terkecil ke terbesar
    const sortedData = sortDataByDateAsc(allData);
    
    if (sortedData.length === 0) {
        tbody.innerHTML = `
            <tr class="no-data">
                <td colspan="41" class="text-center py-4">
                    <i class="fas fa-database fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data Extensometer yang tersedia</p>
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
    
    const groupedByYear = {};
    sortedData.forEach((item, index) => {
        const year = item['pengukuran']['tahun'] || '-';
        if (!groupedByYear[year]) {
            groupedByYear[year] = [];
        }
        groupedByYear[year].push({ item, index });
    });
    
    const sortedYears = Object.keys(groupedByYear).sort((a, b) => parseInt(a) - parseInt(b));
    
    sortedYears.forEach(year => {
        const yearData = groupedByYear[year];
        const rowspan = yearData.length;
        
        yearData.forEach((data, indexInYear) => {
            const item = data.item;
            const p = item['pengukuran'] || {};
            const pembacaan = item['pembacaan'] || {};
            const deformasi = item['deformasi'] || {};
            const readings = item['readings'] || {};
            
            const pid = p.id_pengukuran ?? null;
            const dmaValue = p.dma || '-';
            const displayDate = p.tanggal ? new Date(p.tanggal).toLocaleDateString('id-ID') : '-';
            
            html += `
                <tr data-pid="${pid}" data-year="${year}" data-periode="${p.periode || ''}" data-dma="${dmaValue}">
                    ${indexInYear === 0 ? `
                    <td class="sticky" rowspan="${rowspan}" data-year-header="true">${year}</td>
                    ` : ''}
                    <td class="sticky-2">${p.periode || '-'}</td>
                    <td class="sticky-3">${displayDate}</td>
                    <td class="sticky-4 dma-cell">${dmaValue}</td>
                    
                    ${[1,2,3,4].map(i => `
                        <td class="number-cell">${formatNumber(pembacaan[`ex${i}`]?.pembacaan_10)}</td>
                        <td class="number-cell">${formatNumber(pembacaan[`ex${i}`]?.pembacaan_20)}</td>
                        <td class="number-cell">${formatNumber(pembacaan[`ex${i}`]?.pembacaan_30)}</td>
                    `).join('')}
                    
                    ${[1,2,3,4].map(i => `
                        <td class="number-cell">${formatNumber(deformasi[`ex${i}`]?.deformasi_10)}</td>
                        <td class="number-cell">${formatNumber(deformasi[`ex${i}`]?.deformasi_20)}</td>
                        <td class="number-cell">${formatNumber(deformasi[`ex${i}`]?.deformasi_30)}</td>
                    `).join('')}
                    
                    ${[1,2,3,4].map(i => `
                        <td class="number-cell">${formatNumber(readings[`ex${i}`]?.reading_10)}</td>
                        <td class="number-cell">${formatNumber(readings[`ex${i}`]?.reading_20)}</td>
                        <td class="number-cell">${formatNumber(readings[`ex${i}`]?.reading_30)}</td>
                    `).join('')}
                    
                    <td class="action-cell">
                        <div class="d-flex justify-content-center">
                            ${isAdmin ? `
                                <a href="<?= base_url('extenso/edit/') ?>${pid}" class="btn-action btn-edit" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top" 
                                   title="Edit Data">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button type="button" class="btn-action btn-delete delete-data" 
                                        data-id="${pid}" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : `
                                <button type="button" class="btn-action btn-disabled" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Klik untuk melihat informasi hak akses"
                                       onclick="showAccessWarning('edit')">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn-action btn-disabled"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Klik untuk melihat informasi hak akses"
                                       onclick="showAccessWarning('delete')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `}
                        </div>
                    </td>
                </tr>
            `;
        });
    });
    
    tbody.innerHTML = html;
    originalTableHTML = tbody.innerHTML;
    attachEventListeners();
}

// ============ ATTACH EVENT LISTENERS ============
function attachEventListeners() {
    const addDataBtn = document.getElementById('addData');
    const addDataEmptyBtn = document.getElementById('addDataEmpty');
    
    if (addDataBtn) {
        addDataBtn.addEventListener('click', function() {
            if (isAdmin) {
                window.location.href = '<?= base_url('extenso/create') ?>';
            } else {
                showAccessWarning('add');
            }
        });
    }
    
    if (addDataEmptyBtn) {
        addDataEmptyBtn.addEventListener('click', function() {
            if (isAdmin) {
                window.location.href = '<?= base_url('extenso/create') ?>';
            } else {
                showAccessWarning('add');
            }
        });
    }
    
    if (isAdmin) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        document.querySelectorAll('.delete-data').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
                deleteModal.show();
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deleteId) {
                fetch('<?= base_url('extenso/delete') ?>/' + deleteId, {
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
    
    const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    newTooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// ============ TOAST NOTIFICATION ============
function showToast(message, type = 'info', duration = 5000) {
    const toast = document.getElementById('exportToast');
    const toastText = document.getElementById('exportToastText');
    
    // Set message and type
    toastText.innerHTML = message;
    toast.className = `toast-export ${type} show`;
    toast.style.display = 'block';
    
    // Force reflow
    toast.offsetHeight;
    
    // Auto hide
    setTimeout(() => {
        toast.className = 'toast-export hide';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, duration);
    
    return toast;
}

// ============ EXPORT EXCEL FUNCTIONALITY (TANPA MODAL) ============
function setupExportExcel() {
    const exportBtn = document.getElementById('exportExcelBtn');
    
    if (!exportBtn) return;
    
    exportBtn.addEventListener('click', function() {
        // Tampilkan toast notifikasi
        showToast('<i class="fas fa-spinner fa-spin me-2"></i>Mempersiapkan export Excel...', 'info', 0);
        
        // Ambil nilai filter
        const tahun = document.getElementById('tahunFilter').value;
        const periode = document.getElementById('periodeFilter').value;
        const dma = document.getElementById('dmaFilter').value;
        
        // Buat URL untuk export
        let exportUrl = '<?= base_url('extenso/export-excel/export') ?>';
        const params = new URLSearchParams();
        
        if (tahun) params.append('tahun', tahun);
        if (periode) params.append('periode', periode);
        if (dma) params.append('dma', dma);
        
        const queryString = params.toString();
        if (queryString) {
            exportUrl += '?' + queryString;
        }
        
        // Tambahkan timestamp untuk menghindari cache
        exportUrl += (queryString ? '&' : '?') + '_t=' + Date.now();
        
        // Buat elemen iframe untuk download
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = exportUrl;
        document.body.appendChild(iframe);
        
        // Cek apakah download berhasil
        let downloadCheckInterval;
        let downloadTimeout;
        
        // Timeout untuk download
        downloadTimeout = setTimeout(() => {
            clearInterval(downloadCheckInterval);
            if (iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
            showToast('<i class="fas fa-exclamation-circle me-2"></i>Export timeout. Silakan coba lagi.', 'error', 5000);
        }, 60000); // 60 detik timeout lebih lama untuk data besar
        
        // Cek apakah iframe sudah selesai loading
        iframe.onload = function() {
            clearTimeout(downloadTimeout);
            clearInterval(downloadCheckInterval);
            
            // Beri waktu untuk download mulai
            setTimeout(() => {
                showToast('<i class="fas fa-check-circle me-2"></i>Export berhasil! File sedang didownload.', 'success', 5000);
                if (iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }
            }, 2000);
        };
        
        iframe.onerror = function() {
            clearTimeout(downloadTimeout);
            clearInterval(downloadCheckInterval);
            showToast('<i class="fas fa-exclamation-circle me-2"></i>Gagal memuat file export.', 'error', 5000);
            if (iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
        };
        
        // Cek berkala apakah download sudah dimulai
        downloadCheckInterval = setInterval(() => {
            try {
                if (iframe.contentDocument && iframe.contentDocument.readyState === 'complete') {
                    clearTimeout(downloadTimeout);
                    clearInterval(downloadCheckInterval);
                    showToast('<i class="fas fa-check-circle me-2"></i>Export berhasil! File sedang didownload.', 'success', 5000);
                    setTimeout(() => {
                        if (iframe.parentNode) {
                            iframe.parentNode.removeChild(iframe);
                        }
                    }, 3000);
                }
            } catch (e) {
                // Ignore cross-origin errors
            }
        }, 1000);
    });
}

// ============ SCROLL INDICATOR ============
function setupScrollIndicator() {
    const tableContainer = document.getElementById('tableContainer');
    
    let scrollTimeout;
    if (tableContainer) {
        tableContainer.addEventListener('scroll', function() {
            const { scrollLeft, scrollWidth, clientWidth } = this;
            const showHorizontal = scrollLeft > 0 || scrollLeft + clientWidth < scrollWidth;
            
            // Bisa ditambahkan scroll indicator jika diperlukan
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                // Cleanup jika diperlukan
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
    
    // Render data tabel (default: tanggal terkecil ke terbesar)
    renderTableData();
    
    // Setup filter dengan DMA
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
        if (!file.name.toLowerCase().endsWith('.sql')) {
            showImportStatus('❌ File harus berformat .sql', 'danger');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            showImportStatus('❌ Ukuran file maksimal 50MB', 'danger');
            return;
        }

        if (file.size === 0) {
            showImportStatus('❌ File kosong', 'danger');
            return;
        }

        importProgress.style.display = 'block';
        const progressBar = importProgress.querySelector('.progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        btnImport.disabled = true;
        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

        const formData = new FormData();
        formData.append('sql_file', file);

        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 2;
            if (progress <= 80) {
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
            }
        }, 100);

        fetch('<?= base_url('extenso/importSQL') ?>', {
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
            
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
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

                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                }, 3000);

            } else {
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
            importStatus.className = `alert alert-${type} alert-dismissible fade show`;
            importStatus.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
        }
    });

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