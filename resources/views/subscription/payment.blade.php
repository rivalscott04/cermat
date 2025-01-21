<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instruksi Pembayaran - Mahir Cermat</title>
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
      padding: 20px;
    }

    .back-button {
      margin-bottom: 20px;
    }

    .card {
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      border: none;
      border-radius: 10px;
    }

    .card-header {
      background-color: #fff;
      border-bottom: 1px solid #eee;
      padding: 15px 20px;
      font-weight: 600;
      border-radius: 10px 10px 0 0 !important;
    }

    .card-body {
      padding: 20px;
    }

    .transaction-detail {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    ol li {
      margin-bottom: 10px;
    }

    .qr-code-container {
      padding: 20px;
      border: 1px dashed #ddd;
      border-radius: 8px;
      display: inline-block;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="back-button">
      <a href="{{ url('/') }}" class="btn btn-light">
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            Instruksi Pembayaran
          </div>
          <div class="card-body">
            <div class="transaction-detail">
              <h5>Detail Transaksi</h5>
              <div class="row mt-3">
                <div class="col-md-6">
                  <p class="mb-2">ID Transaksi:</p>
                  <p class="mb-2"><strong>{{ $subscription->transaction_id }}</strong></p>
                </div>
                <div class="col-md-6 text-md-end">
                  <p class="mb-2">Total Pembayaran:</p>
                  <p class="mb-2"><strong>Rp {{ number_format($subscription->amount_paid) }}</strong></p>
                </div>
              </div>
            </div>

            <h5 class="mt-4">Cara Pembayaran</h5>
            <ol class="mt-3">
              @foreach ($instructions as $instruction)
                <li>{{ $instruction }}</li>
              @endforeach
            </ol>

            @if ($subscription->payment_method == 'qris')
              <div class="mt-4 text-center">
                <div class="qr-code-container">
                  <img src="qr-code-image-url" alt="QRIS Code" style="max-width: 200px;">
                  <p class="mb-0 mt-2"><small>Scan QR Code untuk membayar</small></p>
                </div>
              </div>
            @endif

            <div class="alert alert-info mt-4">
              <small>
                <i class="fa fa-info-circle"></i> Pembayaran akan diverifikasi otomatis oleh sistem
                <br>
                <i class="fa fa-info-circle"></i> Halaman ini dapat ditutup setelah pembayaran selesai
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
