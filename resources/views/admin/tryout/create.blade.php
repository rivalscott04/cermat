@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Buat Tryout Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tryout.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="judul">Judul Tryout <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                   id="judul" name="judul" value="{{ old('judul') }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="durasi_menit">Durasi (Menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('durasi_menit') is-invalid @enderror" 
                                           id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit') }}" 
                                           min="1" required>
                                    @error('durasi_menit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="akses_paket">Akses Paket <span class="text-danger">*</span></label>
                                    <select class="form-control @error('akses_paket') is-invalid @enderror" 
                                            id="akses_paket" name="akses_paket" required>
                                        <option value="">Pilih Paket</option>
                                        <option value="free" {{ old('akses_paket') == 'free' ? 'selected' : '' }}>Free</option>
                                        <option value="premium" {{ old('akses_paket') == 'premium' ? 'selected' : '' }}>Premium</option>
                                        <option value="vip" {{ old('akses_paket') == 'vip' ? 'selected' : '' }}>VIP</option>
                                    </select>
                                    @error('akses_paket')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="shuffle_questions">Acak Urutan Nomor Soal</label>
                            </div>
                            <small class="text-muted">Jika aktif, urutan nomor soal diacak per user secara deterministik. Opsi jawaban tetap diacak seperti biasa.</small>
                        </div>

                        <div class="form-group">
                            <label>Blueprint Per Kategori & Level <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                Tentukan jumlah soal untuk setiap kombinasi kategori dan level.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kategori</th>
                                            <th class="text-center">Mudah</th>
                                            <th class="text-center">Sedang</th>
                                            <th class="text-center">Sulit</th>
                                            <th>Tersedia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kategoris as $kategori)
                                            <tr>
                                                <td>
                                                    {{ $kategori->nama }} ({{ $kategori->kode }})
                                                </td>
                                                <td width="140">
                                                    <input type="number" class="form-control blueprint-input" min="0" max="100"
                                                           name="blueprint[{{ $kategori->id }}][mudah]"
                                                           value="{{ old("blueprint.{$kategori->id}.mudah", 0) }}">
                                                </td>
                                                <td width="140">
                                                    <input type="number" class="form-control blueprint-input" min="0" max="100"
                                                           name="blueprint[{{ $kategori->id }}][sedang]"
                                                           value="{{ old("blueprint.{$kategori->id}.sedang", 0) }}">
                                                </td>
                                                <td width="140">
                                                    <input type="number" class="form-control blueprint-input" min="0" max="100"
                                                           name="blueprint[{{ $kategori->id }}][sulit]"
                                                           value="{{ old("blueprint.{$kategori->id}.sulit", 0) }}">
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        Mudah: {{ $kategori->soals()->where('level','mudah')->count() }} |
                                                        Sedang: {{ $kategori->soals()->where('level','sedang')->count() }} |
                                                        Sulit: {{ $kategori->soals()->where('level','sulit')->count() }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @error('blueprint')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Buat Tryout
                            </button>
                            <a href="{{ route('admin.tryout.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate total questions
    function calculateTotal() {
        let total = 0;
        $('input[name^="struktur["]').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        return total;
    }
    
    // Update total when structure changes
    $('input[name^="struktur["]').on('input', function() {
        const total = calculateTotal();
        console.log('Total soal:', total);
    });
});
</script>
@endpush 