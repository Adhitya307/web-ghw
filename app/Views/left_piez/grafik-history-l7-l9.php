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
        /* Styling untuk tabel baru */
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.75rem;
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
            min-width: 1200px;
        }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            max-height: 600px;
            overflow: auto;
        }
        
        /* Warna status */
        .status-aman { background-color: #d4edda !important; color: #155724 !important; }
        .status-peringatan { background-color: #fff3cd !important; color: #856404 !important; }
        .status-bahaya { background-color: #f8d7da !important; color: #721c24 !important; }
        
        /* Warna untuk kolom ambang batas */
        .aman-value { background-color: #d4edda !important; color: #155724 !important; font-weight: bold; }
        .peringatan-value { background-color: #fff3cd !important; color: #856404 !important; font-weight: bold; }
        .bahaya-value { background-color: #f8d7da !important; color: #721c24 !important; font-weight: bold; }
        
        /* Warna untuk header */
        .header-aman { background-color: #28a745 !important; color: white !important; }
        .header-peringatan { background-color: #ffc107 !important; color: black !important; }
        .header-bahaya { background-color: #dc3545 !important; color: white !important; }
        
        .number-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .text-cell {
            text-align: center;
        }
        
        .date-cell {
            text-align: center;
            font-size: 0.7rem;
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

        /* Styling untuk header ambang batas */
        .ambang-header {
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .ambang-aman { background-color: #d4edda !important; color: #155724 !important; }
        .ambang-peringatan { background-color: #fff3cd !important; color: #856404 !important; }
        .ambang-bahaya { background-color: #f8d7da !important; color: #721c24 !important; }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <!-- Table Header Section -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-tachometer-alt me-2"></i>Piezometer - Left Bank (L7-L9)
        </h2>

        <!-- Button Group -->
        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('left-piez') ?>" class="btn btn-primary btn-piez">
                <i class="fas fa-table"></i> Left Bank
            </a>
            <a href="<?= base_url('left_piez/grafik-history-l1-l3') ?>" class="btn btn-outline-primary btn-piez">Grafik History L1-L3</a>
            <a href="<?= base_url('left_piez/grafik-history-l4-l6') ?>" class="btn btn-outline-primary btn-piez">Grafik History L4-L6</a>
            <a href="<?= base_url('left_piez/grafik-history-l7-l9') ?>" class="btn btn-outline-primary btn-piez">Grafik History L7-L9</a>
            <a href="<?= base_url('piezometer/right') ?>" class="btn btn-outline-primary btn-piez">Right Bank</a>
            <a href="<?= base_url('left-piez/create') ?>" class="btn btn-outline-success">
                <i class="fas fa-plus me-1"></i> Add Data
            </a>
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
        <table class="data-table table table-bordered table-hover">
            <thead>
                <!-- Row 1: Main Headers -->
                <tr>
                    <th>Pisometer No.</th>
                    <th colspan="6">L-7</th>
                    <th colspan="6">L-8</th>
                    <th colspan="6">L-9</th>
                </tr>
                
                <!-- Row 2: Sub Headers -->
                <tr>
                    <th>Posisi dr.As Bend</th>
                    <th colspan="6">Upstream</th>
                    <th colspan="6">As Bend</th>
                    <th colspan="6">Downstream</th>
                </tr>
                
                <!-- Row 3: Data Headers -->
                <tr>
                    <th>Elev.Piso.Atas(El.m)</th>
                    <th colspan="2">580.26</th>
                    <th colspan="4" rowspan="4">
                        <div class="ambang-header">Ambang Batas</div>
                        <div class="mt-1 ambang-header">Aman (El.m)</div>
                        <div class="aman-value">644.06</div>
                        <div class="mt-1 ambang-header">Peringatan (El.m)</div>
                        <div class="peringatan-value">644.96</div>
                        <div class="mt-1 ambang-header">Bahaya (El.m)</div>
                        <div class="bahaya-value">647.96</div>
                    </th>
                    <th colspan="2">700.76</th>
                    <th colspan="4" rowspan="4">
                        <div class="ambang-header">Ambang Batas</div>
                        <div class="mt-1 ambang-header">Aman (El.m)</div>
                        <div class="aman-value">649.84</div>
                        <div class="mt-1 ambang-header">Peringatan (El.m)</div>
                        <div class="peringatan-value">650.74</div>
                        <div class="mt-1 ambang-header">Bahaya (El.m)</div>
                        <div class="bahaya-value">653.74</div>
                    </th>
                    <th colspan="2">690.09</th>
                    <th colspan="4" rowspan="4">
                        <div class="ambang-header">Ambang Batas</div>
                        <div class="mt-1 ambang-header">Aman (El.m)</div>
                        <div class="aman-value">594.41</div>
                        <div class="mt-1 ambang-header">Peringatan (El.m)</div>
                        <div class="peringatan-value">596.11</div>
                        <div class="mt-1 ambang-header">Bahaya (El.m)</div>
                        <div class="bahaya-value">594.41</div>
                    </th>
                </tr>

                <!-- Row 4: Kedalaman -->
                <tr>
                    <th>Kedalaman(m)</th>
                    <th colspan="2">50.00</th>
                    <th colspan="2">62.00</th>
                    <th colspan="2">62.00</th>
                </tr>
                
                <!-- Row 5: Koordinat X -->
                <tr>
                    <th>Koordinat X(m)</th>
                    <th colspan="2">6.116,59</th>
                    <th colspan="2">6.168,84</th>
                    <th colspan="2">6.106,56</th>
                </tr>
                
                <!-- Row 6: Koordinat Y -->
                <tr>
                    <th>Koordinat Y(m)</th>
                    <th colspan="2">(8.669,64)</th>
                    <th colspan="2">(9.057,75)</th>
                    <th colspan="2">(8.921,46)</th>
                </tr>
                
                <!-- Row 7: Final Headers -->
                <tr>
                    <th>Tanggal</th>
                    
                    <!-- L-7 Columns -->
                    <th>Bacaan(m)</th>
                    <th>T.Psmetrik(El.m)</th>
                    <th class="header-aman">Aman</th>
                    <th class="header-peringatan">Peringatan</th>
                    <th class="header-bahaya">Bahaya</th>
                    <th>T.Psmetrik(El.m)</th>
                    
                    <!-- L-8 Columns -->
                    <th>Bacaan(m)</th>
                    <th>T.Psmetrik(El.m)</th>
                    <th class="header-aman">Aman</th>
                    <th class="header-peringatan">Peringatan</th>
                    <th class="header-bahaya">Bahaya</th>
                    <th>T.Psmetrik(El.m)</th>
                    
                    <!-- L-9 Columns -->
                    <th>Bacaan(m)</th>
                    <th>T.Psmetrik(El.m)</th>
                    <th class="header-aman">Aman</th>
                    <th class="header-peringatan">Peringatan</th>
                    <th class="header-bahaya">Bahaya</th>
                    <th>T.Psmetrik(El.m)</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <?php if(empty($pengukuran)): ?>
                    <tr>
                        <td colspan="19" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data Piezometer yang tersedia</p>
                            <a href="<?= base_url('left-piez/create') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Data Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    // Fungsi untuk menentukan status berdasarkan nilai default untuk L7-L9
                    function getStatusL7L9($t_psmetrik, $type) {
                        switch($type) {
                            case 'L7':
                                if ($t_psmetrik <= 644.06) return 'aman';
                                if ($t_psmetrik <= 644.96) return 'peringatan';
                                return 'bahaya';
                            case 'L8':
                                if ($t_psmetrik <= 649.84) return 'aman';
                                if ($t_psmetrik <= 650.74) return 'peringatan';
                                return 'bahaya';
                            case 'L9':
                                if ($t_psmetrik <= 594.41) return 'aman';
                                if ($t_psmetrik <= 596.11) return 'peringatan';
                                return 'bahaya';
                            default:
                                return 'aman';
                        }
                    }
                    
                    // Fungsi untuk format tanggal
                    function formatTanggal($tanggal) {
                        if (empty($tanggal) || $tanggal === '0000-00-00') {
                            return '-';
                        }
                        return date('d-m-Y', strtotime($tanggal));
                    }
                    
                    foreach($pengukuran as $item): 
                        $p = $item['pengukuran'];
                        $pembacaan = $item['pembacaan'] ?? [];
                        $perhitunganL07 = $item['perhitungan_l07'] ?? [];
                        $perhitunganL08 = $item['perhitungan_l08'] ?? [];
                        $perhitunganL09 = $item['perhitungan_l09'] ?? [];
                        
                        // Ambil nilai T.Psmetrik untuk masing-masing titik dari tabel perhitungan
                        $t_psmetrik_L07 = $perhitunganL07['t_psmetrik_L07'] ?? 0;
                        $t_psmetrik_L08 = $perhitunganL08['t_psmetrik_L08'] ?? 0;
                        $t_psmetrik_L09 = $perhitunganL09['t_psmetrik_L09'] ?? 0;
                        
                        // Ambil bacaan (feet) dari tabel pembacaan untuk masing-masing titik
                        $bacaan_L07 = $pembacaan['L_07']['feet'] ?? 0;
                        $bacaan_L08 = $pembacaan['L_08']['feet'] ?? 0;
                        $bacaan_L09 = $pembacaan['L_09']['feet'] ?? 0;
                        
                        // Konversi feet ke meter (1 feet = 0.3048 meter)
                        $bacaan_L07_m = $bacaan_L07 * 0.3048;
                        $bacaan_L08_m = $bacaan_L08 * 0.3048;
                        $bacaan_L09_m = $bacaan_L09 * 0.3048;
                        
                        // Tentukan status berdasarkan nilai default untuk L7-L9
                        $status_L07 = getStatusL7L9($t_psmetrik_L07, 'L7');
                        $status_L08 = getStatusL7L9($t_psmetrik_L08, 'L8');
                        $status_L09 = getStatusL7L9($t_psmetrik_L09, 'L9');
                        
                        // Ambil tanggal dari data pengukuran
                        $tanggal = $p['tanggal'] ?? $p['created_at'] ?? $p['updated_at'] ?? '-';
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>">
                        <!-- Tanggal -->
                        <td class="date-cell"><?= formatTanggal($tanggal) ?></td>
                        
                        <!-- L-7 Data -->
                        <td class="number-cell"><?= number_format($bacaan_L07_m, 2) ?></td>
                        <td class="number-cell"><?= number_format($t_psmetrik_L07, 2) ?></td>
                        <!-- Kolom Aman, Peringatan, Bahaya untuk L-7 -->
                        <td class="number-cell aman-value">644.06</td>
                        <td class="number-cell peringatan-value">644.96</td>
                        <td class="number-cell bahaya-value">647.96</td>
                        <td class="number-cell <?= $status_L07 === 'aman' ? 'aman-value' : ($status_L07 === 'peringatan' ? 'peringatan-value' : 'bahaya-value') ?>">
                            <?= number_format($t_psmetrik_L07, 2) ?>
                        </td>
                        
                        <!-- L-8 Data -->
                        <td class="number-cell"><?= number_format($bacaan_L08_m, 2) ?></td>
                        <td class="number-cell"><?= number_format($t_psmetrik_L08, 2) ?></td>
                        <!-- Kolom Aman, Peringatan, Bahaya untuk L-8 -->
                        <td class="number-cell aman-value">649.84</td>
                        <td class="number-cell peringatan-value">650.74</td>
                        <td class="number-cell bahaya-value">653.74</td>
                        <td class="number-cell <?= $status_L08 === 'aman' ? 'aman-value' : ($status_L08 === 'peringatan' ? 'peringatan-value' : 'bahaya-value') ?>">
                            <?= number_format($t_psmetrik_L08, 2) ?>
                        </td>
                        
                        <!-- L-9 Data -->
                        <td class="number-cell"><?= number_format($bacaan_L09_m, 2) ?></td>
                        <td class="number-cell"><?= number_format($t_psmetrik_L09, 2) ?></td>
                        <!-- Kolom Aman, Peringatan, Bahaya untuk L-9 -->
                        <td class="number-cell aman-value">594.41</td>
                        <td class="number-cell peringatan-value">596.11</td>
                        <td class="number-cell bahaya-value">594.41</td>
                        <td class="number-cell <?= $status_L09 === 'aman' ? 'aman-value' : ($status_L09 === 'peringatan' ? 'peringatan-value' : 'bahaya-value') ?>">
                            <?= number_format($t_psmetrik_L09, 2) ?>
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

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Filter Functionality
    const tahunFilter = document.getElementById('tahunFilter');
    const searchInput = document.getElementById('searchInput');
    const resetFilter = document.getElementById('resetFilter');

    function filterTable() {
        const tahunValue = tahunFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();

        const rows = document.querySelectorAll('#dataTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.text-center')) return;
            
            const rowText = row.textContent.toLowerCase();
            const searchMatch = !searchValue || rowText.includes(searchValue);

            row.style.display = searchMatch ? '' : 'none';
        });
    }

    tahunFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
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