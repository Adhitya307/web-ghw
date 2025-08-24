document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#dataTableBody');
    const updateStatus = document.getElementById('updateStatus');
    const tahunFilter = document.getElementById('tahunFilter');
    const bulanFilter = document.getElementById('bulanFilter');
    const periodeFilter = document.getElementById('periodeFilter');
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

    const tahunRowspans = {};

    function pollData() {
        fetch('<?= base_url('get-latest-data') ?>')
            .then(response => response.json())
            .then(data => {
                updateTable(data);
                setTimeout(pollData, 5000);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                setTimeout(pollData, 10000);
            });
    }

    function updateTable(data) {
        updateStatus.style.display = 'flex';

        // Simpan state filter
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

        // Update / tambah baris
        data.forEach(item => {
            const pid = item.pengukuran.id;
            const tahun = item.pengukuran.tahun || '-';
            const bulan = item.pengukuran.bulan || '-';
            const periode = item.pengukuran.periode || '-';

            let row = tableBody.querySelector(`tr[data-pid="${pid}"]`);
            if (!row) {
                row = createNewRow(item, pid, tahun, bulan, periode, tahunCounts);
                tableBody.appendChild(row);
            } else {
                updateExistingRow(row, item);
            }
        });

        // Update rowspan tahun
        updateTahunRowspans(tahunCounts);

        // Restore filter
        tahunFilter.value = tVal;
        bulanFilter.value = bVal;
        periodeFilter.value = pVal;
        searchInput.value = sVal;

        // Terapkan filter
        filterTable();

        setTimeout(() => {
            updateStatus.style.display = 'none';
        }, 1000);
    }

    function createNewRow(item, pid, tahun, bulan, periode, tahunCounts) {
        const row = document.createElement('tr');
        row.dataset.tahun = tahun;
        row.dataset.bulan = bulan;
        row.dataset.periode = periode;
        row.dataset.pid = pid;

        // Tahun
        if (!tahunRowspans[tahun]) {
            const tahunCell = document.createElement('td');
            tahunCell.className = 'sticky';
            tahunCell.rowSpan = tahunCounts[tahun];
            tahunCell.textContent = tahun;
            row.appendChild(tahunCell);
            tahunRowspans[tahun] = true;
        }

        addCellsToRow(row, item);
        return row;
    }

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

        appendCell(row, p.bulan, 'sticky-2');
        appendCell(row, p.periode, 'sticky-3');
        appendCell(row, p.tanggal, 'sticky-4');
        appendCell(row, p.tma_waduk, 'sticky-5');
        appendCell(row, p.curah_hujan, 'sticky-6');

        appendCell(row, thom.a1_r || '-');
        appendCell(row, thom.a1_l || '-');
        appendCell(row, thom.b1 || '-');
        appendCell(row, thom.b3 || '-');
        appendCell(row, thom.b5 || '-');

        srList.forEach(num => {
            appendCell(row, srRow[`sr_${num}_nilai`] || '-');
            appendCell(row, srRow[`sr_${num}_kode`] || '-');
        });

        appendCell(row, boco.elv_624_t1 || '-');
        appendCell(row, boco.elv_624_t1_kode || '-');
        appendCell(row, boco.elv_615_t2 || '-');
        appendCell(row, boco.elv_615_t2_kode || '-');
        appendCell(row, boco.pipa_p1 || '-');
        appendCell(row, boco.pipa_p1_kode || '-');

        appendCell(row, pth.r || '-');
        appendCell(row, pth.l || '-');
        appendCell(row, pth.b1 || '-');
        appendCell(row, pth.b3 || '-');
        appendCell(row, pth.b5 || '-');

        srList.forEach(num => {
            const q = getSrQ(psr, num);
            appendCell(row, q === null ? '-' : fmt(q, 6));
        });

        appendCell(row, fmt(pbb.talang1, 2));
        appendCell(row, fmt(pbb.talang2, 2));
        appendCell(row, fmt(pbb.pipa, 2));

        appendCell(row, fmt(pig.a1, 2));
        appendCell(row, fmt(pig.ambang_a1, 2));

        appendCell(row, fmt(psp.B3 || psp.b3, 2));
        appendCell(row, fmt(psp.ambang, 2));

        appendCell(row, fmt(tk.sr, 2));
        appendCell(row, fmt(tk.ambang, 2));
        appendCell(row, tk.B5 || tk.b5 || '-');

        appendCell(row, fmt(tbTot.R1 || tbTot.r1, 2));
        appendCell(row, fmt(pbatas.batas_maksimal, 2));
    }

    function appendCell(row, value, className = '') {
        const cell = document.createElement('td');
        if (className) cell.className = className;
        cell.textContent = value || '-';
        row.appendChild(cell);
    }

    function updateExistingRow(row, item) {
        let cellIndex = 6; // mulai setelah Curah Hujan
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

        [thom.a1_r, thom.a1_l, thom.b1, thom.b3, thom.b5].forEach(val => updateCell(row, cellIndex++, val || '-'));
        srList.forEach(num => { updateCell(row, cellIndex++, srRow[`sr_${num}_nilai`] || '-'); updateCell(row, cellIndex++, srRow[`sr_${num}_kode`] || '-'); });
        [boco.elv_624_t1, boco.elv_624_t1_kode, boco.elv_615_t2, boco.elv_615_t2_kode, boco.pipa_p1, boco.pipa_p1_kode].forEach(val => updateCell(row, cellIndex++, val || '-'));
        [pth.r, pth.l, pth.b1, pth.b3, pth.b5].forEach(val => updateCell(row, cellIndex++, val || '-'));
        srList.forEach(num => { const q = getSrQ(psr, num); updateCell(row, cellIndex++, q === null ? '-' : fmt(q, 6)); });
        [pbb.talang1, pbb.talang2, pbb.pipa].forEach(val => updateCell(row, cellIndex++, fmt(val, 2)));
        [pig.a1, pig.ambang_a1].forEach(val => updateCell(row, cellIndex++, fmt(val, 2)));
        [psp.B3 || psp.b3, psp.ambang].forEach(val => updateCell(row, cellIndex++, fmt(val, 2)));
        [tk.sr, tk.ambang].forEach(val => updateCell(row, cellIndex++, fmt(val, 2)));
        updateCell(row, cellIndex++, tk.B5 || tk.b5 || '-');
        updateCell(row, cellIndex++, fmt(tbTot.R1 || tbTot.r1, 2));
        updateCell(row, cellIndex, fmt(pbatas.batas_maksimal, 2));
    }

    function updateCell(row, cellIndex, value) {
        if (row.cells.length > cellIndex) {
            row.cells[cellIndex].textContent = value !== undefined && value !== null ? value : '-';
        } else {
            console.error('Cell index out of bounds:', cellIndex);
        }
    }

    function updateTahunRowspans(tahunCounts) {
        Object.keys(tahunRowspans).forEach(tahun => { tahunRowspans[tahun] = false; });
        tableBody.querySelectorAll('tr').forEach(row => {
            const tahun = row.dataset.tahun;
            if (tahun && tahunCounts[tahun] && !tahunRowspans[tahun]) {
                const tahunCell = row.querySelector('td.sticky');
                if (tahunCell) { tahunCell.rowSpan = tahunCounts[tahun]; tahunRowspans[tahun] = true; }
            }
        });
    }

    function filterTable() {
        const tVal = tahunFilter.value;
        const bVal = bulanFilter.value;
        const pVal = periodeFilter.value;
        const searchVal = searchInput.value.toLowerCase();

        tableBody.querySelectorAll('tr').forEach(tr => {
            const tahunMatch = !tVal || tr.dataset.tahun === tVal;
            const bulanMatch = !bVal || tr.dataset.bulan === bVal;
            const periodeMatch = !pVal || tr.dataset.periode === pVal;
            let searchMatch = !searchVal || tr.textContent.toLowerCase().includes(searchVal);
            tr.style.display = (tahunMatch && bulanMatch && periodeMatch && searchMatch) ? '' : 'none';
        });
    }

    pollData(); // start polling
});
