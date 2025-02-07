<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahir Cermat | Checkout</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        .package-card {
            border: 1px solid #e7eaec;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .package-header {
            background: #1ab394;
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .package-features {
            padding: 20px;
        }

        .feature-item {
            margin: 10px 0;
        }

        .package-price {
            font-size: 24px;
            font-weight: bold;
            color: #1ab394;
        }

        .back-button-container {
            position: relative;
            width: 100%;
            padding: 20px;
            z-index: 1000;
        }
    </style>
</head>

<body class="gray-bg">
    <div class="back-button-container">
        <a href="{{ url()->previous() }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 200px;">
                    <h2 class="mt-3">Pilih Paket Berlangganan</h2>
                    <p>Akses penuh ke semua fitur persiapan tes BINTARA POLRI</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif

                <div class="package-card mb-4">
                    <div class="package-header text-center">
                        <h3 class="mb-0">Paket Tes Kecermatan</h3>
                        <p class="mb-0">Persiapan Tes BINTARA POLRI</p>
                    </div>

                    <div class="package-features">
                        <div class="text-center mb-4">
                            <div class="package-price">Rp 100.000</div>
                            <p class="text-muted">Berlaku 30 hari</p>
                        </div>

                        <div class="feature-item">
                            <i class="fa fa-check text-success"></i> Akses latihan soal unlimited
                        </div>
                        <div class="feature-item">
                            <i class="fa fa-check text-success"></i> Riwayat detail hasil tes
                        </div>

                        <a href="{{ route('subscription.process') }}" class="btn btn-primary">Pilih
                            Paket</a>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        Dengan membeli paket ini, Anda menyetujui
                        <a href="#">Syarat & Ketentuan</a> yang berlaku
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
</body>

</html>
