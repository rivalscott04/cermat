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
                                        <label for="akses_paket">Akses Paket <span class="text-danger">*</span></label>
                                        <select class="form-control @error('akses_paket') is-invalid @enderror"
                                            id="akses_paket" name="akses_paket" required>
                                            <option value="">Pilih Paket</option>
                                            <option value="free"
                                                {{ old('akses_paket', $tryout->akses_paket) == 'free' ? 'selected' : '' }}>
                                                Free</option>
                                            <option value="premium"
                                                {{ old('akses_paket', $tryout->akses_paket) == 'premium' ? 'selected' : '' }}>
                                                Premium</option>
                                            <option value="vip"
                                                {{ old('akses_paket', $tryout->akses_paket) == 'vip' ? 'selected' : '' }}>
                                                VIP</option>
                                        </select>
                                        @error('akses_paket')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Struktur Soal <span class="text-danger">*</span></label>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    Tentukan jumlah soal untuk setiap kategori. Total soal akan dihitung otomatis.
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning"></i>
                                    <strong>Perhatian:</strong> Mengubah struktur soal akan menghapus semua jawaban user
                                    yang sudah ada untuk tryout ini.
                                </div>

                                @foreach ($kategoris as $kategori)
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="struktur_{{ $kategori->id }}">
                                                {{ $kategori->nama }} ({{ $kategori->kode }})
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control struktur-input"
                                                id="struktur_{{ $kategori->id }}" name="struktur[{{ $kategori->id }}]"
                                                value="{{ old("struktur.{$kategori->id}", $tryout->struktur[$kategori->id] ?? 0) }}"
                                                min="0" max="100">
                                            <small class="form-text text-muted">
                                                Tersedia: {{ $kategori->soals()->count() }} soal
                                                @if (isset($tryout->struktur[$kategori->id]) && $tryout->struktur[$kategori->id] > 0)
                                                    | Saat ini: {{ $tryout->struktur[$kategori->id] }} soal
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endforeach

                                @error('struktur')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

                                <div class="mt-3">
                                    <div class="alert alert-secondary">
                                        <strong>Total Soal: <span
                                                id="total-soal">{{ array_sum($tryout->struktur) }}</span></strong>
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
            let originalStruktur = @json($tryout->struktur);
            let strukturChanged = false;

            // Calculate total questions
            function calculateTotal() {
                let total = 0;
                $('.struktur-input').each(function() {
                    total += parseInt($(this).val()) || 0;
                });
                $('#total-soal').text(total);
                return total;
            }

            // Check if structure changed
            function checkStrukturChange() {
                let currentStruktur = {};
                $('.struktur-input').each(function() {
                    let name = $(this).attr('name');
                    let kategoriId = name.match(/struktur\[(\d+)\]/)[1];
                    currentStruktur[kategoriId] = parseInt($(this).val()) || 0;
                });

                strukturChanged = JSON.stringify(originalStruktur) !== JSON.stringify(currentStruktur);
            }

            // Update total when structure changes
            $('.struktur-input').on('input', function() {
                calculateTotal();
                checkStrukturChange();
            });

            // Form submission with confirmation if structure changed
            $('form').on('submit', function(e) {
                checkStrukturChange();

                if (strukturChanged) {
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
