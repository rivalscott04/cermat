@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Daftar Tryout Tersedia</h4>
                                <p class="text-muted mb-0">
                                    Paket Anda:
                                    <span
                                        class="badge badge-{{ auth()->user()->paket_akses === 'lengkap' ? 'danger' : (auth()->user()->paket_akses === 'kecerdasan' || auth()->user()->paket_akses === 'kepribadian' ? 'primary' : 'success') }}">
                                        {{ strtoupper(auth()->user()->paket_akses) }}
                                    </span>
                                    @if (auth()->user()->paket_akses === 'free')
                                        <small class="text-muted">(1 tryout tersedia)</small>
                                    @else
                                        <small class="text-muted">({{ auth()->user()->getMaxTryouts() }} tryout
                                            tersedia)</small>
                                    @endif
                                </p>
                            </div>
                            @if (auth()->user()->paket_akses === 'lengkap')
                                <div>
                                    <a href="{{ route('user.tryout.paket-lengkap.status') }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-trophy"></i> Status Paket Lengkap
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            @forelse($tryouts as $tryout)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">{{ $tryout->judul }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">{{ Str::limit($tryout->deskripsi, 100) }}</p>

                                            <div class="mb-3 p-2 border rounded bg-light">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted"><i class="fa fa-question-circle"></i> Jumlah Soal:</span>
                                                    <strong>{{ $tryout->blueprints->sum('jumlah_soal') }} soal</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted"><i class="fa fa-clock-o"></i> Waktu Tersedia:</span>
                                                    <strong>{{ $tryout->durasi_menit }} menit</strong>
                                                </div>
                                            </div>

                                            {{-- <div class="text-center">
                                                <span
                                                    class="badge badge-{{ $tryout->jenis_paket === 'lengkap' ? 'danger' : ($tryout->jenis_paket === 'kecerdasan' || $tryout->jenis_paket === 'kepribadian' ? 'primary' : 'success') }} mb-2">
                                                    {{ strtoupper($tryout->jenis_paket) }}
                                                </span>
                                                @if ($tryout->jenis_paket === 'free')
                                                    <br><small class="text-muted">Gratis untuk semua user</small>
                                                @elseif($tryout->jenis_paket === 'kecerdasan')
                                                    <br><small class="text-muted">Paket Kecerdasan</small>
                                                @elseif($tryout->jenis_paket === 'kepribadian')
                                                    <br><small class="text-muted">Paket Kepribadian</small>
                                                @elseif($tryout->jenis_paket === 'lengkap')
                                                    <br><small class="text-muted">Paket Lengkap</small>
                                                @endif
                                            </div> --}}
                                        </div>
                                        <div class="card-footer">
                                            @if ($tryout->jenis_paket === 'lengkap' && auth()->user()->paket_akses === 'lengkap')
                                                <!-- Multiple buttons for paket lengkap users (full width with spacing) -->
                                                @php
                                                    // Get unique tryouts for this specific card
                                                    $kecerdasanTryout =
                                                        $tryoutMappings[$tryout->id]['kecerdasan'] ?? null;
                                                    $kepribadianTryout =
                                                        $tryoutMappings[$tryout->id]['kepribadian'] ?? null;
                                                @endphp

                                                @if ($kecerdasanTryout)
                                                    <a href="{{ route('user.tryout.start', $kecerdasanTryout) }}?type=kecerdasan"
                                                        class="btn btn-primary btn-sm btn-block mb-2">
                                                        <i class="fa fa-brain"></i> Tryout Kecerdasan
                                                        <small class="d-block">{{ $kecerdasanTryout->judul }}</small>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary btn-sm btn-block mb-2" disabled>
                                                        <i class="fa fa-brain"></i> Kecerdasan (Tidak Tersedia)
                                                    </button>
                                                @endif
                                                @if ($kepribadianTryout)
                                                    <a href="{{ route('user.tryout.start', $kepribadianTryout) }}?type=kepribadian"
                                                        class="btn btn-primary btn-sm btn-block mb-2">
                                                        <i class="fa fa-user"></i> Tryout Kepribadian
                                                        <small class="d-block">{{ $kepribadianTryout->judul }}</small>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary btn-sm btn-block mb-2" disabled>
                                                        <i class="fa fa-user"></i> Kepribadian (Tidak Tersedia)
                                                    </button>
                                                @endif
                                                <a href="{{ route('kecermatan') }}"
                                                    class="btn btn-warning btn-sm btn-block">
                                                    <i class="fa fa-eye"></i> Tryout Kecermatan
                                                </a>
                                            @else
                                                <!-- Single button for other packages -->
                                                <a href="{{ route('user.tryout.start', $tryout) }}?type={{ $tryout->jenis_paket }}"
                                                    class="btn btn-primary btn-block">
                                                    <i class="fa fa-play"></i> Mulai Tryout
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                        <h5>Tidak ada tryout tersedia</h5>
                                        <p class="text-muted">
                                            @php $t = request('type'); @endphp
                                            @if ($t === 'kecerdasan')
                                                Tidak ada tryout kecerdasan yang tersedia untuk paket Anda saat ini.
                                            @elseif($t === 'kepribadian')
                                                Tidak ada tryout kepribadian yang tersedia untuk paket Anda saat ini.
                                            @elseif($t === 'lengkap')
                                                Tidak ada tryout paket lengkap yang tersedia untuk paket Anda saat ini.
                                            @else
                                                Saat ini belum ada tryout yang tersedia untuk paket Anda.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
