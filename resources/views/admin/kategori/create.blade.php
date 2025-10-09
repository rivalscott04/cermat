@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Kategori Soal</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.kategori.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="nama">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kode">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                   id="kode" name="kode" value="{{ old('kode') }}" 
                                   maxlength="10" required>
                            <small class="form-text text-muted">Contoh: TWK, TIU, TKP</small>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        
                        <div class="form-group">
                            <label for="scoring_mode">Mode Skor</label>
                            <select class="form-control @error('scoring_mode') is-invalid @enderror" id="scoring_mode" name="scoring_mode">
                                <option value="" {{ old('scoring_mode') === null ? 'selected' : '' }}>Default (Bobot 1–5)</option>
                                <option value="weighted" {{ old('scoring_mode') === 'weighted' ? 'selected' : '' }}>Bobot 1–5</option>
                                <option value="binary" {{ old('scoring_mode') === 'binary' ? 'selected' : '' }}>Benar/Salah (0/1)</option>
                            </select>
                            <small class="form-text text-muted">Kosongkan untuk mengikuti default TKP (bobot 1–5).</small>
                            @error('scoring_mode')
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

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">
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