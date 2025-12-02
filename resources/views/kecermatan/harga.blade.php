<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paket Persiapan Tes POLRI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .pricing-card {
      border: 2px solid #20c997;
      border-radius: 12px;
      padding: 20px;
      height: 100%;
      position: relative;
    }

    .package-name {
      font-weight: bold;
      font-size: 1.5rem;
      text-align: center;
      margin-bottom: 1rem;
    }

    .old-price {
      color: #6c757d;
      text-decoration: line-through;
      font-size: 1rem;
      text-align: center;
    }

    .current-price {
      font-size: 2rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 1rem;
    }

    .register-button {
      background: #20c997;
      color: white;
      border: none;
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
      margin-bottom: 1rem;
    }

    .register-button:hover {
      background: #1ba884;
    }

    .feature-list {
      list-style: none;
      padding-left: 0;
    }

    .feature-list li {
      margin-bottom: 1rem;
      display: flex;
      align-items: start;
      gap: 10px;
    }

    .feature-check {
      color: #20c997;
      font-weight: bold;
      flex-shrink: 0;
    }

    .package-label {
      background: white;
      color: #20c997;
      font-weight: bold;
      padding: 5px 20px;
      border: 2px solid #20c997;
      border-radius: 20px;
      position: absolute;
      top: -15px;
      left: 50%;
      transform: translateX(-50%);
      white-space: nowrap;
    }

    .section-title {
      color: #444;
      margin-bottom: 1rem;
    }
  </style>
</head>

<body>

  <div class="container py-5">
    <div class="row g-4">
      @forelse($packages as $package)
        <div class="col-md-4">
          <div class="pricing-card">
            @if($package->label)
              <div class="package-label">{{ $package->label }}</div>
            @endif
            
            <h2 class="package-name">{{ $package->name }}</h2>
            
            @if($package->old_price)
              <div class="old-price">Rp {{ number_format($package->old_price, 0, ',', '.') }}</div>
            @endif
            
            <div class="current-price">Rp {{ number_format($package->price, 0, ',', '.') }},-</div>

            @guest
              <a href="{{ route('register') }}" class="register-button text-center d-block text-decoration-none">
                DAFTAR SEKARANG
              </a>
            @else
              <a href="{{ route('subscription.packages') }}" class="register-button text-center d-block text-decoration-none">
                PILIH PAKET
              </a>
            @endguest

            <p class="section-title">Akses yang didapat:</p>
            <ul class="feature-list">
              @foreach($package->features as $feature)
                <li>
                  <span class="feature-check">✓</span>
                  <span>{{ $feature }}</span>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="text-center py-5">
            <h3>Tidak ada paket yang tersedia</h3>
            <p>Silakan hubungi administrator untuk informasi lebih lanjut.</p>
          </div>
        </div>
      @endforelse
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
