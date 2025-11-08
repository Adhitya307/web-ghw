<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bubble Tilt Meter BT-4 - PT Indonesia Power' ?></title>

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
            min-width: 1400px;
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
        .bg-calculation { background-color: #f0f9eb !important; color: #2c3e50 !important; }
        .bg-result { background-color: #e6f7ff !important; color: #2c3e50 !important; }
        .bg-action { background-color: #f8f9fa !important; color: #2c3e50 !important; }
        .bg-scatter { background-color: #fff2cc !important; color: #2c3e50 !important; }
        
        .btn-bt {
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
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-mountain me-2"></i>Bubble Tilt Meter (BTM) - BT-4
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('btm') ?>" class="btn btn-outline-primary btn-bt">BT-1</a>
            <a href="<?= base_url('btm/bt2') ?>" class="btn btn-outline-primary btn-bt">BT-2</a>
            <a href="<?= base_url('btm/bt3') ?>" class="btn btn-outline-primary btn-bt">BT-3</a>
            <a href="<?= base_url('btm/bt4') ?>" class="btn btn-primary btn-bt">
                <i class="fas fa-table"></i> BT-4
             </a>
            <a href="<?= base_url('btm/bt6') ?>" class="btn btn-outline-primary btn-bt">BT-6</a>
            <a href="<?= base_url('btm/bt7') ?>" class="btn btn-outline-primary btn-bt">BT-7</a>
            <a href="<?= base_url('btm/bt8') ?>" class="btn btn-outline-primary btn-bt">BT-8</a>
            <a href="<?= base_url('btm/create') ?>" class="btn btn-outline-success">
                <i class="fas fa-plus me-1"></i> Add Data
            </a>
    
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
                    <th rowspan="4" class="sticky">TAHUN</th>
                    <th rowspan="4" class="sticky-2">PERIODE</th>
                    <th rowspan="4" class="sticky-3">TANGGAL</th>
                    
                    <!-- BACAAN BT4 -->
                    <th colspan="4" class="bg-reading">BACAAN BT-4</th>
                    
                    <!-- PERHITUNGAN BT4 -->
                    <th colspan="2" class="bg-calculation">UTARA-SELATAN</th>
                    <th colspan="2" class="bg-calculation">TIMUR-BARAT</th>
                    <th colspan="2" rowspan="3" class="bg-result">INCLINED ANGLE-C</th>
                    <th colspan="3" rowspan="3" class="bg-result">DIPPED DIRECTION OF-C</th>
                    <th colspan="4" rowspan="3" class="bg-scatter">SCATTER</th>
                    
                    <th rowspan="4" class="action-cell bg-action">AKSI</th>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <!-- BACAAN BT4 Sub Headers -->
                    <th colspan="2" class="bg-reading">UTARA-SELATAN</th>
                    <th colspan="2" class="bg-reading">TIMUR-BARAT</th>
                    <!-- PERHITUNGAN BT4 Sub Headers -->
                    <th rowspan="2" colspan="2" class="bg-calculation">INCLINED ANGLE-A</th>
                    <th rowspan="2" colspan="2" class="bg-calculation">INCLINED ANGLE-B</th>
                </tr>
                
                <!-- Row 3: Measurement Headers -->
                <tr>
                    <th colspan="2" class="bg-reading">GP & ARAH</th>
                    <th colspan="2" class="bg-reading">GP & ARAH</th>
                </tr>

                <!-- Row 4: Column Headers -->
                <tr>
                    <!-- BACAAN BT4 Headers -->
                    <th class="bg-reading">US GP</th>
                    <th class="bg-reading">US Arah</th>
                    <th class="bg-reading">TB GP</th>
                    <th class="bg-reading">TB Arah</th>

                    <!-- PERHITUNGAN BT4 Headers -->
                    <th class="bg-calculation">A_sec</th>
                    <th class="bg-calculation">sin_A_rad</th>
                    <th class="bg-calculation">B_sec</th>
                    <th class="bg-calculation">sin_B_rad</th>
                    <th class="bg-result">sin_C_rad</th>
                    <th class="bg-result">sin_C_deg</th>
                    <th class="bg-result">Cos α</th>
                    <th class="bg-result">α (Rad)</th>
                    <th class="bg-result">DMS</th>
                    
                    <!-- SCATTER Headers -->
                    <th class="bg-scatter">Y (U-S)</th>
                    <th class="bg-scatter">X (T-B)</th>
                    <th class="bg-scatter">Y (cum)</th>
                    <th class="bg-scatter">X (cum)</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <?php if(empty($pengukuran)): ?>
                    <tr>
                        <td colspan="21" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data BTM yang tersedia</p>
                            <a href="<?= base_url('btm/create') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($pengukuran as $item): 
                        $p = $item['pengukuran'];
                        $bacaan = $item['bacaan'];
                        $perhitungan = $item['perhitungan'];
                        $scatter = $item['scatter'] ?? [];
                        
                        // Ambil data untuk BT4
                        $Y_US = $scatter['bt4']['Y_US'] ?? null;
                        $X_TB = $scatter['bt4']['X_TB'] ?? null;
                        $Y_cum = $scatter['bt4']['Y_cum'] ?? null;
                        $X_cum = $scatter['bt4']['X_cum'] ?? null;
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>">
                        <!-- Basic Info -->
                        <td class="sticky"><?= esc($p['tahun'] ?? '-') ?></td>
                        <td class="sticky-2"><?= esc($p['periode'] ?? '-') ?></td>
                        <td class="sticky-3"><?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?></td>
                        
                        <!-- BACAAN DATA BT4 -->
                        <?php 
                            $bt4Bacaan = $bacaan['bt4'] ?? [];
                        ?>
                        <td class="number-cell"><?= isset($bt4Bacaan['US_GP']) ? number_format($bt4Bacaan['US_GP'], 2) : '-' ?></td>
                        <td><?= esc($bt4Bacaan['US_Arah'] ?? '-') ?></td>
                        <td class="number-cell"><?= isset($bt4Bacaan['TB_GP']) ? number_format($bt4Bacaan['TB_GP'], 2) : '-' ?></td>
                        <td><?= esc($bt4Bacaan['TB_Arah'] ?? '-') ?></td>

                        <!-- PERHITUNGAN DATA BT4 -->
                        <?php 
                            $bt4Perhitungan = $perhitungan['bt4'] ?? [];
                        ?>
                        <td class="number-cell"><?= isset($bt4Perhitungan['A_sec']) ? number_format($bt4Perhitungan['A_sec'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['sin_A_rad']) ? number_format($bt4Perhitungan['sin_A_rad'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['B_sec']) ? number_format($bt4Perhitungan['B_sec'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['sin_B_rad']) ? number_format($bt4Perhitungan['sin_B_rad'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['sin_C_rad']) ? number_format($bt4Perhitungan['sin_C_rad'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['sin_C_deg']) ? number_format($bt4Perhitungan['sin_C_deg'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['Cosa']) ? number_format($bt4Perhitungan['Cosa'], 6) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['a_rad']) ? number_format($bt4Perhitungan['a_rad'], 6) : '-' ?></td>
                        <td><?= esc($bt4Perhitungan['DMS'] ?? '-') ?></td>
                        
                        <!-- SCATTER DATA -->
                        <td class="number-cell bg-scatter"><?= $Y_US !== null ? number_format($Y_US, 6) : '-' ?></td>
                        <td class="number-cell bg-scatter"><?= $X_TB !== null ? number_format($X_TB, 6) : '-' ?></td>
                        <td class="number-cell bg-scatter"><?= $Y_cum !== null ? number_format($Y_cum, 6) : '-' ?></td>
                        <td class="number-cell bg-scatter"><?= $X_cum !== null ? number_format($X_cum, 6) : '-' ?></td>
                        
                        <!-- Action Buttons -->
                        <td class="action-cell">
                            <div class="d-flex justify-content-center">
                                <a href="<?= base_url('btm/edit/' . $p['id_pengukuran']) ?>" class="btn-action btn-edit" title="Edit Data">
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
                <p>Apakah Anda yakin ingin menghapus data BTM ini?</p>
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
                const wb = XLSX.utils.table_to_book(table, {sheet: "Data BT4"});
                
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const filename = `BT4_Data_Export_${timestamp}.xlsx`;
                
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

    // Calculate All
    document.getElementById('calculateAll').addEventListener('click', function() {
        if (confirm('Hitung ulang semua data BT4? Proses ini mungkin memakan waktu.')) {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghitung...';
            btn.disabled = true;

            fetch('<?= base_url('btm/calculate-all') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Perhitungan BT4 berhasil!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghitung data');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
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
            fetch('<?= base_url('btm/delete') ?>/' + deleteId, {
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