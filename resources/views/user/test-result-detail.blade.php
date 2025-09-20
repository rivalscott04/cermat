@extends('layouts.app')

@section('content')
<div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><i class="fa fa-chart-line"></i> Detail Hasil Tes</h5>
                        <div class="ibox-tools">
                            <a href="{{ route('user.profile', ['userId' => auth()->id()]) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-arrow-left"></i> Kembali ke Profil
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Jenis Tes</th>
                                            <td>
                                                @if($hasilTes->jenis_tes === 'kecermatan')
                                                    <span class="badge badge-primary">
                                                        <i class="fa fa-eye"></i> {{ ucfirst($hasilTes->jenis_tes) }}
                                                    </span>
                                                @elseif($hasilTes->jenis_tes === 'kecerdasan')
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-brain"></i> {{ ucfirst($hasilTes->jenis_tes) }}
                                                    </span>
                                                @elseif($hasilTes->jenis_tes === 'kepribadian')
                                                    <span class="badge badge-warning">
                                                        <i class="fa fa-user"></i> {{ ucfirst($hasilTes->jenis_tes) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-info">
                                                        {{ ucfirst($hasilTes->jenis_tes) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Tes</th>
                                            <td>{{ date('d M Y H:i', strtotime($hasilTes->tanggal_tes)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Skor Akhir</th>
                                            <td>
                                                @if ($hasilTes->skor_akhir)
                                                    <span class="font-weight-bold text-primary" style="font-size: 1.2em;">
                                                        {{ $hasilTes->skor_akhir }}
                                                        @if($hasilTes->jenis_tes === 'kecerdasan')
                                                            %
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kategori Skor</th>
                                            <td>
                                                @if ($hasilTes->kategori_skor)
                                                    <span class="badge badge-success">{{ $hasilTes->kategori_skor }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Belum Dinilai</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Pengerjaan</th>
                                            <td>
                                                @if ($hasilTes->waktu_total)
                                                    {{ gmdate('H:i:s', $hasilTes->waktu_total) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($hasilTes->jenis_tes === 'kecermatan')
                                        <tr>
                                            <th>Skor Benar</th>
                                            <td><span class="text-success font-weight-bold">{{ $hasilTes->skor_benar }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Skor Salah</th>
                                            <td><span class="text-danger font-weight-bold">{{ $hasilTes->skor_salah }}</span></td>
                                        </tr>
                                        @endif
                                        @if($hasilTes->jenis_tes === 'kecerdasan')
                                        <tr>
                                            <th>Jawaban Benar</th>
                                            <td><span class="text-success font-weight-bold">{{ $hasilTes->skor_benar }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jawaban Salah</th>
                                            <td><span class="text-danger font-weight-bold">{{ $hasilTes->skor_salah }}</span></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5>Statistik Detail</h5>
                                    </div>
                                    <div class="ibox-content">
                                        @if($hasilTes->jenis_tes === 'kecermatan')
                                            @if($hasilTes->panker)
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h4 class="text-primary">{{ $hasilTes->panker }}</h4>
                                                        <small class="text-muted">PANKER</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h4 class="text-info">{{ $hasilTes->tianker }}</h4>
                                                        <small class="text-muted">TIANKER</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h4 class="text-warning">{{ $hasilTes->janker }}</h4>
                                                        <small class="text-muted">JANKER</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h4 class="text-success">{{ $hasilTes->hanker }}</h4>
                                                        <small class="text-muted">HANKER</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endif

                                        @if($hasilTes->jenis_tes === 'kecerdasan' && $hasilTes->detail_jawaban)
                                            @php
                                                $detail = json_decode($hasilTes->detail_jawaban, true);
                                            @endphp
                                            @if(isset($detail['category_scores']))
                                            <h6>Skor per Kategori:</h6>
                                            @foreach($detail['category_scores'] as $category)
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $category['nama'] }}</span>
                                                <span class="font-weight-bold">{{ $category['correct'] }}/{{ $category['total'] }}</span>
                                            </div>
                                            @endforeach
                                            @endif
                                        @endif

                                        @if($hasilTes->jenis_tes === 'kepribadian' && $hasilTes->detail_jawaban)
                                            @php
                                                $detail = json_decode($hasilTes->detail_jawaban, true);
                                            @endphp
                                            @if(isset($detail['N']))
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ $detail['N'] }}</h4>
                                                <small class="text-muted">Total Soal TKP</small>
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($hasilTes->detail_jawaban)
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-list-alt"></i> Detail Jawaban</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        @php
                                            $detailJawaban = json_decode($hasilTes->detail_jawaban, true);
                                        @endphp
                                        
                                        @if($hasilTes->jenis_tes === 'kecermatan')
                                            @if(is_array($detailJawaban) && count($detailJawaban) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th width="8%">Set</th>
                                                                <th width="15%">Soal Asli</th>
                                                                <th width="15%">Soal Acak</th>
                                                                <th width="12%">Huruf Hilang</th>
                                                                <th width="12%">Posisi</th>
                                                                <th width="10%">Jawaban</th>
                                                                <th width="10%">Status</th>
                                                                <th width="8%">Waktu</th>
                                                                <th width="10%">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($detailJawaban as $index => $jawaban)
                                                                @if(isset($jawaban['set']) && isset($jawaban['benar']))
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-info">{{ $jawaban['set'] ?? '-' }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <code class="text-primary">{{ $jawaban['soal_asli'] ?? '-' }}</code>
                                                                    </td>
                                                                    <td>
                                                                        <code class="text-secondary">{{ $jawaban['soal_acak'] ?? '-' }}</code>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-warning">{{ $jawaban['huruf_hilang'] ?? '-' }}</span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-secondary">{{ $jawaban['posisi_huruf_hilang'] ?? '-' }}</span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-primary">{{ $jawaban['jawaban'] ?? '-' }}</span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($jawaban['benar'] === true)
                                                                            <span class="badge badge-success">
                                                                                <i class="fa fa-check"></i> Benar
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-danger">
                                                                                <i class="fa fa-times"></i> Salah
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <small class="text-muted">{{ $jawaban['waktu'] ?? 0 }}s</small>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-xs btn-outline-info" 
                                                                                onclick="showDetail({{ $index }})" 
                                                                                title="Lihat Detail">
                                                                            <i class="fa fa-eye"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Modal untuk detail jawaban -->
                                                <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detail Jawaban</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" id="detailModalBody">
                                                                <!-- Content akan diisi via JavaScript -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-exclamation-triangle"></i> 
                                                    Detail jawaban tidak tersedia atau format tidak valid.
                                                </div>
                                            @endif
                                        @else
                                            <!-- Untuk jenis tes lain, tampilkan JSON yang diformat -->
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> 
                                                Detail jawaban untuk jenis tes {{ $hasilTes->jenis_tes }}:
                                            </div>
                                            <pre class="bg-light p-3" style="max-height: 400px; overflow-y: auto;">{{ json_encode($detailJawaban, JSON_PRETTY_PRINT) }}</pre>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data jawaban untuk modal detail
    const detailJawaban = @json(json_decode($hasilTes->detail_jawaban ?? '[]', true));
    
    function showDetail(index) {
        const jawaban = detailJawaban[index];
        if (!jawaban) return;
        
        let modalContent = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fa fa-info-circle text-primary"></i> Informasi Soal</h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="40%">Set/Kolom:</th>
                            <td><span class="badge badge-info">${jawaban.set || '-'}</span></td>
                        </tr>
                        <tr>
                            <th>Soal Asli:</th>
                            <td><code class="text-primary">${jawaban.soal_asli || '-'}</code></td>
                        </tr>
                        <tr>
                            <th>Soal Acak:</th>
                            <td><code class="text-secondary">${jawaban.soal_acak || '-'}</code></td>
                        </tr>
                        <tr>
                            <th>Huruf Hilang:</th>
                            <td><span class="badge badge-warning">${jawaban.huruf_hilang || '-'}</span></td>
                        </tr>
                        <tr>
                            <th>Posisi:</th>
                            <td><span class="badge badge-secondary">${jawaban.posisi_huruf_hilang || '-'}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="fa fa-user text-success"></i> Jawaban User</h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="40%">Jawaban:</th>
                            <td><span class="badge badge-primary">${jawaban.jawaban || '-'}</span></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                ${jawaban.benar === true ? 
                                    '<span class="badge badge-success"><i class="fa fa-check"></i> Benar</span>' : 
                                    '<span class="badge badge-danger"><i class="fa fa-times"></i> Salah</span>'
                                }
                            </td>
                        </tr>
                        <tr>
                            <th>Waktu:</th>
                            <td><span class="text-muted">${jawaban.waktu || 0} detik</span></td>
                        </tr>
                        <tr>
                            <th>Analisis:</th>
                            <td>
                                ${jawaban.benar === true ? 
                                    '<span class="text-success"><i class="fa fa-thumbs-up"></i> Jawaban tepat!</span>' : 
                                    '<span class="text-danger"><i class="fa fa-thumbs-down"></i> Perlu lebih teliti</span>'
                                }
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
        
        document.getElementById('detailModalBody').innerHTML = modalContent;
        $('#detailModal').modal('show');
    }
    
    // Auto-collapse detail jawaban section on page load
    $(document).ready(function() {
        // Collapse detail jawaban section by default
        $('.ibox-content').find('.table-responsive').parent().hide();
        
        // Toggle collapse functionality
        $('.collapse-link').click(function() {
            const ibox = $(this).closest('.ibox');
            const content = ibox.find('.ibox-content');
            const icon = $(this).find('i');
            
            content.slideToggle(200);
            icon.toggleClass('fa-chevron-up fa-chevron-down');
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Badge styles for test types - simple and consistent */
    .badge {
        font-size: 0.75em;
        padding: 0.4em 0.6em;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    
    .badge i {
        margin-right: 0.3em;
    }
    
    /* Consistent colors for each test type */
    .badge-primary {
        background-color: #007bff;
        color: white;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    /* Table styling improvements */
    .table th {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 0.9em;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table code {
        background-color: #f8f9fa;
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
        font-size: 0.9em;
    }
    
    /* Modal styling */
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-title {
        color: #495057;
        font-weight: 600;
    }
    
    /* Button styling */
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }
    
    /* Collapse icon animation */
    .collapse-link i {
        transition: transform 0.3s ease;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85em;
        }
        
        .badge {
            font-size: 0.7em;
            padding: 0.3em 0.5em;
        }
    }
</style>
@endpush
