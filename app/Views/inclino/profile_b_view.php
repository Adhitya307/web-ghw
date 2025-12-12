<!-- profile_b_view.php -->
<?php
$session = session();
$isLoggedIn = $session->get('isLoggedIn');
$role = $session->get('role');
$isAdmin = $role == 'admin';
$username = $session->get('username');
$fullName = $session->get('fullName');

if (!$isLoggedIn) {
    header('Location: ' . base_url('/login'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Profil B Monitoring - PT Indonesia Power' ?></title>

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
        .bg-info-column { background-color: #e7f1ff !important; }
        .bg-primary-header { 
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%) !important;
            color: white !important;
        }
        .bg-secondary-header {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%) !important;
            color: white !important;
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
            min-width: 800px;
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

        /* STICKY HEADER */
        .table thead th {
            top: 0;
            z-index: 100;
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
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            margin: 0 2px;
            font-size: 0.75rem;
            border: none;
        }
        
        .btn-export {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
        }
        
        .btn-export:hover {
            background: linear-gradient(135deg, #20c997 0%, #198754 100%);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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
            display: none;
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

        /* LOADING */
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
            color: #495057 !important; /* Warna abu tua */
            font-weight: 500 !important; /* Tidak bold */
        }
        
        .text-cell {
            text-align: center;
            font-weight: 500;
            color: #495057 !important; /* Warna abu tua */
        }
        
        .depth-cell {
            text-align: center;
            font-weight: 600;
            background-color: #e6f7ff !important;
            color: #212529 !important; /* Warna hitam untuk depth */
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

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-item {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-chart-line me-2 text-info"></i>Profil B - Data Monitoring
        </h2>

        <!-- Button Group -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="<?= site_url('inclino/view') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Data Inclino
            </a>
            
            <button type="button" class="btn btn-export" onclick="exportProfilBExcel()">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
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
                <label for="yearFilter" class="form-label">Tahun</label>
                <select id="yearFilter" class="form-select shadow-sm">
                    <option value="">Pilih Tahun</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>"><?= $year ?></option>
                    <?php endforeach; ?>
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
    <div id="metadataSection" class="metadata-card">
        <div class="metadata-title">
            <i class="fas fa-info-circle me-2 text-info"></i>Informasi Data
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Tahun:</span>
                    <span class="metadata-value" id="metadataYear">-</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Total Data:</span>
                    <span class="metadata-value" id="metadataTotal">0</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Jumlah Tanggal:</span>
                    <span class="metadata-value" id="metadataDates">0</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metadata-item">
                    <span class="metadata-label">Kedalaman:</span>
                    <span class="metadata-value" id="metadataDepthRange">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="table-container-wrapper">
        <div class="table-responsive" id="tableContainer">
            <div class="loading-overlay" id="tableLoading" style="display: none;">
                <div class="loading-spinner mb-3"></div>
                <div class="text-muted fw-medium">Memuat data Profil B...</div>
            </div>
            
            <div id="tablePlaceholder" class="table-placeholder">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h4 class="text-muted fw-medium mb-2">Data Profil B Belum Ditampilkan</h4>
                <p class="text-muted mb-3">Pilih tahun dan klik "Muat Data" untuk menampilkan data</p>
            </div>
            
            <div id="dataTableWrapper" style="display: none;">
                <table class="table table-bordered table-striped table-hover mb-0" id="profilBTable">
                    <thead id="tableHeader">
                        <!-- Header akan diisi oleh JavaScript -->
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div id="noDataMessage" class="table-placeholder" style="display: none;">
                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                <h4 class="text-muted fw-medium mb-2">Tidak Ada Data Profil B</h4>
                <p class="text-muted">Tidak ditemukan data untuk tahun yang dipilih</p>
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
    </div>
</div>

<!-- Scroll Indicator -->
<div class="scroll-indicator" id="scrollIndicator">
    <i class="fas fa-arrows-alt-h me-1"></i>
    <span id="scrollText">Scroll untuk melihat lebih banyak data</span>
</div>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// ============ VARIABEL GLOBAL ============
let currentProfilBData = null;
let currentData = [];

// ============ INISIALISASI HALAMAN ============
document.addEventListener('DOMContentLoaded', function () {
    // Event listener untuk filter
    const yearFilter = document.getElementById('yearFilter');
    const applyFilter = document.getElementById('applyFilter');
    const resetFilter = document.getElementById('resetFilter');
    const resetFilterBtn = document.getElementById('resetFilterBtn');
    
    // Apply filter
    applyFilter.addEventListener('click', loadProfilBData);
    
    // Reset filter
    resetFilter.addEventListener('click', resetFilters);
    resetFilterBtn.addEventListener('click', resetFilters);
    
    // Scroll indicator functionality
    window.addEventListener('scroll', updateScrollIndicator);
    
    // Auto load data jika ada tahun di URL
    const urlParams = new URLSearchParams(window.location.search);
    const yearFromUrl = urlParams.get('year');
    
    if (yearFromUrl) {
        yearFilter.value = yearFromUrl;
        setTimeout(() => {
            loadProfilBData();
        }, 100);
    }
});

// ============ FUNGSI LOAD DATA PROFIL B ============
async function loadProfilBData() {
    const year = document.getElementById('yearFilter').value;
    
    if (!year) {
        showAlert('warning', 'Pilih tahun terlebih dahulu');
        return;
    }
    
    // Tampilkan loading
    showLoading(true);
    
    try {
        const params = new URLSearchParams({
            year: year || ''
        });
        
        const response = await fetch(`<?= site_url('inclino/profileb/getDataByYear') ?>?${params}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            currentProfilBData = result.data;
            currentData = currentProfilBData.data || [];
            
            // Render tabel
            if (currentData.length > 0) {
                renderProfilBTable(currentData, currentProfilBData.dates);
                showTable(true);
                showMetadata(currentProfilBData.metadata);
                updatePagination(currentData.length);
            } else {
                showTable(false);
                showNoData(true);
            }
        } else {
            showAlert('danger', result.message || 'Gagal memuat data');
            showTable(false);
            showNoData(true);
        }
    } catch (error) {
        console.error('Error loading Profil B:', error);
        showAlert('danger', 'Terjadi kesalahan saat memuat data');
        showTable(false);
        showNoData(true);
    } finally {
        showLoading(false);
    }
}

// ============ FUNGSI RENDER TABEL ============
function renderProfilBTable(data, dates) {
    const tableHeader = document.getElementById('tableHeader');
    const tableBody = document.getElementById('tableBody');
    
    // Clear existing content
    tableHeader.innerHTML = '';
    tableBody.innerHTML = '';
    
    // Create header row
    const headerRow = document.createElement('tr');
    
    // Kolom Depth (sticky)
    const depthHeader = document.createElement('th');
    depthHeader.textContent = 'Depth (m)';
    depthHeader.className = 'bg-info-column sticky-col-depth';
    depthHeader.style.position = 'sticky';
    depthHeader.style.left = '0';
    depthHeader.style.zIndex = '20';
    depthHeader.style.minWidth = '100px';
    headerRow.appendChild(depthHeader);
    
    // Kolom tanggal-tanggal (semua data di tahun yang dipilih)
    if (dates && dates.length > 0) {
        // Urutkan tanggal secara kronologis
        dates.sort((a, b) => new Date(a) - new Date(b));
        
        dates.forEach(date => {
            const dateHeader = document.createElement('th');
            dateHeader.textContent = formatDateForHeader(date);
            dateHeader.className = 'bg-secondary-header';
            dateHeader.style.minWidth = '120px';
            dateHeader.title = formatDateFull(date);
            headerRow.appendChild(dateHeader);
        });
    }
    
    tableHeader.appendChild(headerRow);
    
    // Render data rows
    if (data && data.length > 0) {
        // Sort data berdasarkan depth ASC
        const sortedData = [...data].sort((a, b) => {
            return parseFloat(a.depth) - parseFloat(b.depth);
        });
        
        sortedData.forEach(rowData => {
            const tr = document.createElement('tr');
            
            // Kolom Depth
            const tdDepth = document.createElement('td');
            tdDepth.textContent = rowData.depth;
            tdDepth.className = 'depth-cell sticky-col-depth';
            tdDepth.style.position = 'sticky';
            tdDepth.style.left = '0';
            tdDepth.style.zIndex = '10';
            tdDepth.style.backgroundColor = '#e6f7ff';
            tr.appendChild(tdDepth);
            
            // Kolom nilai untuk setiap tanggal
            if (dates && dates.length > 0) {
                dates.forEach(date => {
                    const td = document.createElement('td');
                    const value = rowData[date] !== null && rowData[date] !== '' ? 
                                 parseFloat(rowData[date]) : null;
                    
                    if (value !== null && !isNaN(value)) {
                        td.textContent = value.toFixed(4);
                        td.className = 'number-cell';
                        
                        // HAPUS KODE WARNA MERAH/HIJAU - SEMUA NILAI SATU WARNA
                        // td.style.color = '#495057'; // Warna abu tua
                        // td.style.fontWeight = '500'; // Tidak bold
                        
                        td.title = `Tanggal: ${formatDateFull(date)}\nNilai: ${value.toFixed(4)}`;
                    } else {
                        td.textContent = '-';
                        td.className = 'text-center text-muted';
                        td.title = 'Data tidak tersedia';
                    }
                    
                    tr.appendChild(td);
                });
            }
            
            tableBody.appendChild(tr);
        });
    } else {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = (dates ? dates.length : 0) + 1;
        td.textContent = 'Tidak ada data yang ditampilkan';
        td.className = 'text-center py-3 text-muted';
        tr.appendChild(td);
        tableBody.appendChild(tr);
    }
    
    // Update scroll indicator
    setTimeout(updateScrollIndicator, 100);
}

// ============ FUNGSI HELPER ============

// Format tanggal untuk header tabel
function formatDateForHeader(dateString) {
    if (!dateString) return '-';
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const day = date.getDate();
        const month = date.toLocaleDateString('id-ID', { month: 'short' });
        const year = date.getFullYear().toString().slice(-2);
        
        return `${day} ${month} ${year}`;
    } catch (e) {
        return dateString;
    }
}

// Format tanggal lengkap untuk tooltip
function formatDateFull(dateString) {
    if (!dateString) return '-';
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

// Reset semua filter
function resetFilters() {
    document.getElementById('yearFilter').value = '';
    
    // Reset tampilan
    document.getElementById('tablePlaceholder').style.display = 'block';
    document.getElementById('dataTableWrapper').style.display = 'none';
    document.getElementById('metadataSection').style.display = 'none';
    document.getElementById('paginationSection').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'none';
    
    currentProfilBData = null;
    currentData = [];
}

// Tampilkan metadata
function showMetadata(metadata) {
    const metadataSection = document.getElementById('metadataSection');
    
    if (metadata) {
        metadataSection.style.display = 'block';
        document.getElementById('metadataYear').textContent = metadata.year || '-';
        document.getElementById('metadataTotal').textContent = metadata.total_records || '0';
        document.getElementById('metadataDates').textContent = metadata.total_dates || '0';
        document.getElementById('metadataDepthRange').textContent = metadata.depth_range || '-';
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
        paginationSection.style.display = 'none';
    }
}

// Tampilkan pesan no data
function showNoData(show) {
    const noDataMessage = document.getElementById('noDataMessage');
    const tablePlaceholder = document.getElementById('tablePlaceholder');
    
    if (show) {
        tablePlaceholder.style.display = 'none';
        noDataMessage.style.display = 'block';
    } else {
        noDataMessage.style.display = 'none';
    }
}

// Update pagination
function updatePagination(count) {
    document.getElementById('currentCount').textContent = count;
    document.getElementById('totalCount').textContent = currentProfilBData ? currentProfilBData.metadata.total_records : 0;
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

// Export ke Excel
function exportProfilBExcel() {
    const year = document.getElementById('yearFilter').value;
    
    if (!year) {
        showAlert('warning', 'Pilih tahun terlebih dahulu untuk export');
        return;
    }
    
    // Build URL dengan parameter
    let url = '<?= site_url("inclino/profileb/exportToExcel") ?>?';
    if (year) url += `year=${year}`;
    
    // Download langsung
    window.open(url, '_blank');
}
</script>
</body>
</html>