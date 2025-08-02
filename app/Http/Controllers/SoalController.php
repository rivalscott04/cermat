<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\OpsiSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;

class SoalController extends Controller
{
    public function index()
    {
        $soals = Soal::with(['kategori', 'opsi'])->paginate(20);
        $kategoris = KategoriSoal::active()->get();
        
        return view('admin.soal.index', compact('soals', 'kategoris'));
    }

    public function create()
    {
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2'];
        
        return view('admin.soal.create', compact('kategoris', 'tipes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:1'
        ]);

        DB::transaction(function () use ($request) {
            $soal = Soal::create([
                'pertanyaan' => $request->pertanyaan,
                'tipe' => $request->tipe,
                'kategori_id' => $request->kategori_id,
                'pembahasan' => $request->pembahasan,
                'jawaban_benar' => $request->jawaban_benar
            ]);

            foreach ($request->opsi as $index => $opsiData) {
                OpsiSoal::create([
                    'soal_id' => $soal->id,
                    'opsi' => chr(65 + $index), // A, B, C, D, E
                    'teks' => $opsiData['teks'],
                    'bobot' => $opsiData['bobot'] ?? 0
                ]);
            }
        });

        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil ditambahkan');
    }

    public function uploadWord(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:docx',
            'kategori_id' => 'required|exists:kategori_soal,id'
        ]);

        try {
            $file = $request->file('file');
            $phpWord = IOFactory::load($file->getPathname());
            
            $soals = $this->parseWordDocument($phpWord, $request->kategori_id);
            
            DB::transaction(function () use ($soals) {
                foreach ($soals as $soalData) {
                    $soal = Soal::create([
                        'pertanyaan' => $soalData['pertanyaan'],
                        'tipe' => $soalData['tipe'],
                        'kategori_id' => $soalData['kategori_id'],
                        'pembahasan' => $soalData['pembahasan'] ?? null,
                        'jawaban_benar' => $soalData['jawaban_benar'] ?? null
                    ]);

                    foreach ($soalData['opsi'] as $opsiData) {
                        OpsiSoal::create([
                            'soal_id' => $soal->id,
                            'opsi' => $opsiData['opsi'],
                            'teks' => $opsiData['teks'],
                            'bobot' => $opsiData['bobot']
                        ]);
                    }
                }
            });

            return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil diupload dari file Word');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupload file: ' . $e->getMessage());
        }
    }

    private function parseWordDocument($phpWord, $kategoriId)
    {
        $soals = [];
        $currentSoal = null;
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $text = trim($childElement->getText());
                            
                            if (preg_match('/\[KATEGORI\]\s*(.+)/', $text, $matches)) {
                                // Skip kategori parsing for now, use provided kategori_id
                            } elseif (preg_match('/\[TIPE\]\s*(.+)/', $text, $matches)) {
                                $currentSoal = [
                                    'tipe' => trim($matches[1]),
                                    'kategori_id' => $kategoriId,
                                    'opsi' => []
                                ];
                            } elseif (preg_match('/\[SOAL\]/', $text)) {
                                // Start of question
                            } elseif (preg_match('/\[A\](.+?)\[(\d+(?:\.\d+)?)\]/', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['opsi'][] = [
                                        'opsi' => 'A',
                                        'teks' => trim($matches[1]),
                                        'bobot' => floatval($matches[2])
                                    ];
                                }
                            } elseif (preg_match('/\[B\](.+?)\[(\d+(?:\.\d+)?)\]/', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['opsi'][] = [
                                        'opsi' => 'B',
                                        'teks' => trim($matches[1]),
                                        'bobot' => floatval($matches[2])
                                    ];
                                }
                            } elseif (preg_match('/\[C\](.+?)\[(\d+(?:\.\d+)?)\]/', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['opsi'][] = [
                                        'opsi' => 'C',
                                        'teks' => trim($matches[1]),
                                        'bobot' => floatval($matches[2])
                                    ];
                                }
                            } elseif (preg_match('/\[D\](.+?)\[(\d+(?:\.\d+)?)\]/', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['opsi'][] = [
                                        'opsi' => 'D',
                                        'teks' => trim($matches[1]),
                                        'bobot' => floatval($matches[2])
                                    ];
                                }
                            } elseif (preg_match('/\[E\](.+?)\[(\d+(?:\.\d+)?)\]/', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['opsi'][] = [
                                        'opsi' => 'E',
                                        'teks' => trim($matches[1]),
                                        'bobot' => floatval($matches[2])
                                    ];
                                }
                            } elseif (preg_match('/\[JAWABAN\]\s*(.+)/', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['jawaban_benar'] = trim($matches[1]);
                                }
                            } elseif (preg_match('/\[PEMBAHASAN\]/', $text)) {
                                // Start of explanation
                            } elseif ($currentSoal && !empty($text) && !preg_match('/\[/', $text)) {
                                if (!isset($currentSoal['pertanyaan'])) {
                                    $currentSoal['pertanyaan'] = $text;
                                } elseif (!isset($currentSoal['pembahasan'])) {
                                    $currentSoal['pembahasan'] = $text;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Add current soal if exists
        if ($currentSoal && isset($currentSoal['pertanyaan'])) {
            $soals[] = $currentSoal;
        }
        
        return $soals;
    }

    public function edit(Soal $soal)
    {
        $soal->load(['kategori', 'opsi']);
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2'];
        
        return view('admin.soal.edit', compact('soal', 'kategoris', 'tipes'));
    }

    public function update(Request $request, Soal $soal)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:1'
        ]);

        DB::transaction(function () use ($request, $soal) {
            $soal->update([
                'pertanyaan' => $request->pertanyaan,
                'tipe' => $request->tipe,
                'kategori_id' => $request->kategori_id,
                'pembahasan' => $request->pembahasan,
                'jawaban_benar' => $request->jawaban_benar
            ]);

            // Delete existing options
            $soal->opsi()->delete();

            // Create new options
            foreach ($request->opsi as $index => $opsiData) {
                OpsiSoal::create([
                    'soal_id' => $soal->id,
                    'opsi' => chr(65 + $index),
                    'teks' => $opsiData['teks'],
                    'bobot' => $opsiData['bobot'] ?? 0
                ]);
            }
        });

        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil diperbarui');
    }

    public function destroy(Soal $soal)
    {
        $soal->delete();
        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil dihapus');
    }
} 