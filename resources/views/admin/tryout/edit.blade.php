@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Tryout</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tryout.update', $tryout) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="judul">Judul Tryout <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                    id="judul" name="judul" value="{{ old('judul', $tryout->judul) }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $tryout->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="durasi_menit">Durasi (Menit) <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('durasi_menit') is-invalid @enderror"
                                            id="durasi_menit" name="durasi_menit"
                                            value="{{ old('durasi_menit', $tryout->durasi_menit) }}" min="1"
                                            required>
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
                                            <option value="free" {{ old('jenis_paket', $tryout->jenis_paket) == 'free' ? 'selected' : '' }}>Free - 1 tryout untuk semua user</option>
                                            <option value="kecerdasan" {{ old('jenis_paket', $tryout->jenis_paket) == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan - TIU, TWK, TKD</option>
                                            <option value="kepribadian" {{ old('jenis_paket', $tryout->jenis_paket) == 'kepribadian' ? 'selected' : '' }}>Kepribadian - TKP, PSIKOTES</option>
                                            <option value="lengkap" {{ old('jenis_paket', $tryout->jenis_paket) == 'lengkap' ? 'selected' : '' }}>Lengkap - Semua kategori</option>
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
                                    <input type="checkbox" class="custom-control-input" id="shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions', $tryout->shuffle_questions) ? 'checked' : '' }}>
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
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning"></i>
                                    <strong>Perhatian:</strong> Mengubah blueprint soal akan menghapus semua jawaban user
                                    yang sudah ada untuk tryout ini.
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
                                            @php
                                                $bp = $tryout->blueprints->groupBy('kategori_id')->map(function($rows){
                                                    return [
                                                        'mudah' => optional($rows->firstWhere('level','mudah'))->jumlah ?? 0,
                                                        'sedang' => optional($rows->firstWhere('level','sedang'))->jumlah ?? 0,
                                                        'sulit' => optional($rows->firstWhere('level','sulit'))->jumlah ?? 0,
                                                    ];
                                                });
                                            @endphp
                                            @foreach ($kategoris as $kategori)
                                                @php
                                                    $row = $bp[$kategori->id] ?? ['mudah'=>0,'sedang'=>0,'sulit'=>0];
                                                @endphp
                                                <tr class="kategori-row" data-kode="{{ $kategori->kode }}">
                                                    <td>{{ $kategori->nama }} ({{ $kategori->kode }})</td>
                                                    <td width="140">
                                                        <input type="number" class="form-control blueprint-input" min="0" max="100"
                                                               name="blueprint[{{ $kategori->id }}][mudah]"
                                                               value="{{ old("blueprint.{$kategori->id}.mudah", $row['mudah']) }}">
                                                    </td>
                                                    <td width="140">
                                                        <input type="number" class="form-control blueprint-input" min="0" max="100"
                                                               name="blueprint[{{ $kategori->id }}][sedang]"
                                                               value="{{ old("blueprint.{$kategori->id}.sedang", $row['sedang']) }}">
                                                    </td>
                                                    <td width="140">
                                                        <input type="number" class="form-control blueprint-input" min="0" max="100"
                                                               name="blueprint[{{ $kategori->id }}][sulit]"
                                                               value="{{ old("blueprint.{$kategori->id}.sulit", $row['sulit']) }}">
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

                                <div class="mt-3">
                                    <div class="alert alert-secondary">
                                        <strong>Total Soal: <span id="total-soal">0</span></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Tryout
                                </button>
                                <a href="{{ route('admin.tryout.show', $tryout) }}" class="btn btn-info">
                                    <i class="fa fa-eye"></i> Lihat Detail
                                </a>
                                <a href="{{ route('admin.tryout.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Informasi Tryout</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <span class="badge badge-{{ $tryout->is_active ? 'success' : 'danger' }}">
                                    {{ $tryout->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Dibuat:</strong><br>
                                {{ $tryout->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Terakhir Diubah:</strong><br>
                                {{ $tryout->updated_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Peserta:</strong><br>
                                {{ $tryout->userTryoutSoal()->distinct('user_id')->count() }} orang
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Perubahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda telah mengubah struktur soal. Ini akan menghapus semua jawaban user yang sudah ada.</p>
                    <p><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" id="confirmSubmit">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let blueprintChanged = false;

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

            // Initialize preview on page load
            $('#jenis_paket').trigger('change');

            // Calculate total questions
            function calculateTotal() {
                let total = 0;
                $('.blueprint-input').each(function() {
                    total += parseInt($(this).val()) || 0;
                });
                $('#total-soal').text(total);
                return total;
            }

            // Check if structure changed
            function checkBlueprintChange() {
                blueprintChanged = true; // simple flag since we don't load original blueprint here
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
                $('.blueprint-input').each(function() {
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

            // Update total when structure changes
            $('.blueprint-input').on('input', function() {
                validateInput($(this));
                checkAllValidations();
                calculateTotal();
                checkBlueprintChange();
            });

            // Initial validation on page load
            checkAllValidations();

            // Form submission with confirmation if structure changed
            $('form').on('submit', function(e) {
                if (blueprintChanged) {
                    e.preventDefault();
                    $('#confirmModal').modal('show');
                }
            });

            // Confirm submission
            $('#confirmSubmit').on('click', function() {
                $('#confirmModal').modal('hide');
                $('form').off('submit').submit();
            });

            // Initialize total calculation
            calculateTotal();

            // Validate available questions
            $('.struktur-input').on('blur', function() {
                let input = $(this);
                let value = parseInt(input.val()) || 0;
                let availableText = input.siblings('small').text();
                let available = parseInt(availableText.match(/Tersedia: (\d+)/)[1]);

                if (value > available) {
                    input.addClass('is-invalid');
                    if (!input.siblings('.invalid-feedback').length) {
                        input.after('<div class="invalid-feedback">Jumlah soal melebihi yang tersedia (' +
                            available + ' soal)</div>');
                    }
                } else {
                    input.removeClass('is-invalid');
                    input.siblings('.invalid-feedback').remove();
                }
            });
        });
    </script>
@endpush
