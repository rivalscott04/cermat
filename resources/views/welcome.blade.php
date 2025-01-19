<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mahir Cermat</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .hero-section {
      background: linear-gradient(135deg, #a8e6cf 0%, #3eaf7c 100%);
      min-height: 100vh;
      padding: 4rem 0;
      position: relative;
      overflow: hidden;
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
      /* Reduced from 800px */
      margin: 2rem auto;
      padding: 0;
      overflow: hidden;
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

  <section class="hero-section">
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
