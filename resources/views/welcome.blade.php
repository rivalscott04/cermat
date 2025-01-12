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
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Animation CSS -->
  <link href="css/animate.css" rel="stylesheet">
  <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet">
</head>

<body id="page-top" class="landing-page no-skin-config">
  <div class="navbar-wrapper">
    <nav class="navbar navbar-default navbar-fixed-top navbar-expand-md" role="navigation">
      <div class="container">
        <a class="navbar-brand" href="index.html">WEBAPPLAYERS</a>
        <div class="navbar-header page-scroll">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
            <i class="fa fa-bars"></i>
          </button>
        </div>
        <div class="navbar-collapse justify-content-end collapse" id="navbar">
          <ul class="nav navbar-nav navbar-right">
            <li><a class="nav-link page-scroll" href="#page-top">Home</a></li>
            <li><a class="nav-link page-scroll" href="#features">Features</a></li>
            <li><a class="nav-link page-scroll" href="#team">Team</a></li>
            <li><a class="nav-link page-scroll" href="#testimonials">Testimonials</a></li>
            <li><a class="nav-link page-scroll" href="#pricing">Pricing</a></li>
            <li><a class="nav-link page-scroll" href="#contact">Contact</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
  <div id="inSlider" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#inSlider" data-slide-to="0" class="active"></li>
      <li data-target="#inSlider" data-slide-to="1"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
      <div class="carousel-item active">
        <div class="container">
          <div class="carousel-caption">
            <h1>Tes Cermat<br />
              Lorem Ipsum,<br />
              dolor sit<br />
              consectetur adipiscing elit</h1>
            <p>Sed do eiusmod tempor incididunt ut labore et dolore..</p>
            <>
              <a class="btn btn-lg btn-primary" href="{{ route('login') }}" role="button">Login</a>
              <a class="btn btn-lg btn-primary" href="{{ route('register') }}" role="button">Register</a>
              <a class="btn btn-lg btn-primary" href="{{ route('trial') }}" role="button">Trial</a>
              </p>
          </div>
          <div class="carousel-image wow zoomIn">
            <img src="img/landing/laptop.png" alt="laptop" />
          </div>
        </div>
        <!-- Set background for slide in css -->
        <div class="header-back one"></div>

      </div>
      <div class="carousel-item">
        <div class="container">
          <div class="carousel-caption blank">
            <h1>Lorem ipsum, adipisicing elit. <br /> dolor sit amet consectetur.</h1>
            <p> Deserunt, neque?</p>
            <p><a class="btn btn-lg btn-primary" href="{{ route('login') }}" role="button">Login</a></p>
            <p><a class="btn btn-lg btn-primary" href="{{ route('register') }}" role="button">Register</a></p>
            <p><a class="btn btn-lg btn-primary" href="{{ route('trial') }}" role="button">Trial</a></p>
          </div>
        </div>
      </div>
      <!-- Set background for slide in css -->
      <div class="header-back two"></div>
    </div>
  </div>
  <a class="carousel-control-prev" href="#inSlider" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#inSlider" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
  </div>




  <!-- Mainly scripts -->
  <script src="js/jquery-3.1.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
  <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

  <!-- Custom and plugin javascript -->
  <script src="js/inspinia.js"></script>
  <script src="js/plugins/pace/pace.min.js"></script>
  <script src="js/plugins/wow/wow.min.js"></script>


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
