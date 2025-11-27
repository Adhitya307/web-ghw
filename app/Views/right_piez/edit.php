<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Piezometer - PT Indonesia Power</title>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .titik-group {
            background-color: rgba(13, 110, 253, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(13, 110, 253, 0.2);
        }
        
        .titik-group-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ced4da;
        }
        
        .value-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
            
            .value-group {
                grid-template-columns: 1fr;
            }
        }
        
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
        
        .duplicate-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        .calculation-notification {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>

    <?= $this->include('layouts/header'); ?>
    
    <!-- Main Content -->
    <div class="container form-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Data Piezometer - Right Bank</h3>
            </div>
            <div class="card-body">
                <!-- Alert Container -->
                <div class="alert-container">
                    <div id="liveAlert" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="alert-message"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Duplicate Warning -->
                <div id="duplicateWarning" class="duplicate-warning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle text-warning me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1 text-warning">Data Pengukuran Sudah Ada!</h6>
                            <p class="mb-0 small" id="duplicateMessage"></p>
                        </div>
                    </div>
                </div>

                <!-- Calculation Notification -->
                <div id="calculationNotification" class="calculation-notification">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calculator text-info me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1 text-info">Perhitungan Otomatis</h6>
                            <p class="mb-0 small" id="calculationMessage"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Edit Data -->
                <form id="editForm" action="<?= base_url('right-piez/update/' . $pengukuran['id_pengukuran']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="current_id" value="<?= $pengukuran['id_pengukuran'] ?>">

                    <!-- Data Pengukuran -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-chart-line"></i> Data Pengukuran</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tahun" class="form-label required-label">Tahun</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="number" class="form-control" id="tahun" name="tahun" 
                                           min="2000" max="2100" required value="<?= $pengukuran['tahun'] ?>">
                                    <div class="invalid-feedback">Tahun harus diisi antara 2000-2100</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="periode" class="form-label required-label">Periode</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-clock"></i>
                                    <select class="form-select" id="periode" name="periode" required>
                                        <option value="">Pilih Periode</option>
                                        <option value="TW-1" <?= $pengukuran['periode'] == 'TW-1' ? 'selected' : '' ?>>TW-1</option>
                                        <option value="TW-2" <?= $pengukuran['periode'] == 'TW-2' ? 'selected' : '' ?>>TW-2</option>
                                        <option value="TW-3" <?= $pengukuran['periode'] == 'TW-3' ? 'selected' : '' ?>>TW-3</option>
                                        <option value="TW-4" <?= $pengukuran['periode'] == 'TW-4' ? 'selected' : '' ?>>TW-4</option>
                                    </select>
                                    <div class="invalid-feedback">Periode harus dipilih</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal" class="form-label required-label">Tanggal Pengukuran</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-day"></i>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= $pengukuran['tanggal'] ?>">
                                    <div class="invalid-feedback">Tanggal harus diisi</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="tma" class="form-label">TMA</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-ruler-combined"></i>
                                    <input type="text" class="form-control numeric-input" id="tma" name="tma" 
                                           pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                           placeholder="0.000" value="<?= $pengukuran['tma'] ?? '' ?>">
                                    <div class="invalid-feedback">Hanya angka dan titik desimal yang diperbolehkan</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ch_hujan" class="form-label">CH Hujan</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-cloud-rain"></i>
                                    <input type="text" class="form-control numeric-input" id="ch_hujan" name="ch_hujan" 
                                           pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                           placeholder="0.000" value="<?= $pengukuran['ch_hujan'] ?? '' ?>">
                                    <div class="invalid-feedback">Hanya angka dan titik desimal yang diperbolehkan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bacaan Metrik (Feet & Inch) -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-ruler"></i> Bacaan Metrik</h4>
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Untuk titik yang "KERING", isi Feet dengan teks <strong>"KERING"</strong> dan Inch dengan <strong>0</strong>.
                        </div>
                        
                        <div class="form-grid">
                            <?php 
                            $titikList = ['R-01', 'R-02', 'R-03', 'R-04', 'R-05', 'R-06', 
                                         'R-07', 'R-08', 'R-09', 'R-10', 'R-11', 'R-12', 
                                         'IPZ-01', 'PZ-04'];
                            
                            foreach($titikList as $titik): 
                                $currentFeet = $pembacaan[$titik]['feet'] ?? '';
                                $currentInch = $pembacaan[$titik]['inch'] ?? '0';
                            ?>
                            <div class="titik-group">
                                <div class="titik-group-title"><?= $titik ?></div>
                                
                                <div class="value-group">
                                    <div class="form-group">
                                        <label for="feet_<?= str_replace('-', '_', $titik) ?>" class="form-label">Feet</label>
                                        <input type="text" class="form-control pembacaan-input" 
                                               id="feet_<?= str_replace('-', '_', $titik) ?>" 
                                               name="pembacaan[<?= $titik ?>][feet]" 
                                               data-titik="<?= $titik ?>"
                                               placeholder="Angka atau KERING" value="<?= $currentFeet ?>">
                                        <div class="invalid-feedback">Format tidak valid</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="inch_<?= str_replace('-', '_', $titik) ?>" class="form-label">Inch</label>
                                        <input type="text" class="form-control numeric-input pembacaan-input" 
                                               id="inch_<?= str_replace('-', '_', $titik) ?>" 
                                               name="pembacaan[<?= $titik ?>][inch]" 
                                               data-titik="<?= $titik ?>"
                                               pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                               placeholder="0.00" value="<?= $currentInch ?>">
                                        <div class="invalid-feedback">Hanya angka dan titik desimal yang diperbolehkan</div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <a href="<?= base_url('right-piez') ?>" class="btn btn-secondary btn-action">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success btn-action" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan & Hitung Ulang
                        </button>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div class="text-center mt-4 loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Menyimpan data...</span>
                        </div>
                        <p class="mt-2">Menyimpan data dan melakukan perhitungan ulang...</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const duplicateWarning = document.getElementById('duplicateWarning');
    const duplicateMessage = document.getElementById('duplicateMessage');
    const liveAlert = document.getElementById('liveAlert');
    const alertMessage = document.getElementById('alert-message');

    // Format input angka
    document.querySelectorAll('.numeric-input').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d.]/g, '')
                                 .replace(/(\..*)\./g, '$1');
        });
    });

    // Fungsi untuk menampilkan alert
    function showAlert(message, type = 'success') {
        alertMessage.textContent = message;
        liveAlert.className = `alert alert-${type} alert-dismissible fade show`;
        liveAlert.style.display = 'block';
        
        setTimeout(() => {
            liveAlert.style.display = 'none';
        }, 5000);
    }

    // Cek duplikat ketika tahun, periode, atau tanggal berubah (untuk edit)
    function checkDuplicateEdit() {
        const tahun = document.getElementById('tahun').value;
        const periode = document.getElementById('periode').value;
        const tanggal = document.getElementById('tanggal').value;
        const current_id = document.querySelector('input[name="current_id"]').value;

        if (tahun && periode && tanggal && current_id) {
            fetch('<?= base_url('right-piez/check-duplicate-edit') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `tahun=${tahun}&periode=${periode}&tanggal=${tanggal}&current_id=${current_id}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.isDuplicate) {
                    duplicateWarning.style.display = 'block';
                    duplicateMessage.textContent = `Data dengan Tahun: ${tahun}, Periode: ${periode}, Tanggal: ${tanggal} sudah ada dalam database.`;
                } else {
                    duplicateWarning.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error checking duplicate:', error);
            });
        }
    }

    // Event listeners untuk cek duplikat edit
    document.getElementById('tahun').addEventListener('change', checkDuplicateEdit);
    document.getElementById('periode').addEventListener('change', checkDuplicateEdit);
    document.getElementById('tanggal').addEventListener('change', checkDuplicateEdit);

    // Submit form dengan AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi form
        const tahun = document.getElementById('tahun').value;
        const periode = document.getElementById('periode').value;
        const tanggal = document.getElementById('tanggal').value;

        if (!tahun || !periode || !tanggal) {
            showAlert('Harap isi semua field yang wajib diisi!', 'danger');
            return;
        }

        // Validasi format angka untuk input numerik
        const numericInputs = document.querySelectorAll('.numeric-input');
        let hasInvalidInput = false;
        
        numericInputs.forEach(input => {
            if (input.value && !/^\d*\.?\d*$/.test(input.value)) {
                input.classList.add('is-invalid');
                hasInvalidInput = true;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (hasInvalidInput) {
            showAlert('Format angka tidak valid! Hanya angka dan titik desimal yang diperbolehkan.', 'danger');
            return;
        }

        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
        loadingSpinner.style.display = 'block';

        // Submit data dengan AJAX
        const formData = new FormData(form);
        
        fetch('<?= base_url('right-piez/update/' . $pengukuran['id_pengukuran']) ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '<?= base_url('right-piez') ?>';
                }, 2000);
            } else {
                throw new Error(data.message || 'Gagal memperbarui data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Perubahan & Hitung Ulang';
            loadingSpinner.style.display = 'none';
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
                const decimalPlaces = this.step && this.step.includes('.') ? this.step.split('.')[1].length : 2;
                this.value = parseFloat(this.value).toFixed(decimalPlaces);
            }
        });
    });

    // Hapus kelas invalid saat user mulai mengisi
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Handle input untuk feet yang bisa berisi "KERING"
    document.querySelectorAll('input[name^="pembacaan"][name$="[feet]"]').forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value.toUpperCase();
            if (value === 'KERING' || value === 'KERIN' || value === 'KERI' || value === 'KER') {
                this.value = 'KERING';
                const titik = this.getAttribute('data-titik');
                const inchInput = document.querySelector(`input[name="pembacaan[${titik}][inch]"]`);
                if (inchInput) {
                    inchInput.value = '0';
                }
            }
        });
    });
});
</script>

    <?= $this->include('layouts/footer'); ?>
</body>
</html>