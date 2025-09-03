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
    /* Professional Header */
    .main-header {
      background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
      color: white;
      padding: 1rem 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
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
    }
    
    .nav-link:hover, .nav-link.active {
      background-color: rgba(255,255,255,0.15);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .header-container {
        flex-direction: column;
        gap: 1rem;
      }
      
      .branding {
        flex-direction: column;
        text-align: center;
      }
      
      .logo-divider {
        display: none;
      }
      
      .main-nav .nav-menu {
        flex-wrap: wrap;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
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
            <span class="unit-name">Unit Bisnis Pembangkitan Saguling</span>
          </h1>
          <p class="system-name">Sistem Monitoring Operasional PLTA</p>
        </div>
      </div>
      
      <nav class="main-nav">
        <ul class="nav-menu">
          <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>">
              <i class="bi bi-house-door"></i>
              <span>Beranda</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('input-data') ?>" class="nav-link <?= strpos(current_url(), 'input-data') !== false ? 'active' : '' ?>">
              <i class="bi bi-table"></i>
              <span>Data Rembesan</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('grafik') ?>" class="nav-link <?= strpos(current_url(), 'grafik') !== false ? 'active' : '' ?>">
              <i class="bi bi-graph-up"></i>
              <span>Grafik</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#kontak" class="nav-link">
              <i class="bi bi-telephone"></i>
              <span>Kontak</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="flex-shrink-0">