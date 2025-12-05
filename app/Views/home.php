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

  <!-- Custom CSS -->
  <link rel="stylesheet" href="/css/style.css">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
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
  </style>
</head>
<body>

  <!-- Main Content -->
  <main>
    <?= $this->include('layouts/header'); ?>
    
    <!-- Hero Section -->
    <section id="home" class="hero-section">
      <div class="container">
        <div class="hero-content">
          <div class="hero-text">
            <h1>Selamat Datang di Sistem Monitoring PLTA Saguling</h1>
            <p class="lead">
              Efisiensi, keamanan, dan transparansi operasional pembangkit listrik tenaga air berbasis teknologi digital.
            </p>
            <a href="/auth/login" class="btn btn-primary btn-cta">
              <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sistem
            </a>
          </div>
          <div class="hero-image">
            <img src="img/slide1.jpg" alt="Monitoring Bendungan Saguling" class="img-fluid">
          </div>
        </div>
      </div>
    </section>

    <!-- Peta Instrumentasi Area Bendungan Saguling -->
    <section id="peta" class="section map-section" style="background:#101820; color:white;">
      <div class="container-fluid px-4 py-3">
        <h2 class="text-center mb-4">INFORMASI GEOTEKNIK AREA BENDUNGAN SAGULING</h2>
        <div id="map"></div>
      </div>
    </section>

    <!-- Visi & Misi-->
    <section id="visi-misi" class="section vision-mission">
      <div class="container">
        <div class="section-header">
          <h2><i class="bi bi-stars"></i> Visi & Misi Perusahaan</h2>
          <div class="divider"></div>
        </div>
        
        <div class="row g-4">
          <div class="col-lg-6">
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
          
          <div class="col-lg-6">
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
        <div class="section-header">
          <h2><i class="bi bi-building"></i> Tentang Kami</h2>
          <div class="divider"></div>
        </div>
        
        <div class="row align-items-center">
          <div class="col-lg-8">
            <p class="about-text">
              PT Indonesia Power Saguling merupakan unit operasi dari PT Indonesia Power, anak perusahaan PLN (Persero), 
              yang bertanggung jawab atas pengelolaan dan pemeliharaan Pembangkit Listrik Tenaga Air (PLTA) Saguling 
              dengan kapasitas 700 MW. Lokasi strategis di Kabupaten Bandung Barat menjadikan PLTA ini salah satu 
              penopang utama kelistrikan Jawa-Bali.
            </p>
            <p class="about-text">
              Dengan sistem monitoring berbasis digital, kami memastikan operasional pembangkit berjalan optimal, 
              aman, dan dapat dipantau secara real-time oleh tim teknis maupun manajemen.
            </p>
            
            <div class="badges-container">
              <span class="badge"><i class="bi bi-lightning-charge"></i> PLTA Saguling</span>
              <span class="badge"><i class="bi bi-lightning"></i> 700 MW</span>
              <span class="badge"><i class="bi bi-cpu"></i> Digital Monitoring</span>
            </div>
          </div>
          
          <div class="col-lg-4">
            <div class="about-image">
              <img src="img/power-plant.jpg" alt="PLTA Saguling" class="img-fluid rounded">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="section contact-section">
      <div class="container">
        <div class="section-header">
          <h2><i class="bi bi-headset"></i> Hubungi Kami</h2>
          <div class="divider"></div>
        </div>
        
        <div class="row g-4">
          <div class="col-md-6">
            <div class="contact-info">
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                <div>
                  <h4>Alamat Kantor</h4>
                  <p>Komplek PLN Cioray<br>Tromol Pos No. 7, Rajamandala<br>Kab. Bandung Barat<br>Jawa Barat 40554</p>
                </div>
              </div>
              
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-building"></i></div>
                <div>
                  <h4>Unit</h4>
                  <p>PT PLN Indonesia Power<br>Saguling POMU</p>
                </div>
              </div>
              
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                <div>
                  <h4>Telepon</h4>
                  <p>(022) 6868 1234</p>
                </div>
              </div>
              
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                <div>
                  <h4>Email</h4>
                  <p>info.saguling@indonesiapower.co.id</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="map-container">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.829944027793!2d107.4479489748169!3d-6.85863999324794!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e7d7d7c7f5c1%3A0x5a5f5d5a5a5a5a5a!2sPLTA%20Saguling%20(PT%20Indonesia%20Power%20-%20Saguling%20POMU)!5e0!3m2!1sid!2sid!4v1720000000000!5m2!1sid!2sid"
                width="100%" height="100%" style="border:0; border-radius:8px;" allowfullscreen=""
                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?= $this->include('layouts/footer'); ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <script>
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
  </script>

</body>
</html>