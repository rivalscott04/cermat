<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>INSPINIA | Login</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">

  <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">

  <div class="middle-box loginscreen animated fadeInDown text-center">
    <div>
      <div>
        <h1 class="logo-name">MC</h1>
      </div>
      <h3>Cermat</h3>
      <p>Login in. To see it in action.</p>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="m-t" role="form" action="{{ route('post.login') }}" method="POST">
        @csrf
        <div class="form-group">
          <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}"
            required>
        </div>
        <div class="form-group">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary full-width m-b block">Login</button>

        <a href="#"><small>Forgot password?</small></a>
        <p class="text-muted text-center"><small>Do not have an account?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="{{ url('register') }}">Create an account</a>
      </form>
      <p class="m-t"> <small>Mahir Cermat &copy; 2025</small> </p>
    </div>
  </div>

  <!-- Mainly scripts -->
  <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>

</body>

</html>
