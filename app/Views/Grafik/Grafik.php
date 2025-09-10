<?= $this->include('layouts/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-chart-line me-2"></i>Grafik Rembesan Bendungan</h2>
        <a href="<?= base_url('input-data') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-table me-1"></i> Kembali ke Tabel
        </a>
    </div>

    <!-- Navigasi grafik -->
    <div class="graph-nav mb-4">
        <div class="btn-group w-100" role="group">
            <?php for($i=1;$i<=2;$i++): ?>
                <a href="<?= base_url('grafik/'.$i) ?>" class="btn <?= $current_graph_set==$i ? 'btn-primary':'btn-outline-primary' ?>">
                    <i class="fas fa-chart-simple me-1"></i> Set Grafik <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Dropdown tahun untuk Set Grafik 1 -->
    <?php if($current_graph_set == 1 && !empty($tahunAvailable)): ?>
    <div class="mb-4">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownTahun" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Tahun
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownTahun">
                <li><a class="dropdown-item tahun-btn" href="#" data-tahun="all">All</a></li>
                <?php foreach($tahunAvailable as $tahun): ?>
                    <li><a class="dropdown-item tahun-btn" href="#" data-tahun="<?= $tahun ?>"><?= $tahun ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Judul set grafik -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <?= $grafana_title ?>
    </div>

    <!-- Container grafik -->
    <?php if($current_graph_set == 2): ?>
        <!-- Hanya 1 grafik penuh -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-simple me-1"></i>
                    <?= $panel_titles[0] ?? 'Grafik Utama' ?>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="graph-iframe-container" style="height: 600px;">
                    <?php if (!empty($grafana_urls[0])): ?>
                        <iframe 
                            class="graph-iframe h-100 w-100" 
                            src="<?= $grafana_urls[0] ?>" 
                            data-index="0"
                            frameborder="0"></iframe>
                    <?php else: ?>
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center text-muted">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <p>Grafik tidak tersedia</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Set grafik normal (4 grafik grid) -->
        <div class="row row-cols-1 row-cols-md-2 g-4 mb-4" id="grafik-container">
            <?php foreach ($grafana_urls as $index => $url): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-simple me-1"></i>
                                <?= $panel_titles[$index] ?? 'Panel '.($index+1) ?>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="graph-iframe-container" style="height: 300px;">
                                <?php if (!empty($url)): ?>
                                    <iframe 
                                        class="graph-iframe h-100 w-100" 
                                        src="<?= $url ?>" 
                                        data-index="<?= $index ?>"
                                        frameborder="0"></iframe>
                                <?php else: ?>
                                    <div class="d-flex justify-content-center align-items-center h-100">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                            <p>Grafik tidak tersedia</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Keterangan grafik -->
    <div class="alert alert-secondary">
        <i class="fas fa-database me-2"></i>
        Data grafik ditampilkan langsung dari sistem monitoring Grafana. 
        Grafik akan diperbarui secara otomatis sesuai dengan data terbaru.
    </div>
</div>

<?= $this->include('layouts/footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Refresh iframe setiap 5 menit
    setInterval(function() {
        document.querySelectorAll('.graph-iframe').forEach(iframe => {
            if (iframe.src) iframe.src = iframe.src;
        });
    }, 300000);

    // Dropdown tahun (hanya untuk set 1)
    document.querySelectorAll('.tahun-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tahun = this.dataset.tahun;
            document.querySelectorAll('.tahun-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.graph-iframe').forEach((iframe) => {
                const idx = iframe.dataset.index;
                <?php if(isset($grafanaUrlsTahun) && isset($grafanaUrlsAll)): ?>
                if(tahun === 'all') {
                    iframe.src = <?= json_encode($grafanaUrlsAll) ?>[idx];
                } else {
                    iframe.src = <?= json_encode($grafanaUrlsTahun) ?>[idx].replace('2023', tahun);
                }
                <?php endif; ?>
            });
        });
    });
});
</script>
