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

    .score-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      margin: 0 2px;
    }

    .score-badge.benar {
      background-color: #28a745;
      color: white;
    }

    .score-badge.salah {
      background-color: #dc3545;
      color: white;
    }

    .detail-section {
      display: none;
      background-color: #fff;
      padding: 20px;
      margin-top: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .detail-section.active {
      display: block;
    }

    .clickable-row {
      cursor: pointer;
    }

    .clickable-row:hover {
      background-color: #f5f5f5;
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
          <!-- Bagian statistik -->
          <div class="row mb-3">
            <div class="col-md-6">
              <h4>Soal Terjawab: {{ $hasilTes->skor_benar + $hasilTes->skor_salah }}</h4>
              <h4>Skor Benar: {{ $hasilTes->skor_benar }}</h4>
              <h4>Skor Salah: {{ $hasilTes->skor_salah }}</h4>
              <h4>Waktu Total: {{ $hasilTes->waktu_total }} detik</h4>
              <h4>Rata-rata Waktu Per Soal: {{ number_format($hasilTes->average_time, 2) }} detik</h4>
            </div>
          </div>
          <hr>

          <!-- Tabel ringkasan -->
          <table class="table-bordered table-hover table text-center">
            <thead>
              <tr>
                <th>Soal</th>
                <th>Terjawab</th>
                <th>Skor</th>
                <th>Persentase</th>
              </tr>
            </thead>
            <tbody>
              @php
                $detailJawaban = json_decode($hasilTes->detail_jawaban, true);
                $soalAsli = array_unique(array_column($detailJawaban, 'soal_asli'));
              @endphp

              @foreach ($soalAsli as $index => $soal)
                @php
                  $setJawaban = array_filter($detailJawaban, function ($jawaban) use ($soal) {
                      return $jawaban['soal_asli'] === $soal;
                  });
                  $skorBenar = count(
                      array_filter($setJawaban, function ($jawaban) {
                          return $jawaban['benar'];
                      }),
                  );
                  $skorSalah = count($setJawaban) - $skorBenar;
                  $persentase = ($skorBenar / count($setJawaban)) * 100;
                @endphp

                <tr class="clickable-row" onclick="toggleDetail({{ $index }})">
                  <td><strong>{{ $soal }}</strong></td>
                  <td>{{ count($setJawaban) }}</td>
                  <td>
                    <span class="score-badge benar">{{ $skorBenar }} benar</span>
                    <span class="score-badge salah">{{ $skorSalah }} salah</span>
                  </td>
                  <td>{{ number_format($persentase, 2) }}/100</td>
                </tr>
                <tr>
                  <td colspan="4" class="p-0">
                    <div id="detail-{{ $index }}" class="detail-section">
                      <table class="table-bordered mb-0 table">
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
                          @foreach ($setJawaban as $idx => $jawaban)
                            <tr>
                              <td>{{ $idx + 1 }}</td>
                              <td>{{ $jawaban['set'] }}</td>
                              <td class="question-display">{{ $jawaban['soal_acak'] }}</td>
                              <td class="missing-letter">{{ $jawaban['huruf_hilang'] }}</td>
                              <td class="correct-answer">{{ $jawaban['posisi_huruf_hilang'] }}</td>
                              <td>{{ $jawaban['jawaban'] }}</td>
                              <td class="{{ $jawaban['benar'] ? 'table-success' : 'table-danger' }}">
                                {{ $jawaban['benar'] ? 'Benar' : 'Salah' }}
                              </td>
                              <td>{{ $jawaban['waktu'] }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <!-- Tambahkan chart -->
          <div class="row mb-3">
            <div class="col-lg-12">
              <div class="ibox">
                <div class="ibox-title">
                  <h5>Perbandingan Jawaban Per Set</h5>
                </div>
                <div class="ibox-content">
                  <div>
                    <canvas id="answerComparisonChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
      function toggleDetail(index) {
        // Get the detail section element
        const detailSection = document.getElementById(`detail-${index}`);

        // Toggle the active class
        if (detailSection) {
          // Close all other detail sections first
          const allDetails = document.querySelectorAll('.detail-section');
          allDetails.forEach(detail => {
            if (detail !== detailSection) {
              detail.classList.remove('active');
            }
          });

          // Toggle the clicked section
          detailSection.classList.toggle('active');
        }
      }
      document.addEventListener('DOMContentLoaded', function() {
        // Mengambil data dari server
        const detailJawaban = @json($detailJawaban);

        // Group answers by set
        const setData = {};
        detailJawaban.forEach(jawaban => {
          if (!setData[jawaban.set]) {
            setData[jawaban.set] = {
              total: 0,
              benar: 0
            };
          }
          setData[jawaban.set].total++;
          if (jawaban.benar) {
            setData[jawaban.set].benar++;
          }
        });

        // Urutkan berdasarkan kunci `set`
        const sortedSets = Object.keys(setData).sort((a, b) => a - b);

        // Siapkan data untuk chart
        const labels = sortedSets.map(set => `Kolom ${set}`);
        const totalData = labels.map((_, index) => setData[sortedSets[index]].total);
        const benarData = labels.map((_, index) => setData[sortedSets[index]].benar);

        // Membuat chart
        const chartData = {
          labels: labels,
          datasets: [{
              label: 'Total Terjawab',
              backgroundColor: 'rgba(79, 70, 229, 0.6)',
              borderColor: 'rgb(79, 70, 229)',
              borderWidth: 1,
              data: totalData
            },
            {
              label: 'Jawaban Benar',
              backgroundColor: 'rgba(249, 115, 22, 0.6)',
              borderColor: 'rgb(249, 115, 22)',
              borderWidth: 1,
              data: benarData
            }
          ]
        };

        const chartOptions = {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          },
          plugins: {
            title: {
              display: true,
              text: 'Perbandingan Jawaban Per Set'
            }
          }
        };

        // Render chart
        const ctx = document.getElementById('answerComparisonChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: chartData,
          options: chartOptions
        });
      });
    </script>
  @endpush
@endsection
