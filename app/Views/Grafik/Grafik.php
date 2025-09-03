<?= $this->include('layouts/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>Grafik Rembesan Bendungan
        </h2>

        <a href="<?= base_url('input-data') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-table me-1"></i> Kembali ke Tabel
        </a>
    </div>

    <!-- Navigasi grafik -->
    <div class="graph-nav mb-4">
        <div class="btn-group w-100" role="group">
            <a href="<?= base_url('grafik/1') ?>" class="btn <?= $current_graph_set == 1 ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-water me-1"></i> Set Grafik 1
            </a>
            <a href="<?= base_url('grafik/2') ?>" class="btn <?= $current_graph_set == 2 ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-chart-bar me-1"></i> Set Grafik 2
            </a>
            <a href="<?= base_url('grafik/3') ?>" class="btn <?= $current_graph_set == 3 ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-chart-pie me-1"></i> Set Grafik 3
            </a>
            <a href="<?= base_url('grafik/4') ?>" class="btn <?= $current_graph_set == 4 ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-tint me-1"></i> Set Grafik 4
            </a>
        </div>
    </div>

    <!-- Judul set grafik -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <?= $grafana_title ?>
    </div>

    <!-- Container untuk 4 grafik -->
    <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
        <?php foreach ($grafana_urls as $index => $url): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-simple me-1"></i>
                            <?= $panel_titles[$index] ?>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="graph-iframe-container" style="height: 300px;">
                            <?php if (!empty($url)): ?>
                                <iframe class="graph-iframe h-100 w-100" src="<?= $url ?>" frameborder="0"></iframe>
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
    
    <!-- Keterangan grafik -->
    <div class="alert alert-secondary">
        <i class="fas fa-database me-2"></i>
        Data grafik ditampilkan langsung dari sistem monitoring Grafana. 
        Setiap set grafik menampilkan 4 visualisasi berbeda yang terkait dengan tema yang sama.
        Grafik akan diperbarui secara otomatis sesuai dengan data terbaru.
    </div>
</div>

<?= $this->include('layouts/footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Refresh iframe setiap 5 menit untuk update data
    setInterval(function() {
        document.querySelectorAll('.graph-iframe').forEach(iframe => {
            if (iframe.src) {
                iframe.src = iframe.src;
            }
        });
    }, 300000); // 300000 ms = 5 menit
});
</script>