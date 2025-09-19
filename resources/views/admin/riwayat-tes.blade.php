@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
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
                                <h3 class="stat-number">{{ $totalTests }}</h3>
                                <p class="stat-label">Total Tes</p>
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
                                <h3 class="stat-number">{{ $todayTests }}</h3>
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
                                <h3 class="stat-number">{{ $thisWeekTests }}</h3>
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
                                <h3 class="stat-number">{{ $thisMonthTests }}</h3>
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
                <h5><i class="fa fa-history"></i> Riwayat Tes User</h5>
                <div class="ibox-tools">
                    <span class="badge badge-primary">{{ $hasilTes->total() }} Hasil Tes</span>
                </div>
            </div>
            <div class="ibox-content">
                <!-- Filter Section -->
                <div class="filter-section mb-4">
                    <form method="GET" action="{{ route('admin.riwayat-tes') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Jenis Tes</label>
                            <select name="jenis_tes" class="form-control">
                                <option value="">Semua Jenis</option>
                                <option value="kecermatan" {{ request('jenis_tes') == 'kecermatan' ? 'selected' : '' }}>Kecermatan</option>
                                <option value="kecerdasan" {{ request('jenis_tes') == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan</option>
                                <option value="kepribadian" {{ request('jenis_tes') == 'kepribadian' ? 'selected' : '' }}>Kepribadian</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari User</label>
                            <input type="text" name="user_search" class="form-control" placeholder="Nama atau email..." value="{{ request('user_search') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.riwayat-tes') }}" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Results Section -->
                @if($hasilTes->count() > 0)
                    <div class="row">
                        @foreach($hasilTes as $tes)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="compact-test-card {{ $tes->kategori_skor ?? 'fair' }}">
                                    <div class="card-header-compact">
                                        <div class="test-type">
                                            <i class="fa {{ $tes->jenis_tes == 'kecermatan' ? 'fa-eye' : ($tes->jenis_tes == 'kecerdasan' ? 'fa-brain' : 'fa-user-friends') }}"></i>
                                            <span class="type-text">{{ ucfirst($tes->jenis_tes ?? 'Tes') }}</span>
                                        </div>
                                        <div class="test-date">
                                            {{ \Carbon\Carbon::parse($tes->tanggal_tes)->format('d/m H:i') }}
                                        </div>
                                    </div>
                                    
                                    <div class="card-body-compact">
                                        <div class="user-info-compact">
                                            <div class="user-name">{{ $tes->user_name }}</div>
                                            <div class="user-package">{{ ucfirst($tes->user_package ?? 'free') }}</div>
                                        </div>

                                        <div class="score-compact">
                                            <div class="score-main">
                                                @if($tes->skor_akhir)
                                                    <span class="score-number">{{ number_format($tes->skor_akhir, 0) }}</span>
                                                    <span class="score-label">Skor</span>
                                                @else
                                                    @php
                                                        $total = $tes->skor_benar + $tes->skor_salah;
                                                        $percentage = $total > 0 ? round(($tes->skor_benar / $total) * 100) : 0;
                                                    @endphp
                                                    <span class="score-number">{{ $percentage }}%</span>
                                                    <span class="score-label">Skor</span>
                                                @endif
                                            </div>
                                            <div class="score-details-compact">
                                                <span class="score-item correct">{{ $tes->skor_benar }}✓</span>
                                                <span class="score-item wrong">{{ $tes->skor_salah }}✗</span>
                                                @if($tes->waktu_total)
                                                    <span class="score-item duration">{{ round($tes->waktu_total / 60) }}m</span>
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
                        {{ $hasilTes->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa fa-clipboard-list fa-3x"></i>
                        </div>
                        <h4>Tidak Ada Data Tes</h4>
                        <p>Tidak ada hasil tes yang sesuai dengan filter yang dipilih.</p>
                        <a href="{{ route('admin.riwayat-tes') }}" class="btn btn-primary">
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
    background: rgba(0,0,0,0.05);
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
    overflow: hidden;
    border-left: 3px solid #ddd;
    height: 100%;
}

.compact-test-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
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

.user-info-compact {
    margin-bottom: 12px;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-package {
    font-size: 10px;
    color: #666;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 10px;
    display: inline-block;
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
    .compact-test-card {
        margin-bottom: 15px;
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

    .user-name {
        font-size: 13px;
    }
}

/* Override Bootstrap Pagination with Inspinia Style */
.pagination {
    display: block !important;
    margin: 0 !important;
    padding-left: 0 !important;
    list-style: none !important;
    border-radius: 0 !important;
}

.pagination > li {
    display: inline-block !important;
}

.pagination > li > a,
.pagination > li > span {
    background-color: #FFFFFF !important;
    border: 1px solid #DDDDDD !important;
    color: inherit !important;
    float: left !important;
    line-height: 1.42857 !important;
    margin-left: -1px !important;
    padding: 4px 10px !important;
    position: relative !important;
    text-decoration: none !important;
    display: block !important;
    border-radius: 0 !important;
}

.pagination > li > a:hover,
.pagination > li > span:hover {
    background-color: #f4f4f4 !important;
    border-color: #DDDDDD !important;
    color: inherit !important;
    text-decoration: none !important;
}

.pagination > .active > a,
.pagination > .active > span,
.pagination > .active > a:hover,
.pagination > .active > span:hover,
.pagination > .active > a:focus,
.pagination > .active > span:focus {
    background-color: #1ab394 !important;
    border-color: #1ab394 !important;
    color: white !important;
    cursor: default !important;
    z-index: 2 !important;
}

.pagination > .disabled > a,
.pagination > .disabled > span,
.pagination > .disabled > a:hover,
.pagination > .disabled > span:hover,
.pagination > .disabled > a:focus,
.pagination > .disabled > span:focus {
    background-color: #fff !important;
    border-color: #ddd !important;
    color: #777 !important;
    cursor: not-allowed !important;
}

/* Override Bootstrap page-link class */
.page-link {
    background-color: #FFFFFF !important;
    border: 1px solid #DDDDDD !important;
    color: inherit !important;
    padding: 4px 10px !important;
    line-height: 1.42857 !important;
    text-decoration: none !important;
    display: block !important;
    border-radius: 0 !important;
}

.page-link:hover {
    background-color: #f4f4f4 !important;
    border-color: #DDDDDD !important;
    color: inherit !important;
    text-decoration: none !important;
}

.page-item.active .page-link {
    background-color: #1ab394 !important;
    border-color: #1ab394 !important;
    color: white !important;
}

/* Fix large icons in pagination */
.pagination i,
.pagination .icon,
.pagination .fa,
.page-link i,
.page-link .icon,
.page-link .fa {
    font-size: 12px !important;
    line-height: 1 !important;
    width: auto !important;
    height: auto !important;
}
</style>
@endpush