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

    .payment-option {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .payment-option:hover {
      border-color: #0dcaf0;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .payment-option.selected {
      border-color: #0dcaf0;
      background-color: #e3f8fb;
    }

    .payment-logo {
      height: 40px;
      object-fit: contain;
    }

    .subscription-box {
      background-color: #20B2AA;
      color: white;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      margin-bottom: 30px;
    }

    .subscription-box h4 {
      margin: 0;
      font-weight: bold;
    }

    .subscription-box p {
      margin: 10px 0;
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

              <!-- New subscription details box -->
              <div class="subscription-box">
                <h4>PAKET CERMAT</h4>
                <p>(Persiapan Tes BINTARA POLRI T.A. 2025)</p>
                <h4>Rp. 100.000</h4>
              </div>
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

            <form class="m-t" role="form" method="POST" action="{{ route('post.register') }}">
              @csrf

              <!-- Data Diri -->
              <h5 class="mb-3">Data Diri</h5>
              <div class="mb-4">
                <div class="form-group mb-3">
                  <input type="text" class="form-control" placeholder="Nama" name="name" required>
                </div>
                <div class="form-group mb-3">
                  <input type="email" class="form-control" placeholder="Email" name="email" required>
                </div>
                <div class="form-group mb-3">
                  <input type="text" class="form-control" placeholder="No Hp" name="phone_number" required>
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
                  <input type="password" class="form-control" placeholder="Password" name="password" required>
                </div>
                <div class="form-group mb-3">
                  <input type="password" class="form-control" placeholder="Konfirmasi Password"
                    name="password_confirmation" required>
                </div>
              </div>

              <!-- Paket Berlangganan -->
              <h5 class="mb-3">Paket Berlangganan</h5>
              <div class="card mb-4">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">Paket Cermat</h6>
                    </div>
                    <h6 class="mb-0">Rp. 100.000</h6>
                  </div>
                </div>
              </div>

              <!-- Metode Pembayaran -->
              <h5 class="mb-3">Metode Pembayaran</h5>
              <div class="row g-3 mb-4">
                <input type="hidden" name="payment_method" id="payment_method" required>
                <input type="hidden" name="payment_details" id="selected_bank">
                <!-- Bank Transfer -->
                <div class="col-md-6">
                  <div class="payment-option" data-payment="bank_transfer" data-bank="mandiri">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/Mandiri.svg"
                      alt="Mandiri" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option" data-payment="bank_transfer" data-bank="bri">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/BRI.svg"
                      alt="BRIVA" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option" data-payment="bank_transfer" data-bank="bni">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/BNI.svg"
                      alt="BNI" class="payment-logo">
                  </div>
                </div>

                <!-- E-Wallet -->
                <div class="col-md-6">
                  <div class="payment-option" data-payment="qris">
                    <img
                      src="https://raw.githubusercontent.com/Adekabang/indonesia-logo-library/main/Payment%20Channel/Miscellaneous/QRIS.png"
                      alt="QRIS" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option" data-payment="ovo">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/E-Wallet/OVO.png"
                      alt="OVO" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option" data-payment="dana">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/E-Wallet/DANA.png"
                      alt="DANA" class="payment-logo">
                  </div>
                </div>
              </div>

              <!-- Summary -->
              <div class="card mb-4">
                <div class="card-body">
                  <div class="d-flex justify-content-between mb-2">
                    <div>Total Pembayaran</div>
                    <div><strong>Rp. 100.000</strong></div>
                  </div>
                </div>
              </div>

              <!-- Terms & Submit -->
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                  Dengan mengklik tombol ini, anda setuju dengan syarat & ketentuan
                </label>
              </div>

              <button type="submit" class="btn btn-primary w-100">Daftar & Lanjutkan ke Pembayaran</button>

              <p class="mt-3 text-center">
                <small>Sudah Memiliki Akun? <a href="{{ route('login') }}">Masuk</a></small>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts remain unchanged -->
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

      // Payment Method Selection
      $('.payment-option').click(function() {
        $('.payment-option').removeClass('selected');
        $(this).addClass('selected');

        let paymentMethod = $(this).data('payment');
        let bank = $(this).data('bank');

        $('#payment_method').val(paymentMethod);
        // Update this line - we were using wrong ID before
        $('#selected_bank').val(bank); // This stores the selected bank value
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
