@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Hasil Tes</h5>
        </div>
        <div class="ibox-content">
          <div class="row">
            <div class="col-md-6">
              <h4>Skor Benar: {{ $hasilTes->skor_benar }}</h4>
              <h4>Skor Salah: {{ $hasilTes->skor_salah }}</h4>
              <h4>Waktu Total: {{ $hasilTes->waktu_total }} detik</h4>
              <h4>Rata-rata Waktu Per Soal: {{ number_format($hasilTes->average_time, 2) }} detik</h4>
            </div>
          </div>
          <hr>
          <h4>Detail Jawaban</h4>
          <table class="table-bordered table-hover table">
            <thead>
              <tr>
                <th>Nomor</th>
                <th>Set</th>
                <th>Jawaban</th>
                <th>Hasil</th>
                <th>Waktu (detik)</th>
              </tr>
            </thead>
            <tbody>
              @foreach (json_decode($hasilTes->detail_jawaban) as $index => $jawaban)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $jawaban->set }}</td>
                  <td>{{ $jawaban->jawaban }}</td>
                  <td class="{{ $jawaban->benar ? 'table-success' : 'table-danger' }}">
                    {{ $jawaban->benar ? 'Benar' : 'Salah' }}
                  </td>
                  <td>{{ $jawaban->waktu }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
