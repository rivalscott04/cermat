<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mahir Cermat | Login</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">

    <div class="middle-box loginscreen animated fadeInDown text-center">
        <div>
            <div>
                <img src="{{ asset('img/login-removebg-preview.png') }}" alt="dashboard" class="img-fluid float-right">
            </div>
            <h3>Mahir Cermat</h3>

            @if (session('message'))
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            @endif

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
                    <input type="email" name="email" class="form-control" placeholder="Email"
                        value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary full-width m-b block">Masuk</button>

                <a href="{{ route('reset-password') }}"><small>Lupa Password?</small></a>
                <p class="text-muted text-center"><small>Belum memiliki akun?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="{{ url('register') }}">Buat Akun</a>
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
