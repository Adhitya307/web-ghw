<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'PT Indonesia Power - Monitoring PLTA Saguling' ?></title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('css/home.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/data.css') ?>">

  <style>
    /* Professional Header - Warna disamakan dengan home */
    .main-header {
      background: linear-gradient(135deg, #4A90E2 0%, #357ABD 50%, #2C5F9E 100%);
      color: white;
      padding: 1rem 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
    }
    
    .branding {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .main-logo {
      height: 50px;
      width: auto;
    }
    
    .logo-divider {
      height: 40px;
      width: 1px;
      background: rgba(255,255,255,0.3);
      margin: 0 1rem;
    }
    
    .company-identity h1 {
      margin: 0;
      line-height: 1.2;
    }
    
    .company-title {
      display: block;
      font-size: 1.2rem;
      font-weight: 700;
      font-family: 'Montserrat', sans-serif;
    }
    
    .unit-name {
      display: block;
      font-size: 0.9rem;
      font-weight: 500;
      opacity: 0.9;
    }
    
    .system-name {
      margin: 0;
      font-size: 0.8rem;
      opacity: 0.8;
    }

    /* Header Actions Container */
    .header-actions {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    /* Button Styles - Lebih Transparan */
    .header-button {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
      text-decoration: none;
      font-weight: 500;
      cursor: pointer;
      font-size: 0.9rem;
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
    }

    .header-button:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-1px);
      color: white;
      text-decoration: none;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
    }

    .header-button i {
      font-size: 0.9rem;
      opacity: 0.9;
    }

    /* Specific button styles - Lebih Subtle */
    .back-button {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .back-button:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
    }

    .menu-button {
      background: rgba(0, 170, 255, 0.15);
      border: 1px solid rgba(0, 170, 255, 0.3);
    }

    .menu-button:hover {
      background: rgba(0, 170, 255, 0.25);
      border-color: rgba(0, 170, 255, 0.4);
    }

    .logout-button {
      background: rgba(255, 68, 68, 0.15);
      border: 1px solid rgba(255, 68, 68, 0.3);
    }

    .logout-button:hover {
      background: rgba(255, 68, 68, 0.25);
      border-color: rgba(255, 68, 68, 0.4);
    }

    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
    }

    /* Section styling untuk anchor target */
    section {
      scroll-margin-top: 80px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .header-container {
        flex-direction: column;
        gap: 1rem;
      }
      
      .branding {
        width: 100%;
        justify-content: flex-start;
      }
      
      .logo-divider {
        display: none;
      }
      
      .header-actions {
        width: 100%;
        justify-content: flex-end;
        margin-top: 0.5rem;
      }
      
      section {
        scroll-margin-top: 120px;
      }
    }

    @media (max-width: 768px) {
      .header-button span {
        display: none;
      }
      
      .header-button {
        padding: 0.5rem;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
      }
      
      .header-button i {
        margin: 0;
        font-size: 1rem;
        opacity: 0.85;
      }

      .company-title {
        font-size: 1rem;
      }

      .unit-name {
        font-size: 0.8rem;
      }

      .system-name {
        font-size: 0.7rem;
      }

      .branding {
        flex-direction: column;
        text-align: left;
        gap: 0.5rem;
      }

      .logo-group {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .logo-divider {
        display: block;
        height: 30px;
      }
    }

    @media (max-width: 576px) {
      .header-actions {
        gap: 0.5rem;
      }
      
      .header-button {
        padding: 0.4rem 0.8rem;
      }
    }
  </style>
</head>
<body>
  <!-- Professional Header -->
  <header class="main-header">
    <div class="header-container container-lg">
      <!-- Branding di Kiri -->
      <div class="branding">
        <div class="logo-group">
          <img src="<?= base_url('img/logo_indonesia_power.png') ?>" alt="Logo Indonesia Power" class="main-logo">
          <div class="logo-divider"></div>
        </div>
        <div class="company-identity">
          <h1 class="company-name">
            <span class="company-title">PT Indonesia Power</span>
            <span class="unit-name">Unit Bisnis Pembangkitan Saguling</span>
          </h1>
          <p class="system-name">Sistem Monitoring Operasional PLTA</p>
        </div>
      </div>

      <!-- Kembali, Menu, dan Logout Buttons di Kanan -->
      <div class="header-actions">
        <a href="javascript:history.back()" class="header-button back-button">
          <i class="bi bi-arrow-left"></i>
          <span>Kembali</span>
        </a>
        
        <a href="/menu" class="header-button menu-button">
          <i class="bi bi-grid-3x3-gap"></i>
          <span>Menu</span>
        </a>
        
        <a href="/auth/logout" class="header-button logout-button">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Konten halaman -->
  <main>
    <!-- Konten halaman Anda di sini -->
  </main>

  <script>
    // Fungsi untuk tombol back yang lebih cerdas
    document.addEventListener('DOMContentLoaded', function() {
      const backButton = document.querySelector('.back-button');
      const logoutButton = document.querySelector('.logout-button');
      
      // Cek jika tidak ada history sebelumnya, sembunyikan tombol back
      if (!document.referrer || document.referrer === window.location.href) {
        backButton.style.display = 'none';
      }
      
      // Tambahkan event listener untuk tombol back
      if (backButton) {
        backButton.addEventListener('click', function(e) {
          console.log('Navigating back from: ' + window.location.href);
        });
      }
      
      // Tambahkan konfirmasi sebelum logout
      if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
          e.preventDefault();
          const logoutUrl = this.getAttribute('href');
          
          // Konfirmasi logout
          if (confirm('Apakah Anda yakin ingin logout dari sistem?')) {
            window.location.href = logoutUrl;
          }
        });
      }
      
      // Tambahkan efek aktif untuk menu button
      const menuButton = document.querySelector('.menu-button');
      if (menuButton && window.location.pathname.includes('/menu')) {
        menuButton.style.opacity = '0.8';
        menuButton.style.cursor = 'default';
        menuButton.style.pointerEvents = 'none';
      }
      
      // Cek jika di halaman home, sembunyikan tombol Kembali
      if (window.location.pathname === '/' || window.location.pathname.includes('/home')) {
        backButton.style.display = 'none';
      }
    });
  </script>
</body>
</html>