@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Riwayat Tes Kecermatan</h5>
        </div>
        <div class="ibox-content">
          <div class="table-responsive">
            <table class="table-bordered table-hover table text-center">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal Tes</th>
                  <th>Skor Benar</th>
                  <th>Skor Salah</th>
                  <th>Detail</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($hasil as $index => $tes)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ date('l, d F Y', strtotime($tes->tanggal_tes)) }}</td>
                    <td>{{ $tes->skor_benar }}</td>
                    <td>{{ $tes->skor_salah }}</td>
                    <td>
                      <a href="{{ route('kecermatan.detail', $tes->id) }}" class="btn btn-primary btn-sm">
                        Lihat Detail
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">Tidak ada data tes.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection