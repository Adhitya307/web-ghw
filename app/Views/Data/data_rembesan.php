<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Gabungan - PT Indonesia Power</title>

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
        
        .tooltip-inner {
            font-size: 12px;
            padding: 4px 8px;
        }
        
        /* Modal konfirmasi hapus yang lebih profesional */
        .modal-danger .modal-header {
            background-color: #dc3545;
            color: white;
        }
        
        .modal-danger .modal-header .btn-close {
            color: white;
            filter: invert(1);
        }
        
        /* Styling untuk modal import */
        #importModal .modal-dialog {
            max-width: 500px;
        }

        #importProgress {
            height: 20px;
            margin: 15px 0;
        }

        #importStatus {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="data-container">
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-table me-2"></i>Data Input Rembesan Bendungan
        </h2>

        <div class="btn-group mb-3" role="group">
            <a href="<?= base_url('input-data') ?>" class="btn btn-outline-primary">
                <i class="fas fa-table"></i> Tabel Gabungan
            </a>
            <a href="<?= base_url('data/tabel_thomson') ?>" class="btn btn-outline-success">
                <i class="fas fa-eye"></i> Lihat Tabel Thomson
            </a>
            <a href="<?= base_url('lihat/tabel_ambang') ?>" class="btn btn-outline-warning">
                <i class="fas fa-ruler"></i> Rumus Ambang Batas
            </a>
            <a href="<?= base_url('analisaLookBurt') ?>" class="btn btn-outline-danger">
                <i class="fas fa-chart-line"></i> Analisa Look Burt
            </a>

            <a href="<?= base_url('data/create') ?>" class="btn btn-outline-primary">
                <i class="fas fa-plus me-1"></i> Add Data
            </a>
            
            <!-- Tombol Import SQL -->
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
                        sort($uniqueYears);
                        foreach ($uniqueYears as $year):
                            if ($year === '-') continue;
                    ?>
                        <option value="<?= esc($year) ?>"><?= esc($year) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <!-- Bulan -->
            <div class="filter-item">
                <label for="bulanFilter" class="form-label">Bulan</label>
                <select id="bulanFilter" class="form-select">
                    <option value="">Semua Bulan</option>
                    <?php
                    $bulanArr = [
                        'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 
                        'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'
                    ];
                    foreach ($bulanArr as $month):
                    ?>
                        <option value="<?= $month ?>"><?= $month ?></option>
                    <?php endforeach; ?>
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

            <!-- Reset -->
            <div class="filter-item" style="align-self: flex-end;">
                <button id="resetFilter" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table" id="exportTable">
            <?php
            $srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
            $srColspan = count($srList) * 2;
            $twHeaders = ['A1 {R}', 'A1 {L}', 'B1', 'B3', 'B5'];

            $indexBy = fn(array $rows) => array_reduce($rows, fn($carry, $item) => isset($item['pengukuran_id']) ? $carry + [$item['pengukuran_id'] => $item] : $carry, []);
            $thomsonBy = $thomson ? $indexBy($thomson) : [];
            $srBy = $sr ? $indexBy($sr) : [];
            $bocoranBy = $bocoran ? $indexBy($bocoran) : [];
            $perhitunganThomsonBy = $perhitungan_thomson ? $indexBy($perhitungan_thomson) : [];
            $perhitunganSrBy = $perhitungan_sr ? $indexBy($perhitungan_sr) : [];
            $perhitunganBocoranBy = $perhitungan_bocoran ? $indexBy($perhitungan_bocoran) : [];
            $perhitunganIgBy = $perhitungan_ig ? $indexBy($perhitungan_ig) : [];
            $perhitunganSpillwayBy = $perhitungan_spillway ? $indexBy($perhitungan_spillway) : [];
            $tebingKananBy = $tebing_kanan ? $indexBy($tebing_kanan) : [];
            $totalBocoranBy = $total_bocoran ? $indexBy($total_bocoran) : [];
            $perhitunganBatasBy = $perhitungan_batas ? $indexBy($perhitungan_batas) : [];

            $fmt = fn($v, $dec = 2) => isset($v) && $v !== '' && $v !== null && $v != 0 ? number_format((float)$v, $dec, '.', '') : '-';
            $getSrQ = function($row, $num) {
                if (!$row) return null;
                foreach (["q_sr_$num", "sr_{$num}_q", "sr{$num}_q", "q{$num}", "sr_$num"] as $k) {
                    if (isset($row[$k])) return $row[$k];
                }
                return null;
            };

            // ✅ Sort pengukuran by tahun ASC, bulan ASC
            if (!empty($pengukuran)) {
                usort($pengukuran, function($a, $b) {
                    $bulanMap = [
                        'Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,
                        'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12
                    ];
                    $tahunA = (int)($a['tahun'] ?? 0);
                    $tahunB = (int)($b['tahun'] ?? 0);
                    if ($tahunA === $tahunB) {
                        $bulanA = $bulanMap[$a['bulan']] ?? 13;
                        $bulanB = $bulanMap[$b['bulan']] ?? 13;
                        return $bulanA <=> $bulanB;
                    }
                    return $tahunA <=> $tahunB;
                });
            }
            ?>

            <thead>
                <tr>
                    <th rowspan="3" class="sticky">Tahun</th>
                    <th rowspan="3" class="sticky-2">Bulan</th>
                    <th rowspan="3" class="sticky-3">Periode</th>
                    <th rowspan="3" class="sticky-4">Tanggal</th>
                    <th rowspan="3" class="sticky-5">TMA Waduk</th>
                    <th rowspan="3" class="sticky-6">Curah Hujan</th>
                    <th rowspan="2" colspan="<?= count($twHeaders) ?>">Thomson Weir</th>
                    <th colspan="<?= $srColspan ?>">SR</th>
                    <th colspan="6" rowspan="2">Bocoran Baru</th>
                    <th colspan="5">Perhitungan Q Thompson Weir (Liter/Menit)</th>
                    <th rowspan="2" colspan="<?= count($srList) ?>">Perhitungan Q SR (Liter/Menit)</th>
                    <th rowspan="2" colspan="3">Perhitungan Bocoran Baru</th>
                    <th rowspan="2" colspan="2">Perhitungan Inti Galery</th>
                    <th rowspan="2" colspan="2">Perhitungan Bawah Bendungan/Spillway</th>
                    <th rowspan="2" colspan="2">Perhitungan Tebing Kanan</th>
                    <th rowspan="2" colspan="1">Perhitungan Tebing Kanan</th>
                    <th rowspan="2" colspan="1">Total Bocoran</th>
                    <th rowspan="2" colspan="1">Batasan Maksimal (Tahun)</th>
                    <th rowspan="3" class="action-cell">Aksi</th> <!-- Kolom Aksi Baru -->
                </tr>
                <tr>
                    <?php foreach ($srList as $num): ?>
                        <th colspan="2">SR <?= $num ?></th>
                    <?php endforeach; ?>
                    <th colspan="5">Thomson Weir (mm)</th>
                </tr>
                <tr>
                    <?php foreach ($twHeaders as $tw): ?>
                        <th><?= $tw ?></th>
                    <?php endforeach; ?>
                    <?php foreach ($srList as $num): ?>
                        <th>Nilai</th><th>Kode</th>
                    <?php endforeach; ?>
                    <th colspan="2">ELV 624 T1</th>
                    <th colspan="2">ELV 615 T2</th>
                    <th colspan="2">Pipa P1</th>
                    <th>R</th><th>L</th><th>B-1</th><th>B-3</th><th>B-5</th>
                    <?php foreach ($srList as $num): ?><th>SR <?= $num ?></th><?php endforeach; ?>
                    <th>Talang 1</th><th>Talang 2</th><th>Pipa</th>
                    <th>A1</th><th>Ambang</th>
                    <th>B3</th><th>Ambang</th>
                    <th>SR</th><th>Ambang</th>
                    <th>B5</th>
                    <th>R1</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
            <?php if (!empty($pengukuran)):
                $tahunCounts = [];
                foreach ($pengukuran as $p) {
                    $tahun = $p['tahun'] ?? '-';
                    $tahunCounts[$tahun] = ($tahunCounts[$tahun] ?? 0) + 1;
                }
                $processedYears = [];
                foreach ($pengukuran as $p):
                    $tahun   = $p['tahun'] ?? '-';
                    $bulan   = $p['bulan'] ?? '-';
                    $periode = $p['periode'] ?? '-';
                    $pid     = $p['id'] ?? null;

                    $thom   = $pid ? ($thomsonBy[$pid] ?? []) : [];
                    $srRow  = $pid ? ($srBy[$pid] ?? []) : [];
                    $boco   = $pid ? ($bocoranBy[$pid] ?? []) : [];
                    $pth    = $pid ? ($perhitunganThomsonBy[$pid] ?? []) : [];
                    $psr    = $pid ? ($perhitunganSrBy[$pid] ?? []) : [];
                    $pbb    = $pid ? ($perhitunganBocoranBy[$pid] ?? []) : [];
                    $pig    = $pid ? ($perhitunganIgBy[$pid] ?? []) : [];
                    $psp    = $pid ? ($perhitunganSpillwayBy[$pid] ?? []) : [];
                    $tk     = $pid ? ($tebingKananBy[$pid] ?? []) : [];
                    $tbTot  = $pid ? ($totalBocoranBy[$pid] ?? []) : [];
                    $pbatas = $pid ? ($perhitunganBatasBy[$pid] ?? []) : [];

                    $showTahun = !in_array($tahun, $processedYears);
                    if ($showTahun) $processedYears[] = $tahun;
            ?>
                <tr data-tahun="<?= esc($tahun) ?>" data-bulan="<?= esc($bulan) ?>" data-periode="<?= esc($periode) ?>" data-pid="<?= esc($pid) ?>">
                    <?php if ($showTahun): ?>
                        <td rowspan="<?= $tahunCounts[$tahun] ?>" class="sticky"><?= esc($tahun) ?></td>
                    <?php endif; ?>
                    <td class="sticky-2"><?= esc($bulan) ?></td>
                    <td class="sticky-3"><?= esc($periode) ?></td>
                    <td class="sticky-4"><?= esc($p['tanggal'] ?? '-') ?></td>
                    <td class="sticky-5"><?= esc($p['tma_waduk'] ?? '-') ?></td>
                    <td class="sticky-6"><?= esc($p['curah_hujan'] ?? '-') ?></td>

                    <td><?= esc($thom['a1_r'] ?? '-') ?></td>
                    <td><?= esc($thom['a1_l'] ?? '-') ?></td>
                    <td><?= esc($thom['b1'] ?? '-') ?></td>
                    <td><?= esc($thom['b3'] ?? '-') ?></td>
                    <td><?= esc($thom['b5'] ?? '-') ?></td>

                    <?php foreach ($srList as $num): ?>
                        <td><?= esc($srRow["sr_{$num}_nilai"] ?? '-') ?></td>
                        <td><?= esc($srRow["sr_{$num}_kode"] ?? '-') ?></td>
                    <?php endforeach; ?>

                    <td><?= esc($boco['elv_624_t1'] ?? '-') ?></td>
                    <td><?= esc($boco['elv_624_t1_kode'] ?? '-') ?></td>
                    <td><?= esc($boco['elv_615_t2'] ?? '-') ?></td>
                    <td><?= esc($boco['elv_615_t2_kode'] ?? '-') ?></td>
                    <td><?= esc($boco['pipa_p1'] ?? '-') ?></td>
                    <td><?= esc($boco['pipa_p1_kode'] ?? '-') ?></td>

                    <td><?= esc($pth['a1_r'] ?? '-') ?></td>
                    <td><?= esc($pth['a1_l'] ?? '-') ?></td>
                    <td><?= esc($pth['b1'] ?? '-') ?></td>
                    <td><?= esc($pth['b3'] ?? '-') ?></td>
                    <td><?= esc($pth['b5'] ?? '-') ?></td>

                    <?php foreach ($srList as $num): ?>
                        <?php $q = $getSrQ($psr, $num); ?>
                        <td><?= $q === null ? '-' : $fmt($q, 6) ?></td>
                    <?php endforeach; ?>

                    <td><?= $fmt($pbb['talang1'] ?? null, 2) ?></td>
                    <td><?= $fmt($pbb['talang2'] ?? null, 2) ?></td>
                    <td><?= $fmt($pbb['pipa'] ?? null, 2) ?></td>

                    <td><?= $fmt($pig['a1'] ?? null, 2) ?></td>
                    <td><?= $fmt($pig['ambang_a1'] ?? null, 2) ?></td>

                    <td><?= $fmt($psp['B3'] ?? ($psp['b3'] ?? null), 2) ?></td>
                    <td><?= $fmt($psp['ambang'] ?? null, 2) ?></td>

                    <td><?= $fmt($tk['sr'] ?? null, 2) ?></td>
