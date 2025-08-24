<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Gabungan - PT Indonesia Power</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/data.css">

    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="data-container">
    <!-- Header -->
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-table me-2"></i>Data Input Rembesan Bendungan
        </h2>

        <!-- Navigasi Cepat -->
        <div class="btn-group mb-3" role="group" aria-label="Navigasi Tabel">
            <a href="<?= base_url('input-data') ?>" class="btn btn-outline-primary">
                <i class="fas fa-table"></i> Tabel Gabungan
            </a>
            <a href="<?= base_url('data/tabel_thomson') ?>" class="btn btn-outline-success">
                <i class="fas fa-eye"></i> Lihat Tabel Thomson
            </a>
            <a href="<?= base_url('lihat/tabel_ambang') ?>" class="btn btn-outline-warning">
                <i class="fas fa-ruler"></i> Rumus Ambang Batas
            </a>
        </div>

        <!-- Filter -->
        <div class="table-controls">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
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
                    if (!empty($pengukuran)):
                        $uniqueMonths = array_unique(array_map(fn($p) => $p['bulan'] ?? '-', $pengukuran));
                        foreach ($uniqueMonths as $month):
                            if ($month === '-') continue;
                    ?>
                        <option value="<?= esc($month) ?>"><?= esc($month) ?></option>
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

    <!-- Status Update -->
    <div id="updateStatus" class="alert alert-info d-flex align-items-center" style="display: none !important;">
        <div class="spinner-border spinner-border-sm me-2" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span>Memperbarui data...</span>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="data-table" id="exportTable">
            <?php
            // List SR
            $srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
            $srColspan = count($srList) * 2;
            $twHeaders = ['A1 {R}', 'A1 {L}', 'B1', 'B3', 'B5'];

            // Index helper
            $indexBy = fn(array $rows) => array_reduce($rows, fn($carry, $item) => isset($item['pengukuran_id']) ? $carry + [$item['pengukuran_id'] => $item] : $carry, []);

            $thomsonBy               = $thomson ? $indexBy($thomson) : [];
            $srBy                    = $sr ? $indexBy($sr) : [];
            $bocoranBy               = $bocoran ? $indexBy($bocoran) : [];
            $perhitunganThomsonBy    = $perhitungan_thomson ? $indexBy($perhitungan_thomson) : [];
            $perhitunganSrBy         = $perhitungan_sr ? $indexBy($perhitungan_sr) : [];
            $perhitunganBocoranBy    = $perhitungan_bocoran ? $indexBy($perhitungan_bocoran) : [];
            $perhitunganIgBy         = $perhitungan_ig ? $indexBy($perhitungan_ig) : [];
            $perhitunganSpillwayBy   = $perhitungan_spillway ? $indexBy($perhitungan_spillway) : [];
            $tebingKananBy           = $tebing_kanan ? $indexBy($tebing_kanan) : [];
            $totalBocoranBy          = $total_bocoran ? $indexBy($total_bocoran) : [];
            $ambangBy                = $ambang ? $indexBy($ambang) : [];
            $perhitunganBatasBy      = $perhitungan_batas ? $indexBy($perhitungan_batas) : [];

            // Format angka
            $fmt = fn($v, $dec = 2) => isset($v) && $v !== '' && $v !== null && $v != 0 ? number_format((float)$v, $dec, '.', '') : '-';

            // Ambil Q SR
            $getSrQ = function($row, $num) {
                if (!$row) return null;
                foreach (["q_sr_$num", "sr_{$num}_q", "sr{$num}_q", "q{$num}", "sr_$num"] as $k) {
                    if (isset($row[$k])) return $row[$k];
                }
                return null;
            };
            ?>

            <thead>
                <tr>
                    <th rowspan="3" class="sticky">Tahun</th>
                    <th rowspan="3" class="sticky-2">Bulan</th>
                    <th rowspan="3" class="sticky-3">Periode</th>
                    <th rowspan="3" class="sticky-4">Tanggal</th>
                    <th rowspan="3" class="sticky-5">TMA Waduk</th>
                    <th rowspan="3" class="sticky-6">Curah Hujan</th>
                    <th rowspan="2" colspan="<?= count($twHeaders) ?>" class="section-thomson">Thomson Weir</th>
                    <th colspan="<?= $srColspan ?>" class="section-sr">SR</th>
                    <th colspan="6" rowspan="2" class="section-bocoran">Bocoran Baru</th>
                    <th colspan="5" class="section-bocoran">Perhitungan Q Thompson Weir (Liter/Menit)</th>
                    <th rowspan="2" colspan="<?= count($srList) ?>" class="section-sr">Perhitungan Q SR (Liter/Menit)</th>
                    <th rowspan="2" colspan="3" class="section-sr">Perhitungan Bocoran Baru</th>
                    <th rowspan="2" colspan="2" class="section-inti">Perhitungan Inti Galery</th>
                    <th rowspan="2" colspan="2" class="section-inti">Perhitungan Bawah Bendungan/Spillway</th>
                    <th rowspan="2" colspan="2" class="section-inti">Perhitungan Tebing Kanan</th>
                    <th rowspan="2" colspan="1" class="section-inti">Perhitungan Tebing Kanan</th>
                    <th rowspan="2" colspan="1" class="section-inti">Total Bocoran</th>
                    <th rowspan="2" colspan="1" class="section-inti">Batasan Maksimal (Tahun)</th>
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

                foreach ($pengukuran as $index => $p):
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
                    if ($showTahun) {
                        $processedYears[] = $tahun;
                    }
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

                    <td><?= esc($pth['r'] ?? '-') ?></td>
                    <td><?= esc($pth['l'] ?? '-') ?></td>
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
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="export-buttons mt-3">
        <button id="exportExcel" class="btn btn-success"><i class="fas fa-file-excel me-1"></i> Export Excel</button>
        <button id="exportPDF" class="btn btn-primary"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
    </div>
