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
        
        .scientific-notation {
            font-size: 0.7rem;
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
            <a href="<?= base_url('btm/bt1') ?>" class="btn btn-outline-primary btn-bt">BT-1</a>
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
                    <?php 
                    // Fungsi untuk memformat angka sesuai permintaan - VERSI FINAL DIPERBAIKI
                    function formatNumber($number) {
                        if ($number === null || $number === '') {
                            return '-';
                        }
                        
                        // Jika angka sangat kecil, gunakan notasi E dengan presisi tinggi seperti Excel
                        if (abs($number) < 0.0001 && $number != 0) {
                            return '<span class="scientific-notation">' . sprintf('%.8E', $number) . '</span>';
                        }
                        
                        // Format angka dengan 9 digit di belakang koma
                        $formatted = number_format($number, 9, '.', '');
                        
                        // Hapus trailing zeros dan titik desimal yang tidak perlu
                        $formatted = preg_replace('/\.?0+$/', '', $formatted);
                        
                        return $formatted;
                    }

                    // FUNGSI PERHITUNGAN YANG DIPERBAIKI - TANPA PEMBULATAN
                    function calculateSinRad($seconds) {
                        if ($seconds === null || $seconds === '') {
                            return null;
                        }
                        
                        // Konversi detik ke radian: (detik * π) / (180 * 3600)
                        $radians = ($seconds * M_PI) / (180 * 3600);
                        
                        // Hitung sin tanpa pembulatan
                        $sinValue = sin($radians);
                        
                        return $sinValue;
                    }

                    // FUNGSI PERHITUNGAN sin_C_rad YANG DIPERBAIKI
                    function calculateSinCRad($sinA, $sinB) {
                        if ($sinA === null || $sinB === null) {
                            return null;
                        }
                        
                        // Rumus: sin_C_rad = sqrt(sin_A_rad^2 + sin_B_rad^2)
                        $sinC = sqrt(pow($sinA, 2) + pow($sinB, 2));
                        
                        return $sinC;
                    }

                    // FUNGSI KONVERSI RADIAN KE DERAJAT
                    function radToDeg($radians) {
                        if ($radians === null) {
                            return null;
                        }
                        
                        // Konversi radian ke derajat: rad * (180 / π)
                        $degrees = $radians * (180 / M_PI);
                        
                        return $degrees;
                    }
                    ?>
                    
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

                        // PERHITUNGAN REAL-TIME YANG DIPERBAIKI
                        $bt4Bacaan = $bacaan['bt4'] ?? [];
                        $bt4Perhitungan = $perhitungan['bt4'] ?? [];
                        
                        // Hitung ulang sin_A_rad dan sin_B_rad dengan presisi tinggi
                        $A_sec = $bt4Perhitungan['A_sec'] ?? null;
                        $B_sec = $bt4Perhitungan['B_sec'] ?? null;
                        
                        $sin_A_rad_calculated = calculateSinRad($A_sec);
                        $sin_B_rad_calculated = calculateSinRad($B_sec);
                        
                        // Hitung ulang sin_C_rad dengan presisi tinggi
                        $sin_C_rad_calculated = calculateSinCRad($sin_A_rad_calculated, $sin_B_rad_calculated);
                        
                        // Hitung ulang sin_C_deg dengan presisi tinggi
                        $sin_C_deg_calculated = $sin_C_rad_calculated !== null ? radToDeg($sin_C_rad_calculated) : null;

                        // Gunakan nilai yang sudah dihitung jika tersedia, jika tidak gunakan dari database
                        $sin_A_rad_display = $sin_A_rad_calculated ?? $bt4Perhitungan['sin_A_rad'] ?? null;
                        $sin_B_rad_display = $sin_B_rad_calculated ?? $bt4Perhitungan['sin_B_rad'] ?? null;
                        $sin_C_rad_display = $sin_C_rad_calculated ?? $bt4Perhitungan['sin_C_rad'] ?? null;
                        $sin_C_deg_display = $sin_C_deg_calculated ?? $bt4Perhitungan['sin_C_deg'] ?? null;
                    ?>
                    <tr data-pid="<?= $p['id_pengukuran'] ?>">
                        <!-- Basic Info -->
                        <td class="sticky"><?= esc($p['tahun'] ?? '-') ?></td>
                        <td class="sticky-2"><?= esc($p['periode'] ?? '-') ?></td>
                        <td class="sticky-3"><?= $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-' ?></td>
                        
                        <!-- BACAAN DATA BT4 -->
                        <td class="number-cell"><?= isset($bt4Bacaan['US_GP']) ? formatNumber($bt4Bacaan['US_GP']) : '-' ?></td>
                        <td><?= esc($bt4Bacaan['US_Arah'] ?? '-') ?></td>
                        <td class="number-cell"><?= isset($bt4Bacaan['TB_GP']) ? formatNumber($bt4Bacaan['TB_GP']) : '-' ?></td>
                        <td><?= esc($bt4Bacaan['TB_Arah'] ?? '-') ?></td>

                        <!-- PERHITUNGAN DATA BT4 -->
                        <td class="number-cell"><?= isset($A_sec) ? formatNumber($A_sec) : '-' ?></td>
                        <td class="number-cell"><?= $sin_A_rad_display !== null ? formatNumber($sin_A_rad_display) : '-' ?></td>
                        <td class="number-cell"><?= isset($B_sec) ? formatNumber($B_sec) : '-' ?></td>
                        <td class="number-cell"><?= $sin_B_rad_display !== null ? formatNumber($sin_B_rad_display) : '-' ?></td>
                        <td class="number-cell"><?= $sin_C_rad_display !== null ? formatNumber($sin_C_rad_display) : '-' ?></td>
                        <td class="number-cell"><?= $sin_C_deg_display !== null ? formatNumber($sin_C_deg_display) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['Cosa']) ? formatNumber($bt4Perhitungan['Cosa']) : '-' ?></td>
                        <td class="number-cell"><?= isset($bt4Perhitungan['a_rad']) ? formatNumber($bt4Perhitungan['a_rad']) : '-' ?></td>
                        <td><?= esc($bt4Perhitungan['DMS'] ?? '-') ?></td>
                        
                        <!-- SCATTER DATA -->
                        <td class="number-cell bg-scatter"><?= $Y_US !== null ? formatNumber($Y_US) : '-' ?></td>
                        <td class="number-cell bg-scatter"><?= $X_TB !== null ? formatNumber($X_TB) : '-' ?></td>
                        <td class="number-cell bg-scatter"><?= $Y_cum !== null ? formatNumber($Y_cum) : '-' ?></td>
                        <td class="number-cell bg-scatter"><?= $X_cum !== null ? formatNumber($X_cum) : '-' ?></td>
                        
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

