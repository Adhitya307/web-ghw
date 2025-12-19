<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Monitoring PLTA Saguling</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- AOS Library untuk animasi scroll -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Custom CSS -->
   <link rel="stylesheet" href="<?= base_url('css/home.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/data.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    /* Style untuk splash screen */
    #splash-screen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #31587eff 0%, #1d76b9ff 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      color: white;
      font-family: 'Montserrat', sans-serif;
    }
    
    .splash-container {
      text-align: center;
      max-width: 600px;
      padding: 20px;
    }
    
    .logo-container {
      position: relative;
      margin: 0 auto 30px;
      width: 120px;
      height: 120px;
    }
    
    .logo {
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, #00aaff 0%, #0077cc 100%);
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto;
      animation: pulse 2s infinite;
      box-shadow: 0 0 30px rgba(0, 170, 255, 0.5);
    }
    
    .logo-inner {
      width: 80px;
      height: 80px;
      background: white;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 40px;
      color: #0077cc;
    }
    
    .water-drop {
      position: absolute;
      background: rgba(0, 170, 255, 0.7);
      border-radius: 50%;
      animation: dropFall 3s infinite linear;
    }
    
    .drop1 {
      width: 20px;
      height: 20px;
      top: -10px;
      left: 50%;
      margin-left: -10px;
      animation-delay: 0s;
    }
    
    .drop2 {
      width: 15px;
      height: 15px;
      top: 20px;
      left: 20px;
      animation-delay: 1s;
    }
    
    .drop3 {
      width: 15px;
      height: 15px;
      top: 20px;
      right: 20px;
      animation-delay: 2s;
    }
    
    .company-name {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 10px;
      background: linear-gradient(to right, #00aaff, #00ffaa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .unit-name {
      font-size: 1.2rem;
      color: #aaa;
      margin-bottom: 5px;
    }
    
    .system-name {
      font-size: 1.1rem;
      color: #00aaff;
      margin-bottom: 30px;
    }
    
    .loading-bar-container {
      width: 300px;
      height: 4px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 2px;
      margin: 20px auto;
      overflow: hidden;
    }
    
    .loading-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(to right, #00aaff, #00ffaa);
      border-radius: 2px;
      animation: loading 3s ease-in-out forwards;
    }
    
    .loading-text {
      color: #aaa;
      font-size: 0.9rem;
      margin-top: 10px;
    }
    
    /* Animasi keyframes */
    @keyframes pulse {
      0% { transform: scale(1); box-shadow: 0 0 30px rgba(0, 170, 255, 0.5); }
      50% { transform: scale(1.05); box-shadow: 0 0 40px rgba(0, 170, 255, 0.7); }
      100% { transform: scale(1); box-shadow: 0 0 30px rgba(0, 170, 255, 0.5); }
    }
    
    @keyframes dropFall {
      0% { transform: translateY(0); opacity: 1; }
      100% { transform: translateY(150px); opacity: 0; }
    }
    
    @keyframes loading {
      0% { width: 0%; }
      100% { width: 100%; }
    }
    
    /* Style untuk peta */
    #map {
      height: 600px;
      border-radius: 10px;
      background: #101820;
    }
    
    .label-titik {
      background: transparent;
      color: white;
      font-size: 12px;
      font-weight: bold;
      text-shadow: 1px 1px 2px black;
    }
    
    .info.legend {
      background: #101820;
      padding: 10px;
      border-radius: 8px;
      color: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.6);
      font-size: 13px;
      line-height: 18px;
    }
    
    .legend-box {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin-right: 6px;
    }
    
    .custom-marker {
      border-radius: 50%;
      border: 2px solid white;
      box-shadow: 0 0 5px rgba(0,0,0,0.7);
    }

    /* Style untuk Professional Header - DIUBAH SESUAI YANG DIINGINKAN */
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
    
    .unit-name-header {
      display: block;
      font-size: 0.9rem;
      font-weight: 500;
      opacity: 0.9;
    }
    
    .system-name-header {
      margin: 0;
      font-size: 0.8rem;
      opacity: 0.8;
    }

    .main-nav .nav-menu {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 0.5rem;
    }
    
    .nav-link {
      color: white;
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: background-color 0.3s;
      cursor: pointer;
    }
    
    .nav-link:hover, .nav-link.active {
      background-color: rgba(255,255,255,0.15);
    }

    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
    }

    /* Section styling untuk anchor target */
    section {
      scroll-margin-top: 80px;
    }

    /* Style untuk section umum */
    .section {
      padding: 4rem 0;
    }

    .section-header {
      text-align: center;
      margin-bottom: 3rem;
    }

    .section-header h2 {
      color: #101820;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .divider {
      width: 60px;
      height: 3px;
      background: linear-gradient(to right, #00aaff, #00ffaa);
      margin: 0 auto;
      border-radius: 2px;
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
      
      .main-nav .nav-menu {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      section {
        scroll-margin-top: 120px;
      }
    }

    @media (max-width: 576px) {
      .company-title {
        font-size: 1rem;
      }

      .unit-name-header {
        font-size: 0.8rem;
      }

      .system-name-header {
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
  </style>
</head>
<body>
  <!-- Splash Screen -->
  <div id="splash-screen">
    <div class="splash-container">
      <div class="logo-container">
        <div class="logo">
          <div class="logo-inner">
            <i class="bi bi-lightning-charge"></i>
          </div>
        </div>
        <div class="water-drop drop1"></div>
        <div class="water-drop drop2"></div>
        <div class="water-drop drop3"></div>
      </div>
      
      <h1 class="company-name">PT Indonesia Power</h1>
      <p class="unit-name">Unit Bisnis Pembangkitan Saguling</p>
      <p class="system-name">Sistem Monitoring Operasional PLTA</p>
      
      <div class="loading-bar-container">
        <div class="loading-bar"></div>
      </div>
      <p class="loading-text">Memuat sistem monitoring...</p>
    </div>
  </div>

  <!-- Main Content (tersembunyi sampai splash selesai) -->
  <div id="main-content" style="display: none;">
    <!-- Professional Header -->
    <header class="main-header">
      <div class="header-container container-lg">
        <div class="branding">
          <div class="logo-group">
            <img src="<?= base_url('img/logo_indonesia_power.png') ?>" alt="Logo Indonesia Power" class="main-logo">
            <div class="logo-divider"></div>
          </div>
          <div class="company-identity">
            <h1 class="company-name">
              <span class="company-title">PT Indonesia Power</span>
              <span class="unit-name-header">Unit Bisnis Pembangkitan Saguling</span>
            </h1>
            <p class="system-name-header">Sistem Monitoring Operasional PLTA</p>
          </div>
        </div>
        
        <nav class="main-nav">
          <ul class="nav-menu">
            <li class="nav-item">
              <a href="#home" class="nav-link home-link">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#visi-misi" class="nav-link visi-misi-link">
                <i class="bi bi-stars"></i>
                <span>Visi Misi</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#tentang" class="nav-link tentang-link">
                <i class="bi bi-building"></i>
                <span>Tentang Kami</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#kontak" class="nav-link kontak-link">
                <i class="bi bi-telephone"></i>
                <span>Kontak</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <main>
      <!-- Hero Section -->
      <section id="home" class="hero-section">
        <div class="container">
          <div class="hero-content">
            <div class="hero-text" data-aos="fade-right" data-aos-duration="1000">
              <h1>Selamat Datang di Sistem Monitoring PLTA Saguling</h1>
              <p class="lead">
                Efisiensi, keamanan, dan transparansi operasional pembangkit listrik tenaga air berbasis teknologi digital.
              </p>
              <a href="/auth/login" class="btn btn-primary btn-cta">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sistem
              </a>
            </div>
            <div class="hero-image" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
              <img src="img/slide1.jpg" alt="Monitoring Bendungan Saguling" class="img-fluid">
            </div>
          </div>
        </div>
      </section>

      <!-- Peta Instrumentasi Area Bendungan Saguling -->
      <section id="peta" class="section map-section" style="background:#101820; color:white;">
        <div class="container-fluid px-4 py-3">
          <h2 class="text-center mb-4" data-aos="fade-up" data-aos-duration="800">INFORMASI GEOTEKNIK AREA BENDUNGAN SAGULING</h2>
          <div id="map" data-aos="zoom-in" data-aos-duration="1000"></div>
        </div>
      </section>

      <!-- Visi & Misi-->
      <section id="visi-misi" class="section vision-mission">
        <div class="container">
          <div class="section-header" data-aos="fade-up" data-aos-duration="800">
            <h2><i class="bi bi-stars"></i> Visi & Misi Perusahaan</h2>
            <div class="divider"></div>
          </div>
          
          <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800" data-aos-delay="200">
              <div class="card vision-card">
                <div class="card-body">
                  <div class="card-icon">
                    <i class="bi bi-eye"></i>
                  </div>
                  <h3>Visi</h3>
                  <p>
                    Menjadi perusahaan penyedia jasa operasi dan pemeliharaan pembangkit listrik terkemuka di Asia Tenggara 
                    yang berbasis teknologi digital, berkelanjutan, dan ramah lingkungan.
                  </p>
                </div>
              </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
              <div class="card mission-card">
                <div class="card-body">
                  <div class="card-icon">
                    <i class="bi bi-bullseye"></i>
                  </div>
                  <h3>Misi</h3>
                  <ul class="mission-list">
                    <li><i class="bi bi-check-circle"></i> Memberikan pelayanan operasi dan pemeliharaan yang andal dan efisien</li>
                    <li><i class="bi bi-check-circle"></i> Mendorong digitalisasi dan transformasi teknologi</li>
                    <li><i class="bi bi-check-circle"></i> Mengutamakan keselamatan dan kelestarian lingkungan</li>
                    <li><i class="bi bi-check-circle"></i> Mengembangkan SDM yang profesional dan berintegritas</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Tentang Kami -->
      <section id="tentang" class="section about-section">
        <div class="container">
          <div class="section-header" data-aos="fade-up" data-aos-duration="800">
            <h2><i class="bi bi-building"></i> Tentang Kami</h2>
            <div class="divider"></div>
          </div>
          
          <div class="row align-items-center">
            <div class="col-lg-8">
              <p class="about-text" data-aos="fade-right" data-aos-duration="800" data-aos-delay="100">
                PT Indonesia Power Saguling merupakan unit operasi dari PT Indonesia Power, anak perusahaan PLN (Persero), 
                yang bertanggung jawab atas pengelolaan dan pemeliharaan Pembangkit Listrik Tenaga Air (PLTA) Saguling 
                dengan kapasitas 700 MW. Lokasi strategis di Kabupaten Bandung Barat menjadikan PLTA ini salah satu 
                penopang utama kelistrikan Jawa-Bali.
              </p>
              <p class="about-text" data-aos="fade-right" data-aos-duration="800" data-aos-delay="200">
                Dengan sistem monitoring berbasis digital, kami memastikan operasional pembangkit berjalan optimal, 
                aman, dan dapat dipantau secara real-time oleh tim teknis maupun manajemen.
              </p>
              
              <div class="badges-container">
                <span class="badge badge-animate" data-aos="zoom-in" data-aos-delay="300"><i class="bi bi-lightning-charge"></i> PLTA Saguling</span>
                <span class="badge badge-animate" data-aos="zoom-in" data-aos-delay="400"><i class="bi bi-lightning"></i> 700 MW</span>
                <span class="badge badge-animate" data-aos="zoom-in" data-aos-delay="500"><i class="bi bi-cpu"></i> Digital Monitoring</span>
              </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-left" data-aos-duration="800" data-aos-delay="300">
              <div class="about-image">
                <img src="img/Foto.jpg" alt="PLTA Saguling" class="img-fluid rounded">
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Kontak -->
      <section id="kontak" class="section contact-section">
        <div class="container">
          <div class="section-header" data-aos="fade-up" data-aos-duration="800">
            <h2><i class="bi bi-headset"></i> Hubungi Kami</h2>
            <div class="divider"></div>
          </div>
          
          <div class="row g-4">
            <div class="col-md-6">
              <div class="contact-info" data-aos="fade-right" data-aos-duration="800">
                <div class="contact-item" data-aos="fade-up" data-aos-delay="100">
                  <div class="contact-icon">
                    <i class="bi bi-geo-alt"></i>
                  </div>
                  <div>
                    <h4>Alamat Kantor</h4>
                    <p>
                      Komplek PLN Cioray<br>
                      Tromol Pos No. 7, Rajamandala<br>
                      Kecamatan Cipongkor, Kabupaten Bandung Barat<br>
                      Jawa Barat 40554
                    </p>
                  </div>
                </div>
                
                <div class="contact-item" data-aos="fade-up" data-aos-delay="200">
                  <div class="contact-icon">
                    <i class="bi bi-building"></i>
                  </div>
                  <div>
                    <h4>Unit</h4>
                    <p>
                      PT PLN Indonesia Power<br>
                      Saguling POMU (Power Generation and O&M Services Unit)
                    </p>
                  </div>
                </div>
                
                <div class="contact-item" data-aos="fade-up" data-aos-delay="300">
                  <div class="contact-icon">
                    <i class="bi bi-telephone"></i>
                  </div>
                  <div>
                    <h4>Telepon</h4>
                    <p>(022) 6868 1234</p>
                  </div>
                </div>
                
                <div class="contact-item" data-aos="fade-up" data-aos-delay="400">
                  <div class="contact-icon">
                    <i class="bi bi-envelope"></i>
                  </div>
                  <div>
                    <h4>Email</h4>
                    <p>info.saguling@indonesiapower.co.id</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6" data-aos="fade-left" data-aos-duration="800" data-aos-delay="300">
              <div class="map-container">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.829944027793!2d107.4479489748169!3d-6.85863999324794!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e7d7d7c7f5c1%3A0x5a5f5d5a5a5a5a5a!2sPLTA%20Saguling%20(PT%20Indonesia%20Power%20-%20Saguling%20POMU)!5e0!3m2!1sid!2sid!4v1720000000000!5m2!1sid!2sid"
                  width="100%"
                  height="100%"
                  style="border:0; border-radius: 8px;"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
<?= $this->include('layouts/footer'); ?>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <script>
    // Script untuk splash screen
    document.addEventListener('DOMContentLoaded', function() {
      // Tampilkan splash screen
      const splashScreen = document.getElementById('splash-screen');
      const mainContent = document.getElementById('main-content');
      
      // Setelah 3 detik, sembunyikan splash screen dan tampilkan konten utama
      setTimeout(function() {
        splashScreen.style.opacity = '0';
        splashScreen.style.transition = 'opacity 0.5s ease';
        
        setTimeout(function() {
          splashScreen.style.display = 'none';
          mainContent.style.display = 'block';
          
          // Inisialisasi AOS
          AOS.init({
            duration: 800,
            once: true,
            offset: 100
          });
          
          // Inisialisasi peta
          initMap();
        }, 500);
      }, 3000);
    });
    
    // Fungsi inisialisasi peta
    function initMap() {
      // Inisialisasi peta
      var map = L.map('map', {
        zoomControl: true,
        attributionControl: false
      }).setView([-6.9145, 107.365], 15);

      // Layer Satelit Google
      L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        attribution: '© Google Satellite'
      }).addTo(map);

      // Data titik instrumentasi dari KML Anda
      const instrumentasiData = [
        // Inclinometer (Merah)
        { name: "SPI-1", lat: -6.914631, lng: 107.364775, category: "Inclinometer" },
        { name: "SPI-3", lat: -6.914307, lng: 107.36455, category: "Inclinometer" },
        { name: "SPI-5(R)", lat: -6.914198, lng: 107.364473, category: "Inclinometer" },
        { name: "SPI-10", lat: -6.914749, lng: 107.365107, category: "Inclinometer" },
        { name: "SPI-9", lat: -6.913772, lng: 107.364413, category: "Inclinometer" },
        { name: "SPI-6", lat: -6.913703, lng: 107.364847, category: "Inclinometer" },
        { name: "SPI-4", lat: -6.914013999999999, lng: 107.365015, category: "Inclinometer" },
        { name: "SPI-8", lat: -6.912039, lng: 107.364294, category: "Inclinometer" },
        { name: "SPI-7", lat: -6.912077000000001, lng: 107.364223, category: "Inclinometer" },
        { name: "SPI-2", lat: -6.91433, lng: 107.365235, category: "Inclinometer" },
        { name: "I-9", lat: -6.911374, lng: 107.366971, category: "Inclinometer" },

        // Bubble Tiltmeter (Putih)
        { name: "BT-8", lat: -6.914446000000001, lng: 107.365536, category: "Bubble Tiltmeter" },
        { name: "BT-7", lat: -6.912076, lng: 107.364221, category: "Bubble Tiltmeter" },
        { name: "BT-6", lat: -6.912441000000001, lng: 107.364212, category: "Bubble Tiltmeter" },
        { name: "BT-1", lat: -6.914653, lng: 107.364917, category: "Bubble Tiltmeter" },
        { name: "BT-2", lat: -6.914062, lng: 107.364588, category: "Bubble Tiltmeter" },
        { name: "BT-4", lat: -6.913903, lng: 107.364854, category: "Bubble Tiltmeter" },
        { name: "BT-3", lat: -6.914621, lng: 107.365137, category: "Bubble Tiltmeter" },

        // Rod Extensometer (Biru telur asin)
        { name: "EX-1", lat: -6.9146055, lng: 107.3654281, category: "Rod Extensometer" },
        { name: "EX-2", lat: -6.9137974, lng: 107.3650325, category: "Rod Extensometer" },
        { name: "EX-3", lat: -6.914345900000001, lng: 107.3647495, category: "Rod Extensometer" },
        { name: "EX-4", lat: -6.9125885, lng: 107.3646087, category: "Rod Extensometer" },

        // HDM (Hijau)
        { name: "625 mdpl", lat: -6.912065699999999, lng: 107.3660635, category: "HDM" },
        { name: "600 mdpl", lat: -6.911739600000001, lng: 107.3657752, category: "HDM" },

        // Piezo Gallery (Kuning)
        { name: "RP-7", lat: -6.9115425, lng: 107.3674113, category: "Piezo Gallery" },
        { name: "RP-6", lat: -6.9117249, lng: 107.3672316, category: "Piezo Gallery" },
        { name: "RP-5", lat: -6.9118687, lng: 107.3670941, category: "Piezo Gallery" },
        { name: "RP-4", lat: -6.9119965, lng: 107.3669761, category: "Piezo Gallery" },
        { name: "RP-3", lat: -6.912097700000001, lng: 107.3668802, category: "Piezo Gallery" },
        { name: "RP-2", lat: -6.9122042, lng: 107.3667568, category: "Piezo Gallery" },
        { name: "RP-1", lat: -6.9123586, lng: 107.366612, category: "Piezo Gallery" },
        { name: "LP-1", lat: -6.9125397, lng: 107.3663921, category: "Piezo Gallery" },
        { name: "LP-2", lat: -6.9126462, lng: 107.3662177, category: "Piezo Gallery" },
        { name: "LP-3", lat: -6.912723400000001, lng: 107.3660594, category: "Piezo Gallery" },
        { name: "LP-4", lat: -6.9128299, lng: 107.3658502, category: "Piezo Gallery" },
        { name: "LP-5", lat: -6.912963100000001, lng: 107.3656088, category: "Piezo Gallery" },
        { name: "LP-6", lat: -6.9130909, lng: 107.3653889, category: "Piezo Gallery" },
        { name: "Talang 1", lat: -6.911777799999999, lng: 107.3672272, category: "Piezo Gallery" },
        { name: "Talang 2", lat: -6.9118257, lng: 107.3671856, category: "Piezo Gallery" },
        { name: "Pipa 1", lat: -6.9118763, lng: 107.3671427, category: "Piezo Gallery" },

        // V-notch (Ungu)
        { name: "R", lat: -6.9124623, lng: 107.3665487, category: "V-notch" },
        { name: "L", lat: -6.9125049, lng: 107.3664844, category: "V-notch" },
        { name: "B1", lat: -6.913404799999999, lng: 107.3652881, category: "V-notch" },
        { name: "B3", lat: -6.9116262, lng: 107.3649662, category: "V-notch" },
        { name: "B5", lat: -6.9115303, lng: 107.365036, category: "V-notch" },
        { name: "B6", lat: -6.910550400000001, lng: 107.3647517, category: "V-notch" },

        // PIZ Piezometer (Oranye)
        { name: "L-5", lat: -6.915093, lng: 107.365028, category: "PIZ Piezometer" },
        { name: "SPZ-12", lat: -6.914169, lng: 107.364489, category: "PIZ Piezometer" },
        { name: "L-6", lat: -6.913956000000001, lng: 107.364508, category: "PIZ Piezometer" },
        { name: "L-1", lat: -6.91367, lng: 107.364947, category: "PIZ Piezometer" },
        { name: "L-2", lat: -6.914455000000001, lng: 107.365286, category: "PIZ Piezometer" },
        { name: "L-8", lat: -6.912424, lng: 107.363795, category: "PIZ Piezometer" },
        { name: "L-7", lat: -6.913056000000001, lng: 107.364201, category: "PIZ Piezometer" },
        { name: "L-4", lat: -6.911599, lng: 107.364555, category: "PIZ Piezometer" },
        { name: "L-9", lat: -6.912633, lng: 107.36522, category: "PIZ Piezometer" },
        { name: "L-10", lat: -6.911519000000001, lng: 107.364979, category: "PIZ Piezometer" },
        { name: "R-3", lat: -6.911417999999999, lng: 107.366677, category: "PIZ Piezometer" },
        { name: "R-11", lat: -6.911508, lng: 107.366998, category: "PIZ Piezometer" },
        { name: "PZ-4", lat: -6.911662, lng: 107.367421, category: "PIZ Piezometer" },
        { name: "R-1", lat: -6.911317, lng: 107.367639, category: "PIZ Piezometer" },
        { name: "R-5", lat: -6.910636, lng: 107.368206, category: "PIZ Piezometer" },
        { name: "L-3", lat: -6.912632, lng: 107.36481, category: "PIZ Piezometer" }
      ];

      // Warna kategori sesuai instruksi
      const kategoriWarna = {
        "V-notch": "#8000ff",          // Ungu
        "Rod Extensometer": "#0070ff", // Biru telur asin
        "PIZ Piezometer": "#ff8000",   // Oranye
        "Piezo Gallery": "#ffff00",    // Kuning
        "Inclinometer": "#ff0000",     // Merah
        "HDM": "#00ff00",              // Hijau
        "Bubble Tiltmeter": "#ffffff"  // Putih
      };

      // Tambahkan marker untuk setiap titik
      instrumentasiData.forEach(point => {
        const color = kategoriWarna[point.category];
        
        // Buat custom marker dengan warna sesuai kategori
        const marker = L.circleMarker([point.lat, point.lng], {
          radius: 8,
          fillColor: color,
          color: "#000000",
          weight: 1,
          opacity: 1,
          fillOpacity: 0.8,
          className: 'custom-marker'
        }).addTo(map);

        // Tambahkan popup dengan informasi
        marker.bindPopup(`
          <div style="text-align: center;">
            <strong>${point.name}</strong><br>
            <span style="color: ${color}; font-weight: bold;">${point.category}</span><br>
            <small>Lat: ${point.lat.toFixed(6)}<br>Lng: ${point.lng.toFixed(6)}</small>
          </div>
        `);

        // Tambahkan label teks
        const label = L.marker([point.lat, point.lng], {
          icon: L.divIcon({
            className: 'label-titik',
            html: `<span style="color:${color}; text-shadow: 2px 2px 4px #000000;">${point.name}</span>`,
            iconSize: [60, 20],
            iconAnchor: [30, 10]
          })
        }).addTo(map);
      });

      // Legenda kiri atas
      var legend = L.control({ position: "topleft" });
      legend.onAdd = function() {
        var div = L.DomUtil.create("div", "info legend");
        div.innerHTML = `
          <img src="/img/logo-pln.png" style="width:120px;margin-bottom:10px;"><br>
          <strong style="color:#00aaff;">INFORMASI GEOTEKNIK AREA BENDUNGAN SAGULING</strong><hr>
          <p><span style="background:#8000ff" class="legend-box"></span>V-notch (Thompson)</p>
          <p><span style="background:#0070ff" class="legend-box"></span>Rod Extensometer</p>
          <p><span style="background:#ff8000" class="legend-box"></span>PIZ Piezometer</p>
          <p><span style="background:#ffff00" class="legend-box"></span>Piezo Gallery</p>
          <p><span style="background:#ff0000" class="legend-box"></span>Inclinometer</p>
          <p><span style="background:#00ff00" class="legend-box"></span>HDM</p>
          <p><span style="background:#ffffff;border:1px solid #aaa" class="legend-box"></span>Bubble Tiltmeter</p>
        `;
        return div;
      };
      legend.addTo(map);

      // Fit bounds untuk menampilkan semua marker
      const group = new L.featureGroup(instrumentasiData.map(point => 
        L.marker([point.lat, point.lng])
      ));
      map.fitBounds(group.getBounds().pad(0.1));
    }
  </script>

</body>
</html>