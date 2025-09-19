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
                                                    <span class="badge badge-info">
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
                                                    <span class="badge badge-info">
                                                        {{ $kategori->kode }}: {{ $jumlah }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @endif
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
                                               class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tryout.edit', $tryout) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @if ($tryout->is_active)
                                                <button type="button" class="btn btn-sm btn-secondary" 
                                                        onclick="toggleStatus({{ $tryout->id }}, false)" title="Nonaktifkan">
                                                    <i class="fa fa-pause"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="toggleStatus({{ $tryout->id }}, true)" title="Aktifkan">
                                                    <i class="fa fa-play"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="showDeleteModal({{ $tryout->id }}, '{{ $tryout->judul }}')" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
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

<!-- Modal Hapus Tryout -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fa fa-exclamation-triangle"></i> Konfirmasi Hapus Tryout
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-trash fa-3x text-danger"></i>
                </div>
                <p class="text-center">
                    Apakah Anda yakin ingin menghapus tryout <strong id="tryoutTitle"></strong>?
                </p>
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data terkait tryout ini akan dihapus permanen.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Ya, Hapus Tryout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Toggle Status -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" role="dialog" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">
                    <i class="fa fa-toggle-on"></i> Konfirmasi Ubah Status
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-question-circle fa-3x text-primary"></i>
                </div>
                <p class="text-center" id="toggleStatusMessage">
                    <!-- Message will be inserted here -->
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmToggleStatus">
                    <i class="fa fa-check"></i> Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showDeleteModal(tryoutId, tryoutTitle) {
    $('#tryoutTitle').text(tryoutTitle);
    $('#deleteForm').attr('action', `/admin/tryout/${tryoutId}`);
    $('#deleteModal').modal('show');
}

function toggleStatus(tryoutId, newStatus) {
    const message = newStatus ?
        'Apakah Anda yakin ingin mengaktifkan tryout ini?' :
        'Apakah Anda yakin ingin menonaktifkan tryout ini?';

    $('#toggleStatusMessage').text(message);

    // Hapus event listener sebelumnya untuk menghindari duplikasi
    $('#confirmToggleStatus').off('click');

    $('#toggleStatusModal').modal('show');

    $('#confirmToggleStatus').on('click', function() {
        const $button = $(this);
        $button.prop('disabled', true).text('Memproses...');

        $.ajax({
            url: `/admin/tryout/${tryoutId}/toggle-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                is_active: newStatus
            },
            success: function(response) {
                $('#toggleStatusModal').modal('hide');
                // Tambahkan delay kecil sebelum reload
                setTimeout(function() {
                    location.reload();
                }, 300);
            },
            error: function(xhr) {
                $button.prop('disabled', false).text('Konfirmasi');
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });
}
</script>
@endpush 