@extends('layouts.app')

@push('styles')
  <style>
    .payment-option {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .payment-option:hover {
      border-color: #0d6efd;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .payment-logo {
      max-height: 50px;
      object-fit: contain;
    }

    .order-summary {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
    }
  </style>
@endpush

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Pilih Metode Pembayaran</h5>
        </div>
        <div class="ibox-content">
          <div class="row">
            <!-- Payment Methods Section -->
            <div class="col-md-8">
              <h2 class="mb-4">Metode Pembayaran yang Tersedia</h2>

              <!-- E-Wallet Options -->
              <div class="mb-4">
                <div class="payment-option">
                  <img src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Remittance/PayPal.svg"
                    alt="PayPal" class="payment-logo">
                </div>
                <div class="payment-option">
                  <div class="d-flex gap-2">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/Card%20Payment/VISA.svg"
                      alt="Visa" class="payment-logo">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/Card%20Payment/Mastercard.svg"
                      alt="Mastercard" class="payment-logo">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/Card%20Payment/Discover.svg"
                      alt="Discover" class="payment-logo">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/Card%20Payment/American%20Express.svg"
                      alt="American Express" class="payment-logo">
                  </div>
                </div>
              </div>

              <!-- Bank Transfer Options -->
              <h3 class="mb-3">Metode pembayaran lainnya</h3>
              <div class="row">
                <div class="col-md-6">
                  <div class="payment-option">
                    <img src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/BSI.svg"
                      alt="BSI" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option">
                    <img src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/BNI.svg"
                      alt="BNI Logo" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option">
                    <img src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/BRI.svg"
                      alt="BRIVA" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Bank/Bank%20Logo/Mandiri.svg"
                      alt="Mandiri" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option">
                    <img
                      src="https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/Payment%20Channel/E-Wallet/OVO.png"
                      alt="OVO" class="payment-logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="payment-option">
                    <img
                      src="https://raw.githubusercontent.com/Adekabang/indonesia-logo-library/main/Payment%20Channel/Miscellaneous/QRIS.png"
                      alt="QRIS" class="payment-logo">
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-md-4">
              <div class="order-summary">
                <h3 class="mb-4">Ringkasan Order</h3>
                <div class="mb-2">
                  <small class="text-muted">Nomor Tagihan: hb_24215313</small>
                </div>

                <div class="mb-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span>.COM Domain</span>
                    <span>Rp319.800</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Domain WHOIS Protection</span>
                    <span>Rp0</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>ICANN fee</span>
                    <span>Rp5.620</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>PPN 11%</span>
                    <span>Rp35.796</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Kredit</span>
                    <span>Rp0</span>
                  </div>
                </div>

                <div class="d-flex justify-content-between border-top pt-3">
                  <h4>Total</h4>
                  <h4 class="text-primary">Rp 361.216</h4>
                </div>

                <div class="mt-4">
                  <div class="d-flex align-items-center text-success gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                      class="bi bi-shield-check" viewBox="0 0 16 16">
                      <path
                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                    </svg>
                    <div>
                      <strong>Transaksi Aman dengan SSL</strong>
                      <p class="small mb-0">Enkripsi Anda dilindungi oleh enkripsi SSL 256-bit</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
