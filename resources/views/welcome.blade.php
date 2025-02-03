<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahir Cermat</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
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
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 style="color: white" font-weight: bold>Tool Latihan</h3>
                        <h1 class="hero-text">TES KECERMATAN KEPOLISIAN</h1>
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
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
