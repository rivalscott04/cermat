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
                                                <span class="badge badge-info">
                                                    {{ ucfirst($hasilTes->jenis_tes) }}
                                                </span>
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
                                        <h5>Detail Jawaban</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <pre class="bg-light p-3" style="max-height: 300px; overflow-y: auto;">{{ json_encode(json_decode($hasilTes->detail_jawaban), JSON_PRETTY_PRINT) }}</pre>
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
