<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahir Cermat</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
            }
        }

        body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
        }

        .background {
            position: fixed;
            width: 100vw;
            height: 100vh;
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            background: #1ab394;
            overflow: hidden;
            z-index: -1;
        }

        .background li {
            position: absolute;
            display: block;
            list-style: none;
            color: rgba(255, 255, 255, 0.2);
            font-size: 86px;
            font-weight: bold;
            animation: animate 19s linear infinite;
        }

        /* Animation positions remain the same */
        .background li:nth-child(1) {
            left: 74%;
            bottom: -100px;
            animation-delay: 4s;
        }

        .background li:nth-child(2) {
            left: 16%;
            bottom: -100px;
            animation-delay: 2s;
        }

        .background li:nth-child(3) {
            left: 88%;
            bottom: -100px;
            animation-delay: 9s;
        }

        .background li:nth-child(4) {
            left: 64%;
            bottom: -100px;
            animation-delay: 12s;
        }

        .background li:nth-child(5) {
            left: 69%;
            bottom: -100px;
            animation-delay: 21s;
        }

        .background li:nth-child(6) {
            left: 71%;
            bottom: -100px;
            animation-delay: 18s;
        }

        .background li:nth-child(7) {
            left: 38%;
            bottom: -100px;
            animation-delay: 9s;
        }

        .background li:nth-child(8) {
            left: 75%;
            bottom: -100px;
            animation-delay: 33s;
        }

        .background li:nth-child(9) {
            left: 9%;
            bottom: -100px;
            animation-delay: 27s;
        }

        .background li:nth-child(10) {
            left: 4%;
            bottom: -100px;
            animation-delay: 1s;
        }

        .background li:nth-child(11) {
            left: 45%;
            bottom: -100px;
            animation-delay: 15s;
        }

        .background li:nth-child(12) {
            left: 25%;
            bottom: -100px;
            animation-delay: 8s;
        }

        .background li:nth-child(13) {
            left: 55%;
            bottom: -100px;
            animation-delay: 23s;
        }

        .background li:nth-child(14) {
            left: 85%;
            bottom: -100px;
            animation-delay: 17s;
        }

        .background li:nth-child(15) {
            left: 32%;
            bottom: -100px;
            animation-delay: 11s;
        }

        .background li:nth-child(16) {
            left: 92%;
            bottom: -100px;
            animation-delay: 14s;
        }

        .background li:nth-child(17) {
            left: 28%;
            bottom: -100px;
            animation-delay: 19s;
        }

        .background li:nth-child(18) {
            left: 62%;
            bottom: -100px;
            animation-delay: 25s;
        }

        .background li:nth-child(19) {
            left: 15%;
            bottom: -100px;
            animation-delay: 30s;
        }

        .background li:nth-child(20) {
            left: 48%;
            bottom: -100px;
            animation-delay: 22s;
        }

        /* Updated Navbar Styles */
        .navbar {
            background-color: rgba(26, 179, 148, 0.95) !important;
            padding: 1rem 0;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .nav-link {
            color: white !important;
            margin: 0 0.5rem;
        }

        .navbar-toggler {
            border-color: white;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .logo {
            width: 80px;
            height: 50px;
            object-fit: contain;
            filter: invert(10%) brightness(200%);
        }

        /* Hero Section Styles */
        .hero-section {
            min-height: 100vh;
            padding: 6rem 0 3rem;
            display: flex;
            align-items: center;
        }

        .hero-text {
            color: white;
            font-weight: bold;
            line-height: 1.2;
            font-size: calc(2rem + 2vw);
            margin-bottom: 1rem;
        }

        .hero-subtext {
            color: white;
            font-size: calc(1rem + 0.5vw);
            margin: 1.5rem 0;
            font-weight: normal;
            text-align: center;
        }

        .tagline-container {
            margin: 2rem 0;
            text-align: center;
        }

        .tagline-item {
            color: white;
            font-size: calc(1.5rem + 1vw);
            font-weight: bold;
            margin: 0 0.5rem;
            display: inline-block;
        }

        .tagline-item.highlight {
            color: #FFD700;
        }

        /* Laptop Container Styles */
        .laptop-container {
            position: relative;
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .laptop-frame {
            position: relative;
            width: 100%;
            margin: 0 auto;
        }

        .laptop-base {
            width: 100%;
            height: auto;
            display: block;
        }

        .screen-content {
            position: absolute;
            top: 5%;
            left: 15.2%;
            width: 70%;
            height: 78%;
            background: white;
            overflow: hidden;
            border-radius: 3px;
        }

        .screen-content img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .get-started-btn {
            background: white;
            color: #333;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .get-started-btn:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            text-decoration: none;
            color: #333;
        }

        /* Packages Section */
        .packages-section {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 5rem 0;
            position: relative;
            z-index: 2;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            color: #1ab394;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .section-title p {
            color: #6c757d;
            font-size: 1.2rem;
        }

        .package-card {
            border: 1px solid #e0e7ff;
            border-radius: 10px;
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 90%;
            overflow: hidden;
            justify-content: space-between;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
            /* bayangan lebih dalam */
            cursor: pointer;
        }

        .popular-package::before {
            content: "⭐ Paling Populer";
            position: absolute;
            top: 12px;
            /* geser sedikit ke bawah */
            right: -50px;
            /* atur posisi horizontal */
            background: #10B981;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 40px;
            transform: rotate(45deg);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .popular-package {
            border: 3px solid #10B981;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.3);
        }

        .package-features {
            flex-grow: 1;
            /* biar fitur ngisi ruang tengah */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .package-pricing {
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .package-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .package-header {
            margin-bottom: 0.5rem;
            min-height: 100px;
        }

        .package-header h3 {
            color: #111827;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .package-description {
            color: #6B7280;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .package-price {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .price-period {
            color: #6B7280;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .features-list {
            flex: 1;
            margin-bottom: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .feature-item i {
            margin-right: 0.75rem;
            font-size: 1rem;
            min-width: 16px;
        }

        .feature-item.available {
            transition: transform 0.3s ease;
        }

        .feature-item.available:hover {
            transform: translateX(5px);
            cursor: pointer;
        }

        .feature-item.available i {
            color: #10B981;
        }

        .feature-item.unavailable {
            opacity: 0.4;
        }

        .feature-item.unavailable i {
            color: #D1D5DB;
        }

        .btn-package {
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 10px;
            width: 100%;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        /* Variants for different package buttons */
        .btn-package.btn-outline-primary {
            border-color: #007bff;
            border: 1px solid #007bff;
            color: #007bff;
            background-color: transparent;
        }

        .btn-package.btn-outline-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-package.btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
            border: 1px solid #17a2b8;
            background-color: transparent;
        }

        .btn-package.btn-outline-info:hover {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }


        .btn-package.btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
            border: 1px solid #ffc107;
            background-color: transparent;
        }

        .btn-package.btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }


        .btn-package.btn-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-package.btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            background: linear-gradient(45deg, #218838, #1c7430);
        }

        .enterprise-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .hero-section {
                padding-top: 5rem;
                text-align: center;
            }

            .laptop-container {
                margin-top: 3rem;
            }

            .navbar-collapse {
                background-color: rgba(26, 179, 148, 0.95);
                padding: 1rem;
                border-radius: 0.5rem;
                margin-top: 0.5rem;
            }

            .d-flex {
                justify-content: center;
                margin-top: 1rem;
            }

            .packages-section {
                padding: 3rem 0;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .package-card {
                margin-bottom: 2rem;
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-text {
                font-size: calc(1.8rem + 1vw);
            }

            .hero-subtext {
                font-size: 1.2rem;
            }

            .get-started-btn {
                padding: 0.6rem 1.5rem;
            }

            .package-price {
                font-size: 2rem;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .section-title p {
                font-size: 1rem;
            }
        }

        /* Smooth scroll between sections */
        html {
            scroll-behavior: smooth;
        }

        /* Add scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            text-align: center;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .scroll-indicator:hover {
            opacity: 1;
        }

        .scroll-indicator i {
            font-size: 2rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }
    </style>
</head>

<body>
    <ul class="background">
        <li>A</li>
        <li>7</li>
        <li>Γ</li>
        <li>4</li>
        <li>B</li>
        <li>Θ</li>
        <li>9</li>
        <li>Σ</li>
        <li>C</li>
        <li>2</li>
        <li>Ω</li>
        <li>D</li>
        <li>5</li>
        <li>ω</li>
        <li>E</li>
        <li>3</li>
        <li>λ</li>
        <li>F</li>
        <li>8</li>
        <li>π</li>
    </ul>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                </ul>
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-light me-2">Masuk</a>
                    <a href="{{ route('privacy') }}" class="btn btn-light me-2">Informasi</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section" id="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-text">Tryout Psikotes</h1>
                    <h1 class="hero-text">Bintara POLRI</h1>
                    <h3 class="hero-subtext">Asah Kecermatan, Kecerdasan, dan Kepribadian untuk Raih Nilai Tinggi</h3>
                    <div class="tagline-container">
                        <span class="tagline-item">Cermat.</span>
                        <span class="tagline-item highlight">Cerdas.</span>
                        <span class="tagline-item highlight">Lulus.</span>
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('trial') }}" class="get-started-btn me-2">Coba</a>
                        <a href="{{ route('register') }}" class="get-started-btn">Daftar</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="laptop-container">
                        <div class="laptop-frame">
                            <img src="{{ asset('img/laptop_image.png') }}" alt="Laptop Frame" class="laptop-base">
                            <div class="screen-content">
                                <img src="{{ asset('img/soal_cermat.png') }}" alt="Soal Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator" onclick="document.getElementById('packages').scrollIntoView()">
            <div>Lihat Paket</div>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section" id="packages">
        <div class="container" style="padding-top:25px">
            <div class="section-title">
                <h2>Pilih Paket Berlangganan</h2>
                <p>Akses penuh ke semua fitur persiapan tes BINTARA POLRI</p>
            </div>

            <div class="row d-flex align-items-stretch">
                <!-- Paket Tes Kecermatan -->
                <div class="col-md-3 mb-4">
                    <div class="package-card h-100">
                        <div class="package-header">
                            <h3>Paket Kecermatan</h3>
                            <p>Fokus Tes Kecermatan</p>
                        </div>

                        <div class="package-features">
                            <div class="text-center mb-2 package-pricing">
                                <div class="package-price">Rp 75.000</div>
                                <p class="price-period">Berlaku 30 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Bank soal kecermatan lengkap
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Latihan soal unlimited
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Analisis kecepatan & akurasi
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Timer simulasi ujian
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Riwayat progress harian
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}"
                                    class="btn btn-package btn-outline-primary">
                                    Pilih Paket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket Psikologi -->
                <div class="col-md-3 mb-4">
                    <div class="package-card h-100">
                        <div class="package-header">
                            <h3>Paket Psikologi</h3>
                            <p>Fokus Tes Psikologi</p>
                        </div>

                        <div class="package-features">
                            <div class="text-center mb-2 package-pricing">
                                <div class="package-price">Rp 75.000</div>
                                <p class="price-period">Berlaku 30 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Bank soal psikologi lengkap
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Tes kepribadian & karakter
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Simulasi wawancara psikologi
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Tips & strategi psikotes
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Analisis profil psikologi
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}"
                                    class="btn btn-package btn-outline-info">
                                    Pilih Paket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket Akademik -->
                <div class="col-md-3 mb-4">
                    <div class="package-card h-100">
                        <div class="package-header">
                            <h3>Paket Akademik</h3>
                            <p>Fokus Tes Akademik</p>
                        </div>

                        <div class="package-features">
                            <div class="text-center mb-2 package-pricing">
                                <div class="package-price">Rp 75.000</div>
                                <p class="price-period">Berlaku 30 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Bank soal akademik lengkap
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Matematika, Bahasa Indonesia, PKN
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Pembahasan soal detail
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Simulasi ujian akademik
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Analisis kemampuan per mata pelajaran
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}"
                                    class="btn btn-package btn-outline-warning">
                                    Pilih Paket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket Lengkap -->
                <div class="col-md-3 mb-4">
                    <div class="package-card h-100 popular-package">
                        <div class="package-header">
                            <h3>Paket Lengkap</h3>
                            <p>Kecermatan + Psikologi + Akademik</p>
                        </div>

                        <div class="package-features">
                            <div class="text-center mb-2 package-pricing">
                                <div class="package-price">Rp 120.000</div>
                                <p class="price-period">Berlaku 45 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Semua fitur Kecermatan
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Semua fitur Psikologi
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Semua fitur Akademik
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Try out gabungan berkala
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Laporan progress lengkap
                                </div>
                                <div class="feature-item available">
                                    <i class="fas fa-check"></i> Sertifikat penyelesaian
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}" class="btn btn-success btn-package">
                                    Pilih Paket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <small class="text-muted">
                    Dengan membeli paket ini, Anda menyetujui
                    <a href="#" style="color: #1ab394;">Syarat & Ketentuan</a> yang berlaku
                </small>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(26, 179, 148, 0.98)';
            } else {
                navbar.style.background = 'rgba(26, 179, 148, 0.95)';
            }
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
    </script>
</body>

</html>
