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
            min-width: 90px;
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
        
        .btn-view {
            color: #fff;
            background-color: #198754;
            border: 1px solid #198754;
        }
        
        .btn-view:hover {
            background-color: #157347;
            border-color: #146c43;
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
            <a href="<?= base_url('horizontal-displacement/data-lengkap') ?>" class="btn btn-outline-info">
                <i class="fas fa-database"></i> Data Lengkap
            </a>
            <a href="<?= base_url('horizontal-displacement/grafik') ?>" class="btn btn-outline-success">
                <i class="fas fa-chart-line"></i> Grafik HDM
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
            <!-- Di bagian filter, ubah dam menjadi dma -->
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

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Detail Data HDM
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Detail content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
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

// Fungsi untuk mengurutkan data berdasarkan tanggal
function sortDataByDate(data) {
    return data.sort((a, b) => {
        const dateA = new Date(a.pengukuran?.tanggal || 0);
        const dateB = new Date(b.pengukuran?.tanggal || 0);
        return dateB - dateA; // Urutkan dari tanggal terbaru ke terlama
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    
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
    // Di bagian renderTableBody, perbaiki tampilan DMA
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
    const tahunCounts = {};
    const processedYears = [];
    
    data.forEach(item => {
        const tahun = item.pengukuran?.tahun ?? '-';
        tahunCounts[tahun] = (tahunCounts[tahun] || 0) + 1;
    });
    
    data.forEach(item => {
        const p = item.pengukuran || {};
        const pembacaanElv600 = item.pembacaan_elv600 || {};
        const pembacaanElv625 = item.pembacaan_elv625 || {};
        const depthElv600 = item.depth_elv600 || {};
        const depthElv625 = item.depth_elv625 || {};
        const initialReadingElv600 = item.initial_reading_elv600 || {}; // PERBAIKAN: tambahkan initial reading
        const initialReadingElv625 = item.initial_reading_elv625 || {}; // PERBAIKAN: tambahkan initial reading
        const pergerakanElv600 = item.pergerakan_elv600 || {};
        const pergerakanElv625 = item.pergerakan_elv625 || {};
        
        const tahun = p.tahun ?? '-';
        const periode = p.periode ?? '-';
        const dma = p.dma ?? '-'; // PERBAIKAN: ubah dam menjadi dma
        const pid = p.id_pengukuran ?? null;
        
        const showTahun = !processedYears.includes(tahun);
        if (showTahun) processedYears.push(tahun);
        
        html += `
            <tr data-tahun="${tahun}" data-periode="${periode}" data-dma="${dma}" data-pid="${pid}">
                ${showTahun ? `<td rowspan="${tahunCounts[tahun]}" class="sticky">${tahun}</td>` : ''}
                <td class="sticky-2">${periode}</td>
                <td class="sticky-3">${p.tanggal ? new Date(p.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                <td class="sticky-4">${dma}</td> <!-- PERBAIKAN: ubah dam menjadi dma -->
                
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
                <td>${initialReadingElv625.hv_1 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
                <td>${initialReadingElv625.hv_2 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
                <td>${initialReadingElv625.hv_3 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->

                <!-- READINGS (S) - ELV 600 -->
                <td>${initialReadingElv600.hv_1 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
                <td>${initialReadingElv600.hv_2 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
                <td>${initialReadingElv600.hv_3 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
                <td>${initialReadingElv600.hv_4 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
                <td>${initialReadingElv600.hv_5 || '-'}</td> <!-- PERBAIKAN: ubah readings menjadi initial reading -->
            
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
                        <button type="button" class="btn-action btn-view view-detail" 
                               data-id="${pid}" data-bs-toggle="tooltip" 
                               data-bs-placement="top" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
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
    // Di bagian JavaScript, perbaiki filter data
function filterData() {
    const tVal = tahunFilter.value;
    const pVal = periodeFilter.value;
    const dVal = damFilter.value;
    const searchVal = searchInput.value.toLowerCase();
    
    filteredData = allData.filter(item => {
        const p = item.pengukuran || {};
        const tahun = p.tahun ?? '-';
        const periode = p.periode ?? '-';
        const dma = p.dma ?? '-'; // PERBAIKAN: ubah dam menjadi dma
        
        const tahunMatch = !tVal || tahun === tVal;
        const periodeMatch = !pVal || periode === pVal;
        const dmaMatch = !dVal || dma.toString() === dVal; // PERBAIKAN: ubah dam menjadi dma
        
        let searchMatch = true;
        if (searchVal) {
            const searchText = Object.values(p).join(' ').toLowerCase() +
                             Object.values(item.pembacaan_elv600 || {}).join(' ').toLowerCase() +
                             Object.values(item.pembacaan_elv625 || {}).join(' ').toLowerCase() +
                             Object.values(item.depth_elv600 || {}).join(' ').toLowerCase() +
                             Object.values(item.depth_elv625 || {}).join(' ').toLowerCase() +
                             Object.values(item.initial_reading_elv600 || {}).join(' ').toLowerCase() + // PERBAIKAN: tambahkan initial reading
                             Object.values(item.initial_reading_elv625 || {}).join(' ').toLowerCase() + // PERBAIKAN: tambahkan initial reading
                             Object.values(item.pergerakan_elv600 || {}).join(' ').toLowerCase() +
                             Object.values(item.pergerakan_elv625 || {}).join(' ').toLowerCase();
            searchMatch = searchText.includes(searchVal);
        }
        
        return tahunMatch && periodeMatch && dmaMatch && searchMatch; // PERBAIKAN: ubah dam menjadi dma
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
        // View Detail
        document.querySelectorAll('.view-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                showDetailModal(id);
            });
        });
        
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
                deleteData(id);
            });
        });
        
        // Re-initialize tooltips
        const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        newTooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // ============ MODAL FUNCTIONALITY ============
    function showDetailModal(id) {
        // Show loading
        document.getElementById('detailModalBody').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;
        
        detailModal.show();
        
        // Load detail data
        fetch('<?= base_url('horizontal-displacement/detail') ?>/' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const detailData = data.data;
                    let html = `
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2">Data Pengukuran</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th class="w-25">Tahun</th><td>${detailData.pengukuran.tahun || '-'}</td></tr>
                                    <tr><th>Periode</th><td>${detailData.pengukuran.periode || '-'}</td></tr>
                                    <tr><th>Tanggal</th><td>${detailData.pengukuran.tanggal ? new Date(detailData.pengukuran.tanggal).toLocaleDateString('id-ID') : '-'}</td></tr>
                                    <tr><th>DAM</th><td>${detailData.pengukuran.dam || '-'}</td></tr>
                                </table>
                            </div>
                    `;
                    
                    // ELV 625 Data
                    html += `
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 text-info">ELV 625 Data</h6>
                            <table class="table table-sm table-bordered">
                                <tr><th colspan="3" class="text-center bg-info text-white">Pembacaan HDM</th></tr>
                                <tr><th>HV 1</th><td>${detailData.pembacaan_elv625.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.pembacaan_elv625.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.pembacaan_elv625.hv_3 || '-'}</td></tr>
                                
                                <tr><th colspan="3" class="text-center bg-primary text-white">Depth (S)</th></tr>
                                <tr><th>HV 1</th><td>${detailData.depth_elv625.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.depth_elv625.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.depth_elv625.hv_3 || '-'}</td></tr>
                                
                                <tr><th colspan="3" class="text-center bg-success text-white">Readings (S)</th></tr>
                                <tr><th>HV 1</th><td>${detailData.readings_elv625.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.readings_elv625.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.readings_elv625.hv_3 || '-'}</td></tr>
                                
                                <tr><th colspan="3" class="text-center bg-warning text-dark">Pergerakan (CM)</th></tr>
                                <tr><th>HV 1</th><td>${detailData.pergerakan_elv625.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.pergerakan_elv625.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.pergerakan_elv625.hv_3 || '-'}</td></tr>
                            </table>
                        </div>
                    `;
                    
                    // ELV 600 Data
                    html += `
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 text-info">ELV 600 Data</h6>
                            <table class="table table-sm table-bordered">
                                <tr><th colspan="5" class="text-center bg-info text-white">Pembacaan HDM</th></tr>
                                <tr><th>HV 1</th><td>${detailData.pembacaan_elv600.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.pembacaan_elv600.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.pembacaan_elv600.hv_3 || '-'}</td></tr>
                                <tr><th>HV 4</th><td>${detailData.pembacaan_elv600.hv_4 || '-'}</td></tr>
                                <tr><th>HV 5</th><td>${detailData.pembacaan_elv600.hv_5 || '-'}</td></tr>
                                
                                <tr><th colspan="5" class="text-center bg-primary text-white">Depth (S)</th></tr>
                                <tr><th>HV 1</th><td>${detailData.depth_elv600.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.depth_elv600.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.depth_elv600.hv_3 || '-'}</td></tr>
                                <tr><th>HV 4</th><td>${detailData.depth_elv600.hv_4 || '-'}</td></tr>
                                <tr><th>HV 5</th><td>${detailData.depth_elv600.hv_5 || '-'}</td></tr>
                                
                                <tr><th colspan="5" class="text-center bg-success text-white">Readings (S)</th></tr>
                                <tr><th>HV 1</th><td>${detailData.readings_elv600.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.readings_elv600.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.readings_elv600.hv_3 || '-'}</td></tr>
                                <tr><th>HV 4</th><td>${detailData.readings_elv600.hv_4 || '-'}</td></tr>
                                <tr><th>HV 5</th><td>${detailData.readings_elv600.hv_5 || '-'}</td></tr>
                                
                                <tr><th colspan="5" class="text-center bg-warning text-dark">Pergerakan (CM)</th></tr>
                                <tr><th>HV 1</th><td>${detailData.pergerakan_elv600.hv_1 || '-'}</td></tr>
                                <tr><th>HV 2</th><td>${detailData.pergerakan_elv600.hv_2 || '-'}</td></tr>
                                <tr><th>HV 3</th><td>${detailData.pergerakan_elv600.hv_3 || '-'}</td></tr>
                                <tr><th>HV 4</th><td>${detailData.pergerakan_elv600.hv_4 || '-'}</td></tr>
                                <tr><th>HV 5</th><td>${detailData.pergerakan_elv600.hv_5 || '-'}</td></tr>
                            </table>
                        </div>
                    `;
                    
                    html += `</div>`;
                    document.getElementById('detailModalBody').innerHTML = html;
                } else {
                    document.getElementById('detailModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${data.error || 'Terjadi kesalahan saat memuat data'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('detailModalBody').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat data: ${error.message}
                    </div>
                `;
            });
    }
    
    // Delete Data Function
    function deleteData(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            fetch('<?= base_url('horizontal-displacement/delete') ?>/' + id, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data berhasil dihapus');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan saat menghapus data');
            });
        }
    }
    
    // Add Data button
    document.getElementById('addData').addEventListener('click', function() {
        window.location.href = '<?= base_url('horizontal-displacement/create') ?>';
    });

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        const table = document.getElementById('exportTable');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Data HDM"});
        XLSX.writeFile(wb, "data_horizontal_displacement_meter.xlsx");
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
</script>
</body>
</html>