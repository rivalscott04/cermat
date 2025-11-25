<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Support - Police Academy Test Prep</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .page-title {
            color: #1ab394;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .btn-info {
            background-color: #1ab394;
        }

        .contact-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .contact-card h2 {
            color: #1ab394;
            margin-bottom: 25px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background-color: #1ab394;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .info-content h3 {
            color: #333;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .info-content p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }

        .info-content a {
            color: #1ab394;
            text-decoration: none;
            font-weight: 600;
        }

        .info-content a:hover {
            color: #0f9469;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }

            .contact-card,
            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Kontak</a>
        </div>
    </nav>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <a href="\" class="btn btn-info">&larr; Kembali</a>

                <h1 class="page-title">Hubungi Tim Support Kami</h1>

                <div class="contact-card" style="max-width: 600px; margin: 0 auto;">
                    <h2>Informasi Kontak</h2>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Alamat</h3>
                            <p>Jl. Merdeka Raya Jempong Baru</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p><a href="mailto:support@tryoutpsikotes.com">support@tryoutpsikotes.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