<!-- Modal Import SQL -->
<div class="modal fade" id="importSqlModal" tabindex="-1" aria-labelledby="importSqlModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSqlModalLabel">
                    <i class="fas fa-database me-2"></i>Import SQL dari Android
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Upload file SQL yang telah diexport dari aplikasi Android.
                </div>
                
                <div class="mb-3">
                    <label for="sqlFile" class="form-label">Pilih File SQL</label>
                    <input class="form-control" type="file" id="sqlFile" accept=".sql">
                    <div class="form-text">
                        Format file: .sql (Maksimal 50MB)
                    </div>
                </div>
                
                <div class="progress mb-3" style="display: none;" id="importProgress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                
                <div id="importStatus" class="alert" style="display: none;"></div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Data yang Akan Diimpor</h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">✅ Tabel BTM yang didukung:</p>
                        <div class="row">
                            <div class="col-6">
                                <ul class="mb-1">
                                    <li>Data Pengukuran</li>
                                    <li>BT-1, BT-2, BT-3</li>
                                    <li>BT-4, BT-6, BT-7</li>
                                    <li>BT-8</li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="mb-1">
                                    <li>Data Bacaan</li>
                                    <li>Data Perhitungan</li>
                                    <li>Data Scatter</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mb-0 text-warning">
                            <i class="fas fa-exclamation-triangle"></i> Data BT-5 akan diabaikan
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnImportSQL">
                    <i class="fas fa-upload me-1"></i> Import SQL
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

    // ============ IMPORT SQL FUNCTIONALITY - DIPERBAIKI ============
    
    // Event listener untuk tombol Import SQL di modal
    document.getElementById('btnImportSQL').addEventListener('click', function() {
        console.log('[BTM IMPORT] Tombol Import diklik.');

        const sqlFileInput = document.getElementById('sqlFile');
        const importProgress = document.getElementById('importProgress');
        const importStatus = document.getElementById('importStatus');
        const btnImport = this;

        importStatus.style.display = 'none';

        // Validasi file - DIPERBAIKI
        if (!sqlFileInput.files || sqlFileInput.files.length === 0) {
            showImportStatus('❌ Pilih file SQL terlebih dahulu', 'danger');
            return;
        }

        const file = sqlFileInput.files[0];
        console.log('[BTM IMPORT] File terpilih:', file.name, '-', (file.size / 1024).toFixed(2), 'KB');

        // VALIDASI EKSTENSI YANG LEBIH ROBUST - DIPERBAIKI
        const fileName = file.name.toLowerCase();
        const validExtensions = ['.sql'];
        const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
        
        if (!hasValidExtension) {
            showImportStatus('❌ File harus berformat .sql. File yang dipilih: ' + file.name, 'danger');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            showImportStatus('❌ Ukuran file maksimal 50MB. File saat ini: ' + (file.size / (1024*1024)).toFixed(2) + 'MB', 'danger');
            return;
        }

        if (file.size === 0) {
            showImportStatus('❌ File kosong', 'danger');
            return;
        }

        // Progress Bar
        importProgress.style.display = 'block';
        const progressBar = importProgress.querySelector('.progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        btnImport.disabled = true;
        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

        const formData = new FormData();
        formData.append('sql_file', file);

        // Simulasi progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 2;
            if (progress <= 80) {
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
            }
        }, 100);

        // Fetch API - DIPERBAIKI dengan error handling yang lebih baik
        fetch('<?= base_url('btm/import-sql') ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            
            // Handle non-JSON responses
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response bukan JSON. Status: ' + response.status);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('[BTM IMPORT] Response JSON:', data);

            if (data.success) {
                showImportStatus('✅ ' + data.message, 'success');

                if (data.stats) {
                    const s = data.stats;
                    let detailHtml = `
                        <div class="mt-3 p-2 bg-light rounded">
                            <h6 class="mb-2">📊 Statistik Import:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Total Query: <strong>${s.total}</strong></small><br>
                                    <small>Berhasil: <strong class="text-success">${s.success}</strong></small><br>
                                    <small>Gagal: <strong class="text-danger">${s.failed}</strong></small>
                                </div>
                                <div class="col-md-6">
                                    <small>Affected Rows: <strong>${s.affected_rows || 0}</strong></small>
                                    ${s.skipped_bt5 ? `<br><small>Skip BT-5: <strong>${s.skipped_bt5}</strong></small>` : ''}
                                </div>
                            </div>
                    `;
                    
                    // Show table statistics
                    if (s.tables && Object.keys(s.tables).length > 0) {
                        detailHtml += `<div class="mt-2"><h6 class="mb-1">📋 Tabel yang Diimpor:</h6>`;
                        detailHtml += `<div class="small">`;
                        Object.entries(s.tables).forEach(([table, count]) => {
                            detailHtml += `<div>${table}: ${count} records</div>`;
                        });
                        detailHtml += `</div></div>`;
                    }
                    
                    detailHtml += `</div>`;
                    importStatus.innerHTML += detailHtml;
                }

                // Auto-refresh setelah sukses
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importSqlModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                }, 3000);

            } else {
                // TAMPILKAN ERROR DARI SERVER DENGAN LEBIH DETAIL
                let errorMessage = data.message || 'Terjadi kesalahan tidak diketahui';
                
                // Jika ada error display dari server, tambahkan
                if (data.error_display) {
                    errorMessage += '<br><br><strong>Detail Error:</strong><br>' + 
                                   data.error_display.replace(/\n/g, '<br>');
                }
                
                showImportStatus('❌ ' + errorMessage, 'danger');
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('[BTM IMPORT ERROR]', error);
            
            let errorMessage = 'Terjadi kesalahan: ' + error.message;
            
            // Handle network errors specifically
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                errorMessage = '❌ Koneksi jaringan terganggu. Periksa koneksi internet Anda.';
            }
            
            showImportStatus(errorMessage, 'danger');
        })
        .finally(() => {
            setTimeout(() => {
                btnImport.disabled = false;
                btnImport.innerHTML = '<i class="fas fa-upload me-1"></i> Import';
            }, 2000);
        });

        function showImportStatus(message, type) {
            importStatus.style.display = 'block';
            importStatus.className = `alert alert-${type} alert-dismissible fade show`;
            importStatus.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    });

    // Reset form ketika modal import ditutup
    document.getElementById('importSqlModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('sqlFile').value = '';
        document.getElementById('importProgress').style.display = 'none';
        document.getElementById('importStatus').style.display = 'none';
        document.getElementById('importProgress').querySelector('.progress-bar').style.width = '0%';
    });

    // Validasi file ketika dipilih - DIPERBAIKI
    document.getElementById('sqlFile').addEventListener('change', function(e) {
        const file = this.files[0];
        const importStatus = document.getElementById('importStatus');
        const btnImport = document.getElementById('btnImportSQL');
        
        if (file) {
            const fileName = file.name.toLowerCase();
            const validExtensions = ['.sql'];
            const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
            
            if (!hasValidExtension) {
                importStatus.style.display = 'block';
                importStatus.className = 'alert alert-warning';
                importStatus.innerHTML = '⚠️ File harus berekstensi .sql. File yang dipilih: ' + file.name;
                this.value = '';
                btnImport.disabled = true;
            } else {
                importStatus.style.display = 'none';
                btnImport.disabled = false;
                
                // Tampilkan info file yang valid
                importStatus.style.display = 'block';
                importStatus.className = 'alert alert-info';
                importStatus.innerHTML = `
                    ✅ File valid: ${file.name} (${(file.size / 1024).toFixed(2)} KB)
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
            }
        } else {
            btnImport.disabled = true;
        }
    });
});

// Function untuk menampilkan modal import
function showImportModal() {
    const modal = new bootstrap.Modal(document.getElementById('importSqlModal'));
    
    // Reset form ketika modal dibuka
    document.getElementById('sqlFile').value = '';
    document.getElementById('importProgress').style.display = 'none';
    document.getElementById('importStatus').style.display = 'none';
    document.getElementById('btnImportSQL').disabled = true;
    
    modal.show();
}
</script>
</body>
</html>