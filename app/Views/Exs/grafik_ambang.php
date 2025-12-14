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
        
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
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
            opacity:1;
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
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data Extensometer</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Melihat grafik & ambang batas</li>
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

    <!-- Main Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="exportTable">
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
                // LOGIKA BARU: 
                // - Hijau jika <= batas hijau
                // - Kuning jika <= batas kuning (sudah melewati hijau)
                // - Merah jika > batas kuning (sudah melewati kuning)
                function getStatusClass($bacaan, $ambang) {
                    if ($bacaan === null || $bacaan === '') {
                        return '';
                    }
                    
                    // Convert to float for comparison
                    $bacaan = (float)$bacaan;
                    
                    // Jika bacaan <= batas hijau, status HIJAU
                    if ($bacaan <= $ambang['hijau']) {
                        return 'bg-status-hijau';
                    } 
                    // Jika bacaan <= batas kuning, status KUNING
                    elseif ($bacaan <= $ambang['kuning']) {
                        return 'bg-status-kuning';
                    } 
                    // Jika bacaan > batas kuning, status MERAH
                    else {
                        return 'bg-status-merah';
                    }
                }
                
                // Fungsi format number
                function formatNumber($number) {
                    if ($number === null || $number === '') return '-';
                    return number_format($number, 2, '.', '');
                }
                
                // Urutkan data berdasarkan tanggal ASC (terlama ke terbaru)
                $sortedPengukuran = $pengukuran ?? [];
                if (!empty($sortedPengukuran)) {
                    usort($sortedPengukuran, function($a, $b) {
                        $dateA = strtotime($a['pengukuran']['tanggal'] ?? '1970-01-01');
                        $dateB = strtotime($b['pengukuran']['tanggal'] ?? '1970-01-01');
                        return $dateA - $dateB; // ASC (terlama ke terbaru)
                    });
                }
                ?>
                
                <?php if(empty($sortedPengukuran)): ?>
                    <tr>
                        <td colspan="91" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data monitoring yang tersedia</p>
                            <?php if ($isAdmin): ?>
                            <a href="<?= base_url('extenso/create') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($sortedPengukuran as $item): 
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
                    <tr data-year="<?= $p['tahun'] ?? '' ?>" data-periode="<?= $p['periode'] ?? '' ?>">
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

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Data dan state management
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;

// Variabel global untuk modal hak akses
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

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

// ============ FILTER FUNCTIONALITY ============
document.addEventListener('DOMContentLoaded', function() {
    const tahunFilter = document.getElementById('tahunFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const searchInput = document.getElementById('searchInput');
    const resetFilter = document.getElementById('resetFilter');
    const rows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const tahunValue = tahunFilter.value;
        const periodeValue = periodeFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();
        
        rows.forEach(row => {
            if (row.cells.length > 0) {
                const tahun = row.getAttribute('data-year') || '';
                const periode = row.getAttribute('data-periode') || '';
                const rowText = row.textContent.toLowerCase();
                
                const tahunMatch = !tahunValue || tahun === tahunValue;
                const periodeMatch = !periodeValue || periode.toLowerCase() === periodeValue;
                const searchMatch = !searchValue || rowText.includes(searchValue);
                
                if (tahunMatch && periodeMatch && searchMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
    
    function resetAllFilters() {
        tahunFilter.value = '';
        periodeFilter.value = '';
        searchInput.value = '';
        
        // Show all rows
        rows.forEach(row => {
            row.style.display = '';
        });
    }
    
    // Event listeners
    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    resetFilter.addEventListener('click', resetAllFilters);
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============ IMPORT SQL FUNCTIONALITY ============
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

        // === Validasi file ===
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

        // === Progress Bar ===
        importProgress.style.display = 'block';
        const progressBar = importProgress.querySelector('.progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        btnImport.disabled = true;
        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

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

                // Auto-refresh 3 detik setelah sukses
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
});
<?php endif; ?>
</script>
</body>
</html>