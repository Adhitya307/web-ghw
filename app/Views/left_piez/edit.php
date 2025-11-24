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
        /* CSS tetap sama seperti sebelumnya */
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
    </style>
</head>
<body>

    <?= $this->include('layouts/header'); ?>
    
    <!-- Main Content -->
    <div class="container form-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Data Piezometer - Left Bank</h3>
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
                
                <!-- Form Edit Data -->
                <form id="editForm" action="<?= base_url('left-piez/update/' . $id_pengukuran) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <!-- Data Pengukuran -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-chart-line"></i> Data Pengukuran</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tahun" class="form-label required-label">Tahun</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="number" class="form-control" id="tahun" name="tahun" 
                                           min="2000" max="2100" required 
                                           value="<?= esc($data['pengukuran']['tahun'] ?? '') ?>">
                                    <div class="invalid-feedback">Tahun harus diisi antara 2000-2100</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="periode" class="form-label required-label">Periode</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-clock"></i>
                                    <select class="form-select" id="periode" name="periode" required>
                                        <option value="">Pilih Periode</option>
                                        <option value="TW-1" <?= (($data['pengukuran']['periode'] ?? '') == 'TW-1') ? 'selected' : '' ?>>TW-1</option>
                                        <option value="TW-2" <?= (($data['pengukuran']['periode'] ?? '') == 'TW-2') ? 'selected' : '' ?>>TW-2</option>
                                        <option value="TW-3" <?= (($data['pengukuran']['periode'] ?? '') == 'TW-3') ? 'selected' : '' ?>>TW-3</option>
                                        <option value="TW-4" <?= (($data['pengukuran']['periode'] ?? '') == 'TW-4') ? 'selected' : '' ?>>TW-4</option>
                                    </select>
                                    <div class="invalid-feedback">Periode harus dipilih</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal" class="form-label required-label">Tanggal Pengukuran</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-day"></i>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required 
                                           value="<?= esc($data['pengukuran']['tanggal'] ?? '') ?>">
                                    <div class="invalid-feedback">Tanggal harus diisi</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="dma" class="form-label">DMA</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-ruler-combined"></i>
                                    <input type="text" class="form-control numeric-input" id="dma" name="dma" 
                                           pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                           placeholder="0.000"
                                           value="<?= esc($data['pengukuran']['dma'] ?? '') ?>">
                                    <div class="invalid-feedback">Hanya angka dan titik desimal yang diperbolehkan</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="temp_id" class="form-label">CH Bulanan</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-hashtag"></i>
                                    <input type="text" class="form-control" id="temp_id" name="temp_id" 
                                           placeholder="Masukkan CH Bulanan"
                                           value="<?= esc($data['pengukuran']['temp_id'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bacaan Metrik (Feet & Inch) -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-ruler"></i> Bacaan Metrik</h4>
                        
                        <div class="form-grid">
                            <?php 
                            $titikList = ['L_01', 'L_02', 'L_03', 'L_04', 'L_05', 'L_06', 'L_07', 'L_08', 'L_09', 'L_10', 'SPZ_02'];
                            
                            foreach($titikList as $titik): 
                                $pembacaan = $data['pembacaan'][$titik] ?? [];
                                $feet = $pembacaan['feet'] ?? '';
                                $inch = $pembacaan['inch'] ?? '';
                            ?>
                            <div class="titik-group">
                                <div class="titik-group-title"><?= str_replace('_', '-', $titik) ?></div>
                                
                                <div class="value-group">
                                    <div class="form-group">
                                        <label for="feet_<?= $titik ?>" class="form-label">Feet</label>
                                        <input type="text" class="form-control numeric-input pembacaan-input" 
                                               id="feet_<?= $titik ?>" name="pembacaan[<?= $titik ?>][feet]" 
                                               data-titik="<?= $titik ?>"
                                               pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                               placeholder="0.00"
                                               value="<?= esc($feet) ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="inch_<?= $titik ?>" class="form-label">Inch</label>
                                        <input type="text" class="form-control numeric-input pembacaan-input" 
                                               id="inch_<?= $titik ?>" name="pembacaan[<?= $titik ?>][inch]" 
                                               data-titik="<?= $titik ?>"
                                               pattern="[0-9]*\.?[0-9]*" title="Hanya angka dan titik diperbolehkan"
                                               placeholder="0.00"
                                               value="<?= esc($inch) ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <a href="<?= base_url('left-piez') ?>" class="btn btn-secondary btn-action">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success btn-action" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Update Data
                        </button>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div class="text-center mt-4 loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Mengupdate data...</span>
                        </div>
                        <p class="mt-2">Mengupdate data piezometer...</p>
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
    const liveAlert = document.getElementById('liveAlert');
    const alertMessage = document.getElementById('alert-message');

    // Fungsi untuk menampilkan alert
    function showAlert(message, type = 'success') {
        alertMessage.textContent = message;
        liveAlert.className = `alert alert-${type} alert-dismissible fade show`;
        liveAlert.style.display = 'block';
        
        // Auto hide setelah 5 detik untuk error, 3 detik untuk success
        const timeout = type === 'success' ? 3000 : 5000;
        setTimeout(() => {
            liveAlert.style.display = 'none';
        }, timeout);
    }

    // Format input angka dengan validasi yang lebih sederhana
    function formatNumericInput(input) {
        // Hapus karakter selain angka dan titik
        let value = input.value.replace(/[^\d.]/g, '');
        
        // Hapus titik desimal berlebih
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        input.value = value;
        return value;
    }

    // Event listener untuk input numerik
    document.querySelectorAll('.numeric-input').forEach(input => {
        input.addEventListener('input', function() {
            formatNumericInput(this);
            this.classList.remove('is-invalid');
        });
        
        input.addEventListener('blur', function() {
            let value = this.value;
            
            if (value === '' || value === '0' || value === '0.00') {
                this.value = '';
            } else if (value) {
                // Format ke 2 desimal
                const numValue = parseFloat(value);
                if (!isNaN(numValue)) {
                    this.value = numValue.toFixed(2);
                }
            }
        });
    });

    // Cek duplikat ketika tahun, periode, atau tanggal berubah
    function checkDuplicate() {
        const tahun = document.getElementById('tahun').value;
        const periode = document.getElementById('periode').value;
        const tanggal = document.getElementById('tanggal').value;
        const currentId = '<?= $id_pengukuran ?>';

        // Reset warning terlebih dahulu
        duplicateWarning.style.display = 'none';

        if (tahun && periode && tanggal) {
            // Show loading state
            duplicateWarning.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm me-2"></div>Mengecek data...</div>';
            duplicateWarning.style.display = 'block';

            // Prepare form data untuk duplicate check
            const formData = new FormData();
            formData.append('tahun', tahun);
            formData.append('periode', periode);
            formData.append('tanggal', tanggal);
            formData.append('current_id', currentId);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            fetch('<?= base_url('left-piez/check-duplicate') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.isDuplicate) {
                        duplicateWarning.innerHTML = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-1 text-warning">Data Pengukuran Sudah Ada!</h6>
                                    <p class="mb-0 small">Data dengan Tahun: ${tahun}, Periode: ${periode}, Tanggal: ${tanggal} sudah ada dalam database.</p>
                                </div>
                            </div>
                        `;
                        duplicateWarning.style.display = 'block';
                    } else {
                        duplicateWarning.style.display = 'none';
                    }
                } else {
                    // Jika success false, sembunyikan warning
                    duplicateWarning.style.display = 'none';
                    console.warn('Duplicate check returned success: false', data);
                }
            })
            .catch(error => {
                console.error('Error checking duplicate:', error);
                duplicateWarning.style.display = 'none';
                // Jangan tampilkan alert untuk error duplicate check agar tidak mengganggu user
            });
        }
    }

    // Debounce untuk duplicate check
    let duplicateCheckTimeout;
    function debounceCheckDuplicate() {
        clearTimeout(duplicateCheckTimeout);
        duplicateCheckTimeout = setTimeout(checkDuplicate, 500);
    }

    // Event listeners untuk cek duplikat dengan debounce
    document.getElementById('tahun').addEventListener('change', debounceCheckDuplicate);
    document.getElementById('periode').addEventListener('change', debounceCheckDuplicate);
    document.getElementById('tanggal').addEventListener('change', debounceCheckDuplicate);

    // Validasi form sebelum submit
    function validateForm() {
        let isValid = true;

        // Reset semua invalid state
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        // Validasi field required
        const requiredFields = [
            { id: 'tahun', name: 'Tahun' },
            { id: 'periode', name: 'Periode' },
            { id: 'tanggal', name: 'Tanggal Pengukuran' }
        ];

        requiredFields.forEach(field => {
            const element = document.getElementById(field.id);
            if (!element.value) {
                element.classList.add('is-invalid');
                isValid = false;
            }
        });

        // Validasi format numerik
        document.querySelectorAll('.numeric-input').forEach(input => {
            if (input.value && !/^\d*\.?\d{0,2}$/.test(input.value)) {
                input.classList.add('is-invalid');
                isValid = false;
            }
        });

        // Validasi tahun range
        const tahun = document.getElementById('tahun');
        if (tahun.value && (parseInt(tahun.value) < 2000 || parseInt(tahun.value) > 2100)) {
            tahun.classList.add('is-invalid');
            isValid = false;
        }

        return isValid;
    }

    // Reset validasi saat user mulai mengetik
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Submit form - TANPA ALERT KONFIRMASI
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi form
        if (!validateForm()) {
            showAlert('Harap periksa kembali data yang diinput! Pastikan semua field required terisi dan format angka benar.', 'danger');
            return;
        }

        // TANPA KONFIRMASI - langsung submit
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengupdate...';
        loadingSpinner.style.display = 'block';

        // Submit data
        const formData = new FormData(form);
        
        fetch('<?= base_url('left-piez/update/' . $id_pengukuran) ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                // Jika response tidak ok, coba parse error message
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('Data piezometer berhasil diupdate! Mengalihkan...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '<?= base_url('left-piez') ?>';
                }, 1500);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat mengupdate data');
            }
        })
        .catch(error => {
            console.error('Submit Error:', error);
            
            // Tampilkan error message yang lebih user-friendly
            let errorMessage = 'Terjadi kesalahan saat mengupdate data';
            if (error.message.includes('HTTP 500')) {
                errorMessage = 'Terjadi kesalahan server. Silakan coba lagi atau hubungi administrator.';
            } else if (error.message.includes('HTTP 404')) {
                errorMessage = 'Data tidak ditemukan. Silakan refresh halaman dan coba lagi.';
            } else {
                errorMessage = error.message;
            }
            
            showAlert(errorMessage, 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Update Data';
            loadingSpinner.style.display = 'none';
        });
    });

    // Auto-format input saat page load
    document.querySelectorAll('.numeric-input').forEach(input => {
        if (input.value) {
            const numValue = parseFloat(input.value);
            if (!isNaN(numValue)) {
                input.value = numValue.toFixed(2);
            }
        }
    });

    // Handle paste event untuk input numerik
    document.querySelectorAll('.numeric-input').forEach(input => {
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            // Hanya izinkan angka dan titik
            const cleaned = pastedText.replace(/[^\d.]/g, '');
            this.value = cleaned;
        });
    });

    // Initial check saat page load
    setTimeout(checkDuplicate, 1000);
});
</script>

    <?= $this->include('layouts/footer'); ?>
</body>
</html>