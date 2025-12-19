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
        /* WARNA TABEL SAMA PERSIS SEPERTI PIEZO LEFT */
        .bg-reading { background-color: #e8f4fd !important; color: #2c3e50 !important; }
        .bg-calculation { background-color: #f0f9eb !important; color: #2c3e50 !important; }
        .bg-result { background-color: #e6f7ff !important; color: #2c3e50 !important; }
        .bg-action { background-color: #f8f9fa !important; color: #2c3e50 !important; }
        .bg-metrik { background-color: #fff2cc !important; color: #2c3e50 !important; }
        .bg-initial { background-color: #e6ffed !important; color: #2c3e50 !important; }
        .bg-info-column { background-color: #e7f1ff !important; color: #2c3e50 !important; }
        
        /* WARNA HEADER NETRAL SAMA PERSIS SEPERTI PIEZO LEFT */
        .point-header, .calculation-header, .initial-header, .conversion-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
        }

        /* SISTEM STICKY HEADER YANG TERSTRUKTUR */
        .table th {
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
            padding: 0.5rem;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.3rem;
            font-size: 0.75rem;
            white-space: nowrap;
        }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 800px;
            overflow: auto;
            position: relative;
        }
        
        /* Header utama - Level 1 (paling bawah) */
        .table thead tr:nth-child(1) th {
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        /* Header level 2 */
        .table thead tr:nth-child(2) th {
            position: sticky;
            top: 56px;
            z-index: 101;
        }
        
        /* Header level 3 */
        .table thead tr:nth-child(3) th {
            position: sticky;
            top: 112px;
            z-index: 102;
        }
        
        /* Header level 4 */
        .table thead tr:nth-child(4) th {
            position: sticky;
            top: 168px;
            z-index: 103;
        }
        
        /* Sticky columns kiri */
        .sticky { 
            position: sticky; 
            left: 0; 
            z-index: 150;
            border-right: 2px solid #dee2e6 !important;
        }
        .sticky-2 { 
            position: sticky; 
            left: 80px; 
            z-index: 150;
            border-right: 2px solid #dee2e6 !important;
        }
        .sticky-3 { 
            position: sticky; 
            left: 160px; 
            z-index: 150;
            border-right: 2px solid #dee2e6 !important;
        }
        .sticky-4 { 
            position: sticky; 
            left: 240px; 
            z-index: 150;
            border-right: 2px solid #dee2e6 !important;
        }
        .sticky-5 { 
            position: sticky; 
            left: 320px; 
            z-index: 150;
            border-right: 2px solid #dee2e6 !important;
        }
        
        /* Action Cell */
        .action-cell {
            position: sticky;
            right: 0;
            z-index: 200;
            padding: 0.3rem;
            min-width: 80px;
            border-left: 2px solid #dee2e6 !important;
            white-space: nowrap;
            vertical-align: middle !important;
            text-align: center !important;
        }
        
        .table thead tr th.action-cell {
            z-index: 250 !important;
            background: #f8f9fa !important;
        }
        
        .table thead tr {
            height: 56px;
        }

        /* Button Styles */
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
        
        /* Button disabled untuk non-admin */
        .btn-disabled {
            color: #6c757d;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            cursor: not-allowed;
        }
        
        .btn-disabled:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            transform: translateY(0);
        }
        
        .data-table {
            min-width: 2000px;
        }
        
        /* User Info Styling */
        .user-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #0d6efd;
            margin-bottom: 1rem;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }
        
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
        
        .action-cell .d-flex {
            height: 100%;
            align-items: center;
            justify-content: center;
            min-height: 40px;
        }
        
        .table td:empty::before {
            content: "-";
            color: #6c757d;
        }
        
        .table thead tr th {
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
        }
        
        .sticky, .sticky-2, .sticky-3, .sticky-4, .sticky-5 {
            box-shadow: 2px 0 2px -1px rgba(0,0,0,0.1);
        }
        
        .action-cell {
            box-shadow: -2px 0 2px -1px rgba(0,0,0,0.1);
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        /* Modal Akses - MODERN & FORMAL */
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
        
        .modal-access .access-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: left;
            border-left: 4px solid #3498db;
        }
        
        .modal-access .access-note {
            color: #7f8c8d;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
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

// Redirect jika belum login
if (!$isLoggedIn) {
    header('Location: ' . base_url('/login'));
    exit();
}
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
            <i class="fas fa-tachometer-alt me-2"></i>Piezometer - Right Bank
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('left-piez') ?>" class="btn btn-outline-primary btn-piez">
                <i class="fas fa-table"></i> Left Bank
            </a>
            <a href="<?= base_url('piezometer/right') ?>" class="btn btn-primary btn-piez">Right Bank</a>
            
            <?php if ($isAdmin): ?>
                <a href="<?= base_url('right-piez/create') ?>" class="btn btn-outline-success">
                    <i class="fas fa-plus me-1"></i> Add Data
                </a>
                
                <button type="button" class="btn btn-outline-warning" onclick="showImportModal()">
                    <i class="fas fa-database me-1"></i> Import SQL
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-outline-success btn-disabled" 
                        onclick="showAccessWarning('add')"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Klik untuk melihat informasi hak akses">
                    <i class="fas fa-plus me-1"></i> Add Data
                </button>
                
                <button type="button" class="btn btn-outline-warning btn-disabled"
                       onclick="showAccessWarning('import')"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Klik untuk melihat informasi hak akses">
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
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data Piezometer</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Mengakses semua titik (R-01 s/d PZ-04)</li>
                            <li><i class="fas fa-check"></i> Melihat data Right Bank</li>
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

            <!-- TMA Filter -->
            <div class="filter-item">
                <label for="tmaFilter" class="form-label">TMA</label>
                <select id="tmaFilter" class="form-select">
                    <option value="">Semua TMA</option>
                    <?php
                    if (!empty($pengukuran)):
                        $uniqueTMA = [];
                        foreach ($pengukuran as $item) {
                            $tma = $item['pengukuran']['tma'] ?? '-';
                            if ($tma !== '-' && $tma !== '' && !in_array($tma, $uniqueTMA)) {
                                $uniqueTMA[] = $tma;
                            }
                        }
                        sort($uniqueTMA);
                        foreach ($uniqueTMA as $tma):
                    ?>
                        <option value="<?= esc($tma) ?>"><?= esc($tma) ?></option>
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
    <div class="table-responsive" id="tableContainer">
        <table class="data-table table table-bordered table-hover" id="exportTable">
            <thead>
                <!-- Row 1: Main Header -->
                <tr>
                    <!-- KOLOM INFORMASI - WARNA BIRU MUDA (#e7f1ff) -->
                    <th rowspan="4" class="sticky bg-info-column">TAHUN</th>
                    <th rowspan="4" class="sticky-2 bg-info-column">PERIODE</th>
                    <th rowspan="4" class="sticky-3 bg-info-column">TANGGAL</th>
                    <th rowspan="4" class="sticky-4 bg-info-column">TMA</th>
                    <th rowspan="4" class="sticky-5 bg-info-column">CH HUJAN</th>
                    
                    <!-- BACAAN METRIK - WARNA KUNING MUDA (#fff2cc) -->
                    <th rowspan="2" colspan="28" class="bg-metrik">BACAAN PIEZOMETER</th>

                    <!-- KONVERSI - WARNA HIJAU MUDA (#f0f9eb) -->
                    <th rowspan="2" colspan="2" class="bg-calculation">KONVERSI</th>

                    <!-- BACAAN PIEZOMETER METRIK - WARNA BIRU SANGAT MUDA (#e8f4fd) -->
                    <th rowspan="2" colspan="14" class="bg-reading">BACAAN PIEZOMETER</th>

                    <!-- INITIAL READINGS - WARNA HIJAU SANGAT MUDA (#e6ffed) -->
                    <th colspan="15" class="bg-initial">INITIAL READINGS ATAS</th>
                    
                    <!-- ACTION - WARNA ABU-ABU MUDA (#f8f9fa) -->
                    <th rowspan="4" class="action-cell bg-action">AKSI</th>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- INITIAL READINGS Sub Headers -->
                    <th class="bg-initial">Elev.Piez</th>
                    <th class="bg-initial">R-01</th>
                    <th class="bg-initial">R-02</th>
                    <th class="bg-initial">R-03</th>
                    <th class="bg-initial">R-04</th>
                    <th class="bg-initial">R-05</th>
                    <th class="bg-initial">R-06</th>
                    <th class="bg-initial">R-07</th>
                    <th class="bg-initial">R-08</th>
                    <th class="bg-initial">R-09</th>
                    <th class="bg-initial">R-10</th>
                    <th class="bg-initial">R-11</th>
                    <th class="bg-initial">R-12</th>
                    <th class="bg-initial">IPZ-01</th>
                    <th class="bg-initial">PZ-04</th>
                </tr>

                <!-- Row 3: Column Headers -->
                <tr>
                    <!-- BACAAN METRIK Headers - WARNA KUNING MUDA -->
                    <th colspan="2" class="bg-metrik">R-01</th>
                    <th colspan="2" class="bg-metrik">R-02</th>
                    <th colspan="2" class="bg-metrik">R-03</th>
                    <th colspan="2" class="bg-metrik">R-04</th>
                    <th colspan="2" class="bg-metrik">R-05</th>
                    <th colspan="2" class="bg-metrik">R-06</th>
                    <th colspan="2" class="bg-metrik">R-07</th>
                    <th colspan="2" class="bg-metrik">R-08</th>
                    <th colspan="2" class="bg-metrik">R-09</th>
                    <th colspan="2" class="bg-metrik">R-10</th>
                    <th colspan="2" class="bg-metrik">R-11</th>
                    <th colspan="2" class="bg-metrik">R-12</th>
                    <th colspan="2" class="bg-metrik">IPZ-01</th>
                    <th colspan="2" class="bg-metrik">PZ-04</th>
                    
                    <!-- KONVERSI Sub Headers - WARNA HIJAU MUDA -->
                    <th rowspan="2" class="bg-calculation">FEET → M</th>
                    <th rowspan="2" class="bg-calculation">INCH → M</th>
                    
                    <!-- BACAAN PIEZOMETER Sub Headers - WARNA BIRU SANGAT MUDA -->
                    <th rowspan="2" class="bg-reading">R-01</th>
                    <th rowspan="2" class="bg-reading">R-02</th>
                    <th rowspan="2" class="bg-reading">R-03</th>
                    <th rowspan="2" class="bg-reading">R-04</th>
                    <th rowspan="2" class="bg-reading">R-05</th>
                    <th rowspan="2" class="bg-reading">R-06</th>
                    <th rowspan="2" class="bg-reading">R-07</th>
                    <th rowspan="2" class="bg-reading">R-08</th>
                    <th rowspan="2" class="bg-reading">R-09</th>
                    <th rowspan="2" class="bg-reading">R-10</th>
                    <th rowspan="2" class="bg-reading">R-11</th>
                    <th rowspan="2" class="bg-reading">R-12</th>
                    <th rowspan="2" class="bg-reading">IPZ-01</th>
                    <th rowspan="2" class="bg-reading">PZ-04</th>

                    <!-- INITIAL READINGS Headers - WARNA HIJAU SANGAT MUDA -->
                    <th class="bg-initial">Elev.Piez</th>
                    <th class="bg-initial">651.48</th>
                    <th class="bg-initial">647.22</th>
                    <th class="bg-initial">606.43</th>
                    <th class="bg-initial">586.41</th>
                    <th class="bg-initial">655.30</th>
                    <th class="bg-initial">661.03</th>
                    <th class="bg-initial">649.06</th>
                    <th class="bg-initial">671.51</th>
                    <th class="bg-initial">656.48</th>
                    <th class="bg-initial">677.35</th>
                    <th class="bg-initial">644.90</th>
                    <th class="bg-initial">630.49</th>
                    <th class="bg-initial">649.90</th>
                    <th class="bg-initial">651.39</th>
                </tr>

                <!-- Row 4: Column Headers -->
                <tr>
                    <!-- BACAAN METRIK Headers - WARNA KUNING MUDA -->
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    <th class="bg-metrik">Feet</th>
                    <th class="bg-metrik">Inch</th>
                    
                    <!-- PERHITUNGAN Headers - WARNA HIJAU MUDA -->
                    <th class="bg-calculation">Kedalaman</th>
                    <th class="bg-calculation">50.00</th>
                    <th class="bg-calculation">60.00</th>
                    <th class="bg-calculation">50.00</th>
                    <th class="bg-calculation">51.00</th>
                    <th class="bg-calculation">50.27</th>
                    <th class="bg-calculation">60.00</th>
                    <th class="bg-calculation">50.00</th>
                    <th class="bg-calculation">40.00</th>
                    <th class="bg-calculation">42.00</th>
                    <th class="bg-calculation">-</th>
                    <th class="bg-calculation">57.00</th>
                    <th class="bg-calculation">42.00</th>
                    <th class="bg-calculation">-</th>
                    <th class="bg-calculation">73.50</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <?php if(empty($pengukuran)): ?>
                    <tr>
                        <td colspan="105" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data Piezometer yang tersedia</p>
                            <?php if ($isAdmin): ?>
                                <a href="<?= base_url('right-piez/create') ?>" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                                </a>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary mt-2 btn-disabled" onclick="showAccessWarning('add')">
                                    <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $titikList = ['R-01', 'R-02', 'R-03', 'R-04', 'R-05', 'R-06', 'R-07', 'R-08', 'R-09', 'R-10', 'R-11', 'R-12', 'IPZ-01', 'PZ-04'];
                    
                    // KELOMPOKKAN DATA BERDASARKAN TAHUN
                    $groupedByYear = [];
                    foreach($pengukuran as $item) {
                        $year = $item['pengukuran']['tahun'] ?? '-';
                        if (!isset($groupedByYear[$year])) {
                            $groupedByYear[$year] = [];
                        }
                        $groupedByYear[$year][] = $item;
                    }
                    
                    // URUTKAN TAHUN ASCENDING
                    ksort($groupedByYear);
                    
                    // LOOP MELALUI SETIAP TAHUN
                    foreach($groupedByYear as $year => $yearData): 
                        // URUTKAN DATA DALAM TAHUN BERDASARKAN TANGGAL ASCENDING (lama ke baru)
                        usort($yearData, function($a, $b) {
                            $dateA = strtotime($a['pengukuran']['tanggal'] ?? '1970-01-01');
                            $dateB = strtotime($b['pengukuran']['tanggal'] ?? '1970-01-01');
                            return $dateA - $dateB; // Urutkan tanggal ascending (lama ke baru)
                        });
                        
                        $rowCount = count($yearData);
                        $firstRow = true;
                        
                        // LOOP MELALUI SETIAP DATA DALAM TAHUN
                        foreach($yearData as $index => $item): 
                            $p = $item['pengukuran'];
                            
                            $metrik = $item['metrik'] ?? [];
                            $initial = $item['initial'] ?? [];
                            $perhitungan = $item['perhitungan'] ?? [];
                            $pembacaan = $item['pembacaan'] ?? [];
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>" data-tahun="<?= esc($p['tahun'] ?? '') ?>" data-periode="<?= esc($p['periode'] ?? '') ?>" data-tma="<?= esc($p['tma'] ?? '') ?>">
                        <!-- Basic Information - WARNA BIRU MUDA -->
                        <?php if ($firstRow): ?>
                            <td class="sticky bg-info-column" rowspan="<?= $rowCount ?>">
                                <strong><?= esc($year) ?></strong>
                            </td>
                        <?php endif; ?>
                        <td class="sticky-2 bg-info-column"><?= esc($p['periode'] ?? '-') ?></td>
                        <td class="sticky-3 bg-info-column"><?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?></td>
                        <td class="sticky-4 bg-info-column"><?= formatNumberAsIs($p['tma']) ?></td>
                        <td class="sticky-5 bg-info-column"><?= formatNumberAsIs($p['ch_hujan']) ?></td>
                        
                        <!-- BACAAN METRIK - Feet & Inch - WARNA KUNING MUDA -->
                        <?php foreach($titikList as $titik): 
                            $bacaanData = $pembacaan[$titik] ?? [];
                            $feet = $bacaanData['feet'] ?? null;
                            $inch = $bacaanData['inch'] ?? null;
                        ?>
                            <td class="number-cell bg-metrik"><?= formatNumberAsIs($feet) ?></td>
                            <td class="number-cell bg-metrik"><?= formatNumberAsIs($inch) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- KONVERSI STATIS - WARNA HIJAU MUDA -->
                        <td class="number-cell bg-calculation">0.3048</td>
                        <td class="number-cell bg-calculation">0.0254</td>
                        
                        <!-- BACAAN PIEZOMETER METRIK - WARNA BIRU SANGAT MUDA -->
                        <?php foreach($titikList as $titik): 
                            $meter = $metrik[$titik] ?? null;
                        ?>
                            <td class="number-cell bg-reading"><?= formatNumberAsIs($meter) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- INITIAL READINGS - WARNA HIJAU SANGAT MUDA -->
                         <td class="number-cell bg-calculation">-</td>
                        <?php foreach($titikList as $titik): 
                            $initialData = $initial[$titik] ?? [];
                            $elvPiez = $initialData['Elv_Piez'] ?? null;
                        ?>
                            <td class="number-cell bg-initial"><?= formatNumberAsIs($elvPiez) ?></td>
                        <?php endforeach; ?>
                        
                        <!-- ACTION BUTTONS - WARNA ABU-ABU MUDA -->
                        <td class="action-cell bg-action">
                            <div class="d-flex justify-content-center align-items-center">
                                <?php if ($isAdmin): ?>
                                    <a href="<?= base_url('right-piez/edit/' . $p['id_pengukuran']) ?>" 
                                       class="btn-action btn-edit" title="Edit Data">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button type="button" class="btn-action btn-delete delete-data" 
                                            data-id="<?= $p['id_pengukuran'] ?>" title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn-action btn-disabled" 
                                            onclick="showAccessWarning('edit', '<?= $p['tahun'] ?? '' ?>', '<?= $p['periode'] ?? '' ?>', '<?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?>')"
                                            title="Klik untuk melihat informasi hak akses">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-disabled"
                                           onclick="showAccessWarning('delete', '<?= $p['tahun'] ?? '' ?>', '<?= $p['periode'] ?? '' ?>', '<?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?>')"
                                           title="Klik untuk melihat informasi hak akses">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        $firstRow = false;
                        endforeach; // End loop data dalam tahun
                    endforeach; // End loop tahun
                    ?>
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

<!-- Modal Import SQL (Hanya untuk Admin) -->
<?php if ($isAdmin): ?>
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
                                    <li>Initial Reading</li>
                                    <li>Perhitungan</li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="mb-1">
                                    <li>Perhitungan R-01 s/d R-12</li>
                                    <li>Perhitungan IPZ-01</li>
                                    <li>Perhitungan PZ-04</li>
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
<?php endif; ?>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Variabel global
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
let deleteId = null;
let lastYear = null;

// Variabel global untuk modal hak akses
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

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
            message = `Fitur pengeditan data Piezometer (Tahun: ${tahun || '-'}, Periode: ${periode || '-'}, Tanggal: ${tanggal || '-'}) tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'delete':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penghapusan data Piezometer (Tahun: ${tahun || '-'}, Periode: ${periode || '-'}, Tanggal: ${tanggal || '-'}) tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'import':
            title = 'Akses Tidak Tersedia';
            message = `Fitur import database Piezometer tidak dapat diakses dengan level pengguna saat ini.`;
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

// ============ FUNGSI UTAMA ============

// PERBAIKAN: Fungsi untuk mengatur ulang tinggi header secara dinamis
function recalculateHeaderHeights() {
    const thead = document.querySelector('.table thead');
    if (!thead) return;
    
    const rows = thead.querySelectorAll('tr');
    let accumulatedHeight = 0;
    
    rows.forEach((row, index) => {
        const rowHeight = row.offsetHeight;
        
        // Update semua th dalam row ini dengan top position yang tepat
        const ths = row.querySelectorAll('th');
        ths.forEach(th => {
            th.style.top = accumulatedHeight + 'px';
        });
        
        accumulatedHeight += rowHeight;
    });
}

// Fungsi untuk memastikan kolom aksi selalu terlihat
function ensureActionColumnVisible() {
    const tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        setTimeout(() => {
            tableContainer.scrollLeft = tableContainer.scrollWidth;
        }, 100);
    }
}

// Fungsi untuk filter tabel
function filterTable() {
    const tahunValue = document.getElementById('tahunFilter').value.toLowerCase();
    const periodeValue = document.getElementById('periodeFilter').value.toLowerCase();
    const tmaValue = document.getElementById('tmaFilter').value.toLowerCase();
    const searchValue = document.getElementById('searchInput').value.toLowerCase();

    const rows = document.querySelectorAll('#dataTableBody tr[data-pid]');
    let visibleCount = 0;
    let currentYear = null;
    let yearRowspan = 0;
    let yearStartRow = null;
    
    rows.forEach(row => {
        const tahun = row.getAttribute('data-tahun')?.toLowerCase() || '';
        const periode = row.getAttribute('data-periode')?.toLowerCase() || '';
        const tma = row.getAttribute('data-tma')?.toLowerCase() || '';
        const rowText = row.textContent.toLowerCase();

        const tahunMatch = !tahunValue || tahun === tahunValue;
        const periodeMatch = !periodeValue || periode === periodeValue;
        const tmaMatch = !tmaValue || tma === tmaValue;
        const searchMatch = !searchValue || rowText.includes(searchValue);

        const isVisible = tahunMatch && periodeMatch && tmaMatch && searchMatch;
        
        // Handle rowspan untuk tahun
        const yearCell = row.querySelector('td.sticky');
        if (yearCell && yearCell.hasAttribute('rowspan')) {
            currentYear = tahun;
            yearRowspan = parseInt(yearCell.getAttribute('rowspan'));
            yearStartRow = row;
        }
        
        if (isVisible) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
            if (yearCell && yearCell.hasAttribute('rowspan')) {
                // Kurangi rowspan jika baris ini disembunyikan
                yearRowspan--;
                yearCell.setAttribute('rowspan', yearRowspan);
                if (yearRowspan === 0) {
                    yearStartRow.style.display = 'none';
                }
            }
        }
    });
}

// Fungsi untuk update tampilan tahun (menyembunyikan duplikat)
function updateYearDisplay() {
    const yearCells = document.querySelectorAll('#dataTableBody td.sticky');
    let lastYear = null;
    
    yearCells.forEach(cell => {
        if (cell.hasAttribute('rowspan')) {
            lastYear = cell.textContent.trim();
        } else {
            cell.style.display = 'none';
        }
    });
}

// ============ EVENT HANDLERS ============

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

    // Export Excel - MENGGUNAKAN CONTROLLER BARU
    document.getElementById('exportExcel').addEventListener('click', function() {
        const originalText = this.innerHTML;
        const originalButton = this;
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
        this.disabled = true;

        // Ambil nilai filter
        const tahunFilter = document.getElementById('tahunFilter').value;
        const periodeFilter = document.getElementById('periodeFilter').value;
        const tmaFilter = document.getElementById('tmaFilter').value;
        
        // Buat URL dengan parameter filter
        let exportUrl = '<?= base_url("rightpiezo/export-excel") ?>';
        const params = [];
        
        if (tahunFilter) params.push(`tahun=${encodeURIComponent(tahunFilter)}`);
        if (periodeFilter) params.push(`periode=${encodeURIComponent(periodeFilter)}`);
        if (tmaFilter) params.push(`tma=${encodeURIComponent(tmaFilter)}`);
        
        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }
        
        // Buat elemen anchor untuk download
        const anchor = document.createElement('a');
        anchor.style.display = 'none';
        anchor.href = exportUrl;
        anchor.download = 'Piezometer_Right_Bank_Export_' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '.xlsx';
        
        // Tambahkan anchor ke body dan klik
        document.body.appendChild(anchor);
        anchor.click();
        
        // Hapus anchor setelah download
        setTimeout(() => {
            document.body.removeChild(anchor);
            originalButton.innerHTML = originalText;
            originalButton.disabled = false;
            
            // Tampilkan notifikasi sukses
            showToast('success', 'Export berhasil! File sedang didownload.');
        }, 1000);
        
        // Fallback timeout
        setTimeout(() => {
            originalButton.innerHTML = originalText;
            originalButton.disabled = false;
        }, 10000);
    });

    // Delete Data (hanya untuk admin)
    <?php if ($isAdmin): ?>
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.querySelectorAll('.delete-data').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteId = this.getAttribute('data-id');
            deleteModal.show();
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const deleteButton = this;
            const originalText = deleteButton.innerHTML;
            
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';

            fetch('<?= base_url('right-piez/delete') ?>/' + deleteId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Data berhasil dihapus');
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
                setTimeout(() => {
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = originalText;
                }, 500);
            });
        }
    });
    <?php endif; ?>

    // Fungsi untuk menampilkan toast notification
    function showToast(type, message) {
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
        
        toastContainer.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toastContainer);
        });
    }

    // Filter Functionality
    const tahunFilter = document.getElementById('tahunFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const tmaFilter = document.getElementById('tmaFilter');
    const searchInput = document.getElementById('searchInput');
    const resetFilter = document.getElementById('resetFilter');

    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    tmaFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
        periodeFilter.value = '';
        tmaFilter.value = '';
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

    // Import SQL Functionality (hanya untuk admin)
    <?php if ($isAdmin): ?>
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

        fetch('<?= base_url('right-piez/import-sql') ?>', {
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

    document.getElementById('importSqlModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('sqlFile').value = '';
        document.getElementById('importProgress').style.display = 'none';
        document.getElementById('importStatus').style.display = 'none';
    });
    <?php endif; ?>

    // Inisialisasi filter pertama kali
    filterTable();
    
    setTimeout(() => {
        const tableContainer = document.getElementById('tableContainer');
        if (tableContainer) {
            tableContainer.scrollLeft = tableContainer.scrollWidth;
        }
    }, 100);
});

function showImportModal() {
    <?php if ($isAdmin): ?>
    const modal = new bootstrap.Modal(document.getElementById('importSqlModal'));
    modal.show();
    <?php else: ?>
    showAccessWarning('import');
    <?php endif; ?>
}
</script>

<?php
/**
 * Fungsi helper untuk menampilkan angka sesuai dengan yang ada di database
 * tanpa melakukan pembulatan atau format tambahan
 */
function formatNumberAsIs($value) {
    if ($value === null || $value === '' || $value === '-') {
        return '-';
    }
    
    // Jika sudah berupa string, kembalikan langsung
    if (is_string($value)) {
        return $value;
    }
    
    // Jika numeric, kembalikan sebagai string tanpa format tambahan
    if (is_numeric($value)) {
        return (string)$value;
    }
    
    return '-';
}
?>
</body>
</html>