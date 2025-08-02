@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Manajemen Tryout</h4>
                    <a href="{{ route('admin.tryout.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Buat Tryout
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Struktur Soal</th>
                                    <th>Durasi</th>
                                    <th>Akses Paket</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tryouts as $tryout)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $tryout->judul }}</strong>
                                    </td>
                                    <td>{{ Str::limit($tryout->deskripsi, 50) }}</td>
                                    <td>
                                        @foreach($tryout->struktur as $kategoriId => $jumlah)
                                            @php
                                                $kategori = \App\Models\KategoriSoal::find($kategoriId);
                                            @endphp
                                            @if($kategori && $jumlah > 0)
                                                <span class="badge badge-info">
                                                    {{ $kategori->kode }}: {{ $jumlah }}
                                                </span>
                                            @endif
                                        @endforeach
                                        <br>
                                        <small class="text-muted">
                                            Total: {{ $tryout->total_soal }} soal
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">
                                            {{ $tryout->durasi_menit }} menit
                                        </span>
                                    </td>
                                    <td>
                                        @switch($tryout->akses_paket)
                                            @case('free')
                                                <span class="badge badge-success">Free</span>
                                                @break
                                            @case('premium')
                                                <span class="badge badge-primary">Premium</span>
                                                @break
                                            @case('vip')
                                                <span class="badge badge-danger">VIP</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($tryout->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.tryout.show', $tryout) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tryout.edit', $tryout) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.tryout.destroy', $tryout) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus tryout ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data tryout</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $tryouts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 