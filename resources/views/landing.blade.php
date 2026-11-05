<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI PS ONLINE — Dinas Kehutanan Provinsi Jawa Timur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f5132">
    <style>
        :root {
            --brand-green: #0f5132;
            --brand-green-hover: #0a3622;
            --brand-leaf: #198754;
            --brand-soft: #e8f5e9;
            --dark: #0f172a;
            --slate: #334155;
            --muted: #64748b;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
            --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        html, body { overflow-x: hidden; width: 100%; max-width: 100%; }
        body { background: #ffffff; color: var(--dark); line-height: 1.6; }

        /* NAVBAR */
        .site-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--dark);
        }
        .brand-logo {
            width: 40px;
            height: auto;
        }
        .brand-text h1 {
            font-size: 17px;
            font-weight: 800;
            color: var(--brand-green);
            letter-spacing: -0.3px;
            line-height: 1.1;
        }
        .brand-text p {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
        }
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 28px;
        }
        .nav-menu a {
            text-decoration: none;
            color: var(--slate);
            font-size: 13px;
            font-weight: 600;
            transition: color 0.2s;
        }
        .nav-menu a:hover { color: var(--brand-leaf); }
        .btn-nav-login {
            background: var(--brand-green);
            color: #fff !important;
            padding: 9px 22px;
            border-radius: 50px;
            font-weight: 700 !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(15, 81, 50, 0.2);
            transition: all 0.2s !important;
        }
        .btn-nav-login:hover {
            background: var(--brand-green-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 81, 50, 0.3);
        }

        /* HERO SECTION */
        .hero {
            padding: 60px 0 50px;
            background: radial-gradient(circle at top right, #edf7ee 0%, #ffffff 60%);
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 40px;
            align-items: center;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand-soft);
            color: var(--brand-green);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .hero-heading {
            font-size: 40px;
            font-weight: 800;
            line-height: 1.2;
            color: #0b2e1b;
            margin-bottom: 16px;
            letter-spacing: -0.8px;
        }
        .hero-heading span {
            color: var(--brand-leaf);
        }
        .hero-desc {
            font-size: 15px;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.7;
            max-width: 540px;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .btn-hero-primary {
            background: var(--brand-green);
            color: #fff;
            padding: 13px 28px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(15, 81, 50, 0.25);
            transition: all 0.2s;
        }
        .btn-hero-primary:hover {
            background: var(--brand-green-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 81, 50, 0.35);
        }
        .btn-hero-secondary {
            background: #fff;
            color: var(--slate);
            border: 1px solid var(--border);
            padding: 13px 26px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-hero-secondary:hover {
            background: var(--light-bg);
            border-color: #cbd5e1;
        }

        /* HERO CARD VISUAL */
        .visual-card-main {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.08);
        }
        .visual-card-main img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }
        .visual-card-body {
            padding: 20px 22px;
        }
        .visual-card-body h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }
        .visual-card-body p {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 12px;
        }
        .visual-cdk-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e0f2fe;
            color: #0369a1;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        /* STATS BAR */
        .stats-section {
            padding: 35px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: var(--light-bg);
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }
        .stat-card-clean {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--card-shadow);
        }
        .stat-chip {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .stat-chip.green { background: #dcfce7; color: #15803d; }
        .stat-chip.blue { background: #e0f2fe; color: #0284c7; }
        .stat-chip.amber { background: #fef3c7; color: #d97706; }
        .stat-chip.purple { background: #f3e8ff; color: #7e22ce; }
        .stat-meta strong {
            font-size: 22px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.1;
            display: block;
        }
        .stat-meta span {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
        }

        /* FEATURES SECTION */
        .features-section {
            padding: 70px 0 50px;
        }
        .section-title-wrap {
            text-align: center;
            max-width: 620px;
            margin: 0 auto 45px;
        }
        .section-tag {
            font-size: 12px;
            font-weight: 700;
            color: var(--brand-leaf);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: block;
        }
        .section-title-wrap h2 {
            font-size: 28px;
            font-weight: 800;
            color: #0b2e1b;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .section-title-wrap p {
            font-size: 14px;
            color: var(--muted);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }
        .feature-box {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 26px 22px;
            transition: all 0.25s ease;
            box-shadow: var(--card-shadow);
        }
        .feature-box:hover {
            transform: translateY(-4px);
            border-color: #86efac;
            box-shadow: 0 12px 30px rgba(0,0,0,0.06);
        }
        .f-icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #f0fdf4;
            color: var(--brand-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 16px;
        }
        .feature-box h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 6px;
        }
        .feature-box p {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* 3 SKEMA PS SECTION */
        .skema-section {
            background: var(--light-bg);
            padding: 60px 0;
            border-top: 1px solid var(--border);
        }
        .skema-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        .skema-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 14px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }
        .skema-card .s-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .skema-card h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }
        .skema-card p {
            font-size: 11px;
            color: var(--muted);
        }

        /* CDK SECTION */
        .cdk-strip {
            background: #0f5132;
            color: #fff;
            padding: 55px 0;
            text-align: center;
        }
        .cdk-strip h3 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .cdk-strip p {
            font-size: 13px;
            color: #bbf7d0;
            max-width: 600px;
            margin: 0 auto 24px;
        }
        .cdk-pills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            max-width: 900px;
            margin: 0 auto 28px;
        }
        .cdk-pill-item {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #ffffff;
        }

        /* FOOTER */
        footer {
            background: #0b2416;
            color: #94a3b8;
            padding: 45px 0 25px;
            font-size: 13px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1.5fr;
            gap: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .footer-col h4 {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .footer-col p {
            line-height: 1.7;
            margin-bottom: 8px;
        }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 6px; }
        .footer-col a { color: #94a3b8; text-decoration: none; transition: color 0.2s; }
        .footer-col a:hover { color: #4ade80; }
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            font-size: 12px;
            color: #64748b;
        }

        /* TOMBOL PWA INSTALL */
        .btn-pwa-install {
            display: none; /* Otomatis tersembunyi secara default */
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            background: linear-gradient(135deg, #0f5132 0%, #198754 100%);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(15, 81, 50, 0.35);
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: pwaPulse 3s infinite;
        }
        .btn-pwa-install:hover {
            background: linear-gradient(135deg, #0a3622 0%, #157347 100%);
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 30px rgba(15, 81, 50, 0.45);
        }
        .btn-pwa-install:active {
            transform: translateY(-1px) scale(0.98);
        }
        .btn-pwa-install i {
            font-size: 16px;
        }
        @keyframes pwaPulse {
            0%, 100% { box-shadow: 0 8px 24px rgba(15, 81, 50, 0.35); }
            50% { box-shadow: 0 8px 32px rgba(25, 135, 84, 0.55); }
        }

        @media(max-width: 992px) {
            .hero-grid { grid-template-columns: 1fr; }
            .hero-visual { display: none; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: 1fr; }
            .skema-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-grid { grid-template-columns: 1fr; }
        }

        @media(max-width: 768px) {
            .site-nav { padding: 10px 0; }
            .nav-menu .nav-link-item { display: none; }
            .nav-menu { gap: 8px; }
            .brand-logo { width: 32px; }
            .brand-text h1 { font-size: 15px; }
            .brand-text p { display: none; }
            .btn-nav-login { padding: 7px 14px; font-size: 12px; }

            .hero { padding: 30px 0 25px; }
            .hero-badge { font-size: 10px; padding: 5px 10px; margin-bottom: 12px; white-space: normal; line-height: 1.3; }
            .hero-heading { font-size: 26px; line-height: 1.25; margin-bottom: 12px; }
            .hero-desc { font-size: 13px; line-height: 1.6; margin-bottom: 20px; }
            .hero-actions { flex-direction: column; width: 100%; gap: 10px; }
            .btn-hero-primary, .btn-hero-secondary { width: 100%; justify-content: center; padding: 12px 16px; font-size: 13.5px; }

            .stats-section { padding: 25px 0; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card-clean { padding: 12px 10px; gap: 10px; border-radius: 10px; }
            .stat-chip { width: 36px; height: 36px; font-size: 15px; border-radius: 8px; }
            .stat-meta strong { font-size: 16px; }
            .stat-meta span { font-size: 10.5px; }

            .features-section { padding: 40px 0 25px; }
            .section-title-wrap { margin-bottom: 25px; }
            .section-title-wrap h2 { font-size: 21px; }
            .section-title-wrap p { font-size: 12.5px; }
            .features-grid { grid-template-columns: 1fr; gap: 14px; }
            .feature-box { padding: 20px 16px; }

            .skema-section { padding: 35px 0; }
            .skema-grid { grid-template-columns: 1fr; gap: 12px; }
            .skema-card { padding: 14px 10px; }

            .cdk-strip { padding: 35px 0; }
            .cdk-strip h3 { font-size: 18px; }
            .cdk-strip p { font-size: 12px; margin-bottom: 18px; }
            .cdk-pills { gap: 6px; }
            .cdk-pill-item { font-size: 11px; padding: 4px 10px; }

            .footer-grid { grid-template-columns: 1fr; gap: 20px; padding-bottom: 20px; }
            footer { padding: 30px 0 20px; }
            .container { padding: 0 16px; width: 100%; max-width: 100%; }
        }

        @media(max-width: 480px) {
            .hero-heading { font-size: 23px; }
            .stats-row { grid-template-columns: 1fr; }
            .skema-grid { grid-template-columns: 1fr; }
            .btn-pwa-install {
                bottom: 16px;
                right: 16px;
                left: 16px;
                justify-content: center;
                padding: 12px 18px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="site-nav">
        <div class="container">
            <div class="nav-inner">
                <a href="{{ route('landing') }}" class="brand-link">
                    <img src="{{ asset('assets/logo-jatim.png') }}"
                         onerror="if(!this.dataset.retry){this.dataset.retry='1';this.src='{{ url('/assets/logo-jatim.png') }}';}else if(this.dataset.retry==='1'){this.dataset.retry='2';this.src='/assets/logo-jatim.png';}else if(this.dataset.retry==='2'){this.dataset.retry='3';this.src='/public/assets/logo-jatim.png';}"
                         alt="Logo Jatim" class="brand-logo">
                    <div class="brand-text">
                        <h1>SI PS ONLINE</h1>
                        <p>Dinas Kehutanan Provinsi Jawa Timur</p>
                    </div>
                </a>
                <nav class="nav-menu">
                    <a href="#fitur" class="nav-link-item">Fitur Sistem</a>
                    <a href="#skema" class="nav-link-item">Skema PS</a>
                    <a href="{{ route('peta.publik') }}" class="nav-link-item"><i class="fas fa-map-location-dot"></i> Peta Sebaran</a>
                    <a href="{{ route('login') }}" class="btn-nav-login">
                        <i class="fas fa-arrow-right-to-bracket"></i> <span>Masuk</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <div class="hero-badge">
                        <i class="fas fa-shield-halved"></i> Portal Resmi Pemerintah Provinsi Jawa Timur
                    </div>
                    <h2 class="hero-heading">
                        Sistem Informasi <span>Perhutanan Sosial</span> Terintegrasi
                    </h2>
                    <p class="hero-desc">
                        Layanan digital terpadu untuk pendataan kelompok, penyusunan RKPS &amp; RKT daring, monitoring transaksi nilai ekonomi, dan pemantauan pendampingan lapangan se-Jawa Timur.
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                        </a>
                        <a href="{{ route('peta.publik') }}" class="btn-hero-secondary">
                            <i class="fas fa-map-marked-alt"></i> Eksplorasi Peta Spasial
                        </a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="visual-card-main">
                        <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&q=80" alt="Hutan Lestari Jatim">
                        <div class="visual-card-body">
                            <h4>Kelestarian Hutan &amp; Kesejahteraan Warga</h4>
                            <p>Mendukung kemandirian kelompok pengelola hutan di Jawa Timur.</p>
                            <div class="visual-cdk-badge">
                                <i class="fas fa-building-flag"></i> Tersebar di 10 Wilayah CDK Jawa Timur
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-row">
                <div class="stat-card-clean">
                    <div class="stat-chip green"><i class="fas fa-users"></i></div>
                    <div class="stat-meta">
                        <strong>{{ number_format($kelompok) }}</strong>
                        <span>Kelompok PS Terdaftar</span>
                    </div>
                </div>
                <div class="stat-card-clean">
                    <div class="stat-chip blue"><i class="fas fa-expand"></i></div>
                    <div class="stat-meta">
                        <strong>{{ number_format($luasLahan, 0, ',', '.') }} Ha</strong>
                        <span>Total Luas Area Kelola</span>
                    </div>
                </div>
                <div class="stat-card-clean">
                    <div class="stat-chip amber"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-meta">
                        <strong>{{ number_format($pendamping) }}</strong>
                        <span>Tenaga Pendamping</span>
                    </div>
                </div>
                <div class="stat-card-clean">
                    <div class="stat-chip purple"><i class="fas fa-coins"></i></div>
                    <div class="stat-meta">
                        <strong>
                            @if($nte >= 1000000000)
                                Rp {{ number_format($nte / 1000000000, 1) }} M
                            @elseif($nte >= 1000000)
                                Rp {{ number_format($nte / 1000000, 1) }} Jt
                            @else
                                Rp {{ number_format($nte, 0, ',', '.') }}
                            @endif
                        </strong>
                        <span>Nilai Transaksi (NTE)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section class="features-section" id="fitur">
        <div class="container">
            <div class="section-title-wrap">
                <span class="section-tag">Pelayanan Digital</span>
                <h2>Kemudahan Tata Kelola Perhutanan Sosial</h2>
                <p>Fitur menyeluruh yang menghubungkan kelompok, pendamping, dan dinas kehutanan.</p>
            </div>

            <div class="features-grid">
                <div class="feature-box">
                    <div class="f-icon-circle"><i class="fas fa-folder-tree"></i></div>
                    <h3>Pangkalan Data Kelompok</h3>
                    <p>Pencatatan data legalitas SK, profil kelembagaan, kepengurusan, dan inventarisasi potensi komoditas kelompok.</p>
                </div>
                <div class="feature-box">
                    <div class="f-icon-circle"><i class="fas fa-file-signature"></i></div>
                    <h3>E-RKPS &amp; E-RKT</h3>
                    <p>Pengajuan dan verifikasi Rencana Kerja Perhutanan Sosial serta Rencana Kerja Tahunan secara daring tanpa berkas fisik.</p>
                </div>
                <div class="feature-box">
                    <div class="f-icon-circle"><i class="fas fa-map-location-dot"></i></div>
                    <h3>Peta Spasial Geografis</h3>
                    <p>Visualisasi poligon kawasan izin kelola dan titik sebaran kelompok PS berbasis GIS citra satelit.</p>
                </div>
                <div class="feature-box">
                    <div class="f-icon-circle"><i class="fas fa-chart-line"></i></div>
                    <h3>Monitoring Nilai Ekonomi (NTE)</h3>
                    <p>Pencatatan transaksi ekonomi hasil hutan bukan kayu (HHBK), pertanian agroforestri, dan valuasi produksi kelompok.</p>
                </div>
                <div class="feature-box">
                    <div class="f-icon-circle"><i class="fas fa-clipboard-check"></i></div>
                    <h3>Laporan Lapangan Berkala</h3>
                    <p>Pendampingan terpantau dengan dokumentasi foto kegiatan riil di lokasi binaan perhutanan sosial.</p>
                </div>
                <div class="feature-box">
                    <div class="f-icon-circle"><i class="fas fa-building-user"></i></div>
                    <h3>Akses Wilayah CDK</h3>
                    <p>Manajemen data terbagi secara spesifik per Cabang Dinas Kehutanan (CDK) untuk percepatan verifikasi dan supervisi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 SKEMA PS -->
    <section class="skema-section" id="skema">
        <div class="container">
            <div class="section-title-wrap">
                <span class="section-tag">Skema Program</span>
                <h2>3 Skema Perhutanan Sosial</h2>
                <p>Model pengelolaan hutan berbasis kemasyarakatan yang diimplementasikan di Jawa Timur.</p>
            </div>

            <div class="skema-grid">
                <div class="skema-card">
                    <span class="s-badge" style="background:#dcfce7;color:#15803d">HKm</span>
                    <h4>Hutan Kemasyarakatan</h4>
                    <p>Pemberdayaan masyarakat dalam pemanfaatan hutan lestari.</p>
                </div>
                <div class="skema-card">
                    <span class="s-badge" style="background:#fef3c7;color:#b45309">HD</span>
                    <h4>Hutan Desa</h4>
                    <p>Hutan negara yang dikelola oleh lembaga desa untuk kesejahteraan desa.</p>
                </div>
                <div class="skema-card">
                    <span class="s-badge" style="background:#e0f2fe;color:#0369a1">HTR</span>
                    <h4>Hutan Tanaman Rakyat</h4>
                    <p>Pembangunan hutan tanaman untuk meningkatkan potensi kayu/HHBK.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CDK STRIP -->
    <section class="cdk-strip">
        <div class="container">
            <h3>Terhubung dengan 10 Cabang Dinas Kehutanan (CDK)</h3>
            <p>Mencakup seluruh unit kerja pengawasan dan pendampingan kehutanan di Jawa Timur</p>
            <div class="cdk-pills">
                <span class="cdk-pill-item">CDK Bojonegoro</span>
                <span class="cdk-pill-item">CDK Madiun</span>
                <span class="cdk-pill-item">CDK Malang</span>
                <span class="cdk-pill-item">CDK Jember</span>
                <span class="cdk-pill-item">CDK Banyuwangi</span>
                <span class="cdk-pill-item">CDK Pacitan</span>
                <span class="cdk-pill-item">CDK Nganjuk</span>
                <span class="cdk-pill-item">CDK Trenggalek</span>
                <span class="cdk-pill-item">CDK Lumajang</span>
                <span class="cdk-pill-item">CDK Sumenep</span>
            </div>
            <a href="{{ route('login') }}" class="btn-hero-primary" style="background:#ffffff;color:#0f5132">
                <i class="fas fa-arrow-right-to-bracket"></i> Masuk ke Akun Anda
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>SI PS ONLINE JAWA TIMUR</h4>
                    <p>Sistem Informasi Perhutanan Sosial Online (SI PS ONLINE) adalah aplikasi resmi Dinas Kehutanan Pemerintah Provinsi Jawa Timur untuk mewujudkan tata kelola hutan yang lestari dan menyejahterakan masyarakat.</p>
                </div>
                <div class="footer-col">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="{{ route('landing') }}">Beranda Utama</a></li>
                        <li><a href="{{ route('peta.publik') }}">Peta Sebaran Kelompok</a></li>
                        <li><a href="{{ route('login') }}">Masuk Sistem</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Kontak Dinas Kehutanan</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Bandara Juanda No. 1, Semambung, Kec. Gedangan, Kab. Sidoarjo, Jawa Timur 61254</p>
                    <p><i class="fas fa-globe"></i> dishut.jatimprov.go.id</p>
                    <p><i class="fas fa-phone"></i> (031) 8667610</p>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Dinas Kehutanan Pemerintah Provinsi Jawa Timur. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <!-- TOMBOL INSTALL APLIKASI PWA (TERSEMBUNYI SECARA DEFAULT) -->
    <button id="btnPwaInstall" class="btn-pwa-install" aria-label="Install Aplikasi SI PS ONLINE">
        <i class="fas fa-mobile-screen-button"></i>
        <span>Install Aplikasi</span>
    </button>

    <!-- SCRIPT PWA BEFOREINSTALLPROMPT -->
    <script>
    let deferredPrompt = null;
    const btnPwa = document.getElementById('btnPwaInstall');

    // 1. Dengarkan event 'beforeinstallprompt' dari browser
    window.addEventListener('beforeinstallprompt', (e) => {
        // Cegah prompt default browser agar tidak langsung muncul otomatis
        e.preventDefault();
        // Simpan event untuk dipicu saat tombol diklik
        deferredPrompt = e;
        // Tampilkan tombol instalasi (dari display: none menjadi inline-flex)
        if (btnPwa) {
            btnPwa.style.display = 'inline-flex';
        }
    });

    // 2. Event saat tombol install diklik
    if (btnPwa) {
        btnPwa.addEventListener('click', async () => {
            if (!deferredPrompt) {
                return;
            }

            // Jalankan fungsi prompt instalasi bawaan browser
            deferredPrompt.prompt();

            // Tunggu pilihan respon dari pengguna (accept / dismiss)
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`Pilihan pengguna: ${outcome}`);

            // Reset variable prompt dan sembunyikan kembali tombolnya
            deferredPrompt = null;
            btnPwa.style.display = 'none';
        });
    }

    // 3. Event ketika aplikasi sudah berhasil terinstal di perangkat
    window.addEventListener('appinstalled', () => {
        if (btnPwa) {
            btnPwa.style.display = 'none';
        }
        deferredPrompt = null;
        console.log('Aplikasi SI PS ONLINE berhasil diinstal ke perangkat.');
    });
    </script>
</body>
</html>