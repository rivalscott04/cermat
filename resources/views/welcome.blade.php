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
      overflow: hidden;
      margin: 0;
      padding: 0;
      height: 100vh;
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

    .background li:nth-child(1) {
      left: 74%;
      bottom: -100px;
      animation-delay: 4s;
      content: "A";
    }

    .background li:nth-child(2) {
      left: 16%;
      bottom: -100px;
      animation-delay: 2s;
      content: "7";
    }

    .background li:nth-child(3) {
      left: 88%;
      bottom: -100px;
      animation-delay: 9s;
      content: "Γ";
    }

    .background li:nth-child(4) {
      left: 64%;
      bottom: -100px;
      animation-delay: 12s;
      content: "4";
    }

    .background li:nth-child(5) {
      left: 69%;
      bottom: -100px;
      animation-delay: 21s;
      content: "B";
    }

    .background li:nth-child(6) {
      left: 71%;
      bottom: -100px;
      animation-delay: 18s;
      content: "Θ";
    }

    .background li:nth-child(7) {
      left: 38%;
      bottom: -100px;
      animation-delay: 9s;
      content: "9";
    }

    .background li:nth-child(8) {
      left: 75%;
      bottom: -100px;
      animation-delay: 33s;
      content: "Σ";
    }

    .background li:nth-child(9) {
      left: 9%;
      bottom: -100px;
      animation-delay: 27s;
      content: "C";
    }

    .background li:nth-child(10) {
      left: 4%;
      bottom: -100px;
      animation-delay: 1s;
      content: "2";
    }

    .background li:nth-child(11) {
      left: 45%;
      bottom: -100px;
      animation-delay: 15s;
      content: "Ω";
    }

    .background li:nth-child(12) {
      left: 25%;
      bottom: -100px;
      animation-delay: 8s;
      content: "D";
    }

    .background li:nth-child(13) {
      left: 55%;
      bottom: -100px;
      animation-delay: 23s;
      content: "5";
    }

    .background li:nth-child(14) {
      left: 85%;
      bottom: -100px;
      animation-delay: 17s;
      content: "ω";
    }

    .background li:nth-child(15) {
      left: 32%;
      bottom: -100px;
      animation-delay: 11s;
      content: "E";
    }

    .background li:nth-child(16) {
      left: 92%;
      bottom: -100px;
      animation-delay: 14s;
      content: "3";
    }

    .background li:nth-child(17) {
      left: 28%;
      bottom: -100px;
      animation-delay: 19s;
      content: "λ";
    }

    .background li:nth-child(18) {
      left: 62%;
      bottom: -100px;
      animation-delay: 25s;
      content: "F";
    }

    .background li:nth-child(19) {
      left: 15%;
      bottom: -100px;
      animation-delay: 30s;
      content: "8";
    }

    .background li:nth-child(20) {
      left: 48%;
      bottom: -100px;
      animation-delay: 22s;
      content: "π";
    }

    .hero-section {
      height: 100vh;
      padding: 0;
      display: flex;
      align-items: center;
    }

    .hero-text {
      color: white;
      font-size: 4rem;
      font-weight: bold;
      line-height: 1.2;
    }

    .hero-subtext {
      color: white;
      font-size: 1.5rem;
      margin: 2rem 0;
      font-weight: bold;
    }

    .laptop-container {
      position: relative;
      max-width: 600px;
      margin: 2rem auto;
      padding: 0;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .laptop-container:hover {
      transform: translateY(-10px);
      filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15));
      cursor: pointer;
    }

    .laptop-frame {
      position: relative;
      width: 100%;
      margin: 0 auto;
      display: block;
    }

    .laptop-base {
      width: 100%;
      height: auto;
      display: block;
      position: relative;
      z-index: 1;
    }

    .screen-content {
      position: absolute;
      top: 6%;
      left: 13.5%;
      width: 73%;
      height: 77%;
      background: #fff;
      overflow: hidden;
      border-radius: 3px;
      z-index: 0;
    }

    .screen-content img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      display: block;
      background: white;
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
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .navbar {
      background: transparent !important;
    }

    .navbar-brand {
      color: white !important;
      font-weight: bold;
    }

    .nav-link {
      color: white !important;
    }

    @media (max-width: 991px) {
      .laptop-container {
        max-width: 90%;
        margin: 2rem auto;
      }

      .laptop-frame {
        width: 100%;
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
      <a class="navbar-brand" href="#">LOGO</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
        </ul>
        <div class="d-flex">
          <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Log In</a>
          <a href="{{ route('trial') }}" class="btn btn-light">Trial</a>
        </div>
      </div>
    </div>
  </nav>

  <section class="hero-section mt-2">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="hero-text">TES KECERMATAN KEPOLISIAN</h1>
          <p class="hero-subtext">Cermat dan Cepat: Tes Kecermatan Terbaik</p>
          <a href="{{ route('register') }}" class="get-started-btn">Mulai</a>
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
