<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data BTM - PT Indonesia Power</title>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS sama persis dengan halaman edit */
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
            max-width: 1200px;
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
            padding: 20px 30px;
        }
        
        .card-header h3 {
            font-weight: 600;
            margin: 0;
            font-size: 1.5rem;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .section-title {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 12px;
            margin-bottom: 25px;
            color: var(--primary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            font-size: 1.3rem;
        }
        
        .section-title i {
            margin-right: 12px;
            font-size: 1.3em;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
            font-size: 0.95rem;
        }
        
        .required-label::after {
            content: " *";
            color: var(--danger-color);
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        .numeric-input {
            text-align: right;
            font-family: monospace;
        }
        
        /* Hilangkan tanda panah atas-bawah pada input number */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
        }
        
        .btn-action {
            min-width: 140px;
            border-radius: 6px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #146c43;
            border-color: #13653f;
            transform: translateY(-1px);
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
            border: none;
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-group-icon .form-control {
            padding-left: 45px;
        }
        
        .input-group-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
            font-size: 1.1rem;
        }
        
        .input-group-icon .form-select {
            padding-left: 45px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        
        .form-section {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #e1e5e9;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .device-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid var(--primary-color);
        }
        
        .horizontal-readings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .reading-item {
            text-align: center;
        }
        
        .reading-item .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .reading-item .form-control {
            text-align: center;
            font-weight: 500;
        }
        
        .field-group {
            margin-bottom: 5px;
        }
        
        .is-invalid {
            border-color: var(--danger-color) !important;
        }
        
        .invalid-feedback {
            display: none;
            color: var(--danger-color);
            font-size: 0.8rem;
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

        .bt-section {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
        }

        .bt-section-header {
            background: linear-gradient(135deg, #2c5fa8, #4a7bc8);
            color: white;
            padding: 12px 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 6px 6px 0 0;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .horizontal-readings {
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
            
            .card-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <?= $this->include('layouts/header'); ?>
    
    <!-- Main Content -->
    <div class="container form-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Data Bubble Tilt Meter (BTM)</h3>
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
                
                <!-- Form Tambah Data -->
                <form id="addForm" action="<?= base_url('btm/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <!-- Data Pengukuran -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-chart-line"></i> Data Pengukuran</h4>
                        <div class="form-grid">
                            <div class="field-group">
                                <label for="tahun" class="form-label required-label">Tahun</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="number" class="form-control" id="tahun" name="tahun" 
                                           min="2000" max="2100" required 
                                           value="<?= date('Y') ?>">
                                    <div class="invalid-feedback">Tahun harus diisi antara 2000-2100</div>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label for="periode" class="form-label required-label">Periode</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-clock"></i>
                                    <select class="form-select" id="periode" name="periode" required>
                                        <option value="">Pilih Periode</option>
                                        <option value="TW-1">TW-1</option>
                                        <option value="TW-2">TW-2</option>
                                        <option value="TW-3">TW-3</option>
                                        <option value="TW-4">TW-4</option>
                                    </select>
                                    <div class="invalid-feedback">Periode harus dipilih</div>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label for="tanggal" class="form-label required-label">Tanggal Pengukuran</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-day"></i>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required 
                                           value="<?= date('Y-m-d') ?>">
                                    <div class="invalid-feedback">Tanggal harus diisi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data BT-1 sampai BT-4 -->
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="bt-section">
                        <div class="bt-section-header">
                            <i class="fas fa-sensor me-2"></i>BT-<?= $i ?> - Bubble Tilt Meter
                        </div>
                        <div class="horizontal-readings">
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_US_GP" class="form-label">US GP</label>
                                <input type="text" class="form-control numeric-input" id="bt<?= $i ?>_US_GP" name="bt<?= $i ?>_US_GP"
                                       pattern="-?[0-9]*\.?[0-9]*" title="Hanya angka, titik, dan minus di depan diperbolehkan"
                                       placeholder="0.000" 
                                       value="0">
                            </div>
                            
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_US_Arah" class="form-label">US Arah</label>
                                <select class="form-select" id="bt<?= $i ?>_US_Arah" name="bt<?= $i ?>_US_Arah">
                                    <option value="U" selected>U</option>
                                    <option value="S">S</option>
                                </select>
                            </div>
                            
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_TB_GP" class="form-label">TB GP</label>
                                <input type="text" class="form-control numeric-input" id="bt<?= $i ?>_TB_GP" name="bt<?= $i ?>_TB_GP"
                                       pattern="-?[0-9]*\.?[0-9]*" title="Hanya angka, titik, dan minus di depan diperbolehkan"
                                       placeholder="0.000" 
                                       value="0">
                            </div>
                            
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_TB_Arah" class="form-label">TB Arah</label>
                                <select class="form-select" id="bt<?= $i ?>_TB_Arah" name="bt<?= $i ?>_TB_Arah">
                                    <option value="T" selected>T</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                    
                    <!-- Data BT-6 sampai BT-8 -->
                    <?php for ($i = 6; $i <= 8; $i++): ?>
                    <div class="bt-section">
                        <div class="bt-section-header">
                            <i class="fas fa-sensor me-2"></i>BT-<?= $i ?> - Bubble Tilt Meter
                        </div>
                        <div class="horizontal-readings">
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_US_GP" class="form-label">US GP</label>
                                <input type="text" class="form-control numeric-input" id="bt<?= $i ?>_US_GP" name="bt<?= $i ?>_US_GP"
                                       pattern="-?[0-9]*\.?[0-9]*" title="Hanya angka, titik, dan minus di depan diperbolehkan"
                                       placeholder="0.000" 
                                       value="0">
                            </div>
                            
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_US_Arah" class="form-label">US Arah</label>
                                <select class="form-select" id="bt<?= $i ?>_US_Arah" name="bt<?= $i ?>_US_Arah">
                                    <option value="U" selected>U</option>
                                    <option value="S">S</option>
                                </select>
                            </div>
                            
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_TB_GP" class="form-label">TB GP</label>
                                <input type="text" class="form-control numeric-input" id="bt<?= $i ?>_TB_GP" name="bt<?= $i ?>_TB_GP"
                                       pattern="-?[0-9]*\.?[0-9]*" title="Hanya angka, titik, dan minus di depan diperbolehkan"
                                       placeholder="0.000" 
                                       value="0">
                            </div>
                            
                            <div class="reading-item">
                                <label for="bt<?= $i ?>_TB_Arah" class="form-label">TB Arah</label>
                                <select class="form-select" id="bt<?= $i ?>_TB_Arah" name="bt<?= $i ?>_TB_Arah">
                                    <option value="T" selected>T</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                    
                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <a href="<?= base_url('btm') ?>" class="btn btn-secondary btn-action">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Data
                        </a>
                        <button type="submit" class="btn btn-success btn-action" id="submitBtn">
                            <i class="fas fa-save me-2"></i> Simpan Data
                        </button>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div class="text-center mt-4 loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Menyimpan data...</span>
                        </div>
                        <p class="mt-2 text-muted">Menyimpan data BTM dan menghitung...</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addForm');
        const submitBtn = document.getElementById('submitBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const duplicateWarning = document.getElementById('duplicateWarning');
        const duplicateMessage = document.getElementById('duplicateMessage');
        const liveAlert = document.getElementById('liveAlert');
        const alertMessage = document.getElementById('alert-message');

        // Format input angka
        document.querySelectorAll('.numeric-input').forEach(input => {
            input.addEventListener('input', function() {
                // Izinkan angka, titik, dan minus di depan
                let value = this.value;
                
                // Hapus karakter yang tidak diinginkan
                let cleaned = value.replace(/[^\d.\-]/g, '');
                
                // Pastikan hanya satu minus di depan
                if (cleaned.includes('-')) {
                    cleaned = '-' + cleaned.replace(/-/g, '');
                }
                
                // Pastikan hanya satu titik desimal
                let parts = cleaned.split('.');
                if (parts.length > 2) {
                    cleaned = parts[0] + '.' + parts.slice(1).join('');
                }
                
                this.value = cleaned;
            });
            
            // Validasi saat blur
            input.addEventListener('blur', function() {
                let value = this.value;
                if (value && !/^-?\d*\.?\d*$/.test(value)) {
                    showAlert('Format tidak valid. Hanya angka, titik, dan minus di depan yang diperbolehkan.', 'danger');
                    this.focus();
                }
            });
        });

        // Fungsi untuk menampilkan alert
        function showAlert(message, type = 'success') {
            alertMessage.textContent = message;
            liveAlert.className = `alert alert-${type} alert-dismissible fade show`;
            liveAlert.style.display = 'block';
            
            // Auto hide setelah 5 detik
            setTimeout(() => {
                liveAlert.style.display = 'none';
            }, 5000);
        }

        // Cek duplikat ketika tahun, periode, atau tanggal berubah
        function checkDuplicate() {
            const tahun = document.getElementById('tahun').value;
            const periode = document.getElementById('periode').value;
            const tanggal = document.getElementById('tanggal').value;

            if (tahun && periode && tanggal) {
                fetch('<?= base_url('btm/check-duplicate') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `tahun=${tahun}&periode=${periode}&tanggal=${tanggal}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
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

        // Event listeners untuk cek duplikat
        document.getElementById('tahun').addEventListener('change', checkDuplicate);
        document.getElementById('periode').addEventListener('change', checkDuplicate);
        document.getElementById('tanggal').addEventListener('change', checkDuplicate);

        // Submit form
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

            // Validasi format angka
            const numericInputs = document.querySelectorAll('.numeric-input');
            let hasInvalidInput = false;

            numericInputs.forEach(input => {
                if (input.value && !/^-?\d*\.?\d*$/.test(input.value)) {
                    input.classList.add('is-invalid');
                    hasInvalidInput = true;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (hasInvalidInput) {
                showAlert('Format angka tidak valid! Hanya angka, titik, dan minus di depan yang diperbolehkan.', 'danger');
                return;
            }

            // Loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            loadingSpinner.style.display = 'block';

            // Submit form
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.redirected) {
                    // Jika redirect, arahkan ke halaman tujuan
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    showAlert('Data BTM berhasil disimpan dan perhitungan dihitung! Mengalihkan...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= base_url('btm') ?>';
                    }, 1500);
                } else if (data && !data.success) {
                    showAlert('Error: ' + (data.message || 'Terjadi kesalahan saat menyimpan data'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan jaringan: ' + error.message, 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Data';
                loadingSpinner.style.display = 'none';
            });
        });

        // Jalankan cek duplikat saat page load
        checkDuplicate();
    });
    </script>
</body>
</html>