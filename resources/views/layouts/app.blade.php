<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <title>@yield('title', 'Dashboard')</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
  <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  @stack('styles')
</head>

<body>
  <div id="wrapper">
    @include('components.sidenav')
    <div id="page-wrapper" class="gray-bg">
      @include('components.topnav')
      @yield('content')
      @include('components.footer')
    </div>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
  <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
  <script src="{{ asset('js/inspinia.js') }}"></script>
  @stack('scripts')
</body>

</html>
