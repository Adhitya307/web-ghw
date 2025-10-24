<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data HDM - PT Indonesia Power</title>
    
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
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
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
                <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Data Horizontal Displacement Meter</h3>
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
                <form id="createForm" action="<?= base_url('horizontal-displacement/store') ?>" method="post">
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
                                           min="2000" max="2100" required value="<?= date('Y') ?>">
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
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                                    <div class="invalid-feedback">Tanggal harus diisi</div>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label for="dma" class="form-label required-label">DMA</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-ruler-combined"></i>
                                    <input type="text" class="form-control numeric-input" id="dma" name="dma" required 
                                           pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                           placeholder="0.000">
                                    <div class="invalid-feedback">DMA harus diisi dengan angka (gunakan titik untuk desimal)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data ELV 625 Horizontal Displacement -->
                    <div class="form-section device-section">
                        <h4 class="section-title"><i class="fas fa-arrows-alt-h"></i> ELV 625 Horizontal Displacement</h4>
                        <div class="horizontal-readings">
                            <div class="reading-item">
                                <label for="elv625_hv1" class="form-label">HV 1</label>
                                <input type="text" class="form-control numeric-input" id="elv625_hv1" name="elv625_hv1"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                            
                            <div class="reading-item">
                                <label for="elv625_hv2" class="form-label">HV 2</label>
                                <input type="text" class="form-control numeric-input" id="elv625_hv2" name="elv625_hv2"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                            
                            <div class="reading-item">
                                <label for="elv625_hv3" class="form-label">HV 3</label>
                                <input type="text" class="form-control numeric-input" id="elv625_hv3" name="elv625_hv3"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data ELV 600 Horizontal Displacement -->
                    <div class="form-section device-section">
                        <h4 class="section-title"><i class="fas fa-arrows-alt-h"></i> ELV 600 Horizontal Displacement</h4>
                        <div class="horizontal-readings">
                            <div class="reading-item">
                                <label for="elv600_hv1" class="form-label">HV 1</label>
                                <input type="text" class="form-control numeric-input" id="elv600_hv1" name="elv600_hv1"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                            
                            <div class="reading-item">
                                <label for="elv600_hv2" class="form-label">HV 2</label>
                                <input type="text" class="form-control numeric-input" id="elv600_hv2" name="elv600_hv2"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                            
                            <div class="reading-item">
                                <label for="elv600_hv3" class="form-label">HV 3</label>
                                <input type="text" class="form-control numeric-input" id="elv600_hv3" name="elv600_hv3"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                            
                            <div class="reading-item">
                                <label for="elv600_hv4" class="form-label">HV 4</label>
                                <input type="text" class="form-control numeric-input" id="elv600_hv4" name="elv600_hv4"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                            
                            <div class="reading-item">
                                <label for="elv600_hv5" class="form-label">HV 5</label>
                                <input type="text" class="form-control numeric-input" id="elv600_hv5" name="elv600_hv5"
                                       pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                       placeholder="0.000">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <a href="<?= base_url('horizontal-displacement') ?>" class="btn btn-secondary btn-action">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
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
                        <p class="mt-2 text-muted">Menyimpan data HDM...</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    // GANTI seluruh script JavaScript dengan yang ini:

