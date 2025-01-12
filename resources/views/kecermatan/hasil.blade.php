@extends('layouts.app')

@push('styles')
  <style>
    .form-control {
      border-radius: 0.375rem !important;
      height: 38px !important;
    }

    .question-display {
      letter-spacing: 0.5em;
      padding: 0.5rem;
      border-radius: 4px;
    }

    .missing-letter {
      color: #dc3545;
      font-weight: bold;
    }

    .correct-answer {
      color: #28a745;
      font-weight: bold;
    }
  </style>
@endpush

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Hasil Tes</h5>
        </div>
        <div class="ibox-content">
          <div class="row mb-3">
            <div class="col-md-6">
              <h4>Soal Terjawab: {{ $hasilTes->skor_benar + $hasilTes->skor_salah }}</h4>
              <h4>Skor Benar: {{ $hasilTes->skor_benar }}</h4>
              <h4>Skor Salah: {{ $hasilTes->skor_salah }}</h4>
              <h4>Waktu Total: {{ $hasilTes->waktu_total }} detik</h4>
              <h4>Rata-rata Waktu Per Soal: {{ number_format($hasilTes->average_time, 2) }} detik</h4>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-end">
              <div class="form-group w-50 mb-2">
                <select class="form-control" id="setFilter">
                  <option value="all">Semua Set</option>
                  @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">Set {{ $i }}</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>
          <hr>
          <h4>Detail Jawaban</h4>
          @php
            $detailJawaban = json_decode($hasilTes->detail_jawaban, true);
            $soalAsli = array_unique(array_column($detailJawaban, 'soal_asli'));
          @endphp

          <h3>Soal: {{ implode(', ', $soalAsli) }}</h3>
          <table class="table-bordered table-hover table">
            <thead>
              <tr>
                <th>Nomor</th>
                <th>Set</th>
                <th>Soal Acak</th>
                <th>Huruf Hilang</th>
                <th>Posisi Benar</th>
                <th>Jawaban</th>
                <th>Hasil</th>
                <th>Waktu (detik)</th>
              </tr>
            </thead>
            <tbody>
              @foreach (json_decode($hasilTes->detail_jawaban) as $index => $jawaban)
                <tr class="jawaban-row" data-set="{{ $jawaban->set }}">
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $jawaban->set }}</td>
                  <td class="question-display">{{ $jawaban->soal_acak }}</td>
                  <td class="missing-letter">{{ $jawaban->huruf_hilang }}</td>
                  <td class="correct-answer">{{ $jawaban->posisi_huruf_hilang }}</td>
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

  @push('scripts')
    <script>
      document.getElementById('setFilter').addEventListener('change', function() {
        const selectedSet = this.value;
        const rows = document.querySelectorAll('.jawaban-row');

        rows.forEach(row => {
          const rowSet = row.getAttribute('data-set');
          if (selectedSet === 'all' || selectedSet === rowSet) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });

        // Update nomor urut yang tampil
        let visibleIndex = 1;
        rows.forEach(row => {
          if (row.style.display !== 'none') {
            row.cells[0].textContent = visibleIndex++;
          }
        });
      });

      // Tampilkan Set 1 saat halaman pertama kali dimuat
      window.addEventListener('load', function() {
        document.getElementById('setFilter').value = '1';
        document.getElementById('setFilter').dispatchEvent(new Event('change'));
      });
    </script>
  @endpush
@endsection
