<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Extensometer Monitoring - PT Indonesia Power' ?></title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
        
        .sticky { position: sticky; left: 0; background: white; z-index: 5; }
        .sticky-2 { position: sticky; left: 80px; background: white; z-index: 5; }
        .sticky-3 { position: sticky; left: 160px; background: white; z-index: 5; }
        
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
        }
        
        .data-table {
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
        
        .bg-reading { background-color: #e8f4fd !important; color: #2c3e50 !important; }
        .bg-deformasi { background-color: #f0f9eb !important; color: #2c3e50 !important; }
        .bg-initial { background-color: #fff2cc !important; color: #2c3e50 !important; }
        .bg-action { background-color: #f8f9fa !important; color: #2c3e50 !important; }
        
        .btn-ex {
            min-width: 60px;
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
            z-index: 1000;
            display: none;
        }
        
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .scientific-notation {
            font-size: 0.7rem;
        }
        
        .ex-header {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-ruler-combined me-2"></i>Extensometer Monitoring System
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('extenso/ex1') ?>" class="btn btn-primary btn-ex">
                <i class="fas fa-table"></i> Input Data
            </a>
            <a href="<?= base_url('extenso/create') ?>" class="btn btn-outline-success">
                <i class="fas fa-plus me-1"></i> Add Data
            </a>
            
            <button type="button" class="btn btn-outline-info" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>

            <button type="button" class="btn btn-outline-warning" onclick="showImportModal()">
                <i class="fas fa-database me-1"></i> Import SQL
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
                    <th rowspan="3" class="sticky">TAHUN</th>
                    <th rowspan="3" class="sticky-2">PERIODE</th>
                    <th rowspan="3" class="sticky-3">TANGGAL</th>
                    
                    <!-- PEMBACAAN (EX1-EX4) -->
                    <th colspan="12" class="bg-reading">PEMBACAAN</th>
                    
                    <!-- DEFORMASI (EX1-EX4) -->
                    <th colspan="12" class="bg-deformasi">DEFORMASI</th>
                    
                    <!-- INITIAL READINGS (EX1-EX4) -->
                    <th colspan="12" class="bg-initial">INITIAL READINGS</th>
                    
                    <th rowspan="3" class="action-cell bg-action">AKSI</th>
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
                <?php if(empty($pengukuran)): ?>
                    <tr>
                        <td colspan="40" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data Extensometer yang tersedia</p>
                            <a href="<?= base_url('extenso/create') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    // Fungsi untuk memformat angka
                    function formatNumber($number) {
                        if ($number === null || $number === '') {
                            return '-';
                        }
                        
                        // Jika angka sangat kecil, gunakan notasi E
                        if (abs($number) < 0.0001 && $number != 0) {
                            return '<span class="scientific-notation">' . sprintf('%.8E', $number) . '</span>';
                        }
                        
                        // Format angka dengan 4 digit di belakang koma
                        $formatted = number_format($number, 4, '.', '');
                        
                        // Hapus trailing zeros dan titik desimal yang tidak perlu
                        $formatted = preg_replace('/\.?0+$/', '', $formatted);
                        
                        return $formatted;
                    }
                    ?>
                    
                    <?php foreach($pengukuran as $item): 
                        $p = $item['pengukuran'];
                        $pembacaan = $item['pembacaan'];
                        $deformasi = $item['deformasi'];
                        $readings = $item['readings'];
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>">
                        <!-- Basic Info -->
                        <td class="sticky"><?= esc($p['tahun'] ?? '-') ?></td>
                        <td class="sticky-2"><?= esc($p['periode'] ?? '-') ?></td>
                        <td class="sticky-3"><?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?></td>
                        
                        <!-- PEMBACAAN DATA (EX1-EX4) -->
                        <?php for($i = 1; $i <= 4; $i++): ?>
                            <td class="number-cell"><?= isset($pembacaan['ex'.$i]['pembacaan_10']) ? formatNumber($pembacaan['ex'.$i]['pembacaan_10']) : '-' ?></td>
                            <td class="number-cell"><?= isset($pembacaan['ex'.$i]['pembacaan_20']) ? formatNumber($pembacaan['ex'.$i]['pembacaan_20']) : '-' ?></td>
                            <td class="number-cell"><?= isset($pembacaan['ex'.$i]['pembacaan_30']) ? formatNumber($pembacaan['ex'.$i]['pembacaan_30']) : '-' ?></td>
                        <?php endfor; ?>
                        
                        <!-- DEFORMASI DATA (EX1-EX4) -->
                        <?php for($i = 1; $i <= 4; $i++): ?>
                            <td class="number-cell"><?= isset($deformasi['ex'.$i]['deformasi_10']) ? formatNumber($deformasi['ex'.$i]['deformasi_10']) : '-' ?></td>
                            <td class="number-cell"><?= isset($deformasi['ex'.$i]['deformasi_20']) ? formatNumber($deformasi['ex'.$i]['deformasi_20']) : '-' ?></td>
                            <td class="number-cell"><?= isset($deformasi['ex'.$i]['deformasi_30']) ? formatNumber($deformasi['ex'.$i]['deformasi_30']) : '-' ?></td>
                        <?php endfor; ?>
                        
                        <!-- INITIAL READINGS DATA (EX1-EX4) -->
                        <?php for($i = 1; $i <= 4; $i++): ?>
                            <td class="number-cell"><?= isset($readings['ex'.$i]['reading_10']) ? formatNumber($readings['ex'.$i]['reading_10']) : '-' ?></td>
                            <td class="number-cell"><?= isset($readings['ex'.$i]['reading_20']) ? formatNumber($readings['ex'.$i]['reading_20']) : '-' ?></td>
                            <td class="number-cell"><?= isset($readings['ex'.$i]['reading_30']) ? formatNumber($readings['ex'.$i]['reading_30']) : '-' ?></td>
                        <?php endfor; ?>
                        
                        <!-- Action Buttons -->
                        <td class="action-cell">
                            <div class="d-flex justify-content-center">
                                <a href="<?= base_url('extenso/edit/' . $p['id_pengukuran']) ?>" class="btn-action btn-edit" title="Edit Data">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button type="button" class="btn-action btn-delete delete-data" 
                                        data-id="<?= $p['id_pengukuran'] ?>" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
        this.disabled = true;

        setTimeout(() => {
            try {
                const table = document.getElementById('exportTable');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Data Extensometer"});
                
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const filename = `Extensometer_Data_Export_${timestamp}.xlsx`;
                
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

    // Delete Data
    let deleteId = null;
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

    // Filter Functionality
    const tahunFilter = document.getElementById('tahunFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const searchInput = document.getElementById('searchInput');
    const resetFilter = document.getElementById('resetFilter');

    function filterTable() {
        const tahunValue = tahunFilter.value.toLowerCase();
        const periodeValue = periodeFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();

        const rows = document.querySelectorAll('#dataTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.text-center')) return;
            
            const tahun = row.cells[0].textContent.toLowerCase();
            const periode = row.cells[1].textContent.toLowerCase();
            const rowText = row.textContent.toLowerCase();

            const tahunMatch = !tahunValue || tahun === tahunValue;
            const periodeMatch = !periodeValue || periode === periodeValue;
            const searchMatch = !searchValue || rowText.includes(searchValue);

            row.style.display = tahunMatch && periodeMatch && searchMatch ? '' : 'none';
        });
    }

    tahunFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
        periodeFilter.value = '';
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
});
</script>
</body>
</html>