<td><?= $fmt($tk['ambang'] ?? null, 2) ?></td>
<td><?= esc($tk['B5'] ?? ($tk['b5'] ?? '-')) ?></td>

<td><?= $fmt($tbTot['R1'] ?? ($tbTot['r1'] ?? null), 2) ?></td>

<td><?= $fmt($pbatas['batas_maksimal'] ?? null, 2) ?></td>

                    <td class="action-cell">
                        <div class="d-flex justify-content-center">
                            <a href="<?= base_url('data/edit/' . $pid) ?>" class="btn-action btn-edit" 
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Data">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button type="button" class="btn-action btn-delete delete-btn" 
                                    data-id="<?= $pid ?>" data-bs-toggle="tooltip" 
                                    data-bs-placement="top" title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('data/modal_hapus'); ?>
<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Konfirmasi hapus data
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const row = this.closest('tr');
            const tahun = row.dataset.tahun;
            const bulan = row.dataset.bulan;
            const periode = row.dataset.periode;
            
            // Update pesan konfirmasi dengan informasi data
            document.querySelector('#deleteModal .modal-body p').textContent = 
                `Apakah Anda yakin ingin menghapus data ${periode} ${bulan} ${tahun}?`;
                
            confirmDeleteBtn.href = '<?= base_url('data/delete') ?>/' + id;
            deleteModal.show();
        });
    });
    
    // ============ FILTER FUNCTIONALITY ============
    const tahunFilter = document.getElementById('tahunFilter');
    const bulanFilter = document.getElementById('bulanFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const resetFilter = document.getElementById('resetFilter');
    const tableBody = document.querySelector('#dataTableBody');
    const searchInput = document.getElementById('searchInput');
    
    // Fungsi filter tabel
    function filterTable() {
        const tVal = tahunFilter.value;
        const bVal = bulanFilter.value;
        const pVal = periodeFilter.value;
        const searchVal = searchInput.value.toLowerCase();

        tableBody.querySelectorAll('tr').forEach(tr => {
            const tahunMatch = !tVal || tr.dataset.tahun === tVal;
            const bulanMatch = !bVal || tr.dataset.bulan === bVal;
            const periodeMatch = !pVal || tr.dataset.periode === pVal;
            
            // Pencarian teks
            let searchMatch = true;
            if (searchVal) {
                const rowText = tr.textContent.toLowerCase();
                searchMatch = rowText.includes(searchVal);
            }

            tr.style.display = (tahunMatch && bulanMatch && periodeMatch && searchMatch) ? '' : 'none';
        });
    }

    // Event listeners untuk filter
    tahunFilter.addEventListener('change', filterTable);
    bulanFilter.addEventListener('change', filterTable);
    periodeFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    
    resetFilter.addEventListener('click', () => {
        tahunFilter.value = '';
        bulanFilter.value = '';
        periodeFilter.value = '';
        searchInput.value = '';
        filterTable();
    });
    
    // Jalankan filter saat halaman pertama kali dimuat
    filterTable();

// ============ IMPORT SQL FUNCTIONALITY ============
const sqlFileInput = document.getElementById('sqlFile');
const btnImportSQL = document.getElementById('btnImportSQL');
const importProgress = document.getElementById('importProgress');
const importStatus = document.getElementById('importStatus');

// Handle import button click
btnImportSQL.addEventListener('click', function() {
    const file = sqlFileInput.files[0];
    if (!file) {
        showStatus('Pilih file SQL terlebih dahulu', 'warning');
        return;
    }

    if (!file.name.endsWith('.sql')) {
        showStatus('File harus berformat .sql', 'warning');
        return;
    }

    // Tampilkan progress bar
    importProgress.style.display = 'block';
    importProgress.querySelector('.progress-bar').style.width = '0%';
    btnImportSQL.disabled = true;
    showStatus('Memproses file...', 'info');

    // Baca file
    const reader = new FileReader();
    reader.onload = function(e) {
        const sqlContent = e.target.result;
        processSQLImport(sqlContent);
    };
    reader.onerror = function() {
        showStatus('Gagal membaca file', 'danger');
        importProgress.style.display = 'none';
        btnImportSQL.disabled = false;
    };
    reader.readAsText(file);
});

// Fungsi untuk memproses import SQL
function processSQLImport(sqlContent) {
    // Tampilkan progress
    importProgress.querySelector('.progress-bar').style.width = '30%';
    showStatus('Mengekstrak data dari file...', 'info');
    
    // Parse SQL content
    try {
        const sqlStatements = parseSQL(sqlContent);
        importProgress.querySelector('.progress-bar').style.width = '60%';
        showStatus(`Mengimport ${sqlStatements.length} data...`, 'info');
        
        // Kirim ke server untuk diproses
        fetch('<?= base_url() ?>import-sql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                sql: sqlStatements
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                importProgress.querySelector('.progress-bar').style.width = '100%';
                
                if (data.imported > 0) {
                    showStatus(`✅ ${data.message}`, 'success');
                    
                    // Refresh data setelah 2 detik jika ada data baru
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showStatus(`ℹ️ ${data.message}`, 'info');
                }
            } else {
                showStatus('❌ Import gagal: ' + data.message, 'danger');
                importProgress.style.display = 'none';
            }
            btnImportSQL.disabled = false;
        })
        .catch(error => {
            showStatus('❌ Error: ' + error.message, 'danger');
            importProgress.style.display = 'none';
            btnImportSQL.disabled = false;
        });
        
    } catch (error) {
        showStatus('❌ Error parsing SQL: ' + error.message, 'danger');
        importProgress.style.display = 'none';
        btnImportSQL.disabled = false;
    }
}

