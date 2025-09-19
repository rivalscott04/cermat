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
                                <i class="fa fa-users text-primary"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{ $totalUsers }}</h3>
                                <p class="stat-label">Total User</p>
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
                                <i class="fa fa-clipboard-list text-success"></i>
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
                                <i class="fa fa-calendar-day text-info"></i>
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
                                <i class="fa fa-calendar-week text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{ $thisWeekTests }}</h3>
                                <p class="stat-label">Minggu Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-users"></i> Daftar User - Riwayat Tes</h5>
                <div class="ibox-tools">
                    <span class="badge badge-primary">{{ $users->total() }} User</span>
                </div>
            </div>
            <div class="ibox-content">
                <!-- Filter Section -->
                <div class="filter-section mb-4">
                    <form method="GET" action="{{ route('admin.riwayat-tes') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Package</label>
                            <select name="package" class="form-control">
                                <option value="">Semua Package</option>
                                <option value="free" {{ request('package') == 'free' ? 'selected' : '' }}>Free</option>
                                <option value="kecermatan" {{ request('package') == 'kecermatan' ? 'selected' : '' }}>Kecermatan</option>
                                <option value="kecerdasan" {{ request('package') == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan</option>
                                <option value="kepribadian" {{ request('package') == 'kepribadian' ? 'selected' : '' }}>Kepribadian</option>
                                <option value="lengkap" {{ request('package') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cari User</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.riwayat-tes') }}" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Results Section -->
                @if($users->count() > 0)
                    <div class="row">
                        @foreach($users as $user)
                            <div class="col-lg-6 col-md-6 mb-4">
                                <div class="user-card">
                                    <div class="card-header">
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="user-details">
                                                <h6 class="user-name">{{ $user->name }}</h6>
                                                <p class="user-email">{{ $user->email }}</p>
                                                <span class="user-package badge badge-{{ $user->package == 'lengkap' ? 'success' : ($user->package == 'kecermatan' ? 'info' : ($user->package == 'kecerdasan' ? 'primary' : ($user->package == 'kepribadian' ? 'warning' : 'secondary'))) }}">
                                                    {{ ucfirst($user->package ?? 'Free') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="stats-row">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $user->total_tests }}</div>
                                                <div class="stat-label">Total Tes</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $user->today_tests }}</div>
                                                <div class="stat-label">Hari Ini</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $user->this_week_tests }}</div>
                                                <div class="stat-label">Minggu Ini</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $user->this_month_tests }}</div>
                                                <div class="stat-label">Bulan Ini</div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-footer">
                                            <a href="{{ route('admin.riwayat-tes-user', $user->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-history"></i> Lihat Riwayat Tes
                                            </a>
                                            <span class="last-test">
                                                @if($user->total_tests > 0)
                                                    Terakhir: {{ \Carbon\Carbon::parse($user->hasilTes->first()->tanggal_tes ?? now())->diffForHumans() }}
                                                @else
                                                    Belum ada tes
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa fa-users fa-3x"></i>
                        </div>
                        <h4>Tidak Ada User</h4>
                        <p>Tidak ada user yang sesuai dengan filter yang dipilih.</p>
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

/* User Cards */
.user-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    border-left: 4px solid #ddd;
    height: 100%;
}

.user-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.card-header {
    padding: 20px 20px 10px;
    border-bottom: 1px solid #eee;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-weight: bold;
    font-size: 16px;
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
    padding: 4px 8px;
}

.card-body {
    padding: 20px;
}

.stats-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    flex: 1;
}

.stat-item .stat-number {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.stat-item .stat-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.last-test {
    font-size: 11px;
    color: #999;
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

/* Pagination Style - Same as User Index and Soal Index */
.pagination {
    margin: 20px 0;
    justify-content: center;
}

.pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
    color: #007bff;
    background-color: #fff;
    margin: 0 2px;
    border-radius: 4px;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-link i {
    font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .user-info {
        flex-direction: column;
        text-align: center;
    }

    .user-avatar {
        margin-right: 0;
        margin-bottom: 10px;
    }

    .stats-row {
        flex-wrap: wrap;
        gap: 10px;
    }

    .stat-item {
        flex: 1 1 45%;
    }

    .card-footer {
        flex-direction: column;
        gap: 10px;
    }

    .filter-section .row {
        margin: 0;
    }

    .filter-section .col-md-4,
    .filter-section .col-md-6,
    .filter-section .col-md-2 {
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

    .user-card {
        margin-bottom: 15px;
    }
}
</style>
@endpush