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
    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <style>
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .logo {
            max-width: 100%;
            height: auto;
            display: none;
            filter: invert(10%) brightness(200%);
        }

        .mini-navbar .logo {
            display: block;
            max-width: 50px;
            margin: 5px auto;
        }

        .mini-navbar .logo-element {
            display: block;
            padding: 10px 0;
        }

        .impersonate-banner {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: #000;
            border: none;
            font-weight: 500;
        }

        .impersonate-banner .btn-outline-warning {
            border-color: #000;
            color: #000;
        }

        .impersonate-banner .btn-outline-warning:hover {
            background-color: #000;
            color: #fff;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div id="wrapper">
        @include('components.sidenav')
        <div id="page-wrapper" class="gray-bg">
            @include('components.topnav')
            @if (session('message'))
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('subscriptionError'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Akses Ditolak',
                            text: '{{ session('subscriptionError') }}',
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: 'Tutup',
                            confirmButtonText: 'Berlangganan sekarang',
                            confirmButtonColor: '#3085d6'

                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('subscription.checkout') }}";
                            }
                        });
                    });
                </script>
            @endif

            {{-- Impersonate Warning Banner --}}
            @if(\App\Models\User::isImpersonating())
                <div class="alert alert-warning alert-dismissible fade show impersonate-banner" role="alert" style="margin: 0; border-radius: 0; border-left: none; border-right: none;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <i class="fa fa-user-secret"></i>
                                <strong>Mode Impersonate Aktif!</strong> 
                                Anda sedang login sebagai <strong>{{ auth()->user()->name }}</strong>
                                @if(\App\Models\User::getOriginalUser())
                                    (Admin: {{ \App\Models\User::getOriginalUser()->name }})
                                @endif
                                @if(\App\Models\User::getImpersonationDuration())
                                    <span class="badge badge-dark ml-2">
                                        <i class="fa fa-clock-o"></i> 
                                        {{ \App\Models\User::getImpersonationDuration() }} menit
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('admin.stop-impersonating') }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa fa-sign-out"></i> Kembali ke Admin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
            @include('components.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.appRoutes = {
            simpanHasil: "{{ route('kecermatan.simpanHasil') }}",
        };
    </script>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/generateSoal.js') }}"></script>
    {{-- <script src="{{ asset('js/kecermatanSoal.js') }}"></script> --}}
    <script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('js/inspinia.js') }}"></script>
    @stack('scripts')
</body>

</html>