// Fungsi untuk parsing SQL (sederhana)
function parseSQL(sql) {
    // Hapus komentar
    sql = sql.replace(/--.*$/gm, '');
    sql = sql.replace(/\/\*[\s\S]*?\*\//g, '');
    
    // Pisahkan per statement
    const statements = sql.split(';')
        .filter(stmt => stmt.trim().length > 0)
        .map(stmt => stmt.trim() + ';');
    
    return statements;
}

// Fungsi untuk menampilkan status
function showStatus(message, type) {
    importStatus.innerHTML = message;
    importStatus.className = 'alert alert-' + type;
    importStatus.style.display = 'block';
}

// Reset status ketika modal ditutup
document.getElementById('importModal').addEventListener('hidden.bs.modal', function() {
    sqlFileInput.value = '';
    importProgress.style.display = 'none';
    importStatus.style.display = 'none';
    btnImportSQL.disabled = false;
});

// Tombol untuk mode advanced (opsional)
document.getElementById('btnAdvancedImport').addEventListener('click', function() {
    const file = sqlFileInput.files[0];
    if (!file) {
        showStatus('Pilih file SQL terlebih dahulu', 'warning');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const sqlContent = e.target.result;
        processAdvancedImport(sqlContent);
    };
    reader.readAsText(file);
});

// Fungsi untuk import advanced (replace instead of insert)
function processAdvancedImport(sqlContent) {
    showStatus('Memproses dengan mode advanced...', 'info');
    
    try {
        const sqlStatements = parseSQL(sqlContent);
        
        fetch('<?= base_url() ?>import-sql-advanced', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                sql: sqlStatements
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStatus(`✅ ${data.message}`, 'success');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showStatus('❌ Advanced import gagal: ' + data.message, 'danger');
            }
        });
        
    } catch (error) {
        showStatus('❌ Error: ' + error.message, 'danger');
    }
}
    
    // Daftar SR
    const srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
    
    // Format angka helper
    const fmt = (v, dec = 2) => {
        if (v === null || v === undefined || v === '' || v == 0) return '-';
        return parseFloat(v).toFixed(dec);
    };

    // Ambil Q SR
    const getSrQ = (row, num) => {
        if (!row) return null;
        for (const k of [`q_sr_${num}`, `sr_${num}_q`, `sr${num}_q`, `q${num}`, `sr_${num}`]) {
            if (row[k] !== undefined) return row[k];
        }
        return null;
    };

    // Simpan state rowspan tahun
    const tahunRowspans = {};

    // Fungsi untuk AJAX polling
    function pollData() {
        fetch('<?= base_url('get-latest-data') ?>')
            .then(response => response.json())
            .then(data => {
                updateTable(data);
                setTimeout(pollData, 5000); // Poll setiap 5 detik
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                setTimeout(pollData, 10000); // Coba lagi setelah 10 detik jika error
            });
    }

    // Fungsi untuk memperbarui tabel dengan data baru
    function updateTable(data) {
        const updateStatus = document.getElementById('updateStatus');
        if (updateStatus) updateStatus.style.display = 'flex';
        
        // Simpan state filter sebelum update
        const tVal = tahunFilter.value;
        const bVal = bulanFilter.value;
        const pVal = periodeFilter.value;
        const sVal = searchInput.value;
        
        // Hitung rowspan untuk tahun
        const tahunCounts = {};
        data.forEach(item => {
            const tahun = item.pengukuran.tahun || '-';
            tahunCounts[tahun] = (tahunCounts[tahun] || 0) + 1;
        });
        
        // Update atau tambah data di tabel
        data.forEach(item => {
            const pid = item.pengukuran.id;
            const tahun = item.pengukuran.tahun || '-';
            const bulan = item.pengukuran.bulan || '-';
            const periode = item.pengukuran.periode || '-';
            
            let row = tableBody.querySelector(`tr[data-pid="${pid}"]`);
            
            if (!row) {
                // Buat baris baru jika tidak ada
                row = createNewRow(item, pid, tahun, bulan, periode, tahunCounts);
                tableBody.appendChild(row);
            } else {
                // Update baris yang sudah ada
                updateExistingRow(row, item);
            }
        });
        
        // Perbarui rowspan untuk tahun
        updateTahunRowspans(tahunCounts);
        
        // Terapkan filter kembali setelah update
        tahunFilter.value = tVal;
        bulanFilter.value = bVal;
        periodeFilter.value = pVal;
        searchInput.value = sVal;
        filterTable();
        
        // Sembunyikan status update setelah 1 detik
        if (updateStatus) {
            setTimeout(() => {
                updateStatus.style.display = 'none';
            }, 1000);
        }
    }

    // Fungsi untuk membuat baris baru
    function createNewRow(item, pid, tahun, bulan, periode, tahunCounts) {
        const row = document.createElement('tr');
        row.dataset.tahun = tahun;
        row.dataset.bulan = bulan;
        row.dataset.periode = periode;
        row.dataset.pid = pid;
        
        // Tambahkan sel untuk tahun (dengan rowspan jika tahun pertama)
        if (!tahunRowspans[tahun]) {
            const tahunCell = document.createElement('td');
            tahunCell.className = 'sticky';
            tahunCell.rowSpan = tahunCounts[tahun];
            tahunCell.textContent = tahun;
            row.appendChild(tahunCell);
            tahunRowspans[tahun] = true;
        }
        
        // Tambahkan sel lainnya
        addCellsToRow(row, item);
        
        return row;
    }

    // Fungsi untuk menambahkan sel ke baris
    function addCellsToRow(row, item) {
        const p = item.pengukuran;
        const thom = item.thomson || {};
        const srRow = item.sr || {};
        const boco = item.bocoran || {};
        const pth = item.perhitungan_thomson || {};
        const psr = item.perhitungan_sr || {};
        const pbb = item.perhitungan_bocoran || {};
        const pig = item.perhitungan_ig || {};
        const psp = item.perhitungan_spillway || {};
        const tk = item.tebing_kanan || {};
        const tbTot = item.total_bocoran || {};
        const pbatas = item.perhitungan_batas || {};
        
        // Bulan, Periode, Tanggal, TMA, Curah Hujan
        appendCell(row, p.bulan, 'sticky-2');
        appendCell(row, p.periode, 'sticky-3');
        appendCell(row, p.tanggal, 'sticky-4');
        appendCell(row, p.tma_waduk, 'sticky-5');
        appendCell(row, p.curah_hujan, 'sticky-6');
        
        // Thomson Weir
        appendCell(row, thom.a1_r || '-');
        appendCell(row, thom.a1_l || '-');
        appendCell(row, thom.b1 || '-');
        appendCell(row, thom.b3 || '-');
        appendCell(row, thom.b5 || '-');
        
        // SR Data
        srList.forEach(num => {
            appendCell(row, srRow[`sr_${num}_nilai`] || '-');
            appendCell(row, srRow[`sr_${num}_kode`] || '-');
        });
        
        // Bocoran Baru
        appendCell(row, boco.elv_624_t1 || '-');
        appendCell(row, boco.elv_624_t1_kode || '-');
        appendCell(row, boco.elv_615_t2 || '-');
        appendCell(row, boco.elv_615_t2_kode || '-');
        appendCell(row, boco.pipa_p1 || '-');
        appendCell(row, boco.pipa_p1_kode || '-');
        
        // Perhitungan Thomson
        appendCell(row, pth.r || '-');
        appendCell(row, pth.l || '-');
        appendCell(row, pth.b1 || '-');
        appendCell(row, pth.b3 || '-');
        appendCell(row, pth.b5 || '-');
        
        // Perhitungan SR
        srList.forEach(num => {
            const q = getSrQ(psr, num);
            appendCell(row, q === null ? '-' : fmt(q, 6));
        });
        
        // Perhitungan Bocoran Baru
        appendCell(row, fmt(pbb.talang1, 2));
        appendCell(row, fmt(pbb.talang2, 2));
        appendCell(row, fmt(pbb.pipa, 2));
        
        // Perhitungan Inti Galery
        appendCell(row, fmt(pig.a1, 2));
        appendCell(row, fmt(pig.ambang_a1, 2));
        
        // Perhitungan Spillway
        appendCell(row, fmt(psp.B3 || psp.b3, 2));
        appendCell(row, fmt(psp.ambang, 2));
        
        // Perhitungan Tebing Kanan
        appendCell(row, fmt(tk.sr, 2));
        appendCell(row, fmt(tk.ambang, 2));
        appendCell(row, tk.B5 || tk.b5 || '-');
        
        // Total Bocoran
        appendCell(row, fmt(tbTot.R1 || tbTot.r1, 2));
        
        // Batas Maksimal
        appendCell(row, fmt(pbatas.batas_maksimal, 2));
        
        // Kolom Aksi
        const actionCell = document.createElement('td');
        actionCell.className = 'action-cell';
        
        const btnContainer = document.createElement('div');
        btnContainer.className = 'd-flex justify-content-center';
        
        // Tombol Edit
        const editBtn = document.createElement('a');
        editBtn.href = '<?= base_url('data/edit/') ?>' + p.id;
        editBtn.className = 'btn-action btn-edit';
        editBtn.setAttribute('data-bs-toggle', 'tooltip');
        editBtn.setAttribute('data-bs-placement', 'top');
        editBtn.title = 'Edit Data';
        editBtn.innerHTML = '<i class="fas fa-pencil-alt"></i>';
        
        // Tombol Hapus
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn-action btn-delete delete-btn';
        deleteBtn.setAttribute('data-id', p.id);
        deleteBtn.setAttribute('data-bs-toggle', 'tooltip');
        deleteBtn.setAttribute('data-bs-placement', 'top');
        deleteBtn.title = 'Hapus Data';
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
        
        // Event listener untuk tombol hapus
        deleteBtn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const row = this.closest('tr');
            const tahun = row.dataset.tahun;
            const bulan = row.dataset.bulan;
            const periode = row.dataset.periode;
            
            // Update pesan konfirmasi dengan informasi data
            document.querySelector('#deleteModal .modal-body p').textContent = 
                `Apakah Anda yakin ingin menghapus data ${periode} ${bulan} ${tahun}?`;
                
            confirmDeleteBtn.href = '<?= base_url('data/delete') ?>/' + id;
            deleteModal.show();
        });
        
        btnContainer.appendChild(editBtn);
        btnContainer.appendChild(deleteBtn);
        actionCell.appendChild(btnContainer);
        row.appendChild(actionCell);
        
        // Inisialisasi tooltip untuk tombol baru
        new bootstrap.Tooltip(editBtn);
        new bootstrap.Tooltip(deleteBtn);
    }

    // Helper untuk menambahkan sel
    function appendCell(row, value, className = '') {
        const cell = document.createElement('td');
        if (className) cell.className = className;
        cell.textContent = value || '-';
        row.appendChild(cell);
    }

    // Fungsi untuk memperbarui baris yang sudah ada
    function updateExistingRow(row, item) {
        const thom = item.thomson || {};
        const srRow = item.sr || {};
        const boco = item.bocoran || {};
        const pth = item.perhitungan_thomson || {};
        const psr = item.perhitungan_sr || {};
        const pbb = item.perhitungan_bocoran || {};
        const pig = item.perhitungan_ig || {};
        const psp = item.perhitungan_spillway || {};
        const tk = item.tebing_kanan || {};
        const tbTot = item.total_bocoran || {};
        const pbatas = item.perhitungan_batas || {};
        
        let cellIndex = 6; // Kolom 0-5: Tahun, Bulan, Periode, Tanggal, TMA Waduk, Curah Hujan
        
        // Update Thomson Weir (5 kolom)
        updateCell(row, cellIndex++, thom.a1_r || '-');
        updateCell(row, cellIndex++, thom.a1_l || '-');
        updateCell(row, cellIndex++, thom.b1 || '-');
        updateCell(row, cellIndex++, thom.b3 || '-');
        updateCell(row, cellIndex++, thom.b5 || '-');
        
        // Update SR data (34 kolom: 17 SR × 2)
        srList.forEach(num => {
            updateCell(row, cellIndex++, srRow[`sr_${num}_nilai`] || '-');
            updateCell(row, cellIndex++, srRow[`sr_${num}_kode`] || '-');
        });
        
        // Update Bocoran Baru (6 kolom)
        updateCell(row, cellIndex++, boco.elv_624_t1 || '-');
        updateCell(row, cellIndex++, boco.elv_624_t1_kode || '-');
        updateCell(row, cellIndex++, boco.elv_615_t2 || '-');
        updateCell(row, cellIndex++, boco.elv_615_t2_kode || '-');
        updateCell(row, cellIndex++, boco.pipa_p1 || '-');
        updateCell(row, cellIndex++, boco.pipa_p1_kode || '-');
        
        // Update Perhitungan Thomson (5 kolom)
        updateCell(row, cellIndex++, pth.r || '-');
        updateCell(row, cellIndex++, pth.l || '-');
        updateCell(row, cellIndex++, pth.b1 || '-');
        updateCell(row, cellIndex++, pth.b3 || '-');
        updateCell(row, cellIndex++, pth.b5 || '-');
        
        // Update Perhitungan SR (17 kolom)
        srList.forEach(num => {
            const q = getSrQ(psr, num);
            updateCell(row, cellIndex++, q === null ? '-' : fmt(q, 6));
        }); 
        
        // Update Perhitungan Bocoran Baru (3 kolom)
        updateCell(row, cellIndex++, fmt(pbb.talang1, 2));
        updateCell(row, cellIndex++, fmt(pbb.talang2, 2));
        updateCell(row, cellIndex++, fmt(pbb.pipa, 2));
        
        // Update Perhitungan Inti Galery (2 kolom)
        updateCell(row, cellIndex++, fmt(pig.a1, 2));
        updateCell(row, cellIndex++, fmt(pig.ambang_a1, 2));
        
        // Update Perhitungan Spillway (2 kolom)
        updateCell(row, cellIndex++, fmt(psp.B3 || psp.b3, 2));
        updateCell(row, cellIndex++, fmt(psp.ambang, 2));
        
        // Update Perhitungan Tebing Kanan (3 kolom)
        updateCell(row, cellIndex++, fmt(tk.sr, 2));
        updateCell(row, cellIndex++, fmt(tk.ambang, 2));
        updateCell(row, cellIndex++, tk.B5 || tk.b5 || '-');
        
        // Update Total Bocoran (1 kolom)
        updateCell(row, cellIndex++, fmt(tbTot.R1 || tbTot.r1, 2));
        
        // Update Batas Maksimal (1 kolom)
        updateCell(row, cellIndex++, fmt(pbatas.batas_maksimal, 2));
    }

    // Helper untuk memperbarui nilai sel
    function updateCell(row, index, value) {
        const cells = row.querySelectorAll('td');
        if (cells[index]) {
            cells[index].textContent = value || '-';
        }
    }

    // Fungsi untuk memperbarui rowspan tahun
    function updateTahunRowspans(tahunCounts) {
        // Reset semua rowspan tahun
        Object.keys(tahunRowspans).forEach(tahun => {
            tahunRowspans[tahun] = false;
        });
        
        // Atur ulang rowspan untuk setiap tahun
        const tahunRows = {};
        tableBody.querySelectorAll('tr').forEach(tr => {
            const tahun = tr.dataset.tahun;
            if (!tahunRows[tahun]) tahunRows[tahun] = [];
            tahunRows[tahun].push(tr);
        });
        
        // Terapkan rowspan yang benar
        Object.keys(tahunRows).forEach(tahun => {
            const rows = tahunRows[tahun];
            if (rows.length > 0) {
                // Hapus sel tahun dari semua baris kecuali yang pertama
                for (let i = 1; i < rows.length; i++) {
                    const firstCell = rows[i].querySelector('td.sticky');
                    if (firstCell && firstCell.rowSpan) {
                        firstCell.remove();
                    }
                }
                
                // Set rowspan pada baris pertama
                const firstCell = rows[0].querySelector('td.sticky');
                if (firstCell) {
                    firstCell.rowSpan = rows.length;
                } else {
                    // Tambahkan sel tahun jika tidak ada
                    const tahunCell = document.createElement('td');
                    tahunCell.className = 'sticky';
                    tahunCell.rowSpan = rows.length;
                    tahunCell.textContent = tahun;
                    rows[0].insertBefore(tahunCell, rows[0].firstChild);
                }
                
                tahunRowspans[tahun] = true;
            }
        });
    }

    // Fungsi untuk mengekspor data ke Excel
    function exportToExcel() {
        const table = document.getElementById('exportTable');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Data Rembesan"});
        XLSX.writeFile(wb, "data_rembesan_bendungan.xlsx");
    }

    // Fungsi untuk mengekspor data ke PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        
        // Tambahkan judul
        doc.setFontSize(16);
        doc.text('Data Input Rembesan Bendungan - PT Indonesia Power', 14, 15);
        doc.setFontSize(10);
        doc.text(`Diekspor pada: ${new Date().toLocaleString()}`, 14, 22);
        
        // Tangkap tabel sebagai gambar
        html2canvas(document.getElementById('exportTable')).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = doc.internal.pageSize.getWidth() - 20;
            const pageHeight = doc.internal.pageSize.getHeight();
            const imgHeight = canvas.height * imgWidth / canvas.width;
            
            let heightLeft = imgHeight;
            let position = 30;
            
            doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
            
            // Tambahkan halaman baru jika diperlukan
            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }
            
            doc.save('data_rembesan_bendungan.pdf');
        });
    }

    // Event listener untuk tombol ekspor
    document.getElementById('exportExcel').addEventListener('click', exportToExcel);
    document.getElementById('exportPDF').addEventListener('click', exportToPDF);

    // Mulai polling data
    pollData();
});

// Fungsi polling yang lebih sederhana dan robust
function startPolling() {
    console.log('Polling started...');
    
    function poll() {
        fetch('<?= base_url('get-latest-data') ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data.length, 'records');
                // Di sini Anda bisa menambahkan logika update UI jika diperlukan
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            })
            .finally(() => {
                // Poll lagi setelah 5 detik
                setTimeout(poll, 5000);
            });
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