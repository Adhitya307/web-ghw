<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'InclinoMeter - PT Indonesia Power' ?></title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        /* WARNA TABEL */
        .bg-reading { background-color: #e8f4fd !important; }
        .bg-calculation { background-color: #f0f9eb !important; }
        .bg-result { background-color: #e6f7ff !important; }
        .bg-metrik { background-color: #fff2cc !important; }
        .bg-initial { background-color: #e6ffed !important; }
        .bg-info-column { background-color: #e7f1ff !important; }

        /* WARNA HEADER */
        .point-header, .calculation-header, .initial-header, .conversion-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
            font-weight: 600;
        }

        .reading-date-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            color: white !important;
            font-weight: 600;
            text-align: center;
            font-size: 0.8rem;
        }

        /* TABEL STYLING */
        .table-container-wrapper {
            position: relative;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-responsive {
            max-height: 700px;
            overflow: auto;
            position: relative;
        }

        .table {
            margin-bottom: 0;
            min-width: 1500px;
        }

        .table th {
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
            padding: 0.4rem 0.3rem;
            position: sticky;
            z-index: 10;
            min-width: 100px;
        }

        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.3rem 0.4rem;
            font-size: 0.8rem;
            white-space: nowrap;
            background-color: white;
        }

        /* STICKY HEADER LEVELS */
        .table thead tr:nth-child(1) th {
            top: 0;
            z-index: 100;
            height: 40px;
        }
        
        .table thead tr:nth-child(2) th {
            top: 40px;
            z-index: 99;
            height: 40px;
        }
        
        .table thead tr:nth-child(3) th {
            top: 80px;
            z-index: 98;
            height: 40px;
        }
        
        .table thead tr:nth-child(4) th {
            top: 120px;
            z-index: 97;
            height: 40px;
        }

        /* STICKY COLUMNS */
        .sticky-col-depth {
            position: sticky; 
            left: 0; 
            z-index: 95;
            background-color: #e6f7ff !important;
            border-right: 2px solid #dee2e6 !important;
            min-width: 80px;
            font-weight: 600;
        }

        /* BUTTON STYLES */
        .btn-action {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
            margin: 0 2px;
            font-size: 0.75rem;
            border: none;
        }
        
        .btn-edit {
            background-color: var(--info-color);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #0bb5d4;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(13, 202, 240, 0.3);
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }
        
        /* Button disabled untuk non-admin */
        .btn-disabled {
            background-color: #e9ecef !important;
            color: #6c757d !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .btn-disabled:hover {
            background-color: #e9ecef !important;
            transform: translateY(0);
            box-shadow: none;
        }

        /* FILTER SECTION */
        .filter-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filter-group {
            display: flex;
            gap: 1.25rem;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 180px;
        }
        
        .filter-item label {
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        /* TABLE HEADER */
        .table-header {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .table-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.25rem;
            font-size: 1.5rem;
        }

        /* METADATA SECTION */
        .metadata-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        
        .metadata-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .metadata-item {
            margin-bottom: 0.75rem;
        }
        
        .metadata-label {
            display: block;
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .metadata-value {
            display: block;
            font-size: 0.95rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        /* LOADING AND PLACEHOLDER */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e9ecef;
            border-top: 3px solid var(--info-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .table-placeholder {
            padding: 3rem;
            text-align: center;
            color: #6c757d;
        }
        
        .table-placeholder i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .no-data-message {
            padding: 3rem;
            text-align: center;
            color: #6c757d;
            display: none;
        }

        /* SCROLL INDICATOR */
        .scroll-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(33, 37, 41, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            z-index: 2000;
            display: none;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .scroll-indicator i {
            margin-right: 8px;
        }

        /* CELL STYLES */
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            padding-right: 10px !important;
        }
        
        .text-cell {
            text-align: center;
            font-weight: 500;
        }
        
        .depth-cell {
            text-align: center;
            font-weight: 600;
            background-color: #e6f7ff !important;
        }
        
        .empty-cell::before {
            content: "-";
            color: #adb5bd;
        }

        /* ROW HOVER EFFECT */
        .table tbody tr:hover {
            background-color: #f5f5f5;
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover td {
            background-color: #f5f5f5;
        }
        
        .table tbody tr:hover .sticky-col-depth {
            background-color: #d1ecf1 !important;
        }
        
        /* ZEBRA STRIPING */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        
        .table-striped tbody tr:nth-of-type(odd):hover {
            background-color: #f5f5f5;
        }

        /* MODAL AKSES */
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

        /* RESPONSIVE ADJUSTMENTS */
        @media (max-width: 1200px) {
            .filter-item {
                min-width: 200px;
            }
            
            .table-title {
                font-size: 1.3rem;
            }
        }
        
        @media (max-width: 768px) {
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-item {
                min-width: 100%;
            }
            
            .table-controls {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .btn-group {
                flex-wrap: wrap;
            }
            
            .btn-action {
                width: 32px;
                height: 32px;
            }
        }

        /* UTILITY CLASSES */
        .text-small {
            font-size: 0.85rem;
        }
        
        .fw-medium {
            font-weight: 500;
        }
        
        .shadow-soft {
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .border-light {
            border-color: #e9ecef !important;
        }

        /* MODAL STYLES */
        .modal-content {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 1.25rem 1.5rem;
        }
        
        .modal-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* HEADER FIXES */
        .col-header {
            min-width: 120px;
        }
        
        .col-header-small {
            min-width: 100px;
        }
    </style>
</head>
<body>
<?php
// ============ CEK LOGIN ============
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
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-compass me-2 text-info"></i>InclinoMeter - Monitoring
        </h2>

        <!-- Button Group (DIMODIFIKASI: Tombol Add Data dihapus, ditambah Profil A & B) -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            
            <!-- TOMBOL PROFIL A - DIUBAH KE LINK -->
            <a href="<?= site_url('inclino/profilea/view') ?>" class="btn btn-info" id="btnProfilA">
                <i class="fas fa-chart-line me-1"></i> Profil A
            </a>
            
           <!-- TOMBOL PROFIL B - DIUBAH KE LINK -->
            <a href="<?= site_url('inclino/profileb') ?>" class="btn btn-success" id="btnProfilB">
                <i class="fas fa-chart-bar me-1"></i> Profil B
            </a>
            
            <!-- TOMBOL EXPORT DATA -->
            <button type="button" class="btn btn-warning" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Data
            </button>
            
            <!-- TOMBOL IMPORT CSV (Hanya untuk Admin) -->
            <?php if ($isAdmin): ?>
                <button type="button" class="btn btn-outline-primary" id="showCsvModalBtn">
                    <i class="fas fa-file-csv me-1"></i> Import CSV
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-outline-primary btn-disabled"
                       onclick="showAccessWarning('import')"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Klik untuk melihat informasi hak akses">
                    <i class="fas fa-file-csv me-1"></i> Import CSV
                </button>
            <?php endif; ?>
        </div>

        <div class="table-controls">
            <div class="input-group" style="max-width: 350px;">
                <span class="input-group-text bg-light"><i class="fas fa-search text-secondary"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal untuk Profil A -->
    <div class="modal fade" id="modalProfilA" tabindex="-1" aria-labelledby="modalProfilALabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalProfilALabel">
                        <i class="fas fa-chart-line me-2"></i>Data Profil A
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="profilAContent">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-info mb-3"></i>
                            <p>Memuat data Profil A...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-info" id="exportProfilA">
                        <i class="fas fa-file-excel me-1"></i> Export Profil A
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Profil B -->
    <div class="modal fade" id="modalProfilB" tabindex="-1" aria-labelledby="modalProfilBLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalProfilBLabel">
                        <i class="fas fa-chart-bar me-2"></i>Data Profil B
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="profilBContent">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-success mb-3"></i>
                            <p>Memuat data Profil B...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="exportProfilB">
                        <i class="fas fa-file-excel me-1"></i> Export Profil B
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Peringatan Hak Akses -->
    <div class="modal fade modal-access" id="accessWarningModal" tabindex="-1" aria-labelledby="accessWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accessWarningModalLabel">
                        <i class="fas fa-shield-alt me-2"></i>Pengaturan Akses InclinoMeter
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
                            <li><i class="fas fa-check"></i> Melihat dan menelusuri data InclinoMeter</li>
                            <li><i class="fas fa-check"></i> Mencari dan memfilter informasi</li>
                            <li><i class="fas fa-check"></i> Mengekspor data ke format Excel</li>
                            <li><i class="fas fa-check"></i> Mengakses semua lokasi InclinoMeter</li>
                            <li><i class="fas fa-check"></i> Melihat metadata dan informasi teknis</li>
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
        <h5 class="fw-bold mb-3">
            <i class="fas fa-filter me-2 text-primary"></i>Filter Data
        </h5>
        <div class="filter-group">
            <!-- Tahun -->
            <div class="filter-item">
                <label for="tahunFilter" class="form-label">Tahun</label>
                <select id="tahunFilter" class="form-select shadow-soft">
                    <option value="">Pilih Tahun</option>
                    <?php 
                    if (isset($years) && !empty($years)): 
                        foreach ($years as $year): ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach; 
                    else: ?>
                    <option value="">-- Data Tahun Kosong --</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Bulan (HANYA TAMPILAN - akan diupdate otomatis) -->
            <div class="filter-item">
                <label for="bulanFilter" class="form-label">Bulan</label>
                <select id="bulanFilter" class="form-select shadow-soft" disabled>
                    <option value="">Pilih Tahun Terlebih Dahulu</option>
                </select>
            </div>

            <!-- Tanggal (HANYA TAMPILAN - akan diupdate otomatis) -->
            <div class="filter-item">
                <label for="tanggalFilter" class="form-label">Tanggal</label>
                <select id="tanggalFilter" class="form-select shadow-soft" disabled>
                    <option value="">Pilih Bulan Terlebih Dahulu</option>
                </select>
            </div>

            <!-- Search Button -->
            <div class="filter-item" style="flex: 0 0 auto;">
                <button id="applyFilter" class="btn btn-primary px-4">
                    <i class="fas fa-search me-1"></i> Muat Data
                </button>
            </div>

            <!-- Reset -->
            <div class="filter-item" style="flex: 0 0 auto;">
                <button id="resetFilter" class="btn btn-outline-secondary">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Metadata Section -->
    <div id="metadataSection" class="metadata-card" style="display: none;">
        <div class="metadata-title">
            <i class="fas fa-info-circle me-2 text-info"></i>Informasi Data
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Lokasi:</span>
                    <span class="metadata-value" id="metadataBorehole">-</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Tanggal Pengukuran:</span>
                    <span class="metadata-value" id="metadataDate">-</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metadata-item">
                    <span class="metadata-label">Serial Probe:</span>
                    <span class="metadata-value" id="metadataProbe">-</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metadata-item">
                    <span class="metadata-label">Serial Reel:</span>
                    <span class="metadata-value" id="metadataReel">-</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metadata-item">
                    <span class="metadata-label">Operator:</span>
                    <span class="metadata-value" id="metadataOperator">-</span>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Total Data:</span>
                    <span class="metadata-value" id="metadataTotal">-</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Kedalaman Minimum:</span>
                    <span class="metadata-value" id="metadataMinDepth">-</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Kedalaman Maksimum:</span>
                    <span class="metadata-value" id="metadataMaxDepth">-</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Status:</span>
                    <span class="metadata-value">
                        <span class="badge bg-success">Aktif</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="table-container-wrapper">
        <div class="table-responsive" id="tableContainer">
            <div class="loading-overlay" id="tableLoading" style="display: none;">
                <div class="loading-spinner mb-3"></div>
                <div class="text-muted fw-medium">Memuat data...</div>
            </div>
            
            <div id="tablePlaceholder" class="table-placeholder">
                <i class="fas fa-compass fa-3x text-muted mb-3"></i>
                <h4 class="text-muted fw-medium mb-2">Data Belum Ditampilkan</h4>
                <p class="text-muted mb-3">Pilih filter dan klik "Muat Data" untuk menampilkan data</p>
            </div>
            
            <div id="dataTableWrapper" style="display: none;">
                <table class="table table-bordered table-striped table-hover mb-0" id="inclinoTable">
                    <thead id="tableHeader">
                        <!-- Header akan diisi oleh JavaScript -->
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div id="noDataMessage" class="no-data-message">
                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                <h4 class="text-muted fw-medium mb-2">Tidak Ada Data</h4>
                <p class="text-muted">Tidak ditemukan data untuk filter yang dipilih</p>
                <button class="btn btn-outline-secondary mt-2" id="resetFilterBtn">
                    <i class="fas fa-sync-alt me-1"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3" id="paginationSection" style="display: none;">
        <div class="text-muted text-small">
            Menampilkan <span id="currentCount">0</span> dari <span id="totalCount">0</span> data
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Scroll Indicator -->
<div class="scroll-indicator" id="scrollIndicator">
    <i class="fas fa-arrows-alt-h me-1"></i>
    <span id="scrollText">Scroll untuk melihat lebih banyak data</span>
</div>

<!-- Modal Upload CSV (Hanya untuk Admin) -->
<?php if ($isAdmin): ?>
<div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCsvModalLabel">
                    <i class="fas fa-file-csv me-2 text-primary"></i>Import Data InclinoMeter CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Upload file CSV data inclinometer dari alat RST Digital Inclinometer.
                </div>
                
                <form id="uploadCsvForm" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="csvFile" class="form-label fw-medium">Pilih File CSV</label>
                        <input class="form-control" type="file" id="csvFile" name="csv_file" accept=".csv,.txt" required>
                        <div class="form-text text-small">
                            Format file: .csv (Maksimal 5MB)
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="display: none;" id="uploadProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    
                    <div id="uploadStatus" class="alert" style="display: none;"></div>
                </form>
                
                <div class="card mt-3 border-light">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-medium">Format File yang Didukung</h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2 fw-medium">✅ Format RST Digital Inclinometer:</p>
                        <ul class="mb-2">
                            <li>File Version 2.2</li>
                            <li>Kolom: Depth, Face A+, Face A-, Face B+, Face B-</li>
                            <li>Metadata: Borehole, Reading Date, Probe Serial, dll</li>
                        </ul>
                        <a href="<?= base_url('inclino/import/template') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="card mt-3 border-light">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-medium">Preview Data</h6>
                    </div>
                    <div class="card-body">
                        <div id="csvPreview" class="small">
                            <p class="text-muted mb-0">Pilih file CSV untuk melihat preview data</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnUploadCSV" form="uploadCsvForm">
                    <i class="fas fa-upload me-1"></i> Upload CSV
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
// ============ VARIABEL GLOBAL ============
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
let tableData = null;
let currentData = [];
let profilAData = null;
let profilBData = null;

// Variabel untuk modal
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const modalProfilA = new bootstrap.Modal(document.getElementById('modalProfilA'));
const modalProfilB = new bootstrap.Modal(document.getElementById('modalProfilB'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

// ============ FUNGSI HAK AKSES ============
function showAccessWarning(actionType, borehole = null, date = null, depth = null) {
    let title = '';
    let message = '';
    
    switch(actionType) {
        case 'add':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penambahan data InclinoMeter tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'edit':
            title = 'Akses Tidak Tersedia';
            message = `Fitur pengeditan data InclinoMeter (Lokasi: ${borehole || '-'}, Tanggal: ${date || '-'}, Depth: ${depth || '-'}) tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'delete':
            title = 'Akses Tidak Tersedia';
            message = `Fitur penghapusan data InclinoMeter (Lokasi: ${borehole || '-'}, Tanggal: ${date || '-'}, Depth: ${depth || '-'}) tidak dapat diakses dengan level pengguna saat ini.`;
            break;
            
        case 'import':
            title = 'Akses Tidak Tersedia';
            message = `Fitur import CSV InclinoMeter tidak dapat diakses dengan level pengguna saat ini.`;
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

// ============ INISIALISASI HALAMAN ============
document.addEventListener('DOMContentLoaded', function () {
    // Event listener untuk button Import CSV
    const showCsvModalBtn = document.getElementById('showCsvModalBtn');
    if (showCsvModalBtn) {
        showCsvModalBtn.addEventListener('click', function() {
            <?php if ($isAdmin): ?>
                const csvModal = new bootstrap.Modal(document.getElementById('uploadCsvModal'));
                csvModal.show();
            <?php else: ?>
                showAccessWarning('import');
            <?php endif; ?>
        });
    }

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        exportToExcel();
    });

    // Tombol Profil A - LANGSUNG REDIRECT KE VIEW PROFIL A
    document.getElementById('btnProfilA').addEventListener('click', function() {
        // Tidak ada alert, langsung redirect
        window.location.href = '<?= site_url("inclino/profilea/view") ?>';
    });

    // Tombol Profil B - TIDAK ADA ALERT
    document.getElementById('btnProfilB').addEventListener('click', function() {
        // Tidak ada alert, langsung redirect
        window.location.href = '<?= site_url("inclino/profileb/view") ?>';
    });

    // Export Profil A
    document.getElementById('exportProfilA').addEventListener('click', function() {
        exportProfilToExcel('A');
    });

    // Export Profil B
    document.getElementById('exportProfilB').addEventListener('click', function() {
        exportProfilToExcel('B');
    });

    // Filter Functionality
    const tahunFilter = document.getElementById('tahunFilter');
    const bulanFilter = document.getElementById('bulanFilter');
    const tanggalFilter = document.getElementById('tanggalFilter');
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const applyFilter = document.getElementById('applyFilter');
    const resetFilter = document.getElementById('resetFilter');
    const resetFilterBtn = document.getElementById('resetFilterBtn');

    // Event listeners untuk filter
    tahunFilter.addEventListener('change', function() {
        if (this.value) {
            loadMonthsByYear(this.value);
        } else {
            resetMonthDayFilters();
        }
    });
    
    bulanFilter.addEventListener('change', function() {
        if (this.value && tahunFilter.value) {
            loadDaysByMonth(tahunFilter.value, this.value);
        } else {
            resetDayFilter();
        }
    });
    
    // Apply filter
    applyFilter.addEventListener('click', loadDataByFilter);
    
    // Reset filter
    resetFilter.addEventListener('click', resetFilters);
    resetFilterBtn.addEventListener('click', resetFilters);
    
    // Clear search
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        if (window.tableData) {
            filterTableData();
        }
    });
    
    // Search on input
    searchInput.addEventListener('input', debounce(filterTableData, 300));

    <?php if ($isAdmin): ?>
    // CSV File Preview
    const csvFileInput = document.getElementById('csvFile');
    if (csvFileInput) {
        csvFileInput.addEventListener('change', previewCSV);
    }

    // Handle form submission untuk CSV upload
    const uploadCsvForm = document.getElementById('uploadCsvForm');
    if (uploadCsvForm) {
        uploadCsvForm.addEventListener('submit', function(e) {
            e.preventDefault();
            uploadCSV();
        });
    }

    // Modal cleanup
    const uploadCsvModal = document.getElementById('uploadCsvModal');
    if (uploadCsvModal) {
        uploadCsvModal.addEventListener('hidden.bs.modal', resetCSVForm);
    }
    <?php endif; ?>

    // Scroll indicator functionality
    window.addEventListener('scroll', updateScrollIndicator);
});

// ============ FUNGSI LOAD DATA PROFIL ============

// Load data Profil A - MODAL
async function loadProfilAData() {
    const tahun = document.getElementById('tahunFilter').value;
    const bulan = document.getElementById('bulanFilter').value;
    const tanggal = document.getElementById('tanggalFilter').value;
    
    // Tampilkan modal
    modalProfilA.show();
    
    try {
        // Parse tanggal jika dipilih
        let day = '', month = '';
        if (tanggal && tanggal !== "Semua Tanggal") {
            const parts = tanggal.split('-');
            if (parts.length === 3) {
                day = parts[0];
                month = parts[1];
            }
        } else if (bulan && bulan !== "Semua Bulan") {
            month = bulan;
        }
        
        const params = new URLSearchParams({
            year: tahun || '',
            month: month || '',
            day: day || '',
            borehole: '' // Bisa ditambahkan jika ada filter lokasi
        });
        
        const response = await fetch(`<?= base_url('inclino/viewProfilA') ?>?${params}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            profilAData = result.data;
            renderProfilAData(profilAData);
        } else {
            document.getElementById('profilAContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${result.message || 'Gagal memuat data Profil A'}
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading Profil A:', error);
        document.getElementById('profilAContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terjadi kesalahan saat memuat data Profil A
            </div>
        `;
    }
}

// Load data Profil B - MODAL
async function loadProfilBData() {
    const tahun = document.getElementById('tahunFilter').value;
    const bulan = document.getElementById('bulanFilter').value;
    const tanggal = document.getElementById('tanggalFilter').value;
    
    // Tampilkan modal
    modalProfilB.show();
    
    try {
        // Parse tanggal jika dipilih
        let day = '', month = '';
        if (tanggal && tanggal !== "Semua Tanggal") {
            const parts = tanggal.split('-');
            if (parts.length === 3) {
                day = parts[0];
                month = parts[1];
            }
        } else if (bulan && bulan !== "Semua Bulan") {
            month = bulan;
        }
        
        const params = new URLSearchParams({
            year: tahun || '',
            month: month || '',
            day: day || '',
            borehole: '' // Bisa ditambahkan jika ada filter lokasi
        });
        
        const response = await fetch(`<?= base_url('inclino/viewProfilB') ?>?${params}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            profilBData = result.data;
            renderProfilBData(profilBData);
        } else {
            document.getElementById('profilBContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${result.message || 'Gagal memuat data Profil B'}
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading Profil B:', error);
        document.getElementById('profilBContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terjadi kesalahan saat memuat data Profil B
            </div>
        `;
    }
}

// Render data Profil A ke modal
function renderProfilAData(data) {
    const container = document.getElementById('profilAContent');
    
    if (!data || !data.data || data.data.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Tidak ada data Profil A untuk filter yang dipilih
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="mb-4">
            <h5 class="fw-bold text-info">
                <i class="fas fa-chart-line me-2"></i>Data Profil A
            </h5>
            ${data.metadata ? `
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted d-block">Lokasi</small>
                            <span class="fw-bold">${data.metadata.borehole_name || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted d-block">Tanggal</small>
                            <span class="fw-bold">${data.metadata.reading_date || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted d-block">Total Data</small>
                            <span class="fw-bold">${data.metadata.total_records || '0'}</span>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-info">
                    <tr>
                        <th>No</th>
                        <th>Depth (m)</th>
                        <th>Profil A (mm)</th>
                        <th>Mean Cum Dev A (mm)</th>
                        <th>Displace Profil A (mm)</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.data.forEach((row, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center fw-bold">${row.depth}</td>
                <td class="text-end">${row.nilai_profil_a}</td>
                <td class="text-end">${row.mean_cum_deviation_a}</td>
                <td class="text-end">${row.displace_profile_a}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

// Render data Profil B ke modal
function renderProfilBData(data) {
    const container = document.getElementById('profilBContent');
    
    if (!data || !data.data || data.data.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Tidak ada data Profil B untuk filter yang dipilih
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="mb-4">
            <h5 class="fw-bold text-success">
                <i class="fas fa-chart-bar me-2"></i>Data Profil B
            </h5>
            ${data.metadata ? `
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted d-block">Lokasi</small>
                            <span class="fw-bold">${data.metadata.borehole_name || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted d-block">Tanggal</small>
                            <span class="fw-bold">${data.metadata.reading_date || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted d-block">Total Data</small>
                            <span class="fw-bold">${data.metadata.total_records || '0'}</span>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Depth (m)</th>
                        <th>Profil B (mm)</th>
                        <th>Mean Cum Dev B (mm)</th>
                        <th>Displace Profil B (mm)</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.data.forEach((row, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center fw-bold">${row.depth}</td>
                <td class="text-end">${row.nilai_profil_b}</td>
                <td class="text-end">${row.mean_cum_deviation_b}</td>
                <td class="text-end">${row.displace_profile_b}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

// Export Profil ke Excel
function exportProfilToExcel(profilType) {
    let data, filename, sheetName;
    
    if (profilType === 'A' && profilAData && profilAData.data) {
        data = profilAData.data;
        filename = `Profil_A_${new Date().toISOString().slice(0,10)}.xlsx`;
        sheetName = "Profil A";
    } else if (profilType === 'B' && profilBData && profilBData.data) {
        data = profilBData.data;
        filename = `Profil_B_${new Date().toISOString().slice(0,10)}.xlsx`;
        sheetName = "Profil B";
    } else {
        showAlert('warning', `Tidak ada data Profil ${profilType} untuk diexport`);
        return;
    }
    
    try {
        // Create workbook
        const wb = XLSX.utils.book_new();
        
        // Prepare data array
        const exportData = [
            [`PROFIL ${profilType} DATA - PT INDONESIA POWER`],
            [],
            ['No', 'Depth (m)', `Profil ${profilType} (mm)`, 'Mean Cum Dev (mm)', 'Displace Profil (mm)']
        ];
        
        // Add data rows
        data.forEach((row, index) => {
            exportData.push([
                index + 1,
                row.depth,
                row[profilType === 'A' ? 'nilai_profil_a' : 'nilai_profil_b'],
                row[profilType === 'A' ? 'mean_cum_deviation_a' : 'mean_cum_deviation_b'],
                row[profilType === 'A' ? 'displace_profile_a' : 'displace_profile_b']
            ]);
        });
        
        // Convert to worksheet
        const ws = XLSX.utils.aoa_to_sheet(exportData);
        
        // Add to workbook and save
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        XLSX.writeFile(wb, filename);
        
        showAlert('success', `Data Profil ${profilType} berhasil diexport`);
    } catch (error) {
        console.error('Export error:', error);
        showAlert('danger', 'Gagal mengexport data');
    }
}

// ============ FUNGSI UTAMA LAINNYA ============

// Load bulan berdasarkan tahun yang dipilih
async function loadMonthsByYear(year) {
    const bulanFilter = document.getElementById('bulanFilter');
    const tanggalFilter = document.getElementById('tanggalFilter');
    
    try {
        const response = await fetch(`<?= base_url('inclino/getMonthsByYear') ?>?year=${year}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            bulanFilter.innerHTML = '<option value="">Semua Bulan</option>';
            
            if (result.months && result.months.length > 0) {
                result.months.forEach(monthObj => {
                    bulanFilter.innerHTML += `
                        <option value="${monthObj.value}">
                            ${monthObj.name}
                        </option>
                    `;
                });
                bulanFilter.disabled = false;
                
                // Reset tanggal filter
                resetDayFilter();
            } else {
                bulanFilter.innerHTML = '<option value="">-- Data Bulan Kosong --</option>';
                bulanFilter.disabled = false;
                resetDayFilter();
            }
        } else {
            showAlert('warning', 'Gagal memuat data bulan');
            resetMonthDayFilters();
        }
    } catch (error) {
        console.error('Error loading months:', error);
        showAlert('danger', 'Terjadi kesalahan saat memuat data bulan');
        resetMonthDayFilters();
    }
}

// Load hari berdasarkan tahun dan bulan yang dipilih
async function loadDaysByMonth(year, month) {
    const tanggalFilter = document.getElementById('tanggalFilter');
    
    try {
        const response = await fetch(`<?= base_url('inclino/getDaysByMonth') ?>?year=${year}&month=${month}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            tanggalFilter.innerHTML = '<option value="">Semua Tanggal</option>';
            
            if (result.days && result.days.length > 0) {
                result.days.forEach(day => {
                    const dayStr = String(day).padStart(2, '0');
                    const monthStr = String(month).padStart(2, '0');
                    const displayDate = `${dayStr}-${monthStr}-${year}`;
                    
                    tanggalFilter.innerHTML += `
                        <option value="${displayDate}">
                            ${dayStr} ${getMonthName(month)} ${year}
                        </option>
                    `;
                });
                tanggalFilter.disabled = false;
            } else {
                tanggalFilter.innerHTML = '<option value="">-- Data Tanggal Kosong --</option>';
                tanggalFilter.disabled = false;
            }
        } else {
            showAlert('warning', 'Gagal memuat data tanggal');
            resetDayFilter();
        }
    } catch (error) {
        console.error('Error loading days:', error);
        showAlert('danger', 'Terjadi kesalahan saat memuat data tanggal');
        resetDayFilter();
    }
}

// Helper function untuk mendapatkan nama bulan
function getMonthName(month) {
    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return monthNames[parseInt(month) - 1] || '';
}

// Reset bulan dan hari filter
function resetMonthDayFilters() {
    const bulanFilter = document.getElementById('bulanFilter');
    const tanggalFilter = document.getElementById('tanggalFilter');
    
    bulanFilter.innerHTML = '<option value="">Pilih Tahun Terlebih Dahulu</option>';
    bulanFilter.disabled = true;
    
    tanggalFilter.innerHTML = '<option value="">Pilih Bulan Terlebih Dahulu</option>';
    tanggalFilter.disabled = true;
}

// Reset hari filter
function resetDayFilter() {
    const tanggalFilter = document.getElementById('tanggalFilter');
    tanggalFilter.innerHTML = '<option value="">Pilih Bulan Terlebih Dahulu</option>';
    tanggalFilter.disabled = true;
}

// Load data berdasarkan filter
async function loadDataByFilter() {
    const tahun = document.getElementById('tahunFilter').value;
    const bulan = document.getElementById('bulanFilter').value;
    const tanggal = document.getElementById('tanggalFilter').value;
    
    // Validasi minimal filter
    if (!tahun) {
        showAlert('warning', 'Pilih tahun terlebih dahulu');
        return;
    }
    
    // Tampilkan loading
    showLoading(true);
    
    try {
        // Parse tanggal jika dipilih
        let day = '', month = '';
        if (tanggal && tanggal !== "Semua Tanggal") {
            const parts = tanggal.split('-');
            if (parts.length === 3) {
                day = parts[0];
                month = parts[1];
            }
        } else if (bulan && bulan !== "Semua Bulan") {
            month = bulan;
        }
        
        const params = new URLSearchParams({
            year: tahun || '',
            month: month || '',
            day: day || '',
            borehole: '' // Bisa ditambahkan jika ada filter lokasi
        });
        
        const response = await fetch(`<?= base_url('inclino/getDataByFilter') ?>?${params}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            // Simpan data
            tableData = result.data;
            currentData = tableData.data || [];
            
            // Sort data berdasarkan depth secara DESCENDING
            if (currentData.length > 0) {
                currentData.sort((a, b) => {
                    const depthA = parseFloat(a.depth || a.kedalaman || 0);
                    const depthB = parseFloat(b.depth || b.kedalaman || 0);
                    return depthB - depthA; // DESCENDING
                });
            }
            
            // Render tabel
            if (currentData.length > 0) {
                renderTable(currentData);
                showTable(true);
                showMetadata(tableData.metadata);
                updatePagination(currentData.length);
                
                // Apply search jika ada
                if (document.getElementById('searchInput').value) {
                    filterTableData();
                }
            } else {
                showTable(false);
                showAlert('info', 'Tidak ada data ditemukan untuk filter yang dipilih');
            }
        } else {
            showAlert('danger', result.message || 'Gagal memuat data');
            showTable(false);
        }
    } catch (error) {
        console.error('Error loading data:', error);
        showAlert('danger', 'Terjadi kesalahan saat memuat data');
        showTable(false);
    } finally {
        showLoading(false);
    }
}

// Render tabel dengan data - TANPA KOLOM AKSI
function renderTable(data) {
    const tableHeader = document.getElementById('tableHeader');
    const tableBody = document.getElementById('tableBody');
    
    // Clear existing content
    tableHeader.innerHTML = '';
    tableBody.innerHTML = '';
    
    // Render header sesuai dengan struktur gambar - TANPA KOLOM AKSI
    // Baris 1: Depth dan dua set Reading Date
    const row1 = document.createElement('tr');
    row1.innerHTML = `
        <th class="reading-date-header sticky-col-depth" rowspan="4">Depth<br>(m)</th>
        <th class="reading-date-header" colspan="6" style="min-width: 600px;">READING DATE 1</th>
        <th class="reading-date-header" colspan="6" style="min-width: 600px;">READING DATE 2</th>
    `;
    tableHeader.appendChild(row1);
    
    // Baris 2: Nama kolom utama
    const row2 = document.createElement('tr');
    row2.innerHTML = `
        <th class="bg-calculation">Deviation face A+</th>
        <th class="bg-initial">Deviation face A-</th>
        <th class="bg-calculation">Mean Deviation A</th>
        <th class="bg-calculation">Deviation face B+</th>
        <th class="bg-initial">Deviation face B-</th>
        <th class="bg-calculation">Mean Deviation B</th>

        <th class="bg-calculation">Mean Deviation 500*((4)-(9))/0,5</th>
        <th class="bg-calculation">Base Reading A</th>
        <th class="bg-initial">Displace Profile A</th>
        <th class="bg-calculation">Mean Deviation 500*((7)-(12))/0,5</th>
        <th class="bg-calculation">Base Reading B</th>
        <th class="bg-initial">Displace Profile B</th>
    `;
    tableHeader.appendChild(row2);
    
    // Baris 3: Satuan
    const row3 = document.createElement('tr');
    row3.innerHTML = `
        <th class="bg-metrik">(m)</th>
        <th class="bg-metrik">(m)</th>
        <th class="bg-metrik">(m)</th>
        <th class="bg-metrik">(m)</th>
        <th class="bg-metrik">(m)</th>
        <th class="bg-metrik">(m)</th>
        <th class="bg-metrik">(mm)</th>
        <th class="bg-metrik">(mm)</th>
        <th class="bg-metrik">(mm)</th>
        <th class="bg-metrik">(mm)</th>
        <th class="bg-metrik">(mm)</th>
        <th class="bg-metrik">(mm)</th>
    `;
    tableHeader.appendChild(row3);
    
    // Baris 4: Nomor kolom
    const row4 = document.createElement('tr');
    row4.innerHTML = `
        <th class="bg-metrik">(2)</th>
        <th class="bg-metrik">(3)</th>
        <th class="bg-metrik">(4)</th>
        <th class="bg-metrik">(5)</th>
        <th class="bg-metrik">(6)</th>
        <th class="bg-metrik">(7)</th>
        <th class="bg-metrik">(8)</th>
        <th class="bg-metrik">(9)</th>
        <th class="bg-metrik">(10)</th>
        <th class="bg-metrik">(11)</th>
        <th class="bg-metrik">(12)</th>
        <th class="bg-metrik">(13)</th>
    `;
    tableHeader.appendChild(row4);
    
    // Render data rows sesuai dengan data dari gambar - TANPA KOLOM AKSI
    if (data && data.length > 0) {
        // Data sudah di-sort DESCENDING (dari -0.5 ke -80)
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            
            // Kolom Depth (1) - sticky
            const tdDepth = document.createElement('td');
            const depthValue = row.depth !== undefined ? row.depth : (row.kedalaman || '');
            tdDepth.textContent = depthValue;
            tdDepth.className = 'depth-cell sticky-col-depth';
            tdDepth.style.fontWeight = '600';
            tr.appendChild(tdDepth);
            
            // Kolom sesuai dengan gambar - MENAMPILKAN NILAI PERSIS DARI DATABASE
            // Kolom 2: Deviation face A+ (initial reading)
            const td2 = createNumberCell(row.face_a_plus || '0.000000', true);
            tr.appendChild(td2);

            // Kolom 3: Deviation face A- (initial reading)
            const td3 = createNumberCell(row.face_a_minus || '0.000000', true);
            tr.appendChild(td3);

            // Kolom 4: Mean Deviation A - diambil dari initial reading attribut face_a
            const td4 = createNumberCell(row.face_a_avg || row.mean_deviation_a || '0.000000', true);
            tr.appendChild(td4);

            // Kolom 5: Deviation face B+ (initial reading)
            const td5 = createNumberCell(row.face_b_plus || '0.000000', true);
            tr.appendChild(td5);

            // Kolom 6: Deviation face B- (initial reading)
            const td6 = createNumberCell(row.face_b_minus || '0.000000', true);
            tr.appendChild(td6);

            // Kolom 7: Mean Deviation B - diambil dari initial reading attribut face_b
            const td7 = createNumberCell(row.face_b_avg || row.mean_deviation_b || '0.000000', true);
            tr.appendChild(td7);

            // Kolom 8: Mean Deviation 500*((4)-(9))/0,5 - diambil dari initial reading attribut mean_cum_deviation_a
            const td8 = createNumberCell(row.mean_cum_deviation_a || '0.000000', true);
            tr.appendChild(td8);

            // Kolom 9: Base Reading A - PERBAIKAN: Ambil dari database
            const td9 = createNumberCell(row.basereading_a || '0.000000', true);
            tr.appendChild(td9);

            // Kolom 10: Displace Profile A
            const td10 = createNumberCell(row.displace_profile_a || '0.000000', true);
            tr.appendChild(td10);

            // Kolom 11: Mean Deviation 500*((7)-(12))/0,5 - diambil dari initial reading attribut mean_cum_deviation_b
            const td11 = createNumberCell(row.mean_cum_deviation_b || '0.000000', true);
            tr.appendChild(td11);

            // Kolom 12: Base Reading B - PERBAIKAN: Ambil dari database
            const td12 = createNumberCell(row.basereading_b || '0.000000', true);
            tr.appendChild(td12);

            // Kolom 13: Displace Profile B
            const td13 = createNumberCell(row.displace_profile_b || '0.000000', true);
            tr.appendChild(td13);
            
            tableBody.appendChild(tr);
        });
    } else {
        // Jika tidak ada data, tampilkan pesan
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 13;
        td.textContent = 'Tidak ada data yang ditampilkan';
        td.className = 'text-center py-3 text-muted';
        tr.appendChild(td);
        tableBody.appendChild(tr);
    }
    
    // Update scroll indicator
    setTimeout(updateScrollIndicator, 100);
}

// Helper function untuk membuat cell number - MENGAMBIL NILAI PERSIS TANPA PEMBULATAN
function createNumberCell(value, preserveFormat = true) {
    const td = document.createElement('td');
    td.className = 'number-cell';
    
    // Jika value adalah null/undefined, tampilkan 0.000000
    if (value === null || value === undefined || value === '') {
        td.textContent = '0.000000';
        return td;
    }
    
    // Konversi ke string
    let stringValue = String(value).trim();
    
    // Jika preserveFormat true, tampilkan nilai persis seperti di database
    if (preserveFormat) {
        // Cek apakah nilai sudah berupa angka dengan desimal
        if (stringValue.includes('.') || stringValue.includes(',')) {
            // Ganti koma dengan titik jika ada
            stringValue = stringValue.replace(',', '.');
            
            // Cek berapa digit desimal
            const decimalPart = stringValue.split('.')[1] || '';
            
            // Jika kurang dari 6 digit, tambahkan nol
            if (decimalPart.length < 6) {
                stringValue = parseFloat(stringValue).toFixed(6);
            }
            
            // Jika lebih dari 6 digit, pertahankan aslinya
            td.textContent = stringValue;
        } else {
            // Jika tidak ada desimal, tambahkan .000000
            td.textContent = parseFloat(stringValue).toFixed(6);
        }
    } else {
        // Format standar (untuk kasus lain)
        td.textContent = stringValue;
    }
    
    return td;
}

// Tampilkan metadata
function showMetadata(metadata) {
    const metadataSection = document.getElementById('metadataSection');
    
    if (metadata) {
        metadataSection.style.display = 'block';
        document.getElementById('metadataBorehole').textContent = metadata.borehole_name || metadata.borehole || '-';
        document.getElementById('metadataDate').textContent = metadata.reading_date || metadata.date || '-';
        document.getElementById('metadataProbe').textContent = metadata.probe_serial || metadata.probe || '-';
        document.getElementById('metadataReel').textContent = metadata.reel_serial || metadata.reel || '-';
        document.getElementById('metadataOperator').textContent = metadata.operator || '-';
        document.getElementById('metadataTotal').textContent = metadata.total_records || metadata.total_data || '0';
        document.getElementById('metadataMinDepth').textContent = metadata.min_depth ? metadata.min_depth + ' m' : '-';
        document.getElementById('metadataMaxDepth').textContent = metadata.max_depth ? metadata.max_depth + ' m' : '-';
    } else {
        metadataSection.style.display = 'none';
    }
}

// Tampilkan/sembunyikan tabel
function showTable(show) {
    const placeholder = document.getElementById('tablePlaceholder');
    const dataWrapper = document.getElementById('dataTableWrapper');
    const noDataMessage = document.getElementById('noDataMessage');
    const paginationSection = document.getElementById('paginationSection');
    
    if (show) {
        placeholder.style.display = 'none';
        dataWrapper.style.display = 'block';
        noDataMessage.style.display = 'none';
        paginationSection.style.display = 'flex';
    } else {
        placeholder.style.display = 'block';
        dataWrapper.style.display = 'none';
        noDataMessage.style.display = 'none';
        paginationSection.style.display = 'none';
    }
}

// Fungsi reset filter
function resetFilters() {
    document.getElementById('tahunFilter').value = '';
    document.getElementById('bulanFilter').innerHTML = '<option value="">Pilih Tahun Terlebih Dahulu</option>';
    document.getElementById('bulanFilter').disabled = true;
    document.getElementById('tanggalFilter').innerHTML = '<option value="">Pilih Bulan Terlebih Dahulu</option>';
    document.getElementById('tanggalFilter').disabled = true;
    document.getElementById('searchInput').value = '';
    
    // Reset tampilan
    document.getElementById('tablePlaceholder').style.display = 'block';
    document.getElementById('dataTableWrapper').style.display = 'none';
    document.getElementById('metadataSection').style.display = 'none';
    document.getElementById('paginationSection').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'none';
    
    tableData = null;
    currentData = [];
    profilAData = null;
    profilBData = null;
}

// Filter data berdasarkan search input dengan tetap menjaga sorting DESC
function filterTableData() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    if (!tableData || !tableData.data) return;
    
    if (!searchTerm.trim()) {
        currentData = tableData.data;
        // Sort ulang data berdasarkan depth DESC (dari -0.5 ke -80)
        currentData.sort((a, b) => {
            const depthA = parseFloat(a.depth || a.kedalaman || 0);
            const depthB = parseFloat(b.depth || b.kedalaman || 0);
            return depthB - depthA; // DESCENDING
        });
        renderTable(currentData);
        updatePagination(currentData.length);
        return;
    }
    
    // Filter data
    const filteredData = tableData.data.filter(row => {
        // Cari di semua kolom
        for (const key in row) {
            if (row.hasOwnProperty(key) && row[key] && 
                row[key].toString().toLowerCase().includes(searchTerm)) {
                return true;
            }
        }
        return false;
    });
    
    currentData = filteredData;
    // Sort data yang sudah difilter berdasarkan depth DESC
    currentData.sort((a, b) => {
        const depthA = parseFloat(a.depth || a.kedalaman || 0);
        const depthB = parseFloat(b.depth || b.kedalaman || 0);
        return depthB - depthA; // DESCENDING
    });
    
    renderTable(currentData);
    updatePagination(currentData.length);
    
    // Tampilkan pesan jika tidak ada hasil
    if (filteredData.length === 0) {
        document.getElementById('noDataMessage').style.display = 'block';
        document.getElementById('dataTableWrapper').style.display = 'none';
    } else {
        document.getElementById('noDataMessage').style.display = 'none';
        document.getElementById('dataTableWrapper').style.display = 'block';
    }
}

// Update pagination
function updatePagination(count) {
    document.getElementById('currentCount').textContent = count;
    document.getElementById('totalCount').textContent = tableData ? tableData.data.length : 0;
}

// Tampilkan loading
function showLoading(show) {
    const loading = document.getElementById('tableLoading');
    loading.style.display = show ? 'flex' : 'none';
}

// Tampilkan alert
function showAlert(type, message) {
    // Buat elemen alert sementara
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '2000';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Hapus otomatis setelah 5 detik
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Debounce untuk search input
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Update scroll indicator
function updateScrollIndicator() {
    const indicator = document.getElementById('scrollIndicator');
    const tableContainer = document.getElementById('tableContainer');
    
    if (tableContainer && tableContainer.scrollWidth > tableContainer.clientWidth) {
        indicator.style.display = 'flex';
    } else {
        indicator.style.display = 'none';
    }
}

<?php if ($isAdmin): ?>
// Preview CSV file
function previewCSV(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('csvPreview');
    
    if (!file) {
        preview.innerHTML = '<p class="text-muted mb-0">Pilih file CSV untuk melihat preview data</p>';
        return;
    }

    if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
        preview.innerHTML = '<div class="alert alert-warning small mb-0">File harus berformat CSV</div>';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const lines = content.split('\n').slice(0, 6);
        
        let previewHTML = '<div class="table-responsive"><table class="table table-sm table-bordered mb-2">';
        
        lines.forEach((line, index) => {
            const cells = line.split(',');
            if (cells.length > 1) {
                previewHTML += '<tr>';
                cells.forEach(cell => {
                    if (index === 0) {
                        previewHTML += `<th class="bg-light small px-2 py-1">${cell.trim()}</th>`;
                    } else {
                        previewHTML += `<td class="small px-2 py-1">${cell.trim()}</td>`;
                    }
                });
                previewHTML += '</tr>';
            }
        });
        
        previewHTML += '</table></div>';
        previewHTML += `<p class="text-muted small mb-0">Menampilkan ${lines.length} baris pertama</p>`;
        
        preview.innerHTML = previewHTML;
    };
    
    reader.readAsText(file);
}

// Upload CSV - PERBAIKAN PENTING: Menambahkan header X-Requested-With untuk AJAX
async function uploadCSV() {
    const progressBar = document.getElementById('uploadProgress');
    const statusDiv = document.getElementById('uploadStatus');
    const uploadBtn = document.getElementById('btnUploadCSV');
    const fileInput = document.getElementById('csvFile');
    const file = fileInput.files[0];
    const formData = new FormData();
    
    // Validasi file
    if (!file) {
        showStatus('warning', 'Pilih file CSV terlebih dahulu');
        return;
    }
    
    if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
        showStatus('warning', 'File harus berformat CSV');
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        showStatus('warning', 'File terlalu besar. Maksimal 5MB');
        return;
    }
    
    // Setup UI
    progressBar.style.display = 'block';
    progressBar.querySelector('.progress-bar').style.width = '0%';
    statusDiv.style.display = 'none';
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading...';
    
    // Tambahkan file ke FormData
    formData.append('csv_file', file);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    try {
        const response = await fetch('<?= base_url("inclino/import/uploadCSV") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        progressBar.style.display = 'none';
        
        if (result.status === 'success') {
            showStatus('success', 
                `<strong>${result.message}</strong><br>
                <small class="mt-1">
                    File: ${result.data.file_name} | 
                    Borehole: ${result.data.borehole} | 
                    Tanggal: ${result.data.reading_date} | 
                    Data Imported: ${result.data.imported} |
                    Data Skipped: ${result.data.skipped}
                </small>`
            );
            
            // Auto close modal setelah 3 detik dan reload data
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadCsvModal'));
                modal.hide();
                loadDataByFilter(); // Reload data
            }, 3000);
        } else {
            showStatus('danger', `Upload gagal: ${result.message || 'Terjadi kesalahan'}`);
        }
    } catch (error) {
        progressBar.style.display = 'none';
        showStatus('danger', 'Network error. Periksa koneksi internet Anda.');
        console.error('Upload error:', error);
    } finally {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload CSV';
    }
}

// Tampilkan status upload
function showStatus(type, message) {
    const statusDiv = document.getElementById('uploadStatus');
    const icons = {
        'success': 'check-circle',
        'warning': 'exclamation-triangle', 
        'danger': 'exclamation-circle'
    };
    
    statusDiv.className = `alert alert-${type} small`;
    statusDiv.innerHTML = `<i class="fas fa-${icons[type]} me-2"></i> ${message}`;
    statusDiv.style.display = 'block';
}

// Reset form CSV
function resetCSVForm() {
    document.getElementById('uploadCsvForm').reset();
    document.getElementById('uploadStatus').style.display = 'none';
    document.getElementById('uploadProgress').style.display = 'none';
    document.getElementById('csvPreview').innerHTML = '<p class="text-muted mb-0">Pilih file CSV untuk melihat preview data</p>';
}
<?php endif; ?>

// Export to Excel - UPDATE UNTUK MENGGUNAKAN CONTROLLER BARU
function exportToExcel() {
    const tahun = document.getElementById('tahunFilter').value;
    const bulan = document.getElementById('bulanFilter').value;
    const tanggal = document.getElementById('tanggalFilter').value;
    
    // Validasi minimal filter
    if (!tahun) {
        showAlert('warning', 'Pilih tahun terlebih dahulu');
        return;
    }
    
    // Build URL dengan parameter filter
    let url = '<?= base_url("inclino/export/excel") ?>?';
    const params = [];
    
    if (tahun) params.push(`year=${tahun}`);
    if (bulan && bulan !== "") params.push(`month=${bulan}`);
    
    // Parse tanggal jika dipilih
    if (tanggal && tanggal !== "" && tanggal !== "Semua Tanggal") {
        const parts = tanggal.split('-');
        if (parts.length === 3) {
            params.push(`day=${parts[0]}`); // Day adalah bagian pertama
        }
    }
    
    // Tambahkan parameter borehole jika ada (sesuai dengan controller)
    // Note: Controller saat ini belum mengambil borehole dari filter, 
    // bisa ditambahkan jika diperlukan
    
    if (params.length > 0) {
        url += params.join('&');
    }
    
    // Tampilkan loading
    showLoading(true);
    
    // Download file
    const link = document.createElement('a');
    link.href = url;
    link.download = 'InclinoMeter_Data.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Sembunyikan loading setelah delay
    setTimeout(() => {
        showLoading(false);
        showAlert('success', 'Data sedang diexport. File akan otomatis terdownload.');
    }, 1000);

    // Tampilkan loading untuk export
function showLoading(show) {
    const loading = document.getElementById('tableLoading');
    if (loading) {
        loading.style.display = show ? 'flex' : 'none';
    }
}
}
</script>
</body>
</html>