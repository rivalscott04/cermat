@extends('layouts.app')

@section('content')
    <div class="back-button-container mt-2">
        <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 100px;">
                    <h2 class="mt-3">Status Paket Berlangganan</h2>
                    <p>Informasi paket dan fitur yang dapat Anda akses</p>
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

                {{-- Layout 2 Kolom: Status & Detail Paket --}}
                <div class="row mb-4">
                    {{-- Kolom 1: Status Paket --}}
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-{{ $user->hasActiveSubscription() ? 'success' : 'warning' }} shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-{{ $user->hasActiveSubscription() ? 'success' : 'warning' }} text-uppercase mb-1">
                                            Status Langganan
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            @if ($user->hasActiveSubscription())
                                                <i class="fa fa-check-circle mr-2 text-success"></i>
                                                {{ $packageDisplayName }}
                                            @else
                                                <i class="fa fa-exclamation-triangle mr-2 text-warning"></i>
                                                Belum Berlangganan
                                            @endif
                                        </div>
                                        @if ($user->hasActiveSubscription() && $subscription && $subscription->end_date)
                                            <div class="text-xs text-muted mt-1">
                                                Berakhir: {{ $subscription->end_date->format('d M Y') }}
                                                <br><small>({{ $subscription->end_date->diffForHumans() }})</small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-auto">
                                        @if ($user->hasActiveSubscription())
                                            <i class="fa fa-check-circle fa-2x text-success"></i>
                                        @else
                                            <i class="fa fa-exclamation-triangle fa-2x text-warning"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom 2: Limit & Kuota --}}
                    <div class="col-md-6 mb-3">
                        <div class="card shadow h-100">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fa fa-bar-chart mr-2"></i>Limit & Kuota
                                </h6>
                            </div>
                            <div class="card-body py-2">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fa fa-info-circle text-info mr-2"></i>
                                        <span class="text-dark">Maksimal Tryout: 
                                            @if ($maxTryouts == 999)
                                                <span class="badge badge-success">Unlimited</span>
                                            @else
                                                <span class="badge badge-primary">{{ $maxTryouts }}</span>
                                            @endif
                                        </span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-graduation-cap text-primary mr-2"></i>
                                        <span class="text-dark">Jenis Tes: 
                                            @foreach ($allowedCategories as $category)
                                                <span class="badge badge-light mr-1">{{ $category }}</span>
                                            @endforeach
                                        </span>
                                    </li>
                                    <li class="mb-0">
                                        <i class="fa fa-clock-o text-warning mr-2"></i>
                                        <span class="text-dark text-sm">{{ $packageLimits['description'] }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Fitur Paket --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fa fa-star mr-2"></i>{{ $packageFeatures['title'] }} - {{ $packageFeatures['description'] }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if (count($packageFeatures['features']) > 0)
                                        @foreach ($packageFeatures['features'] as $feature)
                                            <div class="col-md-6 col-lg-4 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-check text-success mr-2"></i>
                                                    <span class="text-dark">{{ $feature }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <p class="text-muted">Tidak ada fitur yang tersedia</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Saran Upgrade Paket --}}
                @if (!$user->hasActiveSubscription() || $user->package !== 'lengkap')
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-left-info shadow">
                                <div class="card-header py-3 bg-info text-white">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fa fa-rocket mr-2"></i>
                                        @if (!$user->hasActiveSubscription())
                                            Mulai Berlangganan
                                        @else
                                            Saran Upgrade Paket
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="font-weight-bold text-dark mb-2">
                                                @if (!$user->hasActiveSubscription())
                                                    Akses Semua Fitur dengan Berlangganan!
                                                @else
                                                    Akses Lebih Banyak Fitur!
                                                @endif
                                            </h6>
                                            <p class="text-muted mb-3">
                                                @if (!$user->hasActiveSubscription())
                                                    Berlangganan sekarang untuk mengakses semua jenis tes dan fitur lengkap! Mulai dari Rp 75.000 untuk 30 hari akses penuh.
                                                @elseif ($user->package === 'kecermatan')
                                                    Upgrade ke Paket Lengkap untuk akses ke Tes Kecerdasan dan Kepribadian juga!
                                                @elseif ($user->package === 'kecerdasan')
                                                    Upgrade ke Paket Lengkap untuk akses ke Tes Kecermatan dan Kepribadian juga!
                                                @elseif ($user->package === 'kepribadian')
                                                    Upgrade ke Paket Lengkap untuk akses ke Tes Kecermatan dan Kecerdasan juga!
                                                @endif
                                            </p>
                                            
                                            @if ($user->hasActiveSubscription() && $user->package !== 'free')
                                                <div class="alert alert-info">
                                                    <i class="fa fa-lightbulb-o mr-2"></i>
                                                    <strong>Tip:</strong> Paket Lengkap memberikan akses ke semua jenis tes dengan harga yang lebih hemat!
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <a href="{{ route('subscription.packages.admin') }}" 
                                               class="btn btn-success btn-lg btn-block">
                                                <i class="fa fa-{{ !$user->hasActiveSubscription() ? 'shopping-cart' : 'arrow-up' }} mr-2"></i>
                                                @if (!$user->hasActiveSubscription())
                                                    Berlangganan Sekarang
                                                @else
                                                    Lihat Paket Lain
                                                @endif
                                            </a>
                                            <small class="text-muted mt-2 d-block">
                                                @if (!$user->hasActiveSubscription())
                                                    Mulai perjalanan persiapan tes Anda
                                                @else
                                                    Upgrade sekarang untuk pengalaman terbaik
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Menu Akses Cepat & Progress (2 Kolom) --}}
                <div class="row mb-4">
                    {{-- Kolom 1: Menu Akses Cepat --}}
                    <div class="col-md-8 mb-3">
                        <div class="card shadow h-100">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fa fa-bolt mr-2"></i>Akses Cepat
                                </h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    @if ($user->canAccessTryout())
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('show.test') }}" class="btn btn-outline-primary btn-sm btn-block">
                                                <i class="fa fa-check-square-o mr-1"></i>
                                                Mulai Tes
                                            </a>
                                        </div>
                                    @else
                                        <div class="col-6 mb-2">
                                            <button class="btn btn-outline-secondary btn-sm btn-block" disabled>
                                                <i class="fa fa-lock mr-1"></i>
                                                Mulai Tes
                                            </button>
                                            <small class="text-muted">Berlangganan untuk akses</small>
                                        </div>
                                    @endif
                                    
                                    @if ($user->canAccessKecermatan())
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('kecermatan.index') }}" class="btn btn-outline-success btn-sm btn-block">
                                                <i class="fa fa-eye mr-1"></i>
                                                Tes Kecermatan
                                            </a>
                                        </div>
                                    @else
                                        <div class="col-6 mb-2">
                                            <button class="btn btn-outline-secondary btn-sm btn-block" disabled>
                                                <i class="fa fa-lock mr-1"></i>
                                                Tes Kecermatan
                                            </button>
                                            <small class="text-muted">Paket Kecermatan/Lengkap</small>
                                        </div>
                                    @endif
                                    
                                    <div class="col-12">
                                        <a href="{{ route('user.history.index') }}" class="btn btn-outline-info btn-sm btn-block">
                                            <i class="fa fa-history mr-1"></i>
                                            Riwayat Tes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom 2: Progress Paket Lengkap (jika ada) --}}
                    <div class="col-md-4 mb-3">
                        @if ($user->package === 'lengkap' && $user->getPaketLengkapStatus())
                            <div class="card border-left-success shadow h-100">
                                <div class="card-header py-2 bg-success text-white">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fa fa-crown mr-2"></i>Progress
                                    </h6>
                                </div>
                                <div class="card-body py-2 text-center">
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $user->getPaketLengkapProgress() }}%">
                                            {{ $user->getPaketLengkapProgress() }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Penyelesaian Paket Lengkap</small>
                                </div>
                            </div>
                        @else
                            <div class="card border-left-info shadow h-100">
                                <div class="card-header py-2 bg-info text-white">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fa fa-info-circle mr-2"></i>Info
                                    </h6>
                                </div>
                                <div class="card-body py-2 text-center">
                                    <i class="fa fa-lightbulb-o fa-2x text-warning mb-2"></i>
                                    <p class="mb-0 text-sm">
                                        @if (!$user->hasActiveSubscription())
                                            Berlangganan untuk akses penuh
                                        @else
                                            Upgrade ke Paket Lengkap untuk progress tracking
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="text-center mt-4">
                    <small class="text-muted">
                        Butuh bantuan? <a href="#">Hubungi Customer Service</a> kami
                    </small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-left-success {
            border-left: 0.25rem solid #28a745 !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #ffc107 !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #17a2b8 !important;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
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
        
        .badge {
            font-size: 0.75rem;
        }
        
        .text-xs {
            font-size: 0.7rem;
        }
        
        .font-weight-bold {
            font-weight: 700 !important;
        }
        
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        
        .text-sm {
            font-size: 0.875rem;
        }
        
        .card-header {
            padding: 0.5rem 1rem !important;
        }
        
        .card-body {
            padding: 0.75rem 1rem !important;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .h-100 {
            height: 100% !important;
        }
    </style>
@endsection
