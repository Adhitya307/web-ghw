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
        /* PERBAIKAN UTAMA: STICKY HEADER YANG KONSISTEN */
        .table-responsive {
            position: relative;
            max-height: 600px;
            overflow: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        /* PERUBAHAN: SEMUA HEADER WARNA BIRU */
        .data-table thead th {
            position: sticky;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 4px 8px !important;
            min-height: 40px !important;
            height: 40px !important;
            line-height: 1.2 !important;
        }
        
        /* PERBAIKAN: ATUR TOP POSITION UNTUK SETIAP BARIS HEADER */
        .data-table thead tr:nth-child(1) th {
            top: 0;
            z-index: 100;
        }
        
        .data-table thead tr:nth-child(2) th {
            top: 40px;
            z-index: 99;
        }
        
        .data-table thead tr:nth-child(3) th {
            top: 80px;
            z-index: 98;
        }
        
        .data-table thead tr:nth-child(4) th {
            top: 120px;
            z-index: 97;
        }
        
        .data-table thead tr:nth-child(5) th {
            top: 160px;
            z-index: 96;
        }
        
        .data-table thead tr:nth-child(6) th {
            top: 200px;
            z-index: 95;
        }
        
        .data-table thead tr:nth-child(7) th {
            top: 240px;
            z-index: 94;
        }
        
        /* PERBAIKAN KHUSUS: Kolom pertama (Tanggal) */
        .data-table thead tr th.sticky-column {
            position: sticky;
            left: 0;
            background-color: #1976d2 !important;
            z-index: 300;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            color: white !important;
        }
        
        .data-table thead tr:nth-child(1) th.sticky-column {
            top: 0;
            z-index: 310 !important;
        }
        
        .data-table thead tr:nth-child(2) th.sticky-column {
            top: 40px;
            z-index: 309 !important;
        }
        
        .data-table thead tr:nth-child(3) th.sticky-column {
            top: 80px;
            z-index: 308 !important;
        }
        
        .data-table thead tr:nth-child(4) th.sticky-column {
            top: 120px;
            z-index: 307 !important;
        }
        
        .data-table thead tr:nth-child(5) th.sticky-column {
            top: 160px;
            z-index: 306 !important;
        }
        
        .data-table thead tr:nth-child(6) th.sticky-column {
            top: 200px;
            z-index: 305 !important;
        }
        
        .data-table thead tr:nth-child(7) th.sticky-column {
            top: 240px;
            z-index: 304 !important;
        }
        
        /* PERBAIKAN: Action column header */
        .action-header {
            position: sticky;
            right: 0;
            background-color: #0d6efd !important;
            z-index: 300;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            color: white !important;
        }
        
        .data-table thead tr:nth-child(1) .action-header {
            top: 0;
            z-index: 310 !important;
        }
        
        .data-table thead tr:nth-child(2) .action-header {
            top: 40px;
            z-index: 309 !important;
        }
        
        .data-table thead tr:nth-child(3) .action-header {
            top: 80px;
            z-index: 308 !important;
        }
        
        .data-table thead tr:nth-child(4) .action-header {
            top: 120px;
            z-index: 307 !important;
        }
        
        .data-table thead tr:nth-child(5) .action-header {
            top: 160px;
            z-index: 306 !important;
        }
        
        .data-table thead tr:nth-child(6) .action-header {
            top: 200px;
            z-index: 305 !important;
        }
        
        .data-table thead tr:nth-child(7) .action-header {
            top: 240px;
            z-index: 304 !important;
        }
        
        /* PERUBAHAN PENTING: WARNA HEADER KHUSUS UNTUK AMAN, PERINGATAN, BAHAYA - SAMA DENGAN BODY */
        .header-aman { 
            background-color: #28a745 !important; /* HIJAU TUA UNTUK HEADER */
            color: white !important; 
            font-weight: bold;
            border: 1px solid #218838 !important;
        }
        
        .header-peringatan { 
            background-color: #ffc107 !important; /* KUNING TUA UNTUK HEADER */
            color: #212529 !important; 
            font-weight: bold;
            border: 1px solid #e0a800 !important;
        }
        
        .header-bahaya { 
            background-color: #dc3545 !important; /* MERAH TUA UNTUK HEADER */
            color: white !important; 
            font-weight: bold;
            border: 1px solid #c82333 !important;
        }
        
        /* Body cells styling */
        .action-cell {
            position: sticky;
            right: 0;
            background: #f8f9fa !important;
            z-index: 290;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            padding: 8px 5px;
            min-width: 80px;
        }
        
        .sticky { 
            position: sticky; 
            left: 0; 
            background: #bbdefb !important;
            z-index: 290; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            color: #1a237e !important;
            font-weight: 500;
        }
        
        /* Warna latar untuk baris */
        .data-table tbody tr:nth-child(odd) td:not(.sticky):not(.action-cell):not(.aman-column):not(.peringatan-column):not(.bahaya-column):not(.status-aman):not(.status-peringatan):not(.status-bahaya) {
            background-color: #f8f9fa !important;
        }
        
        .data-table tbody tr:nth-child(even) td:not(.sticky):not(.action-cell):not(.aman-column):not(.peringatan-column):not(.bahaya-column):not(.status-aman):not(.status-peringatan):not(.status-bahaya) {
            background-color: white !important;
        }
        
        /* Hover effect */
        .data-table tbody tr:hover td:not(.sticky):not(.action-cell):not(.aman-column):not(.peringatan-column):not(.bahaya-column):not(.status-aman):not(.status-peringatan):not(.status-bahaya) {
            background-color: #e3f2fd !important;
        }
        
        .data-table tbody tr:hover td.sticky {
            background-color: #90caf9 !important;
            color: #0d47a1 !important;
        }
        
        .data-table tbody tr:hover td.action-cell {
            background-color: #f1f3f4 !important;
        }
        
        /* PERUBAHAN: Untuk kolom aman, peringatan, bahaya - hover effect khusus */
        .data-table tbody tr:hover td.aman-column {
            background-color: #34ce57 !important; /* HIJAU LEBIH TERANG */
            color: #155724 !important;
        }
        
        .data-table tbody tr:hover td.peringatan-column {
            background-color: #ffd760 !important; /* KUNING LEBIH TERANG */
            color: #856404 !important;
        }
        
        .data-table tbody tr:hover td.bahaya-column {
            background-color: #e4606d !important; /* MERAH LEBIH TERANG */
            color: #721c24 !important;
        }
        
        /* Button styling */
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
        
        /* Modal Akses - MODERN & FORMAL (SAMA DENGAN L7-L9) */
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
        
        /* Table styling */
        .data-table {
            min-width: 800px;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .data-table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 0.3rem;
            font-size: 0.75rem;
            white-space: nowrap;
            height: 35px;
            text-align: center;
        }
        
        /* Ambang Batas Container */
        .ambang-batas-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 120px;
            padding: 5px 0;
        }
        
        .ambang-header {
            font-size: 0.7rem;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: none;
        }
        
        .ambang-value {
            padding: 3px 0;
            margin: 1px 0;
            border-radius: 2px;
            text-align: center;
            font-size: 0.7rem;
            font-weight: bold;
            min-height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* PERUBAHAN PENTING: WARNA UNTUK KOLOM AMBANG BATAS DI BODY - LEBIH TERANG */
        /* Kolom AMAN (hijau) */
        .aman-column {
            background-color: #d4edda !important; 
            color: #155724 !important; 
            border: 1px solid #c3e6cb !important;
            font-weight: bold;
        }
        
        /* Kolom PERINGATAN (kuning) */
        .peringatan-column {
            background-color: #fff3cd !important; 
            color: #856404 !important; 
            border: 1px solid #ffeaa7 !important;
            font-weight: bold;
        }
        
        /* Kolom BAHAYA (merah) */
        .bahaya-column {
            background-color: #f8d7da !important; 
            color: #721c24 !important; 
            border: 1px solid #f5c6cb !important;
            font-weight: bold;
        }
        
        /* WARNA STATUS UNTUK KOLOM T.PSMETRIK TERAKHIR - DIPERBAIKI */
        /* Status Aman (hijau) - LATAR BELAKANG, BUKAN TEKS */
        .status-aman { 
            background-color: #d4edda !important;  /* LATAR BELAKANG HIJAU */
            color: #155724 !important;             /* TEKS HIJAU TUA */
            font-weight: bold;
            border: 1px solid #c3e6cb !important;
        }
        
        /* Status Peringatan (kuning) - LATAR BELAKANG, BUKAN TEKS */
        .status-peringatan { 
            background-color: #fff3cd !important;  /* LATAR BELAKANG KUNING */
            color: #856404 !important;             /* TEKS KUNING TUA */
            font-weight: bold;
            border: 1px solid #ffeaa7 !important;
        }
        
        /* Status Bahaya (merah) - LATAR BELAKANG, BUKAN TEKS */
        .status-bahaya { 
            background-color: #f8d7da !important;  /* LATAR BELAKANG MERAH */
            color: #721c24 !important;             /* TEKS MERAH TUA */
            font-weight: bold;
            border: 1px solid #f5c6cb !important;
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
        
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .date-cell {
            text-align: center;
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
        
        /* Pastikan semua header memiliki tinggi yang sama */
        .data-table thead tr th {
            line-height: 1.2 !important;
            height: 40px !important;
            min-height: 40px !important;
            padding: 4px 8px !important;
            vertical-align: middle !important;
        }
        
        /* Untuk kolom ambang batas yang rowspan=4 */
        th[rowspan="4"] {
            height: 160px !important;
            min-height: 160px !important;
            background-color: #e3f2fd !important;
            color: #2c3e50 !important;
        }
        
        /* Untuk header L-10, SPZ-02 di row 1 */
        .data-table thead tr:nth-child(1) th:not(.sticky-column):not(.action-header):not(.header-aman):not(.header-peringatan):not(.header-bahaya) {
            background-color: #1976d2 !important;
            color: white !important;
            font-weight: bold;
        }
        
        /* Untuk Downstream, Special Point di row 2 */
        .data-table thead tr:nth-child(2) th:not(.sticky-column):not(.action-header):not(.header-aman):not(.header-peringatan):not(.header-bahaya) {
            background-color: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }
        
        /* PERUBAHAN: Agar kolom aman, peringatan, bahaya tidak terlalu lebar - DIPERBAIKI */
        .ambang-narrow {
            min-width: 80px !important; /* DIPERBESAR DARI 60px */
            max-width: 90px !important; /* DIPERBESAR DARI 70px */
        }
        
        /* PERUBAHAN KHUSUS: Warna untuk nilai ambang batas di container header */
        .aman-value { 
            background-color: #28a745 !important; /* HIJAU SAMA DENGAN HEADER */
            color: white !important; 
            font-weight: bold;
            border: 1px solid #218838 !important;
        }
        
        .peringatan-value { 
            background-color: #ffc107 !important; /* KUNING SAMA DENGAN HEADER */
            color: #212529 !important; 
            font-weight: bold;
            border: 1px solid #e0a800 !important;
        }
        
        .bahaya-value { 
            background-color: #dc3545 !important; /* MERAH SAMA DENGAN HEADER */
            color: white !important; 
            font-weight: bold;
            border: 1px solid #c82333 !important;
        }
        
        /* Header untuk Bacaan(m) dan T.Psmetrik(El.m) pertama - WARNA BIRU */
        .header-biru {
            background-color: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }
        
        /* PERUBAHAN: Hover effect untuk kolom status */
        .data-table tbody tr:hover td.status-aman {
            background-color: #b1dfbb !important; /* HIJAU LEBIH TERANG */
            color: #0c4128 !important;
        }
        
        .data-table tbody tr:hover td.status-peringatan {
            background-color: #ffeaa7 !important; /* KUNING LEBIH TERANG */
            color: #664d03 !important;
        }
        
        .data-table tbody tr:hover td.status-bahaya {
            background-color: #f1b0b7 !important; /* MERAH LEBIH TERANG */
            color: #491217 !important;
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

// FUNGSI UNTUK MENDAPATKAN STATUS WARNA - SESUAI LOGIKA L10 & SPZ-02
function getStatusL10Spz02($t_psmetrik, $type) {
    switch($type) {
        case 'L10':
        case 'L_10':
            // L-10: Aman ≤560.96 - 565.45, Peringatan 565.46 - 569.75, Bahaya ≥569.76
            if ($t_psmetrik <= 565.45) return 'aman';          // ≤ 565.45 → aman
            if ($t_psmetrik <= 569.75) return 'peringatan';    // 565.46 - 569.75 → peringatan
            return 'bahaya';                                   // ≥ 569.76 → bahaya
            
        case 'SPZ02':
        case 'SPZ_02':
            // SPZ-02: Aman ≤690.78 - 691.67, Peringatan 691.68 - 694.67, Bahaya ≥694.68
            if ($t_psmetrik <= 691.67) return 'aman';          // ≤ 691.67 → aman
            if ($t_psmetrik <= 694.67) return 'peringatan';    // 691.68 - 694.67 → peringatan
            return 'bahaya';                                   // ≥ 694.68 → bahaya
            
        default:
            return 'aman';
    }
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
            <i class="fas fa-tachometer-alt me-2"></i>Piezometer - Grafik History L10 & SPZ-02
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('left-piez') ?>" class="btn btn-outline-primary btn-piez">
                <i class="fas fa-table"></i> Left Bank
            </a>
            
            <!-- Tombol Grafik History -->
            <a href="<?= base_url('left_piez/grafik-history-l1-l3') ?>" class="btn btn-outline-primary btn-piez">Grafik History L1-L3</a>
            <a href="<?= base_url('left_piez/grafik-history-l4-l6') ?>" class="btn btn-outline-primary btn-piez">Grafik History L4-L6</a>
            <a href="<?= base_url('left_piez/grafik-history-l7-l9') ?>" class="btn btn-outline-primary btn-piez">Grafik History L7-L9</a>
            <a href="<?= base_url('left_piez/grafik-history-l10-spz02') ?>" class="btn btn-primary btn-piez">Grafik History L10-SPZ02</a>
            
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

    <!-- Modal Peringatan Hak Akses Modern & Formal (SAMA DENGAN L7-L9) -->
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
                        rsort($uniqueYears);
                        foreach ($uniqueYears as $year):
                    ?>
                        <option value="<?= esc($year) ?>"><?= esc($year) ?></option>
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
                <!-- Row 1: Main Headers -->
                <tr>
                    <th rowspan="2" class="sticky-column">Pisometer No.</th>
                    <th colspan="6">L-10</th>
                    <th colspan="6">SPZ-02</th>
                    <?php if($isAdmin): ?>
                    <th rowspan="7" class="action-header">AKSI</th>
                    <?php else: ?>
                    <th rowspan="7" class="action-header">AKSI</th>
                    <?php endif; ?>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <th colspan="6">Downstream</th>
                    <th colspan="6">Special Point</th>
                </tr>
                
                <!-- Row 3: Data Headers -->
                <tr>
                    <th class="sticky-column">Elev.Piso.Atas(El.m)</th>
                    <th colspan="2">580.36</th>
                    <th colspan="4" rowspan="4">
                        <div class="ambang-batas-container">
                            <div class="ambang-header">Ambang Batas</div>
                            <div class="ambang-header">Aman (El.m)</div>
                            <div class="ambang-value aman-value">≤ 560.96 - 565.45</div>
                            <div class="ambang-header">Peringatan (El.m)</div>
                            <div class="ambang-value peringatan-value">565.46 - 569.75</div>
                            <div class="ambang-header">Bahaya (El.m)</div>
                            <div class="ambang-value bahaya-value">≥ 569.76</div>
                        </div>
                    </th>
                    <th colspan="2">700.08</th>
                    <th colspan="4" rowspan="4">
                        <div class="ambang-batas-container">
                            <div class="ambang-header">Ambang Batas</div>
                            <div class="ambang-header">Aman (El.m)</div>
                            <div class="ambang-value aman-value">≤ 690.78 - 691.67</div>
                            <div class="ambang-header">Peringatan (El.m)</div>
                            <div class="ambang-value peringatan-value">691.68 - 694.67</div>
                            <div class="ambang-header">Bahaya (El.m)</div>
                            <div class="ambang-value bahaya-value">≥ 694.68</div>
                        </div>
                    </th>
                </tr>

                <!-- Row 4: Kedalaman -->
                <tr>
                    <th class="sticky-column">Kedalaman(m)</th>
                    <th colspan="2">51.50</th>
                    <th colspan="2">70.00</th>
                </tr>
                
                <!-- Row 5: Koordinat X -->
                <tr>
                    <th class="sticky-column">Koordinat X(m)</th>
                    <th colspan="2">5.960,00</th>
                    <th colspan="2">6.100,00</th>
                </tr>
                
                <!-- Row 6: Koordinat Y -->
                <tr>
                    <th class="sticky-column">Koordinat Y(m)</th>
                    <th colspan="2">(8.500,00)</th>
                    <th colspan="2">(8.950,00)</th>
                </tr>
                
                <!-- Row 7: Final Headers -->
                <tr>
                    <th class="sticky-column">Tanggal</th>
                    
                    <!-- L-10 Columns -->
                    <th class="header-biru">Bacaan(m)</th>
                    <th class="header-biru">T.Psmetrik(El.m)</th>
                    <th class="header-aman ambang-narrow">Aman</th>
                    <th class="header-peringatan ambang-narrow">Peringatan</th>
                    <th class="header-bahaya ambang-narrow">Bahaya</th>
                    <th class="header-biru">T.Psmetrik(El.m)</th>
                    
                    <!-- SPZ-02 Columns -->
                    <th class="header-biru">Bacaan(m)</th>
                    <th class="header-biru">T.Psmetrik(El.m)</th>
                    <th class="header-aman ambang-narrow">Aman</th>
                    <th class="header-peringatan ambang-narrow">Peringatan</th>
                    <th class="header-bahaya ambang-narrow">Bahaya</th>
                    <th class="header-biru">T.Psmetrik(El.m)</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <?php 
                if(empty($pengukuran)): ?>
                    <tr class="no-data">
                        <td colspan="15" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data Piezometer yang tersedia</p>
                            <?php if ($isAdmin): ?>
                                <button type="button" class="btn btn-primary mt-2" id="addDataEmpty">
                                    <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary mt-2 btn-disabled" 
                                        onclick="showAccessWarning('add')">
                                    <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    function formatTanggal($tanggal) {
                        if (empty($tanggal) || $tanggal === '0000-00-00') {
                            return '-';
                        }
                        return date('d-m-Y', strtotime($tanggal));
                    }
                    
                    // Fungsi untuk konversi dan validasi angka
                    function convertToFloat($value) {
                        if (is_numeric($value)) {
                            return (float)$value;
                        }
                        // Jika ada koma, ubah ke titik
                        $value = str_replace(',', '.', $value);
                        if (is_numeric($value)) {
                            return (float)$value;
                        }
                        return 0.0;
                    }
                    
                    // Urutkan data
                    $sortedData = $pengukuran;
                    usort($sortedData, function($a, $b) {
                        $dateA = strtotime($a['pengukuran']['tanggal'] ?? $a['pengukuran']['created_at'] ?? '1970-01-01');
                        $dateB = strtotime($b['pengukuran']['tanggal'] ?? $b['pengukuran']['created_at'] ?? '1970-01-01');
                        return $dateA - $dateB;
                    });
                    
                    foreach($sortedData as $item): 
                        $p = $item['pengukuran'];
                        
                        // Menggunakan struktur data baru dari controller
                        $bacaan_L10 = $item['pembacaan']['L_10']['feet'] ?? 0;
                        $bacaan_SPZ02 = $item['pembacaan']['SPZ_02']['feet'] ?? 0;
                        
                        // Mengambil t_psmetrik dari struktur yang benar
                        $t_psmetrik_L10 = $item['perhitungan_l10']['t_psmetrik'] ?? 0;
                        $t_psmetrik_SPZ02 = $item['perhitungan_spz02']['t_psmetrik'] ?? 0;
                        
                        // PERBAIKAN: Konversi ke float sebelum operasi matematika
                        $bacaan_L10 = convertToFloat($bacaan_L10);
                        $bacaan_SPZ02 = convertToFloat($bacaan_SPZ02);
                        $t_psmetrik_L10 = convertToFloat($t_psmetrik_L10);
                        $t_psmetrik_SPZ02 = convertToFloat($t_psmetrik_SPZ02);
                        
                        // Konversi feet ke meter (1 feet = 0.3048 meter)
                        $bacaan_L10_m = $bacaan_L10 * 0.3048;
                        $bacaan_SPZ02_m = $bacaan_SPZ02 * 0.3048;
                        
                        // Tentukan status dengan logika L10 & SPZ-02 yang telah diperbaiki
                        $status_L10 = getStatusL10Spz02($t_psmetrik_L10, 'L10');
                        $status_SPZ02 = getStatusL10Spz02($t_psmetrik_SPZ02, 'SPZ02');
                        
                        // Ambil tanggal dari data pengukuran
                        $tanggal = $p['tanggal'] ?? $p['created_at'] ?? $p['updated_at'] ?? '-';
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>" class="data-row">
                        <!-- Tanggal -->
                        <td class="date-cell sticky"><?= formatTanggal($tanggal) ?></td>
                        
                        <!-- L-10 Data -->
                        <td class="number-cell"><?= number_format($bacaan_L10_m, 2) ?></td>
                        <td class="number-cell"><?= number_format($t_psmetrik_L10, 2) ?></td>
                        <!-- Kolom Ambang Batas untuk L-10 dengan background warna -->
                        <td class="number-cell aman-column">560.96</td>
                        <td class="number-cell peringatan-column">565.46</td>
                        <td class="number-cell bahaya-column">569.76</td>
                        <!-- Kolom Status T.Psmetrik untuk L-10 - DENGAN BACKGROUND WARNA -->
                        <td class="number-cell status-<?= $status_L10 ?>">
                            <?= number_format($t_psmetrik_L10, 2) ?>
                        </td>
                        
                        <!-- SPZ-02 Data -->
                        <td class="number-cell"><?= number_format($bacaan_SPZ02_m, 2) ?></td>
                        <td class="number-cell"><?= number_format($t_psmetrik_SPZ02, 2) ?></td>
                        <!-- Kolom Ambang Batas untuk SPZ-02 dengan background warna -->
                        <td class="number-cell aman-column">690.78</td>
                        <td class="number-cell peringatan-column">691.68</td>
                        <td class="number-cell bahaya-column">694.68</td>
                        <!-- Kolom Status T.Psmetrik untuk SPZ-02 - DENGAN BACKGROUND WARNA -->
                        <td class="number-cell status-<?= $status_SPZ02 ?>">
                            <?= number_format($t_psmetrik_SPZ02, 2) ?>
                        </td>
                        
                        <!-- Action Buttons -->
                        <?php if ($isAdmin): ?>
                        <td class="action-cell">
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn-action btn-edit edit-data" 
                                       data-id="<?= $p['id_pengukuran'] ?>" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Edit Data">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn-action btn-delete delete-data" 
                                        data-id="<?= $p['id_pengukuran'] ?>" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <?php else: ?>
                        <td class="action-cell">
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn-action btn-disabled" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Klik untuk melihat informasi hak akses"
                                       onclick="showAccessWarning('edit', '<?= $p['tahun'] ?? '' ?>', '<?= $p['periode'] ?? '' ?>', '<?= formatTanggal($tanggal) ?>')">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn-action btn-disabled"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Klik untuk melihat informasi hak akses"
                                       onclick="showAccessWarning('delete', '<?= $p['tahun'] ?? '' ?>', '<?= $p['periode'] ?? '' ?>', '<?= formatTanggal($tanggal) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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

// Variabel global untuk modal hak akses
const accessWarningModal = new bootstrap.Modal(document.getElementById('accessWarningModal'));
const warningTitle = document.getElementById('warningTitle');
const warningMessage = document.getElementById('warningMessage');

// Variabel untuk filter
let tahunFilter, searchInput, resetFilter;

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
    
    // Update judul dan pesan
    warningTitle.textContent = title;
    warningMessage.innerHTML = message;
    
    // Tampilkan modal
    accessWarningModal.show();
}

// ============ FUNGSI FILTER ============
function initializeFilter() {
    tahunFilter = document.getElementById('tahunFilter');
    searchInput = document.getElementById('searchInput');
    resetFilter = document.getElementById('resetFilter');
    
    // Event listeners untuk filter
    tahunFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    resetFilter.addEventListener('click', resetAllFilters);
}

function filterTable() {
    const tahunValue = tahunFilter.value.toLowerCase();
    const searchValue = searchInput.value.toLowerCase();
    
    const rows = document.querySelectorAll('#dataTableBody .data-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const tahun = row.querySelector('.date-cell')?.textContent?.toLowerCase() || '';
        const rowText = row.textContent.toLowerCase();
        
        const tahunMatch = !tahunValue || tahun.includes(tahunValue);
        const searchMatch = !searchValue || rowText.includes(searchValue);
        
        const isVisible = tahunMatch && searchMatch;
        
        if (isVisible) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Tampilkan pesan jika tidak ada data yang cocok
    const noDataRow = document.querySelector('#dataTableBody tr.no-data');
    
    if (visibleCount === 0 && rows.length > 0) {
        if (!noDataRow) {
            const tbody = document.getElementById('dataTableBody');
            const newRow = document.createElement('tr');
            newRow.className = 'no-data';
            newRow.innerHTML = `
                <td colspan="15" class="text-center py-4">
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
    } else if (noDataRow && visibleCount > 0) {
        noDataRow.remove();
    }
}

function resetAllFilters() {
    tahunFilter.value = '';
    searchInput.value = '';
    filterTable();
}

// ============ FUNGSI PERBAIKAN STICKY HEADER ============
function fixStickyHeaderIssues() {
    const tableContainer = document.getElementById('tableContainer');
    const headerRows = document.querySelectorAll('#exportTable thead tr');
    
    if (!tableContainer) return;
    
    const scrollTop = tableContainer.scrollTop;
    
    headerRows.forEach((row, rowIndex) => {
        const topPosition = rowIndex * 40;
        const cells = row.querySelectorAll('th');
        
        cells.forEach(cell => {
            if (cell.classList.contains('sticky-column')) {
                cell.style.top = `${topPosition}px`;
                cell.style.left = '0';
                cell.style.zIndex = 310 - rowIndex;
            } else if (cell.classList.contains('action-header')) {
                cell.style.top = `${topPosition}px`;
                cell.style.right = '0';
                cell.style.zIndex = 310 - rowIndex;
            } else {
                cell.style.top = `${topPosition}px`;
                cell.style.zIndex = 100 - rowIndex;
            }
        });
    });
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
                const wb = XLSX.utils.table_to_book(table, {sheet: "Data Grafik History L10-SPZ02"});
                
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const filename = `Piezometer_Grafik_History_L10-SPZ02_${timestamp}.xlsx`;
                
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
    
    // Setup filter
    initializeFilter();
    
    // Setup export Excel
    setupExportExcel();
    
    // Attach event listeners
    attachEventListeners();
    
    // Perbaiki sticky header
    setTimeout(fixStickyHeaderIssues, 100);
    
    // Tambahkan scroll listener
    const tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        tableContainer.addEventListener('scroll', fixStickyHeaderIssues);
    }
    
    // Inisialisasi header saat load
    fixStickyHeaderIssues();
    
    // Resize listener
    window.addEventListener('resize', fixStickyHeaderIssues);
});
</script>
</body>
</html>