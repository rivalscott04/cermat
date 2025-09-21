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
                                                @if($tes->jenis_tes == 'kecermatan')
                                                    <a href="{{ route('kecermatan.detail', $tes->id) }}" class="btn-detail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @else
                                                    <a href="#" class="btn-detail" onclick="showTestDetail({{ $tes->id }}, '{{ $tes->jenis_tes }}')">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
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

@push('scripts')
<script>
// Admin Riwayat Tes Filter - Custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter functionality
    initAdminTestFilter();
    
    // Initialize test detail modal
    initTestDetailModal();
});

function initAdminTestFilter() {
    const filterForm = document.querySelector('.filter-section form');
    const filterButton = filterForm.querySelector('button[type="submit"]');
    const resetButton = filterForm.querySelector('a[href*="riwayat-tes-user"]');
    
    // Add loading state to filter button
    filterButton.addEventListener('click', function(e) {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        this.disabled = true;
        
        // Re-enable after 2 seconds (fallback)
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
        }, 2000);
    });
    
    // Auto-submit on date change
    const dateInputs = filterForm.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit form when date changes
            setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    });
    
    // Auto-submit on jenis tes change
    const jenisTesSelect = filterForm.querySelector('select[name="jenis_tes"]');
    if (jenisTesSelect) {
        jenisTesSelect.addEventListener('change', function() {
            setTimeout(() => {
                filterForm.submit();
            }, 300);
        });
    }
}

function initTestDetailModal() {
    // Create modal if it doesn't exist
    if (!document.getElementById('testDetailModal')) {
        const modalHTML = `
            <div class="modal fade" id="testDetailModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fa fa-eye"></i> Detail Hasil Tes
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="testDetailContent">
                            <div class="text-center">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p>Memuat detail tes...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
}

function showTestDetail(testId, testType) {
    const modal = document.getElementById('testDetailModal');
    const content = document.getElementById('testDetailContent');
    
    // Show modal
    $(modal).modal('show');
    
    // Reset content
    content.innerHTML = `
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Memuat detail tes...</p>
        </div>
    `;
    
    // Fetch test details
    fetch(`/admin/test-detail/${testId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            content.innerHTML = generateTestDetailHTML(data.test, testType);
        } else {
            content.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    ${data.message || 'Gagal memuat detail tes'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i>
                Terjadi kesalahan saat memuat detail tes. Silakan coba lagi.
            </div>
        `;
    });
}

function generateTestDetailHTML(test, testType) {
    const testDate = new Date(test.tanggal_tes).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const scorePercentage = test.skor_benar + test.skor_salah > 0 
        ? Math.round((test.skor_benar / (test.skor_benar + test.skor_salah)) * 100)
        : 0;
    
    return `
        <div class="test-detail-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-section">
                        <h6><i class="fa fa-info-circle"></i> Informasi Tes</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Jenis Tes:</strong></td>
                                <td>
                                    <span class="badge badge-primary">
                                        ${testType.charAt(0).toUpperCase() + testType.slice(1)}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Tes:</strong></td>
                                <td>${testDate}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu Total:</strong></td>
                                <td>${test.waktu_total ? Math.round(test.waktu_total / 60) + ' menit' : 'Tidak tersedia'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-section">
                        <h6><i class="fa fa-chart-bar"></i> Hasil Tes</h6>
                        <div class="score-display">
                            <div class="score-circle">
                                <span class="score-number">${scorePercentage}%</span>
                                <span class="score-label">Skor</span>
                            </div>
                            <div class="score-breakdown">
                                <div class="score-item correct">
                                    <i class="fa fa-check"></i>
                                    <span>${test.skor_benar} Benar</span>
                                </div>
                                <div class="score-item wrong">
                                    <i class="fa fa-times"></i>
                                    <span>${test.skor_salah} Salah</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            ${test.skor_akhir ? `
                <div class="detail-section">
                    <h6><i class="fa fa-star"></i> Skor Akhir</h6>
                    <div class="final-score">
                        <span class="final-score-number">${test.skor_akhir}</span>
                        <span class="final-score-label">Skor Akhir</span>
                    </div>
                </div>
            ` : ''}
            
            ${test.kategori_skor ? `
                <div class="detail-section">
                    <h6><i class="fa fa-trophy"></i> Kategori Skor</h6>
                    <div class="category-badge ${test.kategori_skor}">
                        ${getCategoryDisplay(test.kategori_skor)}
                    </div>
                </div>
            ` : ''}
        </div>
        
        <style>
            .test-detail-content .detail-section {
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #007bff;
            }
            
            .test-detail-content .detail-section h6 {
                margin-bottom: 15px;
                color: #333;
                font-weight: 600;
            }
            
            .score-display {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            
            .score-circle {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: linear-gradient(135deg, #007bff, #0056b3);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: white;
                text-align: center;
            }
            
            .score-number {
                font-size: 20px;
                font-weight: bold;
                line-height: 1;
            }
            
            .score-label {
                font-size: 10px;
                opacity: 0.9;
            }
            
            .score-breakdown {
                flex: 1;
            }
            
            .score-item {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
                font-size: 14px;
            }
            
            .score-item.correct {
                color: #28a745;
            }
            
            .score-item.wrong {
                color: #dc3545;
            }
            
            .score-item i {
                margin-right: 8px;
                width: 16px;
            }
            
            .final-score {
                text-align: center;
                padding: 20px;
                background: linear-gradient(135deg, #28a745, #20c997);
                border-radius: 8px;
                color: white;
            }
            
            .final-score-number {
                display: block;
                font-size: 32px;
                font-weight: bold;
                line-height: 1;
            }
            
            .final-score-label {
                font-size: 14px;
                opacity: 0.9;
            }
            
            .category-badge {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 12px;
            }
            
            .category-badge.excellent {
                background: #d4edda;
                color: #155724;
            }
            
            .category-badge.good {
                background: #d1ecf1;
                color: #0c5460;
            }
            
            .category-badge.fair {
                background: #fff3cd;
                color: #856404;
            }
            
            .category-badge.poor {
                background: #f8d7da;
                color: #721c24;
            }
        </style>
    `;
}

function getCategoryDisplay(category) {
    const categories = {
        'excellent': '<i class="fa fa-star"></i> Excellent',
        'good': '<i class="fa fa-thumbs-up"></i> Good',
        'fair': '<i class="fa fa-meh-o"></i> Fair',
        'poor': '<i class="fa fa-frown-o"></i> Poor'
    };
    return categories[category] || '<i class="fa fa-question"></i> Belum Dinilai';
}

// Export functions for global access
window.showTestDetail = showTestDetail;
window.initAdminTestFilter = initAdminTestFilter;
</script>
@endpush
