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
                            <div class="col-lg-6 col-md-6 mb-4">
                                <div class="test-history-card {{ $tes->kategori_skor ?? 'fair' }}">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="fa {{ $tes->jenis_tes == 'kecermatan' ? 'fa-eye' : ($tes->jenis_tes == 'kecerdasan' ? 'fa-brain' : 'fa-user-friends') }}"></i>
                                            {{ ucfirst($tes->jenis_tes ?? 'Tes') }}
                                            <span class="type-badge {{ $tes->jenis_tes ?? 'lain' }}">
                                                {{ ucfirst($tes->jenis_tes ?? 'Lainnya') }}
                                            </span>
                                        </div>
                                        <div class="card-date">
                                            {{ \Carbon\Carbon::parse($tes->tanggal_tes)->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="user-info mb-3">
                                            <div class="user-avatar">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="user-details">
                                                <h6 class="user-name">{{ $tes->user_name }}</h6>
                                                <p class="user-email">{{ $tes->user_email }}</p>
                                                <span class="user-package badge badge-info">{{ ucfirst($tes->user_package ?? 'free') }}</span>
                                            </div>
                                        </div>

                                        <div class="score-section">
                                            <div class="score-circle">
                                                @if($tes->skor_akhir)
                                                    <div class="score-percentage">{{ number_format($tes->skor_akhir, 0) }}</div>
                                                    <div class="score-label">Skor Akhir</div>
                                                @else
                                                    @php
                                                        $total = $tes->skor_benar + $tes->skor_salah;
                                                        $percentage = $total > 0 ? round(($tes->skor_benar / $total) * 100) : 0;
                                                    @endphp
                                                    <div class="score-percentage">{{ $percentage }}%</div>
                                                    <div class="score-label">Skor</div>
                                                @endif
                                            </div>
                                            
                                            <div class="score-details">
                                                <div class="score-item correct">
                                                    <i class="fa fa-check-circle"></i>
                                                    <span>{{ $tes->skor_benar }} Benar</span>
                                                </div>
                                                <div class="score-item wrong">
                                                    <i class="fa fa-times-circle"></i>
                                                    <span>{{ $tes->skor_salah }} Salah</span>
                                                </div>
                                                <div class="score-item total">
                                                    <i class="fa fa-list"></i>
                                                    <span>{{ $tes->skor_benar + $tes->skor_salah }} Soal</span>
                                                </div>
                                                @if($tes->waktu_total)
                                                    <div class="score-item duration">
                                                        <i class="fa fa-clock-o"></i>
                                                        <span>{{ round($tes->waktu_total / 60) }} menit</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($tes->skor_akhir)
                                            <div class="progress-section">
                                                <div class="progress">
                                                    <div class="progress-bar {{ $tes->kategori_skor ?? 'fair' }}" 
                                                         style="width: {{ min(100, ($tes->skor_akhir / 100) * 100) }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="card-footer">
                                            <div class="test-actions">
                                                <a href="{{ route('kecermatan.detail', $tes->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-eye"></i> Lihat Detail
                                                </a>
                                            </div>
                                            <div class="status-badge {{ $tes->kategori_skor ?? 'fair' }}">
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

/* Test History Cards */
.test-history-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    border-left: 4px solid #ddd;
    height: 100%;
}

.test-history-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.test-history-card.excellent {
    border-left-color: #28a745;
}

.test-history-card.good {
    border-left-color: #17a2b8;
}

.test-history-card.fair {
    border-left-color: #ffc107;
}

.test-history-card.poor {
    border-left-color: #dc3545;
}

.card-header {
    padding: 20px 20px 10px;
    border-bottom: 1px solid #eee;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
}

.card-title i {
    margin-right: 8px;
    color: #666;
}

.type-badge {
    display: inline-block;
    margin-left: 8px;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    vertical-align: middle;
}

.type-badge.kecermatan { background: #e8f5e9; color: #2e7d32; }
.type-badge.kecerdasan { background: #e3f2fd; color: #1565c0; }
.type-badge.kepribadian { background: #f3e5f5; color: #6a1b9a; }
.type-badge.lain { background: #f5f5f5; color: #666; }

.card-date {
    font-size: 12px;
    color: #999;
}

.card-body {
    padding: 20px;
}

/* User Info */
.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    border: 2px solid #e9ecef;
}

.user-avatar i {
    font-size: 20px;
    color: #666;
}

.user-details {
    flex: 1;
}

.user-name {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.user-email {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: #666;
}

.user-package {
    font-size: 11px;
    padding: 2px 8px;
}

/* Score Section */
.score-section {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.score-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    position: relative;
}

.test-history-card.excellent .score-circle {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.test-history-card.good .score-circle {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
    color: white;
}

.test-history-card.fair .score-circle {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.test-history-card.poor .score-circle {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    color: white;
}

.score-percentage {
    font-size: 20px;
    font-weight: bold;
    line-height: 1;
}

.score-label {
    font-size: 10px;
    opacity: 0.9;
}

.score-details {
    flex: 1;
}

.score-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 14px;
}

.score-item i {
    width: 16px;
    margin-right: 8px;
}

.score-item.correct {
    color: #28a745;
}

.score-item.wrong {
    color: #dc3545;
}

.score-item.total {
    color: #6c757d;
}

.score-item.duration {
    color: #17a2b8;
}

/* Progress Section */
.progress-section {
    margin-bottom: 15px;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.6s ease;
}

.progress-bar.excellent {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.progress-bar.good {
    background: linear-gradient(90deg, #17a2b8, #6f42c1);
}

.progress-bar.fair {
    background: linear-gradient(90deg, #ffc107, #fd7e14);
}

.progress-bar.poor {
    background: linear-gradient(90deg, #dc3545, #e83e8c);
}

/* Card Footer */
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.test-actions .btn {
    padding: 6px 12px;
    font-size: 12px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.excellent {
    background: #d4edda;
    color: #155724;
}

.status-badge.good {
    background: #d1ecf1;
    color: #0c5460;
}

.status-badge.fair {
    background: #fff3cd;
    color: #856404;
}

.status-badge.poor {
    background: #f8d7da;
    color: #721c24;
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
    .score-section {
        flex-direction: column;
        text-align: center;
    }
    
    .score-circle {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .card-footer {
        flex-direction: column;
        gap: 10px;
    }

    .user-info {
        flex-direction: column;
        text-align: center;
    }

    .user-avatar {
        margin-right: 0;
        margin-bottom: 10px;
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
}
</style>
@endpush