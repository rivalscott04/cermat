<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahir Cermat | Buat Akun</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        .back-button-container {
            position: relative;
            width: 100%;
            padding: 20px;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .back-button-container {
                position: sticky;
                top: 0;
            }
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <img src="{{ asset('img/regis-removebg-preview.png') }}" alt="dashboard" class="img-fluid"
                                style="max-width: 200px;">
                            <h3 class="mt-3">Buat Akun Mahir Cermat</h3>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif


                        <form class="m-t" role="form" method="POST" action="{{ route('post.register') }}">
                            @csrf

                            <!-- Data Diri -->
                            <h5 class="mb-3">Data Diri</h5>
                            <div class="mb-4">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" placeholder="Nama" name="name"
                                        required>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="email" class="form-control" placeholder="Email" name="email"
                                        required>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" placeholder="No Hp" name="phone_number"
                                        required>
                                </div>
                                <div class="form-group mb-3">
                                    <select class="form-control" id="province" name="province" required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <select class="form-control" id="regency" name="regency" required disabled>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" class="form-control" placeholder="Password" name="password"
                                        required>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" class="form-control" placeholder="Konfirmasi Password"
                                        name="password_confirmation" required>
                                </div>
                            </div>

                            <!-- Terms & Submit -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Dengan mengklik tombol ini, anda setuju dengan syarat & ketentuan
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Daftar</button>

                            <p class="mt-3 text-center">
                                <small>Sudah Memiliki Akun? <a href="{{ route('login') }}">Masuk</a></small>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/plugins/iCheck/icheck.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Province & Regency API
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(provinces => {
                    const provinceSelect = document.getElementById('province');
                    provinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        option.dataset.id = province.id;
                        provinceSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching provinces:', error));

            $('#province').change(function() {
                const selectedOption = this.options[this.selectedIndex];
                const provinceId = selectedOption.dataset.id;
                const regencySelect = $('#regency');

                regencySelect.empty().append('<option value="">Pilih Kabupaten/Kota</option>');

                if (provinceId) {
                    regencySelect.prop('disabled', false);

                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                        .then(response => response.json())
                        .then(regencies => {
                            regencies.forEach(regency => {
                                const option = document.createElement('option');
                                option.value = regency.name;
                                option.textContent = regency.name;
                                regencySelect.append(option);
                            });
                        })
                        .catch(error => console.error('Error fetching regencies:', error));
                } else {
                    regencySelect.prop('disabled', true);
                }
            });

            // iCheck initialization
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
</body>

</html>
