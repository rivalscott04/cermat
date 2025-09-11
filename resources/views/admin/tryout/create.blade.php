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
                                    <label for="jenis_paket">Jenis Paket <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_paket') is-invalid @enderror" 
                                            id="jenis_paket" name="jenis_paket" required>
                                        <option value="">Pilih Jenis Paket</option>
                                        <option value="free" {{ old('jenis_paket') == 'free' ? 'selected' : '' }}>Free - 1 tryout untuk semua user</option>
                                        <option value="kecerdasan" {{ old('jenis_paket') == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan - TIU, TWK, TKD</option>
                                        <option value="kepribadian" {{ old('jenis_paket') == 'kepribadian' ? 'selected' : '' }}>Kepribadian - TKP, PSIKOTES</option>
                                        <option value="lengkap" {{ old('jenis_paket') == 'lengkap' ? 'selected' : '' }}>Lengkap - Semua kategori</option>
                                    </select>
                                    @error('jenis_paket')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <strong>Free:</strong> User free bisa akses 1 tryout<br>
                                        <strong>Kecerdasan:</strong> User paket kecerdasan bisa akses<br>
                                        <strong>Kepribadian:</strong> User paket kepribadian bisa akses<br>
                                        <strong>Lengkap:</strong> User paket lengkap bisa akses
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Preview Kategori yang Akan Muncul:</label>
                                    <div id="kategori-preview" class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Pilih jenis paket untuk melihat kategori yang akan muncul
                                    </div>
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
                                    <tbody id="kategori-table-body">
                                        @foreach($kategoris as $kategori)
                                            <tr class="kategori-row" data-kode="{{ $kategori->kode }}">
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
    // Package mapping
    const packageMapping = {
        'free': ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD'],
        'kecerdasan': ['TIU', 'TWK', 'TKD'],
        'kepribadian': ['TKP', 'PSIKOTES'],
        'lengkap': ['TIU', 'TWK', 'TKP', 'PSIKOTES', 'TKD']
    };

    // Update kategori preview and filter table when jenis paket changes
    $('#jenis_paket').on('change', function() {
        const selectedPackage = $(this).val();
        const allowedCategories = packageMapping[selectedPackage] || [];
        
        // Update preview
        if (allowedCategories.length > 0) {
            $('#kategori-preview').html(`
                <i class="fa fa-check text-success"></i> 
                <strong>Kategori yang akan muncul:</strong><br>
                ${allowedCategories.map(cat => `<span class="badge badge-primary mr-1">${cat}</span>`).join('')}
            `).removeClass('alert-info').addClass('alert-success');
        } else {
            $('#kategori-preview').html(`
                <i class="fa fa-info-circle"></i> Pilih jenis paket untuk melihat kategori yang akan muncul
            `).removeClass('alert-success').addClass('alert-info');
        }

        // Filter kategori table
        $('.kategori-row').each(function() {
            const kode = $(this).data('kode');
            if (allowedCategories.includes(kode)) {
                $(this).show();
                $(this).find('input').prop('disabled', false);
            } else {
                $(this).hide();
                $(this).find('input').prop('disabled', true).val(0);
            }
        });
    });

    // Calculate total questions
    function calculateTotal() {
        let total = 0;
        $('input[name^="blueprint["]').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        return total;
    }

    // Validate input against available questions
    function validateInput(input) {
        const value = parseInt(input.val()) || 0;
        const row = input.closest('tr');
        const tersediaText = row.find('td:last-child small').text();
        
        // Extract available count from text like "Mudah: 10 | Sedang: 10 | Sulit: 10"
        const level = input.attr('name').includes('mudah') ? 'Mudah' : 
                     input.attr('name').includes('sedang') ? 'Sedang' : 'Sulit';
        const match = tersediaText.match(new RegExp(level + ': (\\d+)'));
        const available = match ? parseInt(match[1]) : 0;
        
        // Remove previous validation classes and messages
        input.removeClass('is-valid is-invalid');
        row.find('.validation-message').remove();
        
        if (value > available) {
            input.addClass('is-invalid');
            row.append(`
                <tr class="validation-message">
                    <td colspan="5" class="text-danger small">
                        <i class="fa fa-exclamation-triangle"></i> 
                        Jumlah soal ${level.toLowerCase()} yang diminta (${value}) melebihi soal yang tersedia (${available})
                    </td>
                </tr>
            `);
            return false;
        } else if (value > 0) {
            input.addClass('is-valid');
        }
        
        return true;
    }

    // Check if all inputs are valid
    function checkAllValidations() {
        let allValid = true;
        $('input[name^="blueprint["]').each(function() {
            if (!validateInput($(this))) {
                allValid = false;
            }
        });
        
        // Update submit button state
        const submitBtn = $('button[type="submit"]');
        if (allValid) {
            submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            submitBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
        
        return allValid;
    }
    
    // Update total and validate when structure changes
    $('input[name^="blueprint["]').on('input', function() {
        validateInput($(this));
        checkAllValidations();
        
        const total = calculateTotal();
        console.log('Total soal:', total);
    });

    // Initial validation on page load
    checkAllValidations();
});
</script>
@endpush 