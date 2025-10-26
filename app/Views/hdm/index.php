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
        
        .tooltip-inner {
            font-size: 12px;
            padding: 4px 8px;
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
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
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
            <a href="<?= base_url('hdm625') ?>" class="btn btn-outline-info">
                <i class="fas fa-database"></i> HDM 625
            </a>
            <a href="<?= base_url('hdm600') ?>" class="btn btn-outline-info">
                <i class="fas fa-arrow-right-arrow-left"></i> HDM 600
            </a>
            
            <button type="button" class="btn btn-outline-primary" id="addData">
                <i class="fas fa-plus me-1"></i> Add Data
            </button>
            
            <button type="button" class="btn btn-outline-success" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>

            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-database"></i> Import SQL
            </button>
        </div>

        <div class="table-controls">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
            </div>
        </div>
    </div>

    <!-- Modal Import SQL -->
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

<!-- Scroll Indicator -->
<div class="scroll-indicator" id="scrollIndicator">
    <i class="fas fa-arrows-alt-h me-1"></i>
    <span id="scrollText">Scroll untuk melihat lebih banyak data</span>
</div>

<!-- Include Modal Hapus HDM -->
<?= $this->include('hdm/modal_hapus') ?>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Data dan state management
let allData = <?= json_encode($dataWithReadings ?? []) ?>;
let currentPage = 1;
let pageSize = 10;
let filteredData = [];

// Fungsi untuk mengurutkan data berdasarkan tanggal (terlama ke terbaru)
function sortDataByDate(data) {
    return data.sort((a, b) => {
        const dateA = new Date(a.pengukuran?.tanggal || 0);
        const dateB = new Date(b.pengukuran?.tanggal || 0);
        return dateA - dateB; // Urutkan dari tanggal terlama ke terbaru
    });
}

// Fungsi untuk memformat tanggal dengan benar
function formatDateForExport(dateString) {
    if (!dateString || dateString === '-') return '-';
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        // Format: DD/MM/YYYY untuk Excel (mencegah issue ###)
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}/${month}/${year}`;
    } catch (e) {
        console.error('Error formatting date:', e);
        return dateString;
    }
}

// Fungsi untuk memformat nilai numerik
function formatNumericValue(value) {
    if (value === null || value === undefined || value === '' || value === '-') {
        return '-';
    }
    
    // Jika sudah number, return as is
    if (typeof value === 'number') return value;
    
    // Jika string, coba parse sebagai float
    if (typeof value === 'string') {
        const num = parseFloat(value);
        return isNaN(num) ? value : num;
    }
    
    return value;
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
    
    // ============ SCROLL INDICATOR ============
    const scrollIndicator = document.getElementById('scrollIndicator');
    const tableContainer = document.getElementById('tableContainer');
    
    tableContainer.addEventListener('scroll', function() {
        const { scrollLeft, scrollTop, scrollWidth, scrollHeight, clientWidth, clientHeight } = this;
        
        const showHorizontal = scrollLeft > 0 || scrollLeft + clientWidth < scrollWidth;
        const showVertical = scrollTop > 0 || scrollTop + clientHeight < scrollHeight;
        
        if (showHorizontal || showVertical) {
            let text = 'Scroll ';
            if (showHorizontal && showVertical) {
                text += 'horizontal & vertikal';
            } else if (showHorizontal) {
                text += 'horizontal';
            } else {
                text += 'vertikal';
            }
            text += ' untuk melihat lebih banyak data';
            
            document.getElementById('scrollText').textContent = text;
            scrollIndicator.style.display = 'block';
        } else {
            scrollIndicator.style.display = 'none';
        }
    });
    
    // Hide scroll indicator when not scrolling
    let scrollTimeout;
    tableContainer.addEventListener('scroll', function() {
        scrollIndicator.style.display = 'block';
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            scrollIndicator.style.display = 'none';
        }, 2000);
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
        
        // Delete Data
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
        
        // Re-initialize tooltips
        const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        newTooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // ============ EXPORT EXCEL FUNCTIONALITY ============
document.getElementById('exportExcel').addEventListener('click', function() {
    // Show loading
    const originalText = this.innerHTML;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
    this.disabled = true;

    setTimeout(() => {
        try {
            // Buat workbook baru
            const wb = XLSX.utils.book_new();
            
            // Data untuk worksheet
            const wsData = [];
            
            // ===== HEADER ROWS =====
            // Row 1: Main Headers (TANPA AKSI)
            const header1 = [
                'TAHUN', 'PERIODE', 'TANGGAL', 'DAM',
                // PEMBACAAN HDM - ELV 625 & ELV 600
                ...Array(8).fill('PEMBACAAN HDM'),
                // DEPTH (S) - ELV 625 & ELV 600
                ...Array(8).fill('DEPTH (S)'),
                // READINGS (S) - ELV 625 & ELV 600
                ...Array(8).fill('READINGS (S)'),
                // PERGERAKAN (CM) - ELV 625 & ELV 600
                ...Array(8).fill('PERGERAKAN (CM)')
                // AKSI DIHAPUS DARI EXPORT
            ];
            wsData.push(header1);
            
            // Row 2: Sub Headers (TANPA AKSI)
            const header2 = [
                '', '', '', '',
                // PEMBACAAN HDM
                ...Array(3).fill('ELV.625'),
                ...Array(5).fill('ELV.600'),
                // DEPTH (S)
                ...Array(3).fill('ELV.625'),
                ...Array(5).fill('ELV.600'),
                // READINGS (S)
                ...Array(3).fill('ELV.625'),
                ...Array(5).fill('ELV.600'),
                // PERGERAKAN (CM)
                ...Array(3).fill('ELV.625'),
                ...Array(5).fill('ELV.600')
                // AKSI DIHAPUS DARI EXPORT
            ];
            wsData.push(header2);
            
            // Row 3: Measurement Headers (TANPA AKSI)
            const header3 = [
                'TAHUN', 'PERIODE', 'TANGGAL', 'DAM',
                // PEMBACAAN HDM - ELV 625
                'HV-1', 'HV-2', 'HV-3',
                // PEMBACAAN HDM - ELV 600
                'HV-1', 'HV-2', 'HV-3', 'HV-4', 'HV-5',
                // DEPTH (S) - ELV 625
                'HV-1', 'HV-2', 'HV-3',
                // DEPTH (S) - ELV 600
                'HV-1', 'HV-2', 'HV-3', 'HV-4', 'HV-5',
                // READINGS (S) - ELV 625
                'HV-1', 'HV-2', 'HV-3',
                // READINGS (S) - ELV 600
                'HV-1', 'HV-2', 'HV-3', 'HV-4', 'HV-5',
                // PERGERAKAN (CM) - ELV 625
                'HV-1', 'HV-2', 'HV-3',
                // PERGERAKAN (CM) - ELV 600
                'HV-1', 'HV-2', 'HV-3', 'HV-4', 'HV-5'
                // AKSI DIHAPUS DARI EXPORT
            ];
            wsData.push(header3);
            
            // ===== DATA ROWS =====
            filteredData.forEach(item => {
                const p = item.pengukuran || {};
                const pembacaanElv600 = item.pembacaan_elv600 || {};
                const pembacaanElv625 = item.pembacaan_elv625 || {};
                const depthElv600 = item.depth_elv600 || {};
                const depthElv625 = item.depth_elv625 || {};
                const initialReadingElv600 = item.initial_reading_elv600 || {};
                const initialReadingElv625 = item.initial_reading_elv625 || {};
                const pergerakanElv600 = item.pergerakan_elv600 || {};
                const pergerakanElv625 = item.pergerakan_elv625 || {};
                
                // Format tanggal untuk Excel (DD/MM/YYYY)
                const excelDate = p.tanggal ? formatDateForExport(p.tanggal) : '-';
                
                // Format nilai numerik - pastikan sebagai string dengan format yang benar
                const formatNumber = (value) => {
                    if (value === null || value === undefined || value === '' || value === '-') {
                        return '-';
                    }
                    
                    // Konversi ke number dulu
                    const numValue = typeof value === 'number' ? value : parseFloat(value);
                    
                    // Jika bukan number, return as is
                    if (isNaN(numValue)) return value;
                    
                    // Format dengan 2 digit desimal dan konversi ke string
                    return numValue.toFixed(2);
                };

                const row = [
                    // Basic Info
                    p.tahun || '-',
                    p.periode || '-',
                    excelDate,
                    formatNumber(p.dma),
                    
                    // PEMBACAAN HDM - ELV 625
                    formatNumber(pembacaanElv625.hv_1),
                    formatNumber(pembacaanElv625.hv_2),
                    formatNumber(pembacaanElv625.hv_3),
                    
                    // PEMBACAAN HDM - ELV 600
                    formatNumber(pembacaanElv600.hv_1),
                    formatNumber(pembacaanElv600.hv_2),
                    formatNumber(pembacaanElv600.hv_3),
                    formatNumber(pembacaanElv600.hv_4),
                    formatNumber(pembacaanElv600.hv_5),
                    
                    // DEPTH (S) - ELV 625
                    formatNumber(depthElv625.hv_1),
                    formatNumber(depthElv625.hv_2),
                    formatNumber(depthElv625.hv_3),
                    
                    // DEPTH (S) - ELV 600
                    formatNumber(depthElv600.hv_1),
                    formatNumber(depthElv600.hv_2),
                    formatNumber(depthElv600.hv_3),
                    formatNumber(depthElv600.hv_4),
                    formatNumber(depthElv600.hv_5),
                    
                    // READINGS (S) - ELV 625
                    formatNumber(initialReadingElv625.hv_1),
                    formatNumber(initialReadingElv625.hv_2),
                    formatNumber(initialReadingElv625.hv_3),
                    
                    // READINGS (S) - ELV 600
                    formatNumber(initialReadingElv600.hv_1),
                    formatNumber(initialReadingElv600.hv_2),
                    formatNumber(initialReadingElv600.hv_3),
                    formatNumber(initialReadingElv600.hv_4),
                    formatNumber(initialReadingElv600.hv_5),
                    
                    // PERGERAKAN (CM) - ELV 625
                    formatNumber(pergerakanElv625.hv_1),
                    formatNumber(pergerakanElv625.hv_2),
                    formatNumber(pergerakanElv625.hv_3),
                    
                    // PERGERAKAN (CM) - ELV 600
                    formatNumber(pergerakanElv600.hv_1),
                    formatNumber(pergerakanElv600.hv_2),
                    formatNumber(pergerakanElv600.hv_3),
                    formatNumber(pergerakanElv600.hv_4),
                    formatNumber(pergerakanElv600.hv_5)
                    
                    // AKSI DIHAPUS DARI EXPORT
                ];
                wsData.push(row);
            });
            
            // Buat worksheet
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            
            // ===== STYLING DENGAN WARNA =====
            if (!ws['!merges']) ws['!merges'] = [];
            
            // Merge untuk Header Row 1 (Main Sections) - TANPA AKSI
            const mainSections = [
                { start: 4, end: 11, label: 'PEMBACAAN HDM' },    // 8 columns
                { start: 12, end: 19, label: 'DEPTH (S)' },       // 8 columns
                { start: 20, end: 27, label: 'READINGS (S)' },    // 8 columns
                { start: 28, end: 35, label: 'PERGERAKAN (CM)' }  // 8 columns
            ];
            
            mainSections.forEach(section => {
                ws['!merges'].push({ 
                    s: { r: 0, c: section.start }, 
                    e: { r: 0, c: section.end } 
                });
            });
            
            // Merge untuk Header Row 2 (ELV Sections) - TANPA AKSI
            const elvSections = [
                // PEMBACAAN HDM
                { start: 4, end: 6, label: 'ELV.625' },      // 3 columns
                { start: 7, end: 11, label: 'ELV.600' },     // 5 columns
                // DEPTH (S)
                { start: 12, end: 14, label: 'ELV.625' },    // 3 columns
                { start: 15, end: 19, label: 'ELV.600' },    // 5 columns
                // READINGS (S)
                { start: 20, end: 22, label: 'ELV.625' },    // 3 columns
                { start: 23, end: 27, label: 'ELV.600' },    // 5 columns
                // PERGERAKAN (CM)
                { start: 28, end: 30, label: 'ELV.625' },    // 3 columns
                { start: 31, end: 35, label: 'ELV.600' }     // 5 columns
            ];
            
            elvSections.forEach(section => {
                ws['!merges'].push({ 
                    s: { r: 1, c: section.start }, 
                    e: { r: 1, c: section.end } 
                });
            });
            
            // ===== PENGATURAN KOLOM ===== (TANPA KOLOM AKSI)
            const colWidths = [
                { wch: 8 },   // TAHUN
                { wch: 10 },  // PERIODE
                { wch: 12 },  // TANGGAL
                { wch: 8 },   // DAM
                // PEMBACAAN HDM - ELV 625 (3 kolom)
                { wch: 12 }, { wch: 12 }, { wch: 12 },
                // PEMBACAAN HDM - ELV 600 (5 kolom)
                { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 },
                // DEPTH (S) - ELV 625 (3 kolom)
                { wch: 12 }, { wch: 12 }, { wch: 12 },
                // DEPTH (S) - ELV 600 (5 kolom)
                { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 },
                // READINGS (S) - ELV 625 (3 kolom)
                { wch: 12 }, { wch: 12 }, { wch: 12 },
                // READINGS (S) - ELV 600 (5 kolom)
                { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 },
                // PERGERAKAN (CM) - ELV 625 (3 kolom)
                { wch: 14 }, { wch: 14 }, { wch: 14 },
                // PERGERAKAN (CM) - ELV 600 (5 kolom)
                { wch: 14 }, { wch: 14 }, { wch: 14 }, { wch: 14 }, { wch: 14 }
                // KOLOM AKSI DIHAPUS
            ];
            ws['!cols'] = colWidths;
            
            // ===== WARNA HEADER =====
            // Definisikan range untuk styling
            const range = XLSX.utils.decode_range(ws['!ref']);
            
            // Warna untuk header rows (0, 1, 2) - TANPA AKSI
            for (let R = 0; R < 3; R++) {
                for (let C = range.s.c; C <= range.e.c; C++) {
                    const cell_ref = XLSX.utils.encode_cell({r:R, c:C});
                    if (!ws[cell_ref]) continue;
                    
                    // Inisialisasi style jika belum ada
                    if (!ws[cell_ref].s) {
                        ws[cell_ref].s = {};
                    }
                    
                    // Background colors berdasarkan section
                    let fillColor = '';
                    
                    // Row 1 - Main Sections
                    if (R === 0) {
                        if (C >= 4 && C <= 11) fillColor = 'FF0DCAF0'; // Biru muda - PEMBACAAN HDM
                        else if (C >= 12 && C <= 19) fillColor = 'FF0D6EFD'; // Biru - DEPTH (S)
                        else if (C >= 20 && C <= 27) fillColor = 'FF198754'; // Hijau - READINGS (S)
                        else if (C >= 28 && C <= 35) fillColor = 'FFFFC107'; // Kuning - PERGERAKAN (CM)
                    }
                    // Row 2 - ELV Sections
                    else if (R === 1) {
                        if ((C >= 4 && C <= 6) || (C >= 12 && C <= 14) || (C >= 20 && C <= 22) || (C >= 28 && C <= 30)) {
                            fillColor = 'FF0DCAF0'; // Biru muda - ELV.625
                        } else if ((C >= 7 && C <= 11) || (C >= 15 && C <= 19) || (C >= 23 && C <= 27) || (C >= 31 && C <= 35)) {
                            fillColor = 'FF0DCAF0'; // Biru muda - ELV.600
                        }
                    }
                    // Row 3 - Measurement Headers
                    else if (R === 2) {
                        if (C >= 4 && C <= 11) fillColor = 'FF0DCAF0'; // Biru muda - PEMBACAAN HDM
                        else if (C >= 12 && C <= 19) fillColor = 'FF0D6EFD'; // Biru - DEPTH (S)
                        else if (C >= 20 && C <= 27) fillColor = 'FF198754'; // Hijau - READINGS (S)
                        else if (C >= 28 && C <= 35) fillColor = 'FFFFC107'; // Kuning - PERGERAKAN (CM)
                    }
                    
                    // Terapkan warna background
                    if (fillColor) {
                        ws[cell_ref].s.fill = {
                            fgColor: { rgb: fillColor }
                        };
                        
                        // Text color - putih untuk header gelap, hitam untuk kuning
                        if (fillColor === 'FFFFC107') {
                            ws[cell_ref].s.font = { color: { rgb: "FF000000" }, bold: true };
                        } else {
                            ws[cell_ref].s.font = { color: { rgb: "FFFFFFFF" }, bold: true };
                        }
                        
                        // Border untuk semua header
                        ws[cell_ref].s.border = {
                            top: { style: 'thin', color: { rgb: "FF000000" } },
                            left: { style: 'thin', color: { rgb: "FF000000" } },
                            bottom: { style: 'thin', color: { rgb: "FF000000" } },
                            right: { style: 'thin', color: { rgb: "FF000000" } }
                        };
                        
                        // Alignment center
                        ws[cell_ref].s.alignment = { 
                            horizontal: 'center',
                            vertical: 'center',
                            wrapText: true
                        };
                    }
                }
            }
            
            // ===== STYLING DATA ROWS ===== (TANPA KOLOM AKSI)
            for (let R = 3; R <= range.e.r; R++) {
                for (let C = range.s.c; C <= range.e.c; C++) {
                    const cell_ref = XLSX.utils.encode_cell({r:R, c:C});
                    if (!ws[cell_ref]) continue;
                    
                    // Inisialisasi style jika belum ada
                    if (!ws[cell_ref].s) {
                        ws[cell_ref].s = {};
                    }
                    
                    // Border untuk semua cell data
                    ws[cell_ref].s.border = {
                        top: { style: 'thin', color: { rgb: "FFDEE2E6" } },
                        left: { style: 'thin', color: { rgb: "FFDEE2E6" } },
                        bottom: { style: 'thin', color: { rgb: "FFDEE2E6" } },
                        right: { style: 'thin', color: { rgb: "FFDEE2E6" } }
                    };
                    
                    // Alignment untuk data
                    if (C <= 3) {
                        // Kolom dasar (TAHUN, PERIODE, TANGGAL, DAM) - center
                        ws[cell_ref].s.alignment = { 
                            horizontal: 'center',
                            vertical: 'center'
                        };
                    } else {
                        // Semua kolom numerik - right align
                        ws[cell_ref].s.alignment = { 
                            horizontal: 'right',
                            vertical: 'center'
                        };
                        
                        // Format angka dengan 2 desimal
                        const cellValue = ws[cell_ref].v;
                        if (cellValue !== '-' && !isNaN(parseFloat(cellValue))) {
                            ws[cell_ref].z = '0.00';
                        }
                    }
                }
            }
            
            // Tambahkan worksheet ke workbook
            XLSX.utils.book_append_sheet(wb, ws, "Data HDM");
            
            // Generate filename dengan timestamp
            const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const filename = `HDM_Data_Export_${timestamp}.xlsx`;
            
            // Simpan file
            XLSX.writeFile(wb, filename);
            
            // Show success message
            setTimeout(() => {
                alert('Export berhasil! File Excel telah didownload.\n\nFile: ' + filename);
            }, 500);
            
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Terjadi kesalahan saat mengexport data: ' + error.message);
        } finally {
            // Restore button state
            this.innerHTML = originalText;
            this.disabled = false;
        }
    }, 1000);
});
    // Add Data button
    document.getElementById('addData').addEventListener('click', function() {
        window.location.href = '<?= base_url('horizontal-displacement/create') ?>';
    });
});

// Fungsi untuk auto-refresh data (opsional)
function startPolling() {
    function poll() {
        // Di sini bisa ditambahkan logika untuk auto-refresh data
        console.log('Polling HDM data...');
        
        // Poll lagi setelah 30 detik
        setTimeout(poll, 30000);
    }
    
    // Mulai polling
    poll();
}

// Mulai polling ketika halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    startPolling();
});

// Import SQL Functionality - VERSI PERBAIKAN LENGKAP
document.getElementById('btnImportSQL').addEventListener('click', function() {
    const sqlFileInput = document.getElementById('sqlFile');
    const importProgress = document.getElementById('importProgress');
    const importStatus = document.getElementById('importStatus');
    const btnImport = this;
    
    // Reset status sebelumnya
    importStatus.style.display = 'none';
    
    // Validasi file
    if (!sqlFileInput.files || sqlFileInput.files.length === 0) {
        showImportStatus('❌ Pilih file SQL terlebih dahulu', 'danger');
        return;
    }
    
    const file = sqlFileInput.files[0];
    
    // Validasi ekstensi
    if (!file.name.toLowerCase().endsWith('.sql')) {
        showImportStatus('❌ File harus berformat .sql', 'danger');
        return;
    }
    
    // Validasi ukuran file (client-side)
    if (file.size > 50 * 1024 * 1024) {
        showImportStatus('❌ Ukuran file maksimal 50MB', 'danger');
        return;
    }
    
    if (file.size === 0) {
        showImportStatus('❌ File kosong', 'danger');
        return;
    }
    
    // Show progress
    importProgress.style.display = 'block';
    const progressBar = importProgress.querySelector('.progress-bar');
    progressBar.style.width = '0%';
    progressBar.textContent = '0%';
    
    btnImport.disabled = true;
    btnImport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
    
    // Create form data
    const formData = new FormData();
    formData.append('sql_file', file);
    
    // Progress simulation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 2;
        if (progress <= 80) { // Hanya sampai 80%, sisanya menunggu response
            progressBar.style.width = progress + '%';
            progressBar.textContent = progress + '%';
        }
    }, 100);
    
    // Send request
    fetch('<?= base_url('horizontal-displacement/import-sql') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressBar.textContent = '100%';
        
        // Cek status response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showImportStatus('✅ ' + data.message, 'success');
            
            // Tampilkan detail stats jika ada
            if (data.stats) {
                const stats = data.stats;
                let detailHtml = `
                    <div class="mt-3 p-2 bg-light rounded">
                        <h6 class="mb-2">📊 Detail Import:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small>Total Query: <strong>${stats.total}</strong></small><br>
                                <small>Berhasil: <strong class="text-success">${stats.success}</strong></small>
                            </div>
                            <div class="col-6">
                                <small>Gagal: <strong class="text-danger">${stats.failed}</strong></small><br>
                                <small>Affected Rows: <strong>${stats.affected_rows || 0}</strong></small>
                            </div>
                        </div>
                `;
                
                if (data.error_display) {
                    detailHtml += `
                        <div class="mt-2">
                            <h6 class="mb-1">❌ Error Details:</h6>
                            <div class="bg-white p-2 rounded small text-danger" style="max-height: 100px; overflow-y: auto;">
                                ${data.error_display.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    `;
                }
                
                detailHtml += `</div>`;
                importStatus.innerHTML += detailHtml;
            }
            
            // Auto refresh setelah 3 detik jika sukses
            if (data.success) {
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                    if (modal) {
                        modal.hide();
                    }
                    // Refresh halaman untuk menampilkan data baru
                    window.location.reload();
                }, 3000);
            }
            
        } else {
            showImportStatus('❌ ' + data.message, 'danger');
            
            // Tampilkan error details jika ada
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
        console.error('Import Error:', error);
        showImportStatus('❌ Terjadi kesalahan: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button state setelah delay
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

</script>
</body>
</html>