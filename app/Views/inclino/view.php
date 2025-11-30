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
        
        .data-table {
            min-width: 2000px;
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
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-compass me-2"></i>InclinoMeter - Monitoring
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <a href="<?= base_url('inclino/create') ?>" class="btn btn-outline-success">
                <i class="fas fa-plus me-1"></i> Add Data
            </a>
            
            <button type="button" class="btn btn-outline-info" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            
            <button type="button" class="btn btn-outline-primary" id="showCsvModalBtn">
                <i class="fas fa-file-csv me-1"></i> Import CSV
            </button>
        </div>

        <div class="table-controls">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
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
                </select>
            </div>

            <!-- Periode -->
            <div class="filter-item">
                <label for="periodeFilter" class="form-label">Periode</label>
                <select id="periodeFilter" class="form-select">
                    <option value="">Semua Periode</option>
                </select>
            </div>

            <!-- Lokasi Filter -->
            <div class="filter-item">
                <label for="lokasiFilter" class="form-label">Lokasi</label>
                <select id="lokasiFilter" class="form-select">
                    <option value="">Semua Lokasi</option>
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

    <!-- Main Table Container - KOSONG -->
    <div class="table-responsive" id="tableContainer">
        <div class="text-center py-5">
            <i class="fas fa-compass fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Data InclinoMeter Belum Tersedia</h4>
            <p class="text-muted">Silakan tambah data pertama untuk memulai monitoring InclinoMeter</p>
            <a href="<?= base_url('inclino/create') ?>" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
            </a>
        </div>
    </div>
</div>

<!-- Scroll Indicator -->
<div class="scroll-indicator" id="scrollIndicator">
    <i class="fas fa-arrows-alt-h me-1"></i>
    <span id="scrollText">Scroll untuk melihat lebih banyak data</span>
</div>

<!-- Delete Confirmation Modal -->
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
                <p>Apakah Anda yakin ingin menghapus data InclinoMeter ini?</p>
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

<!-- Modal Upload CSV -->
<div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCsvModalLabel">
                    <i class="fas fa-file-csv me-2"></i>Import Data InclinoMeter CSV
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
                        <label for="csvFile" class="form-label">Pilih File CSV</label>
                        <input class="form-control" type="file" id="csvFile" name="csv_file" accept=".csv,.txt" required>
                        <div class="form-text">
                            Format file: .csv (Maksimal 5MB)
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="display: none;" id="uploadProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    
                    <div id="uploadStatus" class="alert" style="display: none;"></div>
                </form>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Format File yang Didukung</h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">✅ Format RST Digital Inclinometer:</p>
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
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Preview Data</h6>
                    </div>
                    <div class="card-body">
                        <div id="csvPreview" class="small">
                            <p class="text-muted">Pilih file CSV untuk melihat preview data</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUploadCSV">
                    <i class="fas fa-upload me-1"></i> Upload CSV
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// REAL UPLOAD FUNCTION - FIXED
function uploadCSVFile(progressBar, statusDiv, uploadBtn, file) {
    const formData = new FormData();
    formData.append('csv_file', file);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.querySelector('.progress-bar').style.width = percentComplete + '%';
        }
    });

    xhr.addEventListener('load', function() {
        progressBar.style.display = 'none';
        
        try {
            const response = JSON.parse(xhr.responseText);
            
            if (response.status === 'success') {
                showStatus('success', 
                    `<strong>${response.message}</strong><br>
                    <small class="mt-1">
                        File: ${response.data.file_name} | 
                        Borehole: ${response.data.borehole} | 
                        Tanggal: ${response.data.reading_date} | 
                        Data Imported: ${response.data.imported} |
                        Data Skipped: ${response.data.skipped}
                    </small>`
                );
                
                // Auto close dan refresh
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadCsvModal'));
                    modal.hide();
                    location.reload();
                }, 3000);
            } else {
                showStatus('danger', `Upload gagal: ${response.message || 'Terjadi kesalahan'}`);
            }
        } catch (e) {
            showStatus('danger', 'Error parsing server response: ' + e.message);
        }
        
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload CSV';
    });

    xhr.addEventListener('error', function() {
        progressBar.style.display = 'none';
        showStatus('danger', 'Network error. Periksa koneksi internet Anda.');
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload CSV';
    });

    // PERBAIKAN: TAMBAH HEADER INI
    xhr.open('POST', '<?= base_url("inclino/import/uploadCSV") ?>');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // HEADER INI YANG PENTING
    xhr.send(formData);
}

