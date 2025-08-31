<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Rembesan - PT Indonesia Power</title>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/home.css') ?>">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 15px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(120deg, #0d6efd, #0a58ca);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 25px;
        }
        
        .card-header h3 {
            font-weight: 600;
            margin: 0;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .section-title {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
            color: var(--primary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
        }
        
        .required-label::after {
            content: " *";
            color: var(--danger-color);
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        .numeric-input {
            text-align: right;
            font-family: monospace;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-action {
            min-width: 140px;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #146c43;
            border-color: #13653f;
        }
        
        .loading-spinner {
            display: none;
            padding: 30px 0;
        }
        
        .alert-container {
            position: fixed;
            top: 100px;
            right: 30px;
            z-index: 1050;
            min-width: 350px;
        }
        
        .alert {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-group-icon .form-control {
            padding-left: 40px;
        }
        
        .input-group-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }
        
        /* Perbaikan untuk select dengan ikon */
        .input-group-icon .form-select {
            padding-left: 40px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        
        .form-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
        }
        
        /* Gaya untuk grup nilai dan kode yang berdampingan */
        .value-code-group {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        
        .value-code-group .form-group {
            flex: 2;
        }
        
        .value-code-group .code-group {
            flex: 1;
        }
        
        .value-code-group .form-label {
            white-space: nowrap;
        }
        
        /* Gaya untuk grup SR */
        .sr-group {
            background-color: rgba(13, 110, 253, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(13, 110, 253, 0.2);
        }
        
        .sr-group-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ced4da;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
            
            .alert-container {
                left: 15px;
                right: 15px;
                min-width: auto;
            }
            
            .value-code-group {
                flex-direction: column;
                align-items: stretch;
            }
        }
        
        /* Validasi form */
        .is-invalid {
            border-color: var(--danger-color) !important;
        }
        
        .invalid-feedback {
            display: none;
            color: var(--danger-color);
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>

    <?= $this->include('layouts/header'); ?>
    
    <!-- Main Content -->
    <div class="container form-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Data Pengukuran Rembesan</h3>
            </div>
            <div class="card-body">
                <!-- Alert Container -->
                <div class="alert-container">
                    <div id="liveAlert" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="alert-message"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                
                <!-- Form Edit Data - DIUBAH: Hapus _method PUT dan ganti dengan POST biasa -->
                <form id="editForm" action="<?= base_url('data/update/' . $pengukuran['id']) ?>" method="post">
                    <!-- DIUBAH: Hapus input hidden _method -->
                    <input type="hidden" id="dataId" value="<?= $pengukuran['id'] ?>">
                    
                    <!-- Data Pengukuran -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-chart-line"></i> Data Pengukuran</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tahun" class="form-label required-label">Tahun</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="number" class="form-control" id="tahun" name="tahun" 
                                           value="<?= isset($pengukuran['tahun']) ? esc($pengukuran['tahun']) : '' ?>" 
                                           min="2000" max="2100" required>
                                    <div class="invalid-feedback">Tahun harus diisi antara 2000-2100</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="bulan" class="form-label required-label">Bulan</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar"></i>
                                    <select class="form-select" id="bulan" name="bulan" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php
                                        $bulanList = [
                                            'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 
                                            'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'
                                        ];
                                        foreach ($bulanList as $index => $bulanItem): 
                                            $bulanValue = $index + 1;
                                        ?>
                                            <option value="<?= $bulanItem ?>" 
                                                <?= (isset($pengukuran['bulan']) && $pengukuran['bulan'] == $bulanItem) ? 'selected' : '' ?>>
                                                <?= $bulanItem ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Bulan harus dipilih</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="periode" class="form-label required-label">Periode</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-clock"></i>
                                    <select class="form-select" id="periode" name="periode" required>
                                        <option value="">Pilih Periode</option>
                                        <?php
                                        $periodeList = ['TW-1', 'TW-2', 'TW-3', 'TW-4'];
                                        foreach ($periodeList as $periodeItem): 
                                        ?>
                                            <option value="<?= $periodeItem ?>" 
                                                <?= (isset($pengukuran['periode']) && $pengukuran['periode'] == $periodeItem) ? 'selected' : '' ?>>
                                                <?= $periodeItem ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Periode harus dipilih</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal" class="form-label">Tanggal Pengukuran</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-day"></i>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                           value="<?= isset($pengukuran['tanggal']) ? esc($pengukuran['tanggal']) : '' ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="tma_waduk" class="form-label">TMA Waduk (m)</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-ruler"></i>
                                    <input type="number" step="0.01" class="form-control numeric-input" id="tma_waduk" name="tma_waduk" 
                                           value="<?= isset($pengukuran['tma_waduk']) && $pengukuran['tma_waduk'] != 0 ? $pengukuran['tma_waduk'] : '' ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="curah_hujan" class="form-label">Curah Hujan (mm)</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-cloud-rain"></i>
                                    <input type="number" step="0.1" class="form-control numeric-input" id="curah_hujan" name="curah_hujan" 
                                           value="<?= isset($pengukuran['curah_hujan']) && $pengukuran['curah_hujan'] != 0 ? $pengukuran['curah_hujan'] : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Thomson Weir -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-tint"></i> Data Thomson Weir</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="a1_r" class="form-label">A1 R (mm)</label>
                                <input type="number" step="0.01" class="form-control numeric-input" id="a1_r" name="a1_r" 
                                       value="<?= isset($thomson['a1_r']) && $thomson['a1_r'] != 0 ? $thomson['a1_r'] : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="a1_l" class="form-label">A1 L (mm)</label>
                                <input type="number" step="0.01" class="form-control numeric-input" id="a1_l" name="a1_l" 
                                       value="<?= isset($thomson['a1_l']) && $thomson['a1_l'] != 0 ? $thomson['a1_l'] : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="b1" class="form-label">B1 (mm)</label>
                                <input type="number" step="0.01" class="form-control numeric-input" id="b1" name="b1" 
                                       value="<?= isset($thomson['b1']) && $thomson['b1'] != 0 ? $thomson['b1'] : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="b3" class="form-label">B3 (mm)</label>
                                <input type="number" step="0.01" class="form-control numeric-input" id="b3" name="b3" 
                                       value="<?= isset($thomson['b3']) && $thomson['b3'] != 0 ? $thomson['b3'] : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="b5" class="form-label">B5 (mm)</label>
                                <input type="number" step="0.01" class="form-control numeric-input" id="b5" name="b5" 
                                       value="<?= isset($thomson['b5']) && $thomson['b5'] != 0 ? $thomson['b5'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data SR - DIUBAH: Dipindahkan sebelum Bocoran Baru -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-map-marked-alt"></i> Data SR</h4>
                        <div class="form-grid">
                            <?php
                            $srList = [1, 40, 66, 68, 70, 79, 81, 83, 85, 92, 94, 96, 98, 100, 102, 104, 106];
                            foreach ($srList as $srNum):
                                $srNilai = isset($sr["sr_{$srNum}_nilai"]) ? $sr["sr_{$srNum}_nilai"] : '';
                                $srKode = isset($sr["sr_{$srNum}_kode"]) ? $sr["sr_{$srNum}_kode"] : '';
                            ?>
                            <div class="sr-group">
                                <div class="sr-group-title">SR <?= $srNum ?></div>
                                <div class="value-code-group">
                                    <div class="form-group">
                                        <label for="sr_<?= $srNum ?>_nilai" class="form-label">Nilai</label>
                                        <input type="number" step="0.01" class="form-control numeric-input" 
                                               id="sr_<?= $srNum ?>_nilai" 
                                               name="sr_<?= $srNum ?>_nilai" 
                                               value="<?= !empty($srNilai) && $srNilai != 0 ? $srNilai : '' ?>">
                                    </div>
                                    <div class="form-group code-group">
                                        <label for="sr_<?= $srNum ?>_kode" class="form-label">Kode</label>
                                        <select class="form-select" id="sr_<?= $srNum ?>_kode" name="sr_<?= $srNum ?>_kode">
                                            <option value="">Pilih</option>
                                            <option value="S" <?= $srKode == 'S' ? 'selected' : '' ?>>S</option>
                                            <option value="M" <?= $srKode == 'M' ? 'selected' : '' ?>>M</option>
                                            <option value="L" <?= $srKode == 'L' ? 'selected' : '' ?>>L</option>
                                            <option value="E" <?= $srKode == 'E' ? 'selected' : '' ?>>E</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Data Bocoran Baru - DIUBAH: Dipindahkan setelah SR -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-water"></i> Data Bocoran Baru</h4>
                        <div class="form-grid">
                            <!-- ELV 624 T1 - Nilai dan Kode berdampingan -->
                            <div class="value-code-group">
                                <div class="form-group">
                                    <label for="elv_624_t1" class="form-label">ELV 624 T1 (m)</label>
                                    <input type="number" step="0.001" class="form-control numeric-input" id="elv_624_t1" name="elv_624_t1" 
                                           value="<?= isset($bocoran['elv_624_t1']) && $bocoran['elv_624_t1'] != 0 ? $bocoran['elv_624_t1'] : '' ?>">
                                </div>
                                <div class="form-group code-group">
                                    <label for="elv_624_t1_kode" class="form-label">Kode</label>
                                    <select class="form-select" id="elv_624_t1_kode" name="elv_624_t1_kode">
                                        <option value="">Pilih</option>
                                        <option value="S" <?= (isset($bocoran['elv_624_t1_kode']) && $bocoran['elv_624_t1_kode'] == 'S') ? 'selected' : '' ?>>S</option>
                                        <option value="M" <?= (isset($bocoran['elv_624_t1_kode']) && $bocoran['elv_624_t1_kode'] == 'M') ? 'selected' : '' ?>>M</option>
                                        <option value="L" <?= (isset($bocoran['elv_624_t1_kode']) && $bocoran['elv_624_t1_kode'] == 'L') ? 'selected' : '' ?>>L</option>
                                        <option value="E" <?= (isset($bocoran['elv_624_t1_kode']) && $bocoran['elv_624_t1_kode'] == 'E') ? 'selected' : '' ?>>E</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- ELV 615 T2 - Nilai dan Kode berdampingan -->
                            <div class="value-code-group">
                                <div class="form-group">
                                    <label for="elv_615_t2" class="form-label">ELV 615 T2 (m)</label>
                                    <input type="number" step="0.001" class="form-control numeric-input" id="elv_615_t2" name="elv_615_t2" 
                                           value="<?= isset($bocoran['elv_615_t2']) && $bocoran['elv_615_t2'] != 0 ? $bocoran['elv_615_t2'] : '' ?>">
                                </div>
                                <div class="form-group code-group">
                                    <label for="elv_615_t2_kode" class="form-label">Kode</label>
                                    <select class="form-select" id="elv_615_t2_kode" name="elv_615_t2_kode">
                                        <option value="">Pilih</option>
                                        <option value="S" <?= (isset($bocoran['elv_615_t2_kode']) && $bocoran['elv_615_t2_kode'] == 'S') ? 'selected' : '' ?>>S</option>
                                        <option value="M" <?= (isset($bocoran['elv_615_t2_kode']) && $bocoran['elv_615_t2_kode'] == 'M') ? 'selected' : '' ?>>M</option>
                                        <option value="L" <?= (isset($bocoran['elv_615_t2_kode']) && $bocoran['elv_615_t2_kode'] == 'L') ? 'selected' : '' ?>>L</option>
                                        <option value="E" <?= (isset($bocoran['elv_615_t2_kode']) && $bocoran['elv_615_t2_kode'] == 'E') ? 'selected' : '' ?>>E</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Pipa P1 - Nilai dan Kode berdampingan -->
                            <div class="value-code-group">
                                <div class="form-group">
                                    <label for="pipa_p1" class="form-label">Pipa P1 (m)</label>
                                    <input type="number" step="0.001" class="form-control numeric-input" id="pipa_p1" name="pipa_p1" 
                                           value="<?= isset($bocoran['pipa_p1']) && $bocoran['pipa_p1'] != 0 ? $bocoran['pipa_p1'] : '' ?>">
                                </div>
                                <div class="form-group code-group">
                                    <label for="pipa_p1_kode" class="form-label">Kode</label>
                                    <select class="form-select" id="pipa_p1_kode" name="pipa_p1_kode">
                                        <option value="">Pilih</option>
                                        <option value="S" <?= (isset($bocoran['pipa_p1_kode']) && $bocoran['pipa_p1_kode'] == 'S') ? 'selected' : '' ?>>S</option>
                                        <option value="M" <?= (isset($bocoran['pipa_p1_kode']) && $bocoran['pipa_p1_kode'] == 'M') ? 'selected' : '' ?>>M</option>
                                        <option value="L" <?= (isset($bocoran['pipa_p1_kode']) && $bocoran['pipa_p1_kode'] == 'L') ? 'selected' : '' ?>>L</option>
                                        <option value="E" <?= (isset($bocoran['pipa_p1_kode']) && $bocoran['pipa_p1_kode'] == 'E') ? 'selected' : '' ?>>E</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <a href="<?= base_url('input-data') ?>" class="btn btn-secondary btn-action">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success btn-action">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div class="text-center mt-4 loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Menyimpan data...</span>
                        </div>
                        <p class="mt-2">Menyimpan perubahan data...</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi form sebelum submit
            function validateForm() {
                let isValid = true;
                
                // Validasi tahun
                const tahun = document.getElementById('tahun');
                if (!tahun.value || tahun.value < 2000 || tahun.value > 2100) {
                    tahun.classList.add('is-invalid');
                    isValid = false;
                } else {
                    tahun.classList.remove('is-invalid');
                }
                
                // Validasi bulan
                const bulan = document.getElementById('bulan');
                if (!bulan.value) {
                    bulan.classList.add('is-invalid');
                    isValid = false;
                } else {
                    bulan.classList.remove('is-invalid');
                }
                
                // Validasi periode
                const periode = document.getElementById('periode');
                if (!periode.value) {
                    periode.classList.add('is-invalid');
                    isValid = false;
                } else {
                    periode.classList.remove('is-invalid');
                }
                
                return isValid;
            }
            
            // Handle form submission - DIUBAH: Gunakan POST biasa
            document.getElementById('editForm').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    showAlert('Harap periksa kembali data yang wajib diisi', 'danger');
                    return;
                }
                
                // Show loading spinner
                document.getElementById('loadingSpinner').style.display = 'block';
                
                // Submit form via AJAX - DIUBAH: Hapus _method dan gunakan POST biasa
                e.preventDefault();
                
                const formData = new FormData(this);
                const id = document.getElementById('dataId').value;
                
                // DIUBAH: Hapus _method dari FormData karena kita menggunakan POST biasa
                // formData.delete('_method'); // Tidak perlu lagi karena kita sudah menghapus input _method
                
                fetch(this.action, {
                    method: 'POST', // Tetap gunakan POST
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Data berhasil diperbarui', 'success');
                        setTimeout(() => {
                            window.location.href = '<?= base_url('input-data') ?>';
                        }, 1500);
                    } else {
                        showAlert('Gagal memperbarui data: ' + (data.message || 'Terjadi kesalahan'), 'danger');
                        document.getElementById('loadingSpinner').style.display = 'none';
                        
                        // Tampilkan error validasi jika ada
                        if (data.errors) {
                            for (const field in data.errors) {
                                const input = document.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    // Buat elemen untuk menampilkan pesan error
                                    let feedback = input.nextElementSibling;
                                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                                        feedback = document.createElement('div');
                                        feedback.className = 'invalid-feedback';
                                        input.parentNode.appendChild(feedback);
                                    }
                                    feedback.textContent = data.errors[field];
                                }
                                showAlert(data.errors[field], 'danger');
                            }
                        }
                    }
                })
                .catch(error => {
                    showAlert('Terjadi kesalahan: ' + error, 'danger');
                    document.getElementById('loadingSpinner').style.display = 'none';
                });
            });
            
            // Auto-clear zero values on focus
            document.querySelectorAll('.numeric-input').forEach(input => {
                input.addEventListener('focus', function() {
                    if (this.value === '0' || this.value === '0.00' || this.value === '0.0') {
                        this.value = '';
                    }
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === '' || this.value === '0' || this.value === '0.00' || this.value === '0.0') {
                        this.value = '';
                    } else if (this.value) {
                        // Format angka sesuai step
                        const decimalPlaces = this.step.includes('.') ? this.step.split('.')[1].length : 0;
                        this.value = parseFloat(this.value).toFixed(decimalPlaces);
                    }
                });
            });
            
            // Function to show alert
            function showAlert(message, type) {
                const alert = document.getElementById('liveAlert');
                alert.className = `alert alert-${type} alert-dismissible fade show`;
                document.getElementById('alert-message').textContent = message;
                alert.style.display = 'block';
                
                // Auto hide after 5 seconds
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            }
            
            // Hapus kelas invalid saat user mulai mengisi
            document.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
    
    <?= $this->include('layouts/footer'); ?>
</body>
</html>