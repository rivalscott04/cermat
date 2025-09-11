@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Daftar Tryout Tersedia</h4>
                    <p class="text-muted mb-0">
                        Paket Anda: 
                        <span class="badge badge-{{ auth()->user()->paket_akses === 'lengkap' ? 'danger' : (auth()->user()->paket_akses === 'kecerdasan' || auth()->user()->paket_akses === 'kepribadian' ? 'primary' : 'success') }}">
                            {{ strtoupper(auth()->user()->paket_akses) }}
                        </span>
                        @if(auth()->user()->paket_akses === 'free')
                            <small class="text-muted">(1 tryout tersedia)</small>
                        @else
                            <small class="text-muted">({{ auth()->user()->getMaxTryouts() }} tryout tersedia)</small>
                        @endif
                    </p>
                </div>
                <div class="card-body">
                    @if(session('error'))
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
                                    
                                    <div class="mb-3">
                                        <strong>Struktur Soal:</strong>
                                        <div class="mt-2">
                                            @if($tryout->blueprints && $tryout->blueprints->count() > 0)
                                                @php
                                                    $groupedBlueprints = $tryout->blueprints->groupBy('kategori_id');
                                                @endphp
                                                @foreach($groupedBlueprints as $kategoriId => $blueprints)
                                                    @php
                                                        $kategori = \App\Models\KategoriSoal::find($kategoriId);
                                                        $totalKategori = $blueprints->sum('jumlah');
                                                    @endphp
                                                    @if($kategori && $totalKategori > 0)
                                                        <span class="badge badge-info mr-1">
                                                            {{ $kategori->kode }}: {{ $totalKategori }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach($tryout->struktur as $kategoriId => $jumlah)
                                                    @php
                                                        $kategori = \App\Models\KategoriSoal::find($kategoriId);
                                                    @endphp
                                                    @if($kategori && $jumlah > 0)
                                                        <span class="badge badge-info mr-1">
                                                            {{ $kategori->kode }}: {{ $jumlah }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small class="text-muted">Total Soal</small>
                                                <div class="font-weight-bold">{{ $tryout->total_soal }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small class="text-muted">Durasi</small>
                                                <div class="font-weight-bold">{{ $tryout->durasi_menit }} menit</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <span class="badge badge-{{ $tryout->jenis_paket === 'lengkap' ? 'danger' : ($tryout->jenis_paket === 'kecerdasan' || $tryout->jenis_paket === 'kepribadian' ? 'primary' : 'success') }} mb-2">
                                            {{ strtoupper($tryout->jenis_paket) }}
                                        </span>
                                        @if($tryout->jenis_paket === 'free')
                                            <br><small class="text-muted">Gratis untuk semua user</small>
                                        @elseif($tryout->jenis_paket === 'kecerdasan')
                                            <br><small class="text-muted">Paket Kecerdasan</small>
                                        @elseif($tryout->jenis_paket === 'kepribadian')
                                            <br><small class="text-muted">Paket Kepribadian</small>
                                        @elseif($tryout->jenis_paket === 'lengkap')
                                            <br><small class="text-muted">Paket Lengkap</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('user.tryout.start', $tryout) }}" 
                                       class="btn btn-primary btn-block">
                                        <i class="fa fa-play"></i> Mulai Tryout
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada tryout tersedia</h5>
                                <p class="text-muted">
                                    Saat ini belum ada tryout yang tersedia untuk paket Anda.
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