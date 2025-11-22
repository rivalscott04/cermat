@extends('layouts.app')

@section('content')
    <div class="back-button-container mt-2">
        <a href="{{ route('subscription.packages') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 100px;">
                    <h2 class="mt-3">Checkout Paket Berlangganan</h2>
                    <p>Pilih metode pembayaran untuk melanjutkan</p>
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

                <div class="row">
                    <!-- Detail Paket -->
                    <div class="col-md-6 mb-4">
                        <div class="checkout-card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fa fa-package text-primary"></i> Detail Paket
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="package-summary">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="package-name">{{ $package['name'] }}</h5>
                                            <p class="package-description text-muted mb-0">{{ $package['description'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="package-price">Rp
                                                {{ number_format($package['price'], 0, ',', '.') }}</div>
                                            <small class="text-muted">Berlaku {{ $package['duration'] }} hari</small>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">Fitur yang Didapatkan:</h6>
                                    <div class="features-list">
                                        @foreach ($package['features'] as $feature)
                                            <div class="feature-item">
                                                <i class="fa fa-check text-success"></i>
                                                {{ $feature }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Informasi Pengguna -->
                                <hr>
                                <h6 class="mb-3">
                                    <i class="fa fa-user text-info"></i> Informasi Pengguna
                                </h6>
                                <div class="user-info">
                                    <div class="info-item">
                                        <strong>Nama:</strong> {{ $user->name }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Email:</strong> {{ $user->email }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Order ID:</strong>
                                        <span
                                            class="order-id">{{ strtoupper($package['id']) }}-{{ $user->id }}-{{ date('Ymd') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="col-md-6">
                        <div class="checkout-card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fa fa-credit-card text-warning"></i> Pilih Metode Pembayaran
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('subscription.process') }}" method="POST" id="paymentForm">
                                    @csrf

                                    <!-- Input package (hidden) -->
                                    <input type="hidden" name="package" value="{{ $package['name'] }}">

                                    <!-- Opsi Metode Pembayaran -->
                                    <div class="payment-methods mb-4">
                                        <h6 class="mb-3">
                                            <i class="fa fa-list text-info"></i> Metode Pembayaran Tersedia:
                                        </h6>

                                        <div class="payment-option mb-3">
                                            <label class="custom-radio">
                                                <input type="radio" name="payment_method" value="QRIS" checked>
                                                <span class="radio-label">
                                                    <i class="fa fa-qrcode"></i> QRIS (Instant)
                                                </span>
                                            </label>
                                            <small class="text-muted d-block ms-4">Scan dengan aplikasi pembayaran apa
                                                pun</small>
                                        </div>

                                        <div class="payment-option mb-3">
                                            <label class="custom-radio">
                                                <input type="radio" name="payment_method" value="BRIVA">
                                                <span class="radio-label">
                                                    <i class="fa fa-university"></i> BRI Virtual Account
                                                </span>
                                            </label>
                                            <small class="text-muted d-block ms-4">Transfer ke Virtual Account BRI</small>
                                        </div>

                                        <div class="payment-option mb-3">
                                            <label class="custom-radio">
                                                <input type="radio" name="payment_method" value="BCAVA">
                                                <span class="radio-label">
                                                    <i class="fa fa-university"></i> BCA Virtual Account
                                                </span>
                                            </label>
                                            <small class="text-muted d-block ms-4">Transfer ke Virtual Account BCA</small>
                                        </div>

                                        <div class="payment-option mb-3">
                                            <label class="custom-radio">
                                                <input type="radio" name="payment_method" value="BNIVA">
                                                <span class="radio-label">
                                                    <i class="fa fa-university"></i> BNI Virtual Account
                                                </span>
                                            </label>
                                            <small class="text-muted d-block ms-4">Transfer ke Virtual Account BNI</small>
                                        </div>



                                        <div class="payment-option mb-3">
                                            <label class="custom-radio">
                                                <input type="radio" name="payment_method" value="SHOPEEPAY">
                                                <span class="radio-label">
                                                    <i class="fa fa-mobile"></i> ShopeePay
                                                </span>
                                            </label>
                                            <small class="text-muted d-block ms-4">Pembayaran melalui ShopeePay</small>
                                        </div>
                                    </div>

                                    <!-- Ringkasan Harga -->
                                    <div class="price-summary mb-4 p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Harga Paket:</span>
                                            <strong>Rp {{ number_format($package['price'], 0, ',', '.') }}</strong>
                                        </div>
                                        @if ($package['old_price'])
                                            <small class="text-muted">
                                                <del>Harga Normal: Rp
                                                    {{ number_format($package['old_price'], 0, ',', '.') }}</del>
                                            </small>
                                        @endif
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span><strong>Total Pembayaran:</strong></span>
                                            <strong class="text-danger">Rp
                                                {{ number_format($package['price'], 0, ',', '.') }}</strong>
                                        </div>
                                    </div>

                                    <!-- Catatan Penting -->
                                    <div class="alert alert-info mb-4">
                                        <h6><i class="fa fa-info-circle"></i> Catatan Penting:</h6>
                                        <ul class="mb-0 small">
                                            <li>Pembayaran akan diproses langsung setelah Anda mengklik tombol di bawah</li>
                                            <li>Anda akan diarahkan ke halaman pembayaran Tripay</li>
                                            <li>Paket akan diaktivasi otomatis setelah pembayaran dikonfirmasi</li>
                                            <li>Simpan bukti transaksi Anda</li>
                                        </ul>
                                    </div>

                                    <!-- Button Pembayaran -->
                                    <button type="submit" class="btn btn-success btn-lg btn-block" id="payBtn">
                                        <i class="fa fa-lock"></i> Lanjut ke Pembayaran
                                    </button>

                                    <a href="{{ route('subscription.packages') }}"
                                        class="btn btn-outline-secondary btn-block mt-2">
                                        <i class="fa fa-arrow-left"></i> Kembali
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bantuan -->
                <div class="row mt-4" style="margin-bottom:50px;">
                    <div class="col-12">
                        <div class="help-section text-center">
                            <h3>Butuh Bantuan?</h3>
                            <p class="text-muted mb-3">Tim customer service kami siap membantu Anda</p>
                            <div class="contact-options">
                                <a href="https://wa.me/6282339150860" target="_blank"
                                    class="btn btn-outline-success me-2">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <a href="mailto:support@bintarapolri.com" class="btn btn-outline-primary">
                                    <i class="fa fa-envelope"></i> Email Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .checkout-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .checkout-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .checkout-card .card-body {
            padding: 1.5rem;
        }

        .package-name {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .package-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #18a689;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.95rem;
        }

        .feature-item i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .user-info .info-item {
            margin-bottom: 0.5rem;
        }

        .order-id {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #18a689;
            font-weight: 600;
        }

        .payment-total-box {
            background: linear-gradient(135deg, #18a689, #0f6e58);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .bank-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fafafa;
        }

        .bank-info {
            display: flex;
            align-items: center;
        }

        .bank-logo {
            width: 40px;
            height: 40px;
            margin-right: 1rem;
            object-fit: contain;
        }

        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            font-weight: 600;
            color: #18a689;
        }

        .account-name {
            color: #6c757d;
        }

        .copy-btn {
            border: 1px solid #18a689;
            color: #18a689;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: #18a689;
            color: white;
        }

        .steps-list {
            padding-left: 1.5rem;
        }

        .steps-list li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .whatsapp-section {
            background: #f0f9f7;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .whatsapp-icon {
            font-size: 3rem;
            color: #25D366;
            margin-bottom: 1rem;
        }

        .whatsapp-btn {
            background: #25D366;
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .whatsapp-btn:hover {
            background: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }

        .help-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }

        .contact-options .btn {
            margin: 0 0.5rem;
        }

        .btn-default {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-default:hover {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .bank-info {
                flex-direction: column;
                text-align: center;
            }

            .bank-logo {
                margin-bottom: 0.5rem;
                margin-right: 0;
            }

            .contact-options .btn {
                display: block;
                margin: 0.5rem 0;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Success notification
                const btn = event.target.closest('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-primary');

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            }, function(err) {
                alert('Gagal menyalin nomor rekening. Silakan salin manual: ' + text);
            });
        }

        // Auto-select text when clicked
        document.querySelectorAll('.account-number').forEach(function(element) {
            element.addEventListener('click', function() {
                const range = document.createRange();
                range.selectNodeContents(this);
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            });
        });
    </script>
@endsection