</div>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tahunFilter = document.getElementById('tahunFilter');
    const bulanFilter = document.getElementById('bulanFilter');
    const periodeFilter = document.getElementById('periodeFilter');
    const resetFilter = document.getElementById('resetFilter');
    const tableBody = document.querySelector('#dataTableBody');
    const updateStatus = document.getElementById('updateStatus');
    const searchInput = document.getElementById('searchInput');
    
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
        updateStatus.style.display = 'flex';
        
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
        setTimeout(() => {
            updateStatus.style.display = 'none';
        }, 1000);
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
    
    // Thomson Weir - PERBAIKAN: Pastikan menggunakan nilai default jika undefined
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
    
    // Pastikan urutan kolom sesuai dengan struktur header
    // PERBAIKAN: Mulai dari kolom setelah "Curah Hujan" (kolom ke-6)
    let cellIndex = 6; // Kolom 0-5: Tahun, Bulan, Periode, Tanggal, TMA Waduk, Curah Hujan
    
    // Update Thomson Weir (5 kolom) - A1 {R}, A1 {L}, B1, B3, B5
    updateCell(row, cellIndex++, thom.a1_r || '-');
    updateCell(row, cellIndex++, thom.a1_l || '-');
    updateCell(row, cellIndex++, thom.b1 || '-');
    updateCell(row, cellIndex++, thom.b3 || '-');
    updateCell(row, cellIndex++, thom.b5 || '-');
    
    // Update SR data (34 kolom: 17 SR × 2) - Nilai dan Kode untuk setiap SR
    srList.forEach(num => {
        updateCell(row, cellIndex++, srRow[`sr_${num}_nilai`] || '-');
        updateCell(row, cellIndex++, srRow[`sr_${num}_kode`] || '-');
    });
    
    // Update Bocoran Baru (6 kolom) - ELV 624 T1, Kode, ELV 615 T2, Kode, Pipa P1, Kode
    updateCell(row, cellIndex++, boco.elv_624_t1 || '-');
    updateCell(row, cellIndex++, boco.elv_624_t1_kode || '-');
    updateCell(row, cellIndex++, boco.elv_615_t2 || '-');
    updateCell(row, cellIndex++, boco.elv_615_t2_kode || '-');
    updateCell(row, cellIndex++, boco.pipa_p1 || '-');
    updateCell(row, cellIndex++, boco.pipa_p1_kode || '-');
    
    // Update Perhitungan Thomson (5 kolom) - R, L, B-1, B-3, B-5
    updateCell(row, cellIndex++, pth.r || '-');
    updateCell(row, cellIndex++, pth.l || '-');
    updateCell(row, cellIndex++, pth.b1 || '-');
    updateCell(row, cellIndex++, pth.b3 || '-');
    updateCell(row, cellIndex++, pth.b5 || '-');
    
    // Update Perhitungan SR (17 kolom) - SR 1, SR 40, SR 66, dst...
    srList.forEach(num => {
        const q = getSrQ(psr, num);
        updateCell(row, cellIndex++, q === null ? '-' : fmt(q, 6));
    });
    
    // Update Perhitungan Bocoran Baru (3 kolom) - Talang 1, Talang 2, Pipa
    updateCell(row, cellIndex++, fmt(pbb.talang1, 2));
    updateCell(row, cellIndex++, fmt(pbb.talang2, 2));
    updateCell(row, cellIndex++, fmt(pbb.pipa, 2));
    
    // Update Perhitungan Inti Galery (2 kolom) - A1, Ambang
    updateCell(row, cellIndex++, fmt(pig.a1, 2));
    updateCell(row, cellIndex++, fmt(pig.ambang_a1, 2));
    
    // Update Perhitungan Spillway (2 kolom) - B3, Ambang
    updateCell(row, cellIndex++, fmt(psp.B3 || psp.b3, 2));
    updateCell(row, cellIndex++, fmt(psp.ambang, 2));
    
    // Update Perhitungan Tebing Kanan (3 kolom) - SR, Ambang, B5
    updateCell(row, cellIndex++, fmt(tk.sr, 2));
    updateCell(row, cellIndex++, fmt(tk.ambang, 2));
    updateCell(row, cellIndex++, tk.B5 || tk.b5 || '-');
    
    // Update Total Bocoran (1 kolom) - R1
    updateCell(row, cellIndex++, fmt(tbTot.R1 || tbTot.r1, 2));
    
    // Update Batas Maksimal (1 kolom)
    updateCell(row, cellIndex, fmt(pbatas.batas_maksimal, 2));
}
    // Helper untuk update cell
