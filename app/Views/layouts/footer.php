  </main>

  <!-- Professional Footer -->
  <footer class="main-footer">
    <div class="footer-top">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="footer-brand">
              <img src="<?= base_url('img/logo_indonesia_power.png') ?>" alt="Logo" class="footer-logo" style="height: 40px;">
              <p class="footer-about">
                PT Indonesia Power Saguling merupakan unit operasi yang mengelola Pembangkit Listrik Tenaga Air Saguling dengan kapasitas 700 MW.
              </p>
            </div>
          </div>
          
          <div class="col-lg-2 col-md-4">
            <h4>Menu</h4>
            <ul class="footer-links">
              <li><a href="<?= base_url() ?>"><i class="bi bi-chevron-right"></i> Beranda</a></li>
              <li><a href="<?= base_url('input-data') ?>"><i class="bi bi-chevron-right"></i> Data Rembesan</a></li>
              <li><a href="<?= base_url('grafik') ?>"><i class="bi bi-chevron-right"></i> Grafik</a></li>
              <li><a href="#kontak"><i class="bi bi-chevron-right"></i> Kontak</a></li>
            </ul>
          </div>
          
          <div class="col-lg-3 col-md-4">
            <h4>Dokumen</h4>
            <ul class="footer-links">
              <li><a href="#"><i class="bi bi-chevron-right"></i> Kebijakan Privasi</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Syarat & Ketentuan</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> FAQ</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Peta Situs</a></li>
            </ul>
          </div>
          
          <div class="col-lg-3 col-md-4">
            <h4>Kontak</h4>
            <ul class="footer-contact">
              <li><i class="bi bi-geo-alt"></i> Jl. PLTA Saguling, Bandung Barat</li>
              <li><i class="bi bi-telephone"></i> (022) 1234 5678</li>
              <li><i class="bi bi-envelope"></i> info@saguling.indonesiapower.co.id</li>
            </ul>
            
            <div class="social-media">
              <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
              <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
              <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
              <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <p class="copyright">
              &copy; <span id="current-year"><?= date('Y') ?></span> PT Indonesia Power. Seluruh hak cipta dilindungi.
            </p>
          </div>
          <div class="col-md-6">
            <p class="version">
              Sistem Monitoring Operasional PLTA v2.1
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Script untuk tahun copyright
    document.getElementById('current-year').textContent = new Date().getFullYear();
  </script>
</body>
</html>