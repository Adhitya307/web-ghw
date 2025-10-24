<!-- Modal Hapus HDM -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Data HDM
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-arrows-alt-h text-danger" style="font-size: 3rem;"></i>
                </div>
                <h6 class="text-center">Apakah Anda yakin ingin menghapus data Horizontal Displacement Meter ini?</h6>
                <p class="text-muted text-center small" id="dataToDelete"></p>
                <div class="alert alert-warning mt-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data pembacaan ELV 625 dan ELV 600 yang terkait juga akan dihapus secara permanen.
                </div>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Data yang akan dihapus mencakup:
                    <ul class="mb-0 mt-1 small">
                        <li>Data pengukuran (Tahun, Periode, Tanggal, DMA)</li>
                        <li>Pembacaan ELV 625 (HV 1, HV 2, HV 3)</li>
                        <li>Pembacaan ELV 600 (HV 1, HV 2, HV 3, HV 4, HV 5)</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="confirmDelete" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Hapus Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

// GANTI script JavaScript di modal_hapus.php dengan yang ini:

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    let currentDeleteId = null;
    
    // Event listener untuk semua tombol hapus di tabel HDM
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-data')) {
            const btn = e.target.closest('.delete-data');
            const id = btn.getAttribute('data-id');
            currentDeleteId = id;
            
            // Ambil data dari baris untuk ditampilkan di modal
            const row = btn.closest('tr');
            if (row) {
                // Ambil data dari kolom tabel
                const tahun = row.getAttribute('data-tahun') || (row.cells[0] ? row.cells[0].textContent.trim() : '-');
                const periode = row.getAttribute('data-periode') || (row.cells[1] ? row.cells[1].textContent.trim() : '-');
                const tanggal = row.getAttribute('data-tanggal') || (row.cells[2] ? row.cells[2].textContent.trim() : '-');
                const dma = row.getAttribute('data-dma') || (row.cells[3] ? row.cells[3].textContent.trim() : '-');
                
                // Tampilkan informasi data yang akan dihapus
                const dataElement = document.getElementById('dataToDelete');
                if (dataElement) {
                    dataElement.innerHTML = `
                        <strong>Detail Data:</strong><br>
                        Tahun: ${tahun} | Periode: ${periode}<br>
                        Tanggal: ${tanggal} | DMA: ${dma}
                    `;
                }
            }
            
            deleteModal.show();
        }
    });
    
    // Handler untuk konfirmasi hapus (AJAX)
    confirmDeleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!currentDeleteId) {
            showNotification('ID data tidak valid', 'error');
            return;
        }
        
        // Tampilkan loading state
        const originalText = confirmDeleteBtn.innerHTML;
        confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';
        confirmDeleteBtn.disabled = true;
        
        // Kirim request hapus via AJAX dengan method DELETE
        fetch(`<?= base_url('horizontal-displacement/delete') ?>/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => {
            if (!response.ok) {
                // Jika response tidak ok, coba parse error message
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Network response was not ok: ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            // Reset button state
            confirmDeleteBtn.innerHTML = originalText;
            confirmDeleteBtn.disabled = false;
            
            if (data.success) {
                // Tutup modal dan tampilkan notifikasi
                deleteModal.hide();
                showNotification(data.message || 'Data HDM berhasil dihapus', 'success');
                
                // Refresh data tabel setelah 1.5 detik
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Gagal menghapus data', 'error');
            }
        })
        .catch(error => {
            // Reset button state
            confirmDeleteBtn.innerHTML = originalText;
            confirmDeleteBtn.disabled = false;
            
            showNotification('Terjadi kesalahan: ' + error.message, 'error');
            console.error('Delete error:', error);
        });
    });
    
    // Reset modal ketika ditutup
    deleteModal._element.addEventListener('hidden.bs.modal', function() {
        currentDeleteId = null;
        const dataElement = document.getElementById('dataToDelete');
        if (dataElement) {
            dataElement.textContent = '';
        }
    });
    
    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        const notificationContainer = document.getElementById('notificationContainer');
        
        // Buat element notifikasi
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.style.cssText = 'min-width: 300px; margin-bottom: 10px;';
        
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const title = type === 'success' ? 'Berhasil' : 'Error';
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${icon} me-2 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>${title}</strong><br>
                    <span class="small">${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        notificationContainer.appendChild(notification);
        
        // Auto hapus notifikasi setelah 5 detik
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
        
        // Handle manual close
        notification.querySelector('.btn-close').addEventListener('click', function() {
            notification.remove();
        });
    }
});
</script>