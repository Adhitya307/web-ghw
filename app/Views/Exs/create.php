<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tambah Data Extensometer - PT Indonesia Power' ?></title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .extenso-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .extenso-header {
            background: #e9ecef;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #495057;
        }
        
        .form-section {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .number-input {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .btn-submit {
            min-width: 120px;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .default-value {
            background-color: #fff3cd;
            font-weight: 500;
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .info-card {
            border-left: 4px solid #0dcaf0;
        }
        
        .decimal-info {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .year-input {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        
        .decimal-warning {
            display: none;
            font-size: 0.75rem;
            color: #dc3545;
            margin-top: 0.25rem;
        }
        
        .dma-input {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header'); ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Data Extensometer
                </h2>
                <a href="<?= base_url('extenso') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('extenso/store') ?>" method="POST" id="extensoForm">
                <?= csrf_field() ?>

                <!-- Basic Information Section -->
                <div class="form-section">
                    <h4 class="mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dasar Pengukuran</h4>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun" class="form-label required-field">Tahun</label>
                            <input type="text" class="form-control year-input" 
                                   id="tahun" name="tahun" 
                                   value="<?= old('tahun', date('Y')) ?>" 
                                   placeholder="YYYY" 
                                   maxlength="4"
                                   pattern="[0-9]{4}"
                                   title="Masukkan tahun dengan format 4 digit (contoh: 2024)"
                                   required>
                            <div class="decimal-info">Format: YYYY (contoh: 2024)</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="periode" class="form-label required-field">Periode</label>
                            <select class="form-select" id="periode" name="periode" required>
                                <option value="">Pilih Periode</option>
                                <option value="TW-1" <?= old('periode') == 'TW-1' ? 'selected' : '' ?>>TW-1</option>
                                <option value="TW-2" <?= old('periode') == 'TW-2' ? 'selected' : '' ?>>TW-2</option>
                                <option value="TW-3" <?= old('periode') == 'TW-3' ? 'selected' : '' ?>>TW-3</option>
                                <option value="TW-4" <?= old('periode') == 'TW-4' ? 'selected' : '' ?>>TW-4</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label required-field">Tanggal Pengukuran</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="dma" class="form-label">DMA</label>
                            <input type="text" class="form-control dma-input no-comma-input" 
                                   id="dma" name="dma" 
                                   value="<?= old('dma') ?>" placeholder="Masukkan DMA"
                                   title="Masukkan nilai DMA">
                            <div class="decimal-warning" id="warning-dma">Koma (,) tidak diizinkan</div>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Informasi Pengisian Data</h6>
                    <p class="mb-0">Hanya input <strong>nilai pembacaan</strong> saja. <strong>Initial readings</strong> menggunakan nilai default dan <strong>deformasi</strong> akan dihitung otomatis.</p>
                    <p class="mb-0 mt-1"><strong>Catatan:</strong> Semua input <strong>tidak menerima koma (,)</strong> sebagai pemisah.</p>
                </div>

                <!-- Extensometer Data Sections -->
                <?php 
                // Nilai default initial readings untuk setiap extensometer
                $defaultInitials = [
                    'ex1' => ['10' => 35.00, '20' => 40.95, '30' => 29.80],
                    'ex2' => ['10' => 22.60, '20' => 23.70, '30' => 30.75],
                    'ex3' => ['10' => 37.75, '20' => 39.15, '30' => 41.40],
                    'ex4' => ['10' => 33.80, '20' => 29.30, '30' => 48.95]
                ];
                ?>

                <?php for($i = 1; $i <= 4; $i++): 
                    $currentEx = 'ex' . $i;
                ?>
                <div class="extenso-section">
                    <div class="extenso-header">
                        <i class="fas fa-ruler-combined me-2"></i>EXTENSOMETER EX-<?= $i ?>
                    </div>
                    
                    <div class="row">
                        <!-- Input Pembacaan -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-edit me-2"></i>INPUT PEMBACAAN</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">Masukkan nilai pembacaan terbaru:</p>
                                    
                                    <div class="mb-3">
                                        <label for="ex<?= $i ?>_pembacaan_10" class="form-label">Pembacaan 10 m</label>
                                        <input type="text" class="form-control number-input decimal-input no-comma-input" 
                                               id="ex<?= $i ?>_pembacaan_10" name="ex<?= $i ?>_pembacaan_10" 
                                               value="<?= old('ex'.$i.'_pembacaan_10') ?>" placeholder="0.00"
                                               title="Gunakan titik (.) sebagai pemisah desimal">
                                        <div class="decimal-info">Gunakan titik (.) untuk desimal, contoh: 35.50</div>
                                        <div class="decimal-warning" id="warning-ex<?= $i ?>_pembacaan_10">Koma (,) tidak diizinkan. Gunakan titik (.)</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ex<?= $i ?>_pembacaan_20" class="form-label">Pembacaan 20 m</label>
                                        <input type="text" class="form-control number-input decimal-input no-comma-input" 
                                               id="ex<?= $i ?>_pembacaan_20" name="ex<?= $i ?>_pembacaan_20" 
                                               value="<?= old('ex'.$i.'_pembacaan_20') ?>" placeholder="0.00"
                                               title="Gunakan titik (.) sebagai pemisah desimal">
                                        <div class="decimal-info">Gunakan titik (.) untuk desimal, contoh: 40.95</div>
                                        <div class="decimal-warning" id="warning-ex<?= $i ?>_pembacaan_20">Koma (,) tidak diizinkan. Gunakan titik (.)</div>
                                    </div>
                                    <div class="mb-0">
                                        <label for="ex<?= $i ?>_pembacaan_30" class="form-label">Pembacaan 30 m</label>
                                        <input type="text" class="form-control number-input decimal-input no-comma-input" 
                                               id="ex<?= $i ?>_pembacaan_30" name="ex<?= $i ?>_pembacaan_30" 
                                               value="<?= old('ex'.$i.'_pembacaan_30') ?>" placeholder="0.00"
                                               title="Gunakan titik (.) sebagai pemisah desimal">
                                        <div class="decimal-info">Gunakan titik (.) untuk desimal, contoh: 29.80</div>
                                        <div class="decimal-warning" id="warning-ex<?= $i ?>_pembacaan_30">Koma (,) tidak diizinkan. Gunakan titik (.)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Default Values -->
                        <div class="col-md-6">
                            <div class="card h-100 info-card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-database me-2"></i>NILAI DEFAULT</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Initial Reading 10 m</label>
                                        <div class="form-control default-value">
                                            <?= number_format($defaultInitials[$currentEx]['10'], 2, '.', '') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Initial Reading 20 m</label>
                                        <div class="form-control default-value">
                                            <?= number_format($defaultInitials[$currentEx]['20'], 2, '.', '') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Initial Reading 30 m</label>
                                        <div class="form-control default-value">
                                            <?= number_format($defaultInitials[$currentEx]['30'], 2, '.', '') ?>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-success mt-3">
                                        <small>
                                            <i class="fas fa-check-circle me-1"></i>
                                            <strong>Deformasi akan dihitung otomatis</strong><br>
                                            Berdasarkan nilai pembacaan dan initial readings
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>

                <!-- Action Buttons -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('extenso') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-save me-1"></i> Simpan Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer'); ?>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Form validation
    const form = document.getElementById('extensoForm');
    
    form.addEventListener('submit', function (e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Validasi tahun
        const tahunInput = document.getElementById('tahun');
        const tahunValue = tahunInput.value.trim();
        if (tahunValue && !/^[0-9]{4}$/.test(tahunValue)) {
            isValid = false;
            tahunInput.classList.add('is-invalid');
        }
        
        // Validasi semua input yang tidak boleh ada koma
        const noCommaInputs = document.querySelectorAll('.no-comma-input');
        noCommaInputs.forEach(input => {
            const value = input.value;
            if (value && value.includes(',')) {
                isValid = false;
                input.classList.add('is-invalid');
                showNoCommaWarning(input);
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi dengan format yang benar!');
        }
    });

    // Validasi input tahun - hanya angka dan maksimal 4 digit
    const tahunInput = document.getElementById('tahun');
    
    tahunInput.addEventListener('input', function() {
        // Hanya menerima angka
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Batasi maksimal 4 digit
        if (this.value.length > 4) {
            this.value = this.value.slice(0, 4);
        }
        
        // Validasi format
        if (this.value.length === 4) {
            const year = parseInt(this.value);
            const currentYear = new Date().getFullYear();
            
            if (year < 2000 || year > currentYear + 1) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        } else if (this.value.length > 0) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Validasi semua input yang TIDAK BOLEH ADA KOMA
    const noCommaInputs = document.querySelectorAll('.no-comma-input');
    
    noCommaInputs.forEach(input => {
        // BLOCK comma input completely - tidak muncul sama sekali
        input.addEventListener('keydown', function(e) {
            if (e.key === ',' || e.key === ';') {
                e.preventDefault();
                showNoCommaWarning(this);
                return false;
            }
        });
        
        // Handle input event untuk memastikan tidak ada koma
        input.addEventListener('input', function() {
            // Jika ada koma, hapus secara otomatis
            if (this.value.includes(',')) {
                this.value = this.value.replace(/,/g, '');
                showNoCommaWarning(this);
            }
        });
        
        // Replace comma on paste dan hapus koma
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            
            // Get pasted text
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            
            // Remove commas and other unwanted characters
            let cleanedText = pastedText.replace(/,/g, '');
            
            // Untuk input decimal, hanya izinkan angka dan titik
            if (input.classList.contains('decimal-input')) {
                cleanedText = cleanedText.replace(/[^0-9.]/g, '');
            }
            
            // Insert cleaned text
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.substring(0, start) + cleanedText + this.value.substring(end);
            
            // Set cursor position
            this.setSelectionRange(start + cleanedText.length, start + cleanedText.length);
            
            if (pastedText.includes(',')) {
                showNoCommaWarning(this);
            }
        });
        
        // Format on blur untuk input decimal
        input.addEventListener('blur', function() {
            if (this.value && this.value !== '' && this.classList.contains('decimal-input')) {
                // Pastikan tidak ada koma
                let value = this.value.replace(/,/g, '');
                
                // Validate format
                if (!/^[0-9]*\.?[0-9]*$/.test(value)) {
                    this.classList.add('is-invalid');
                    return;
                }
                
                // Format to 2 decimal places
                const numValue = parseFloat(value);
                if (!isNaN(numValue)) {
                    this.value = numValue.toFixed(2);
                    this.classList.remove('is-invalid');
                    hideNoCommaWarning(this);
                } else {
                    this.classList.add('is-invalid');
                }
            }
        });
        
        // Remove warning on focus
        input.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
            hideNoCommaWarning(this);
        });
    });

    function showNoCommaWarning(input) {
        const warningId = 'warning-' + input.id;
        const warningElement = document.getElementById(warningId);
        if (warningElement) {
            warningElement.style.display = 'block';
        }
        input.classList.add('is-invalid');
        
        // Hide warning after 3 seconds
        setTimeout(() => {
            hideNoCommaWarning(input);
        }, 3000);
    }
    
    function hideNoCommaWarning(input) {
        const warningId = 'warning-' + input.id;
        const warningElement = document.getElementById(warningId);
        if (warningElement) {
            warningElement.style.display = 'none';
        }
    }
});
</script>
</body>
</html>