<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>INSPINIA - Landing Page</title>

  <!-- Bootstrap core CSS -->
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

  <!-- Animation CSS -->
  <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">

  <style>
    .landing-page .features-block {
      margin-top: 150px
    }

    .landing-page .navbar-default .nav li a {
      color: #999c9e
    }

    .landing-page h1 {
      font-size: 45px;
    }

    .btn-warning {
      font-size: 14px;
      padding: 10px 20px;
      font-weight: 600;
    }

    /* Add these styles to your existing style block */
    .landing-page .features.container {
      min-height: calc(100vh - 60px);
      /* Subtracting navbar height */
      display: flex;
      align-items: center;
    }

    .landing-page .features-block {
      margin-top: 0;
      /* Remove the original margin */
      width: 100%;
    }

    /* Ensure the image doesn't overflow */
    .landing-page .features-block img {
      max-height: 70vh;
      width: auto;
      object-fit: contain;
    }
  </style>
</head>

<body id="page-top" class="landing-page no-skin-config">
  <div class="navbar-wrapper">
    <nav class="navbar navbar-default navbar-fixed-top navbar-expand-md" role="navigation">
      <div class="container">
        <a class="navbar-brand" href="index.html">CERMAT</a>
        <div class="navbar-header page-scroll">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
            <i class="fa fa-bars"></i>
          </button>
        </div>
        <div class="navbar-collapse justify-content-end collapse" id="navbar">
          <ul class="nav navbar-nav navbar-right">
            <li><a class="nav-link page-scroll" href="{{ route('trial') }}">Trial</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
  <section class="features container">
    <div class="row features-block">
      <div class="col-lg-6 features-text wow fadeInLeft">
        <h1 class="font-weight-bold">Tes Cermat</h1>
        <br>
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        <a href="{{ route('register') }}" class="btn btn-warning ml-3">Register</a>
      </div>
      <div class="col-lg-6 wow fadeInRight text-right">
        <img src="{{ asset('img/polr.png') }}" alt="dashboard" class="img-fluid float-right">
      </div>
    </div>
  </section>
  <section id="contact" class="gray-section contact">
    <div class="container">
      <div class="row m-b-lg">
        <div class="col-lg-12 text-center">
          <div class="navy-line"></div>
          <h1>Contact Us</h1>
          <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod.</p>
        </div>
      </div>
      <div class="row m-b-lg justify-content-center">
        <div class="col-lg-3">
          <address>
            <strong><span class="navy">Company name, Inc.</span></strong><br />
            795 Folsom Ave, Suite 600<br />
            San Francisco, CA 94107<br />
            <abbr title="Phone">P:</abbr> (123) 456-7890
          </address>
        </div>
        <div class="col-lg-4">
          <p class="text-color">
            Consectetur adipisicing elit. Aut eaque, totam corporis laboriosam veritatis quis ad perspiciatis, totam
            corporis laboriosam veritatis, consectetur adipisicing elit quos non quis ad perspiciatis, totam corporis
            ea,
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 text-center">
          <a href="mailto:test@email.com" class="btn btn-primary">Send us mail</a>
          <p class="m-t-sm">
            Or follow us on social platform
          </p>
          <ul class="list-inline social-icon">
            <li class="list-inline-item"><a href="#"><i class="fa fa-twitter"></i></a>
            </li>
            <li class="list-inline-item"><a href="#"><i class="fa fa-facebook"></i></a>
            </li>
            <li class="list-inline-item"><a href="#"><i class="fa fa-linkedin"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 m-t-lg m-b-lg text-center">
          <p><strong>&copy; 2015 Company Name</strong><br /> consectetur adipisicing elit. Aut eaque, laboriosam
            veritatis, quos non quis ad perspiciatis, totam corporis ea, alias ut unde.</p>
        </div>
      </div>
    </div>
  </section>




  <!-- Mainly scripts -->
  <script src={{ asset('js/jquery-3.1.1.min.js') }}></script>
  <script src={{ asset('js/popper.min.j') }}></script>
  <script src={{ asset('js/bootstrap.js') }}></script>
  <script src={{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}></script>
  <script src={{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}></script>

  <!-- Custom and plugin javascript -->
  <script src={{ asset('js/inspinia.js') }}></script>
  <script src={{ asset('js/plugins/pace/pace.min.js') }}></script>
  <script src={{ asset('js/plugins/wow/wow.min.js') }}></script>

  <script>
    $(document).ready(function() {

      $('body').scrollspy({
        target: '#navbar',
        offset: 80
      });

      // Page scrolling feature
      $('a.page-scroll').bind('click', function(event) {
        var link = $(this);
        $('html, body').stop().animate({
          scrollTop: $(link.attr('href')).offset().top - 50
        }, 500);
        event.preventDefault();
        $("#navbar").collapse('hide');
      });
    });

    var cbpAnimatedHeader = (function() {
      var docElem = document.documentElement,
        header = document.querySelector('.navbar-default'),
        didScroll = false,
        changeHeaderOn = 200;

      function init() {
        window.addEventListener('scroll', function(event) {
          if (!didScroll) {
            didScroll = true;
            setTimeout(scrollPage, 250);
          }
        }, false);
      }

      function scrollPage() {
        var sy = scrollY();
        if (sy >= changeHeaderOn) {
          $(header).addClass('navbar-scroll')
        } else {
          $(header).removeClass('navbar-scroll')
        }
        didScroll = false;
      }

      function scrollY() {
        return window.pageYOffset || docElem.scrollTop;
      }
      init();

    })();

    // Activate WOW.js plugin for animation on scrol
    new WOW().init();
  </script>

</body>

</html>