// Fungsi untuk menampilkan status
function showStatus(type, message) {
    const statusDiv = document.getElementById('uploadStatus');
    const icons = {
        'success': 'check-circle',
        'warning': 'exclamation-triangle', 
        'danger': 'exclamation-circle'
    };
    
    statusDiv.className = `alert alert-${type}`;
    statusDiv.innerHTML = `<i class="fas fa-${icons[type]} me-2"></i> ${message}`;
    statusDiv.style.display = 'block';
}

// Basic JavaScript functionality
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi modal CSV
    const csvModal = new bootstrap.Modal(document.getElementById('uploadCsvModal'));
    
    // Event listener untuk button Import CSV
    document.getElementById('showCsvModalBtn').addEventListener('click', function() {
        csvModal.show();
    });

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        alert('Fitur export Excel akan tersedia ketika data sudah ada.');
    });

    // Filter Functionality
    const tahunFilter = document.getElementById('tahunFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const lokasiFilter = document.getElementById('lokasiFilter');
    const searchInput = document.getElementById('searchInput');
    const resetFilter = document.getElementById('resetFilter');

    function filterTable() {
        console.log('Filter functionality akan diimplementasikan');
    }

    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    lokasiFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
        periodeFilter.value = '';
        lokasiFilter.value = '';
        searchInput.value = '';
    });

    // CSV File Preview
    document.getElementById('csvFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('csvPreview');
        
        if (!file) {
            preview.innerHTML = '<p class="text-muted">Pilih file CSV untuk melihat preview data</p>';
            return;
        }

        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            preview.innerHTML = '<div class="alert alert-warning">File harus berformat CSV</div>';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            const lines = content.split('\n').slice(0, 10);
            
            let previewHTML = '<div class="table-responsive"><table class="table table-sm table-bordered">';
            
            lines.forEach((line, index) => {
                const cells = line.split(',');
                previewHTML += '<tr>';
                cells.forEach(cell => {
                    if (index === 0) {
                        previewHTML += `<th class="bg-light">${cell.trim()}</th>`;
                    } else {
                        previewHTML += `<td>${cell.trim()}</td>`;
                    }
                });
                previewHTML += '</tr>';
            });
            
            previewHTML += '</table></div>';
            previewHTML += `<p class="text-muted small">Menampilkan ${lines.length} baris pertama</p>`;
            
            preview.innerHTML = previewHTML;
        };
        
        reader.readAsText(file);
    });

    // REAL CSV UPLOAD HANDLER
    document.getElementById('btnUploadCSV').addEventListener('click', function() {
        const progressBar = document.getElementById('uploadProgress');
        const statusDiv = document.getElementById('uploadStatus');
        const uploadBtn = document.getElementById('btnUploadCSV');
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];
        
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
        
        // REAL UPLOAD - TIDAK SIMULASI LAGI
        uploadCSVFile(progressBar, statusDiv, uploadBtn, file);
    });

    // Reset form ketika modal ditutup
    document.getElementById('uploadCsvModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('uploadCsvForm').reset();
        document.getElementById('uploadStatus').style.display = 'none';
        document.getElementById('uploadProgress').style.display = 'none';
        document.getElementById('csvPreview').innerHTML = '<p class="text-muted">Pilih file CSV untuk melihat preview data</p>';
    });
});

// Scroll indicator functionality
window.addEventListener('scroll', function() {
    const indicator = document.getElementById('scrollIndicator');
    const tableContainer = document.getElementById('tableContainer');
    
    if (tableContainer && tableContainer.scrollWidth > tableContainer.clientWidth) {
        indicator.style.display = 'block';
        document.getElementById('scrollText').textContent = 'Scroll horizontal untuk melihat lebih banyak data';
    } else {
        indicator.style.display = 'none';
    }
});
</script>
</body>
</html>