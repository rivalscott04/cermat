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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* SweetAlert Custom Styling */
        .swal-wide {
            width: 500px !important;
        }

        .admin-info {
            border-left: 4px solid #ffc107;
        }

        .user-info {
            border-left: 4px solid #28a745;
            padding-left: 10px;
        }

        .swal2-popup {
            font-size: 14px;
        }

        .swal2-confirm {
            margin-right: 10px;
        }

        .stop-impersonate-btn:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
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
            @if (session('impersonate_taken'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Impersonate Berhasil!',
                            text: '{{ session('impersonate_taken') }}',
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
                </script>
            @endif
            @if (session('impersonate_leave'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'info',
                            title: 'Kembali ke Admin!',
                            text: '{{ session('impersonate_leave') }}',
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
                </script>
            @endif

            {{-- Impersonate Warning Banner --}}
            @if (auth()->user() && app('impersonate')->isImpersonating())
                <div class="alert alert-warning alert-dismissible fade show impersonate-banner" role="alert"
                    style="margin: 0; border-radius: 0; border-left: none; border-right: none;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <i class="fa fa-user-secret"></i>
                                <strong>Mode Impersonate Aktif!</strong>
                                Anda sedang login sebagai <strong>{{ auth()->user()->name }}</strong>
                                @if (app('impersonate')->getImpersonator())
                                    (Admin: {{ app('impersonate')->getImpersonator()->name }})
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="#" class="btn btn-sm btn-outline-warning stop-impersonate-btn">
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

        // Stop impersonating handler
        $(document).ready(function() {
            $('.stop-impersonate-btn').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Stop Impersonating',
                    html: `
                        <div class="text-left">
                            <p><strong>Anda akan kembali ke akun admin:</strong></p>
                            <div class="admin-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;">
                                <p><i class="fa fa-user-shield"></i> <strong>Admin:</strong> {{ app('impersonate')->getImpersonator() ? app('impersonate')->getImpersonator()->name : 'Unknown' }}</p>
                                <p><i class="fa fa-user"></i> <strong>User yang di-impersonate:</strong> {{ auth()->user()->name }}</p>
                            </div>
                            <p class="text-info"><i class="fa fa-info-circle"></i> <strong>Informasi:</strong></p>
                            <ul class="text-left" style="margin-left: 20px;">
                                <li>Semua aktivitas impersonate telah tercatat</li>
                                <li>Anda akan kembali ke dashboard admin</li>
                                <li>Session akan dibersihkan secara otomatis</li>
                            </ul>
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-sign-out"></i> Ya, Kembali ke Admin',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-wide',
                        confirmButton: 'btn btn-warning',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            // Show loading state
                            Swal.showLoading();

                            // Redirect to stop impersonating route
                            window.location.href =
                                "{{ route('admin.stop-impersonating') }}";
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            });
        });
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
