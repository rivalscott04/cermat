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
            font-weight: bold;
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
            border: 2px solid #e9ecef;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            transition: all 0.4s ease;
            background: white;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .package-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #1ab394, #0f8a73);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .package-card:hover {
            border-color: #1ab394;
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(26, 179, 148, 0.15);
        }

        .package-card:hover::before {
            transform: scaleX(1);
        }

        .popular-package {
            border-color: #1ab394;
            position: relative;
            background: linear-gradient(135deg, #fff 0%, #f8fffd 100%);
        }

        .popular-badge {
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(45deg, #1ab394, #0f8a73);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(26, 179, 148, 0.3);
            z-index: 10;
        }

        .package-header {
            text-align: center;
            margin-block: 1.5rem;
        }

        .package-header h3 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .package-header p {
            color: #6c757d;
            font-size: 1rem;
        }

        .package-features {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .features-list {
            flex: 1;
            margin-bottom: 2rem;
        }

        .button-container {
            margin-top: auto;
        }

        .package-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1ab394;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #1ab394, #0f8a73);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .price-period {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 500;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(5px);
            color: #1ab394;
        }

        .feature-item i {
            margin-right: 1rem;
            font-size: 1.2rem;
            color: #1ab394;
            min-width: 20px;
        }

        .btn-package {
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid transparent;
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border: none;
            color: white;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
            background: transparent;
        }

        .btn-outline-info:hover {
            background: linear-gradient(45deg, #17a2b8, #138496);
            border-color: #17a2b8;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(23, 162, 184, 0.3);
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border-color: #007bff;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
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
                    <h2 style="color: white; font-weight: bold;">Tool Latihan</h2>
                    <h1 class="hero-text">TES KECERMATAN</h1>
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

            <div class="row">
                <!-- Paket Tes Kecermatan -->
                <div class="col-md-3 mb-4">
                    <div class="package-card h-100">
                        <div class="package-header">
                            <h3>Paket Kecermatan</h3>
                            <p>Fokus Tes Kecermatan</p>
                        </div>

                        <div class="package-features">
                            <div class="text-center mb-4">
                                <div class="package-price">Rp 75.000</div>
                                <p class="price-period">Berlaku 30 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Bank soal kecermatan lengkap
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Latihan soal unlimited
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Analisis kecepatan & akurasi
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Timer simulasi ujian
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Riwayat progress harian
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}"
                                    class="btn btn-outline-primary btn-package">
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
                            <div class="text-center mb-4">
                                <div class="package-price">Rp 75.000</div>
                                <p class="price-period">Berlaku 30 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Bank soal psikologi lengkap
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Tes kepribadian & karakter
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Simulasi wawancara psikologi
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Tips & strategi psikotes
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Analisis profil psikologi
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}"
                                    class="btn btn-outline-info btn-package">
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
                            <div class="text-center mb-4">
                                <div class="package-price">Rp 75.000</div>
                                <p class="price-period">Berlaku 30 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Bank soal akademik lengkap
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Matematika, Bahasa Indonesia, PKN
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Pembahasan soal detail
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Simulasi ujian akademik
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Analisis kemampuan per mata pelajaran
                                </div>
                            </div>

                            <div class="button-container">
                                <a href="{{ route('subscription.packages') }}"
                                    class="btn btn-outline-warning btn-package">
                                    Pilih Paket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket Lengkap -->
                <div class="col-md-3 mb-4">
                    <div class="package-card h-100 popular-package">
                        <div class="popular-badge">
                            <span>Terpopuler</span>
                        </div>
                        <div class="package-header">
                            <h3>Paket Lengkap</h3>
                            <p>Kecermatan + Psikologi + Akademik</p>
                        </div>

                        <div class="package-features">
                            <div class="text-center mb-4">
                                <div class="package-price">Rp 120.000</div>
                                <p class="price-period">Berlaku 45 hari</p>
                            </div>

                            <div class="features-list">
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Semua fitur Kecermatan
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Semua fitur Psikologi
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Semua fitur Akademik
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Try out gabungan berkala
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i> Laporan progress lengkap
                                </div>
                                <div class="feature-item">
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

        // Observe package cards
        document.querySelectorAll('.package-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>

</html>