// GANTI script JavaScript di create.php dengan yang ini:

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isDuplicate = false;
    
    // Fungsi untuk mengecek data duplikat - PERBAIKAN: berdasarkan tahun, periode, dan tanggal
    function checkDuplicateData() {
        const tahun = document.getElementById('tahun').value;
        const periode = document.getElementById('periode').value;
        const tanggal = document.getElementById('tanggal').value;
        
        if (!tahun || !periode || !tanggal) {
            hideDuplicateWarning();
            return;
        }
        
        // Kirim request untuk cek duplikat
        fetch('<?= base_url('horizontal-displacement/check-duplicate') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: `tahun=${tahun}&periode=${periode}&tanggal=${tanggal}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.isDuplicate) {
                isDuplicate = true;
                showDuplicateWarning(data.message);
            } else {
                isDuplicate = false;
                hideDuplicateWarning();
            }
        })
        .catch(error => {
            console.error('Error checking duplicate:', error);
            isDuplicate = false;
            hideDuplicateWarning();
        });
    }
    
    // Tampilkan peringatan duplikat
    function showDuplicateWarning(message) {
        const warning = document.getElementById('duplicateWarning');
        const messageElement = document.getElementById('duplicateMessage');
        const submitBtn = document.getElementById('submitBtn');
        
        if (messageElement) messageElement.textContent = message;
        if (warning) warning.style.display = 'block';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Data Sudah Ada';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-warning');
        }
    }
    
    // Sembunyikan peringatan duplikat
    function hideDuplicateWarning() {
        const warning = document.getElementById('duplicateWarning');
        const submitBtn = document.getElementById('submitBtn');
        
        if (warning) warning.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Data';
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-success');
        }
    }
    
    // Event listeners untuk field yang memicu pengecekan duplikat
    document.getElementById('tahun').addEventListener('change', checkDuplicateData);
    document.getElementById('periode').addEventListener('change', checkDuplicateData);
    document.getElementById('tanggal').addEventListener('change', checkDuplicateData);
    
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
        
        // Validasi periode
        const periode = document.getElementById('periode');
        if (!periode.value) {
            periode.classList.add('is-invalid');
            isValid = false;
        } else {
            periode.classList.remove('is-invalid');
        }
        
        // Validasi tanggal
        const tanggal = document.getElementById('tanggal');
        if (!tanggal.value) {
            tanggal.classList.add('is-invalid');
            isValid = false;
        } else {
            tanggal.classList.remove('is-invalid');
        }
        
        // Validasi DMA
        const dma = document.getElementById('dma');
        if (!dma.value) {
            dma.classList.add('is-invalid');
            isValid = false;
        } else {
            dma.classList.remove('is-invalid');
        }
        
        return isValid;
    }

    // Validasi input numerik dengan titik
    function validateNumericInput(input) {
        const value = input.value;
        // Validasi lebih longgar - boleh kosong atau angka dengan titik
        if (value && !/^-?\d*\.?\d*$/.test(value)) {
            input.classList.add('is-invalid');
            return false;
        } else {
            input.classList.remove('is-invalid');
            return true;
        }
    }
    
    // Handle form submission
    document.getElementById('createForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            showAlert('Harap periksa kembali data yang wajib diisi', 'danger');
            return;
        }

        // Cek jika data duplikat
        if (isDuplicate) {
            showAlert('Data pengukuran dengan tahun, periode, dan tanggal tersebut sudah ada. Silakan periksa kembali.', 'warning');
            return;
        }

        // Validasi semua input numerik
        let allNumericValid = true;
        document.querySelectorAll('.numeric-input').forEach(input => {
            if (!validateNumericInput(input)) {
                allNumericValid = false;
            }
        });

        if (!allNumericValid) {
            showAlert('Harap periksa format angka (gunakan titik untuk desimal)', 'danger');
            return;
        }
        
        // Show loading spinner
        const loadingSpinner = document.getElementById('loadingSpinner');
        const submitBtn = document.getElementById('submitBtn');
        
        if (loadingSpinner) loadingSpinner.style.display = 'block';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
        }
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message || 'Data HDM berhasil ditambahkan', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '<?= base_url('horizontal-displacement') ?>';
                }, 1500);
            } else {
                let errorMessage = 'Gagal menambahkan data';
                if (data.message) {
                    errorMessage += ': ' + data.message;
                } else if (data.errors) {
                    errorMessage += ': ' + Object.values(data.errors).join(', ');
                }
                showAlert(errorMessage, 'danger');
                
                // Tampilkan error validasi di field yang sesuai
                if (data.errors) {
                    for (const field in data.errors) {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            let feedback = input.nextElementSibling;
                            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                                feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                input.parentNode.appendChild(feedback);
                            }
                            feedback.textContent = data.errors[field];
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan jaringan: ' + error.message, 'danger');
        })
        .finally(() => {
            if (loadingSpinner) loadingSpinner.style.display = 'none';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Data';
            }
        });
    });
    
    // Validasi input numerik real-time
    document.querySelectorAll('.numeric-input').forEach(input => {
        input.addEventListener('input', function() {
            validateNumericInput(this);
            
            // Format input: ganti koma dengan titik
            this.value = this.value.replace(',', '.');
            
            // Hapus karakter selain angka, titik, dan minus
            this.value = this.value.replace(/[^\d.-]/g, '');
            
            // Hanya boleh ada satu titik
            const parts = this.value.split('.');
            if (parts.length > 2) {
                this.value = parts[0] + '.' + parts.slice(1).join('');
            }
        });
    });
    
    // Function to show alert
    function showAlert(message, type) {
        const alert = document.getElementById('liveAlert');
        const alertMessage = document.getElementById('alert-message');
        
        if (alert && alertMessage) {
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alertMessage.textContent = message;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        } else {
            // Fallback jika element alert tidak ada
            alert(message);
        }
    }
    
    // Hapus kelas invalid saat user mulai mengisi
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Inisialisasi check duplicate saat halaman dimuat
    checkDuplicateData();
});
</script>
</body>
</html>