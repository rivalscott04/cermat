@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('admin.riwayat-tes') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar User
                </a>
            </div>

            <!-- User Info Header -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-user"></i> Riwayat Tes - {{ $user->name }}</h5>
                    <div class="ibox-tools">
                        <span class="badge badge-info">{{ $user->getPackageDisplayName() }}</span>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="user-profile">
                                <div class="user-avatar-large">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="user-details">
                                    <h4 class="user-name">{{ $user->name }}</h4>
                                    <p class="user-email">{{ $user->email }}</p>
                                    <span
                                        class="user-package badge badge-{{ $user->package == 'lengkap' ? 'success' : ($user->package == 'kecermatan' ? 'info' : ($user->package == 'kecerdasan' ? 'primary' : ($user->package == 'kepribadian' ? 'warning' : 'secondary'))) }}">
                                        Package: {{ $user->getPackageDisplayName() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-stats">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['total_tests'] }}</div>
                                    <div class="stat-label">Total Tes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['today_tests'] }}</div>
                                    <div class="stat-label">Hari Ini</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['this_week_tests'] }}</div>
                                    <div class="stat-label">Minggu Ini</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['this_month_tests'] }}</div>
                                    <div class="stat-label">Bulan Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Overview Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-clipboard-list text-primary"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $hasilTes->total() }}</h3>
                                    <p class="stat-label">Total Hasil Tes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-day text-success"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $userStats['today_tests'] }}</h3>
                                    <p class="stat-label">Hari Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-week text-info"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $userStats['this_week_tests'] }}</h3>
                                    <p class="stat-label">Minggu Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-alt text-warning"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $userStats['this_month_tests'] }}</h3>
                                    <p class="stat-label">Bulan Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-history"></i> Riwayat Tes Detail</h5>
                    <div class="ibox-tools">
                        <span class="badge badge-primary">{{ $hasilTes->total() }} Hasil Tes</span>
                    </div>
                </div>
                <div class="ibox-content">
                    <!-- Filter Section -->
                    <div class="filter-section mb-4">
                        <form method="GET" action="{{ route('admin.riwayat-tes-user', $user->id) }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Jenis Tes</label>
                                <select name="jenis_tes" class="form-control">
                                    <option value="">Semua Jenis</option>
                                    <option value="kecermatan"
                                        {{ request('jenis_tes') == 'kecermatan' ? 'selected' : '' }}>Kecermatan</option>
                                    <option value="kecerdasan"
                                        {{ request('jenis_tes') == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan</option>
                                    <option value="kepribadian"
                                        {{ request('jenis_tes') == 'kepribadian' ? 'selected' : '' }}>Kepribadian</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.riwayat-tes-user', $user->id) }}" class="btn btn-secondary">
                                        <i class="fa fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Results Section -->
                    @if ($hasilTes->count() > 0)
                        <div class="row">
                            @foreach ($hasilTes as $tes)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="compact-test-card {{ $tes->kategori_skor ?? 'fair' }}">
                                        <div class="card-header-compact">
                                            <div class="test-type">
                                                <i
                                                    class="fa {{ $tes->jenis_tes == 'kecermatan' ? 'fa-eye' : ($tes->jenis_tes == 'kecerdasan' ? 'fa-brain' : 'fa-user-friends') }}"></i>
                                                <span class="type-text">{{ ucfirst($tes->jenis_tes ?? 'Tes') }}</span>
                                            </div>
                                            <div class="test-date">
                                                {{ \Carbon\Carbon::parse($tes->tanggal_tes)->format('d/m H:i') }}
                                            </div>
                                        </div>

                                        <div class="card-body-compact">
                                            <div class="score-compact">
                                                <div class="score-main">
                                                    @if ($tes->skor_akhir)
                                                        <span
                                                            class="score-number">{{ number_format($tes->skor_akhir, 0) }}</span>
                                                        <span class="score-label">Skor</span>
                                                    @else
                                                        @php
                                                            $total = $tes->skor_benar + $tes->skor_salah;
                                                            $percentage =
                                                                $total > 0
                                                                    ? round(($tes->skor_benar / $total) * 100)
                                                                    : 0;
                                                        @endphp
                                                        <span class="score-number">{{ $percentage }}%</span>
                                                        <span class="score-label">Skor</span>
                                                    @endif
                                                </div>
                                                <div class="score-details-compact">
                                                    <span class="score-item correct">{{ $tes->skor_benar }}✓</span>
                                                    <span class="score-item wrong">{{ $tes->skor_salah }}✗</span>
                                                    @if ($tes->waktu_total)
                                                        <span
                                                            class="score-item duration">{{ round($tes->waktu_total / 60) }}m</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card-footer-compact">
                                                <div class="status-badge-compact {{ $tes->kategori_skor ?? 'fair' }}">
                                                    @switch($tes->kategori_skor)
                                                        @case('excellent')
                                                            <i class="fa fa-star"></i> Excellent
                                                        @break

                                                        @case('good')
                                                            <i class="fa fa-thumbs-up"></i> Good
                                                        @break

                                                        @case('fair')
                                                            <i class="fa fa-meh-o"></i> Fair
                                                        @break

                                                        @case('poor')
                                                            <i class="fa fa-frown-o"></i> Poor
                                                        @break

                                                        @default
                                                            <i class="fa fa-question"></i> Belum Dinilai
                                                    @endswitch
                                                </div>
                                                <a href="{{ route('kecermatan.detail', $tes->id) }}" class="btn-detail">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $hasilTes->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fa fa-clipboard-list fa-3x"></i>
                            </div>
                            <h4>Tidak Ada Data Tes</h4>
                            <p>Tidak ada hasil tes yang sesuai dengan filter yang dipilih.</p>
                            <a href="{{ route('admin.riwayat-tes-user', $user->id) }}" class="btn btn-primary">
                                <i class="fa fa-refresh"></i> Lihat Semua
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* User Profile Header */
        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }

        .user-details h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .user-details p {
            margin: 0 0 10px 0;
            color: #666;
        }

        .user-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .user-stats .stat-item {
            flex: 1;
        }

        .user-stats .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .user-stats .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        /* Statistics Cards */
        .stat-card {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background: rgba(0, 0, 0, 0.05);
        }

        .stat-icon i {
            font-size: 24px;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .stat-label {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        /* Filter Section */
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .filter-section .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        /* Compact Test Cards */
        .compact-test-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            overflow: hidden;
            border-left: 3px solid #ddd;
            height: 100%;
        }

        .compact-test-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .compact-test-card.excellent {
            border-left-color: #28a745;
        }

        .compact-test-card.good {
            border-left-color: #17a2b8;
        }

        .compact-test-card.fair {
            border-left-color: #ffc107;
        }

        .compact-test-card.poor {
            border-left-color: #dc3545;
        }

        .card-header-compact {
            padding: 12px 15px 8px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .test-type {
            display: flex;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .test-type i {
            margin-right: 6px;
            font-size: 12px;
            color: #666;
        }

        .test-date {
            font-size: 11px;
            color: #999;
            font-weight: 500;
        }

        .card-body-compact {
            padding: 12px 15px;
        }

        .score-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .score-main {
            text-align: center;
        }

        .score-number {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            line-height: 1;
        }

        .score-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .score-details-compact {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .score-item {
            font-size: 11px;
            font-weight: 500;
            padding: 1px 4px;
            border-radius: 3px;
            text-align: center;
            min-width: 35px;
        }

        .score-item.correct {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .score-item.wrong {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }

        .score-item.duration {
            color: #17a2b8;
            background: rgba(23, 162, 184, 0.1);
        }

        .card-footer-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
        }

        .status-badge-compact {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .status-badge-compact.excellent {
            background: #d4edda;
            color: #155724;
        }

        .status-badge-compact.good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge-compact.fair {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge-compact.poor {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-detail {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 10px;
            transition: all 0.2s ease;
        }

        .btn-detail:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
            transform: scale(1.1);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-icon {
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            margin-bottom: 10px;
            color: #495057;
        }

        .empty-state p {
            margin-bottom: 30px;
            font-size: 16px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-profile {
                flex-direction: column;
                text-align: center;
            }

            .user-avatar-large {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .user-stats {
                flex-wrap: wrap;
                gap: 15px;
            }

            .score-compact {
                flex-direction: column;
                gap: 8px;
            }

            .score-details-compact {
                flex-direction: row;
                justify-content: center;
                gap: 8px;
            }

            .filter-section .row {
                margin: 0;
            }

            .filter-section .col-md-3 {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .stat-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .compact-test-card {
                margin-bottom: 12px;
            }

            .card-header-compact {
                padding: 10px 12px 6px;
            }

            .card-body-compact {
                padding: 10px 12px;
            }

            .score-number {
                font-size: 16px;
            }
        }
    </style>
@endpush