// Helper untuk update cell - PERBAIKAN: Handle undefined values
function updateCell(row, cellIndex, value) {
    if (row.cells.length > cellIndex) {
        const cell = row.cells[cellIndex];
        cell.textContent = value !== undefined && value !== null ? value : '-';
    } else {
        console.error('Cell index out of bounds:', cellIndex, 'for row with', row.cells.length, 'cells');
    }
}

    // Fungsi untuk memperbarui rowspan tahun
    function updateTahunRowspans(tahunCounts) {
        // Reset semua rowspan tahun
        Object.keys(tahunRowspans).forEach(tahun => {
            tahunRowspans[tahun] = false;
        });
        
        // Set rowspan untuk tahun yang ada
        tableBody.querySelectorAll('tr').forEach((row, index) => {
            const tahun = row.dataset.tahun;
            if (tahun && tahunCounts[tahun] && !tahunRowspans[tahun]) {
                // Cari sel tahun di baris ini
                const tahunCell = row.querySelector('td.sticky');
                if (tahunCell) {
                    tahunCell.rowSpan = tahunCounts[tahun];
                    tahunRowspans[tahun] = true;
                }
            }
        });
    }

    // Mulai polling saat halaman dimuat
    pollData();

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', () => {
        const wb = XLSX.utils.table_to_book(document.getElementById('exportTable'), {sheet: "Data Rembesan"});
        XLSX.writeFile(wb, "data_rembesan.xlsx");
    });

    // Export PDF
    document.getElementById('exportPDF').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        
        html2canvas(document.getElementById('exportTable')).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = doc.internal.pageSize.getWidth();
            const imgHeight = canvas.height * imgWidth / canvas.width;
            
            doc.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            doc.save('data_rembesan.pdf');
        });
    });
});
</script>

<style>
.data-container { padding: 20px; }
.table-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; }
.table-title { font-size: 1.5rem; margin-bottom: 10px; }
.filter-section { margin-bottom: 15px; }
.filter-group { display: flex; gap: 15px; flex-wrap: wrap; }
.filter-item { display: flex; flex-direction: column; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.data-table th, .data-table td { border: 1px solid #ddd; padding: 4px 6px; text-align: center; }
.data-table th.sticky { position: sticky; top: 0; background: #f8f9fa; z-index: 3; }
.data-table th.sticky-2 { position: sticky; top: 0; background: #f8f9fa; z-index: 3; }
.data-table th.sticky-3 { position: sticky; top: 0; background: #f8f9fa; z-index: 3; }
.data-table th.sticky-4 { position: sticky; top: 0; background: #f8f9fa; z-index: 3; }
.data-table th.sticky-5 { position: sticky; top: 0; background: #f8f9fa; z-index: 3; }
.data-table th.sticky-6 { position: sticky; top: 0; background: #f8f9fa; z-index: 3; }
.section-thomson { background: #e0f7fa; }
.section-sr { background: #fff3e0; }
.section-bocoran { background: #f1f8e9; }
.section-inti { background: #fce4ec; }
</style>
</body>
</html>