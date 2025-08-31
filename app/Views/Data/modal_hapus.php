<!-- app/Views/Data/modal_hapus.php -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                </div>
                <h6 class="text-center">Apakah Anda yakin ingin menghapus data ini?</h6>
                <p class="text-muted text-center small" id="dataToDelete"></p>
                <div class="alert alert-warning mt-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tindakan ini tidak dapat dibatalkan. Semua data terkait (Thomson Weir, SR, Bocoran) juga akan dihapus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <a href="#" id="confirmDelete" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript untuk modal hapus - langsung embed di file
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    // Event listener untuk semua tombol hapus
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            // Set URL hapus
            confirmDeleteBtn.href = '<?= base_url('data/delete/') ?>' + id;
            
            // Ambil data dari baris untuk ditampilkan di modal
            const row = this.closest('tr');
            if (row) {
                const tahun = row.getAttribute('data-tahun');
                const bulan = row.getAttribute('data-bulan');
                const periode = row.getAttribute('data-periode');
                
                // Tampilkan informasi data yang akan dihapus
                const dataElement = document.getElementById('dataToDelete');
                if (dataElement) {
                    dataElement.textContent = `Tahun: ${tahun}, Bulan: ${bulan}, Periode: ${periode}`;
                }
            }
            
            deleteModal.show();
        });
    });
    
    // Handler untuk konfirmasi hapus (AJAX)
    confirmDeleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const deleteUrl = this.href;
        
        // Kirim request hapus via AJAX
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tutup modal dan refresh halaman
                deleteModal.hide();
                showNotification('Data berhasil dihapus', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification('Gagal menghapus data: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Terjadi kesalahan: ' + error.message, 'error');
        });
    });
    
    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        // Buat element notifikasi
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Hapus notifikasi setelah 3 detik
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>