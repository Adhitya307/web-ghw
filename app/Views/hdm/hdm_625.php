<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HDM 625 - PT Indonesia Power</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    
    <style>
        :root {
            --color-aman: #d4edda;
            --color-peringatan: #fff3cd;
            --color-bahaya: #f8d7da;
            --color-primary: #0d6efd;
            --color-success: #198754;
            --color-warning: #ffc107;
            --color-danger: #dc3545;
        }
        
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            vertical-align: middle;
            text-align: center;
            font-size: 0.85rem;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
            padding: 0.5rem;
        }
        
        .bg-primary { background-color: var(--color-primary) !important; color: white; }
        .bg-success { background-color: var(--color-success) !important; color: white; }
        .bg-warning { background-color: var(--color-warning) !important; color: black; }
        .bg-danger { background-color: var(--color-danger) !important; color: white; }
        .bg-light { background-color: #f8f9fa !important; }
        .bg-info { background-color: #0dcaf0 !important; color: white; }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 700px;
            overflow: auto;
        }
        
        .filter-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .table-header {
            background: white;
            padding: 1rem;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }
        
        .status-aman { background-color: var(--color-aman) !important; }
        .status-peringatan { background-color: var(--color-peringatan) !important; }
        .status-bahaya { background-color: var(--color-bahaya) !important; }
        
        .sticky-header {
            position: sticky;
            left: 0;
            background: white;
            z-index: 5;
        }
        
        .sticky-header-2 {
            position: sticky;
            left: 80px;
            background: white;
            z-index: 5;
        }
        
        .sticky-header-3 {
            position: sticky;
            left: 160px;
            background: white;
            z-index: 5;
        }
        
        .sticky-header {
            width: 80px;
            min-width: 80px;
            max-width: 80px;
        }
        
        .sticky-header-2 {
            width: 80px;
            min-width: 80px;
            max-width: 80px;
        }
        
        .sticky-header-3 {
            width: 100px;
            min-width: 100px;
            max-width: 100px;
        }
        
        .table {
            border-collapse: collapse !important;
            border-spacing: 0 !important;
        }
        
        .table th, .table td {
            margin: 0;
            padding: 0.5rem;
            border: 1px solid #dee2e6;
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
        
        .legend {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .table-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .btn-group .btn {
            white-space: nowrap;
        }
        
        .reading-cell { 
            background-color: #e3f2fd !important;
            font-weight: 500;
        }
        
        .movement-cell { 
            background-color: #fff8e1 !important;
            font-weight: 500;
        }
        
        .threshold-cell { 
            background-color: #ffebee !important;
            font-weight: 500;
        }
        
        .header-large {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            padding: 0.8rem 0.5rem !important;
        }
        
        /* Style untuk rowspan tahun */
        .year-rowspan {
            background-color: #f8f9fa !important;
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
            <i class="fas fa-arrow-left-right me-2"></i>Horizontal Displacement Meter - ELV 625
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('horizontal-displacement') ?>" class="btn btn-outline-primary">
                <i class="fas fa-table"></i> Tabel Data HDM
            </a>
            
            <button type="button" class="btn btn-outline-success" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            
            <button type="button" class="btn btn-outline-info" id="syncPergerakanBtn">
                <i class="fas fa-sync-alt me-1"></i> Sync Pergerakan
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
        <div class="row g-3">
            <div class="col-md-4">
                <label for="tahunFilter" class="form-label">Tahun</label>
                <select id="tahunFilter" class="form-select">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($uniqueYears as $year): ?>
                        <option value="<?= $year ?>"><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="periodeFilter" class="form-label">Periode</label>
                <select id="periodeFilter" class="form-select">
                    <option value="">Semua Periode</option>
                    <?php foreach ($uniquePeriods as $period): ?>
                        <option value="<?= $period ?>"><?= $period ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button id="resetFilter" class="btn btn-secondary w-100">
                    <i class="fas fa-sync-alt me-1"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color status-aman"></div>
            <span>Aman</span>
        </div>
        <div class="legend-item">
            <div class="legend-color status-peringatan"></div>
            <span>Peringatan</span>
        </div>
        <div class="legend-item">
            <div class="legend-color status-bahaya"></div>
            <span>Bahaya</span>
        </div>
    </div>

    <!-- Main Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="exportTable">
            <thead>
                <tr>
                    <th colspan="3" class="bg-info header-large">Hor. Displ. Meter No.</th>
                    <th colspan="6" class="bg-info header-large">H.1</th>
                    <th colspan="6" class="bg-info header-large">H.2</th>
                    <th colspan="6" class="bg-info header-large">H.3</th>
                </tr>
                <tr>
                    <th colspan="3" class="bg-info header-large">Elevasi (EL. m)</th>
                    <th colspan="2" class="bg-info">625</th>
                    <th rowspan="3" colspan="4" class="bg-info">Ambang Batas</th>
                    <th colspan="2" class="bg-info">625</th>
                    <th rowspan="3" colspan="4" class="bg-info">Ambang Batas</th>
                    <th colspan="2" class="bg-info">625</th>
                    <th rowspan="3" colspan="4" class="bg-info">Ambang Batas</th>
                </tr>
                <tr>
                    <th colspan="3" class="bg-info header-large">Kedalaman (m)</th>
                    <th colspan="2" class="bg-info">20.00</th>
                    <th colspan="2" class="bg-info">40.00</th>
                    <th colspan="2" class="bg-info">50.00</th>
                </tr>
                <tr>
                    <th colspan="3" class="bg-info header-large">Bacaan Awal /Initial Reading (cm)</th>
                    <th colspan="2" class="bg-info">36.00</th>
                    <th colspan="2" class="bg-info">35.50</th>
                    <th colspan="2" class="bg-info">35.00</th>
                </tr>
                <tr>
                    <th rowspan="2" class="sticky-header bg-light">TAHUN</th>
                    <th rowspan="2" class="sticky-header-2 bg-light">PERIODE</th>
                    <th rowspan="2" class="sticky-header-3 bg-light">TANGGAL</th>
                    
                    <!-- HV1 Headers -->
                    <th colspan="1" class="bg-primary">PEMBACAAN (cm)</th>
                    <th colspan="1" class="bg-warning">PERGERAKAN (mm)</th>
                    <th colspan="1" class="bg-danger">Aman</th>
                    <th colspan="1" class="bg-danger">Peringatan</th>
                    <th colspan="1" class="bg-danger">Bahaya</th>
                    <th colspan="1" class="bg-danger">Pergerakan</th>

                    <!-- HV2 Headers -->
                    <th colspan="1" class="bg-primary">PEMBACAAN (cm)</th>
                    <th colspan="1" class="bg-warning">PERGERAKAN (mm)</th>
                    <th colspan="1" class="bg-danger">Aman</th>
                    <th colspan="1" class="bg-danger">Peringatan</th>
                    <th colspan="1" class="bg-danger">Bahaya</th>
                    <th colspan="1" class="bg-danger">Pergerakan</th>
                    
                    <!-- HV3 Headers -->
                    <th colspan="1" class="bg-primary">PEMBACAAN (cm)</th>
                    <th colspan="1" class="bg-warning">PERGERAKAN (mm)</th>
                    <th colspan="1" class="bg-danger">Aman</th>
                    <th colspan="1" class="bg-danger">Peringatan</th>
                    <th colspan="1" class="bg-danger">Bahaya</th>
                    <th colspan="1" class="bg-danger">Pergerakan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Helper functions untuk menentukan status berdasarkan kedalaman
                function getStatusClass625($pergerakanValue, $depth) {
                    if ($pergerakanValue === null || $pergerakanValue === '' || $pergerakanValue === '-') return '';
                    
                    $pergerakanValue = floatval($pergerakanValue);
                    
                    switch($depth) {
                        case 'H1':
                            if ($pergerakanValue >= -18.77) return 'status-aman';
                            if ($pergerakanValue <= -25.60) return 'status-bahaya';
                            return 'status-peringatan';
                        case 'H2':
                            if ($pergerakanValue >= -9.02) return 'status-aman';
                            if ($pergerakanValue <= -12.30) return 'status-bahaya';
                            return 'status-peringatan';
                        case 'H3':
                            if ($pergerakanValue >= -5.94) return 'status-aman';
                            if ($pergerakanValue <= -8.10) return 'status-bahaya';
                            return 'status-peringatan';
                        default:
                            return '';
                    }
                }

                function getAmbangBatas625($depth) {
                    switch($depth) {
                        case 'H1':
                            return ['aman' => -18.77, 'peringatan' => -21.66, 'bahaya' => -25.60];
                        case 'H2':
                            return ['aman' => -9.02, 'peringatan' => -10.41, 'bahaya' => -12.30];
                        case 'H3':
                            return ['aman' => -5.94, 'peringatan' => -6.85, 'bahaya' => -8.10];
                        default:
                            return ['aman' => 0, 'peringatan' => 0, 'bahaya' => 0];
                    }
                }

                // Helper function khusus untuk format pergerakan dengan 2 desimal
                function formatPergerakan625($value) {
                    if ($value === null || $value === '' || $value === '-') return '-';
                    $floatVal = floatval($value);
                    return number_format($floatVal, 2, '.', '');
                }

                // Group data by tahun untuk rowspan
                $groupedData = [];
                if (!empty($data)) {
                    foreach ($data as $item) {
                        $tahun = $item['pengukuran']['tahun'];
                        if (!isset($groupedData[$tahun])) {
                            $groupedData[$tahun] = [];
                        }
                        $groupedData[$tahun][] = $item;
                    }
                }
                ?>

                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="24" class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i><br>
                            <span class="text-muted">Tidak ada data HDM 625 yang ditemukan</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($groupedData as $tahun => $itemsInYear): ?>
                        <?php $rowCount = count($itemsInYear); ?>
                        <?php foreach ($itemsInYear as $index => $item): 
                            $pengukuran = $item['pengukuran'];
                            $pembacaan = $item['pembacaan_elv625'] ?? [];
                            $initial = $item['initial_reading_elv625'] ?? [];
                            $pergerakan = $item['pergerakan_elv625'] ?? [];
                            $depth = $item['depth_elv625'] ?? [];
                            $ambangBatas = $item['ambang_batas'] ?? [];
                            
                            // Ambang batas dari database jika ada, jika tidak gunakan default
                            $ambangH1 = $ambangBatas['h1'] ?? getAmbangBatas625('H1');
                            $ambangH2 = $ambangBatas['h2'] ?? getAmbangBatas625('H2');
                            $ambangH3 = $ambangBatas['h3'] ?? getAmbangBatas625('H3');
                            
                            // Pergerakan asli (belum dikali 10) untuk kolom pergerakan biasa
                            $pergerakan_hv1 = $pergerakan['hv_1'] ?? 0;
                            $pergerakan_hv2 = $pergerakan['hv_2'] ?? 0;
                            $pergerakan_hv3 = $pergerakan['hv_3'] ?? 0;
                            
                            // Pergerakan untuk ambang batas (sudah dikali 10)
                            $pergerakan_ambang_hv1 = $ambangH1['pergerakan'] ?? ($pergerakan_hv1 * 10);
                            $pergerakan_ambang_hv2 = $ambangH2['pergerakan'] ?? ($pergerakan_hv2 * 10);
                            $pergerakan_ambang_hv3 = $ambangH3['pergerakan'] ?? ($pergerakan_hv3 * 10);
                        ?>
                        <tr data-tahun="<?= $pengukuran['tahun'] ?>" 
                            data-periode="<?= $pengukuran['periode'] ?>"
                            data-id="<?= $pengukuran['id_pengukuran'] ?>">
                            
                            <!-- Basic Info Columns -->
                            <?php if ($index === 0): ?>
                                <td rowspan="<?= $rowCount ?>" class="sticky-header year-rowspan">
                                    <?= $pengukuran['tahun'] ?>
                                </td>
                            <?php endif; ?>
                            
                            <td class="sticky-header-2 bg-light"><?= $pengukuran['periode'] ?></td>
                            <td class="sticky-header-3 bg-light"><?= date('d-m-Y', strtotime($pengukuran['tanggal'])) ?></td>
                            
                            <!-- HV 1 Data (Kedalaman 20.00m) -->
                            <td class="reading-cell"><?= $pembacaan['hv_1'] ?? '-' ?></td>
                            <td class="movement-cell"><?= formatPergerakan625($pergerakan_ambang_hv1) ?></td>
                            <td class="threshold-cell"><?= $ambangH1['aman'] ?? $ambangH1['aman'] ?></td>
                            <td class="threshold-cell"><?= $ambangH1['peringatan'] ?? $ambangH1['peringatan'] ?></td>
                            <td class="threshold-cell"><?= $ambangH1['bahaya'] ?? $ambangH1['bahaya'] ?></td>
                            <td class="<?= getStatusClass625($pergerakan_ambang_hv1, 'H1') ?>">
                                <?= formatPergerakan625($pergerakan_ambang_hv1) ?>
                            </td>

                            <!-- HV 2 Data (Kedalaman 40.00m) -->
                            <td class="reading-cell"><?= $pembacaan['hv_2'] ?? '-' ?></td>
                            <td class="movement-cell"><?= formatPergerakan625($pergerakan_ambang_hv2) ?></td>
                            <td class="threshold-cell"><?= $ambangH2['aman'] ?? $ambangH2['aman'] ?></td>
                            <td class="threshold-cell"><?= $ambangH2['peringatan'] ?? $ambangH2['peringatan'] ?></td>
                            <td class="threshold-cell"><?= $ambangH2['bahaya'] ?? $ambangH2['bahaya'] ?></td>
                            <td class="<?= getStatusClass625($pergerakan_ambang_hv2, 'H2') ?>">
                                <?= formatPergerakan625($pergerakan_ambang_hv2) ?>
                            </td>

                            <!-- HV 3 Data (Kedalaman 50.00m) -->
                            <td class="reading-cell"><?= $pembacaan['hv_3'] ?? '-' ?></td>
                            <td class="movement-cell"><?= formatPergerakan625($pergerakan_ambang_hv3) ?></td>
                            <td class="threshold-cell"><?= $ambangH3['aman'] ?? $ambangH3['aman'] ?></td>
                            <td class="threshold-cell"><?= $ambangH3['peringatan'] ?? $ambangH3['peringatan'] ?></td>
                            <td class="threshold-cell"><?= $ambangH3['bahaya'] ?? $ambangH3['bahaya'] ?></td>
                            <td class="<?= getStatusClass625($pergerakan_ambang_hv3, 'H3') ?>">
                                <?= formatPergerakan625($pergerakan_ambang_hv3) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Scroll Indicator -->
    <div class="scroll-indicator" id="scrollIndicator">
        <i class="fas fa-arrows-left-right me-1"></i>
        <span>Geser untuk melihat lebih banyak kolom</span>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
// Document Ready
$(document).ready(function() {
    // Search Functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Filter Functionality
    function applyFilters() {
        const tahun = $('#tahunFilter').val().toLowerCase();
        const periode = $('#periodeFilter').val().toLowerCase();

        $('tbody tr').each(function() {
            const rowTahun = $(this).data('tahun').toString().toLowerCase();
            const rowPeriode = $(this).data('periode').toString().toLowerCase();

            const showRow = 
                (tahun === '' || rowTahun === tahun) &&
                (periode === '' || rowPeriode === periode);

            $(this).toggle(showRow);
        });
    }

    $('#tahunFilter, #periodeFilter').on('change', applyFilters);

    // Reset Filter
    $('#resetFilter').on('click', function() {
        $('#tahunFilter, #periodeFilter').val('');
        $('#searchInput').val('');
        applyFilters();
    });

    // Scroll Indicator
    const tableContainer = $('.table-responsive');
    const scrollIndicator = $('#scrollIndicator');

    tableContainer.on('scroll', function() {
        const scrollLeft = $(this).scrollLeft();
        const maxScroll = $(this)[0].scrollWidth - $(this).width();
        
        if (scrollLeft > 50 && scrollLeft < maxScroll - 50) {
            scrollIndicator.fadeIn();
        } else {
            scrollIndicator.fadeOut();
        }
    });

    // Export to Excel
    $('#exportExcel').on('click', function() {
        exportToExcel();
    });

    // Sync Pergerakan
    $('#syncPergerakanBtn').on('click', function() {
        syncPergerakanAmbangBatas();
    });

    function exportToExcel() {
        try {
            const originalText = $('#exportExcel').html();
            $('#exportExcel').html('<i class="fas fa-spinner fa-spin me-1"></i>Exporting...');
            $('#exportExcel').prop('disabled', true);

            $.ajax({
                url: '<?= base_url("hdm/hdm625/exportExcel") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const wb = XLSX.utils.book_new();
                        const ws = XLSX.utils.json_to_sheet(response.data);
                        XLSX.utils.book_append_sheet(wb, ws, 'HDM_625_Data');
                        const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
                        saveAs(new Blob([wbout], { type: 'application/octet-stream' }), 'HDM_625_Data.xlsx');
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error exporting data: ' + error);
                },
                complete: function() {
                    $('#exportExcel').html(originalText);
                    $('#exportExcel').prop('disabled', false);
                }
            });
        } catch (error) {
            alert('Error: ' + error.message);
            $('#exportExcel').html(originalText);
            $('#exportExcel').prop('disabled', false);
        }
    }

    function syncPergerakanAmbangBatas() {
        const originalText = $('#syncPergerakanBtn').html();
        $('#syncPergerakanBtn').html('<i class="fas fa-spinner fa-spin me-1"></i>Syncing...');
        $('#syncPergerakanBtn').prop('disabled', true);

        $.ajax({
            url: '<?= base_url("hdm/hdm625/syncPergerakanAmbangBatas") ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Sync pergerakan ambang batas berhasil!');
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error syncing data: ' + error);
            },
            complete: function() {
                $('#syncPergerakanBtn').html(originalText);
                $('#syncPergerakanBtn').prop('disabled', false);
            }
        });
    }

    // Initialize
    applyFilters();
});
</script>

</body>
</html>