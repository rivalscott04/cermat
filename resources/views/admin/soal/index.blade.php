@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Manajemen Soal</h4>
                    <div>
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
                            <i class="fa fa-upload"></i> Upload Word
                        </button>
                        <a href="{{ route('admin.soal.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Soal
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="filterKategori">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterTipe">
                                <option value="">Semua Tipe</option>
                                <option value="benar_salah">Benar/Salah</option>
                                <option value="pg_satu">Pilihan Ganda (1 Jawaban)</option>
                                <option value="pg_bobot">Pilihan Ganda (Bobot)</option>
                                <option value="pg_pilih_2">Pilih 2 Jawaban</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pertanyaan</th>
                                    <th>Kategori</th>
                                    <th>Tipe</th>
                                    <th>Opsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($soals as $soal)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="soal-preview">
                                            {{ Str::limit(strip_tags($soal->pertanyaan), 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $soal->kategori->kode }}</span>
                                    </td>
                                    <td>
                                        @switch($soal->tipe)
                                            @case('benar_salah')
                                                <span class="badge badge-warning">Benar/Salah</span>
                                                @break
                                            @case('pg_satu')
                                                <span class="badge badge-primary">PG (1 Jawaban)</span>
                                                @break
                                            @case('pg_bobot')
                                                <span class="badge badge-success">PG (Bobot)</span>
                                                @break
                                            @case('pg_pilih_2')
                                                <span class="badge badge-secondary">Pilih 2</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-toggle="modal" data-target="#opsiModal{{ $soal->id }}">
                                            Lihat Opsi ({{ $soal->opsi->count() }})
                                        </button>
                                    </td>
                                    <td>
                                        @if($soal->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.soal.edit', $soal) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.soal.destroy', $soal) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Opsi -->
                                <div class="modal fade" id="opsiModal{{ $soal->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Opsi Soal</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>Pertanyaan:</h6>
                                                <p>{{ $soal->pertanyaan }}</p>
                                                
                                                <h6>Opsi Jawaban:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Opsi</th>
                                                                <th>Teks</th>
                                                                <th>Bobot</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($soal->opsi as $opsi)
                                                            <tr>
                                                                <td><strong>{{ $opsi->opsi }}</strong></td>
                                                                <td>{{ $opsi->teks }}</td>
                                                                <td>
                                                                    <span class="badge badge-{{ $opsi->bobot > 0 ? 'success' : 'secondary' }}">
                                                                        {{ $opsi->bobot }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data soal</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $soals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Soal dari Word</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.soal.upload-word') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kategori_id">Kategori Soal <span class="text-danger">*</span></label>
                        <select class="form-control" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="file">File Word (.docx) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" accept=".docx" required>
                        <small class="form-text text-muted">
                            Format file harus sesuai dengan template yang telah ditentukan
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    $('#filterKategori, #filterTipe').change(function() {
        // Implement AJAX filtering here
    });
});
</script>
@endpush 