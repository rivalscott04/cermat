<?php

namespace App\Services;

use App\Models\HasilTes;
use Illuminate\Support\Facades\DB;

class KecermatanService
{
    /**
     * $data harus berisi:
     * - user_id, skor_benar, skor_salah, waktu_total, detail_jawaban (array)
     * - optional: total_questions (mis. 500)
     */
    public function simpanHasil(array $data)
    {
        DB::beginTransaction();
        try {
            $detailJawaban = $data['detail_jawaban'] ?? [];
            $waktuTotal = $data['waktu_total'] ?? 0;

            // FIX: Handle card_id properly - convert "?" or empty string to null
            $cardId = $data['card_id'] ?? null;
            if ($cardId === '?' || $cardId === '' || $cardId === 'undefined') {
                $cardId = null;
            }

            \Log::info('Extracted values:', [
                'detailJawaban_count' => count($detailJawaban),
                'waktuTotal' => $waktuTotal,
                'cardId' => $cardId,
                'cardId_original' => $data['card_id'] ?? 'not_set'
            ]);

            // Jika diberikan, gunakan total_questions. Kalau tidak, coba infer.
            $totalQuestions = $data['total_questions'] ?? null;
            if ($totalQuestions === null) {
                // infer totalQuestions: untuk tes kecermatan biasanya 10 kolom × 50 soal = 500
                $sets = array_unique(array_map(fn($j) => (int)($j['set'] ?? 0), $detailJawaban));
                $sets = array_filter($sets, fn($s) => $s > 0);
                sort($sets);
                if (count($sets) > 0) {
                    // Untuk tes kecermatan, asumsikan setiap kolom memiliki 50 soal
                    $totalQuestions = count($sets) * 50;
                } else {
                    // fallback: total jawaban yang ada
                    $totalQuestions = count($detailJawaban);
                }
            }

            $averageTime = count($detailJawaban) > 0 ? ($waktuTotal / count($detailJawaban)) : 0;

            // Hitung indikator (mengembalikan PANKER, TIANKER, JANKER, HANKER)
            $indikator = $this->hitungIndikator($detailJawaban, $totalQuestions);
            \Log::info('Calculated indikator:', $indikator);

            // Check if similar result already exists within last 5 minutes to prevent duplicates
            $existingResult = HasilTes::where('user_id', $data['user_id'])
                ->where('jenis_tes', 'kecermatan')
                ->where('skor_benar', $data['skor_benar'])
                ->where('skor_salah', $data['skor_salah'])
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();

            if ($existingResult) {
                DB::rollBack();
                return $existingResult; // Return existing result instead of creating new one
            }

            // Simpan hasil
            $hasilData = [
                'user_id' => $data['user_id'],
                'jenis_tes' => 'kecermatan',
                'card_id' => $cardId, // Now properly handled
                'skor_benar' => $data['skor_benar'],
                'skor_salah' => $data['skor_salah'],
                'waktu_total' => $waktuTotal,
                'average_time' => $averageTime,
                'detail_jawaban' => json_encode($detailJawaban),
                'tanggal_tes' => now(),
                'panker' => $indikator['PANKER'],
                'tianker' => $indikator['TIANKER'],
                'janker' => $indikator['JANKER'],
                'hanker' => $indikator['HANKER'],
                'skor_akhir' => $indikator['SKOR_AKHIR'],
                'kategori_skor' => $indikator['KATEGORI_SKOR'],
            ];

            \Log::info('Data to be saved:', $hasilData);

            $hasil = HasilTes::create($hasilData);

            DB::commit();
            return $hasil;
        } catch (\Exception $e) {
            \Log::error('Error in KecermatanService:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            DB::rollBack();
            throw $e;
        }
    }

    private function hitungIndikator(array $detailJawaban, int $totalQuestions): array
    {
        // --- group by set (kolom)
        $answeredPerSet = []; // A_i
        $correctPerSet = [];

        // Temukan rentang set (untuk mengisi set yang kosong)
        $setsRaw = array_map(fn($j) => isset($j['set']) ? (int)$j['set'] : 0, $detailJawaban);
        $setsRaw = array_filter($setsRaw, fn($s) => $s > 0);
        if (count($setsRaw) === 0) {
            // tidak ada info set, treat whole data as single "set"
            $sets = [1];
        } else {
            $minSet = min($setsRaw);
            $maxSet = max($setsRaw);
            $sets = range($minSet, $maxSet);
        }

        // init
        foreach ($sets as $s) {
            $answeredPerSet[$s] = 0;
            $correctPerSet[$s] = 0;
        }

        foreach ($detailJawaban as $j) {
            $s = isset($j['set']) ? (int)$j['set'] : 0;
            if ($s <= 0) continue; // skip invalid set
            // terjawab: ada field jawaban (non empty)
            if (isset($j['jawaban']) && $j['jawaban'] !== '') {
                $answeredPerSet[$s] = ($answeredPerSet[$s] ?? 0) + 1;
            }
            // benar: field boolean 'benar' true
            if (!empty($j['benar']) && ($j['benar'] === true || $j['benar'] == 1)) {
                $correctPerSet[$s] = ($correctPerSet[$s] ?? 0) + 1;
            }
        }

        // Ai dan Ci sebagai array nilai per kolom (urut sesuai $sets)
        ksort($answeredPerSet); // pastikan urut
        ksort($correctPerSet);
        $Ai = array_values($answeredPerSet);
        $Ci = array_values($correctPerSet);

        $totalTerjawab = array_sum($Ai);
        $totalBenar = array_sum($Ci);

        // 1) PANKER: proporsi dari total soal (gunakan totalQuestions yang valid)
        // Rumus: (Σ A_i) / N × 100
        $PANKER = $totalQuestions > 0 ? ($totalTerjawab / $totalQuestions) * 100 : 0;

        // 2) TIANKER: proporsi benar dari yang dikerjakan
        $TIANKER = $totalTerjawab > 0 ? ($totalBenar / $totalTerjawab) * 100 : 0;

        // 3) JANKER: koefisien variasi dari Ai
        $n = count($Ai);
        $meanA = $n > 0 ? array_sum($Ai) / $n : 0;
        $variance = 0;
        if ($n > 0) {
            foreach ($Ai as $v) $variance += pow($v - $meanA, 2);
            $variance = $variance / $n;
        }
        $stdDev = sqrt($variance);
        $CV = $meanA > 0 ? ($stdDev / $meanA) : 0;
        $JANKER = max(0, min(100, (1 - $CV) * 100));

        // 4) HANKER: rata-rata 3 kolom pertama vs 3 kolom terakhir
        $first3 = array_slice($Ai, 0, 3);
        $last3 = array_slice($Ai, -3);
        $meanFirst3 = count($first3) > 0 ? array_sum($first3) / count($first3) : 0;
        $meanLast3 = count($last3) > 0 ? array_sum($last3) / count($last3) : 0;
        $HANKER = $meanFirst3 > 0 ? ($meanLast3 / $meanFirst3) * 100 : 0;
        $HANKER = min($HANKER, 100);

        // 5) SKOR AKHIR: rata-rata dari 4 indikator
        $skorAkhir = ($PANKER + $TIANKER + $JANKER + $HANKER) / 4;

        // 6) KATEGORI SKOR AKHIR
        $kategoriSkor = $this->tentukanKategoriSkor($skorAkhir);

        return [
            'PANKER' => round($PANKER, 2),
            'TIANKER' => round($TIANKER, 2),
            'JANKER' => round($JANKER, 2),
            'HANKER' => round($HANKER, 2),
            'SKOR_AKHIR' => round($skorAkhir, 2),
            'KATEGORI_SKOR' => $kategoriSkor,
        ];
    }

    /**
     * Tentukan kategori skor berdasarkan rentang skor
     */
    private function tentukanKategoriSkor(float $skor): string
    {
        if ($skor >= 91) {
            return 'Sangat Tinggi';
        } elseif ($skor >= 76) {
            return 'Tinggi';
        } elseif ($skor >= 61) {
            return 'Cukup Tinggi';
        } elseif ($skor >= 41) {
            return 'Sedang';
        } else {
            return 'Rendah';
        }
    }
}
