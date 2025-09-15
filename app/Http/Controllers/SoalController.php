<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\OpsiSoal;
use App\Models\PackageCategoryMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $query = Soal::with(['kategori', 'opsi']);

        // Apply kategori filter
        if ($request->filled('kategori')) {
            $query->byKategori($request->kategori);
        }

        // Apply tipe filter
        if ($request->filled('tipe')) {
            $query->byTipe($request->tipe);
        }

        // Apply level filter
        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        $soals = $query->paginate(20)->withQueryString();
        $kategoris = KategoriSoal::active()->get();

        return view('admin.soal.index', compact('soals', 'kategoris'));
    }

    public function create()
    {
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];

        return view('admin.soal.create', compact('kategoris', 'tipes'));
    }

    public function store(Request $request)
    {
        $rules = [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2,gambar',
            'level' => 'required|in:mudah,sedang,sulit',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'pembahasan_type' => 'nullable|in:text,image,both',
            'pembahasan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:5'
        ];

        // Add image validation if tipe is gambar
        if ($request->tipe === 'gambar') {
            $rules['gambar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Dynamic validation for kepribadian categories (TKP/PSIKOTES via package mapping)
        if ($request->filled('kategori_id')) {
            $kategori = KategoriSoal::find($request->kategori_id);
            if ($kategori) {
                $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                    $rules['opsi.*.bobot'] = 'required|integer|between:1,5';
                }
            }
        }

        $request->validate($rules);

        DB::transaction(function () use ($request) {
            $soalData = [
                'pertanyaan' => $request->pertanyaan,
                'tipe' => $request->tipe,
                'level' => $request->level,
                'kategori_id' => $request->kategori_id,
                'pembahasan' => $request->pembahasan,
                'pembahasan_type' => $request->pembahasan_type ?? 'text',
                'jawaban_benar' => $request->jawaban_benar
            ];

            // Handle image upload
            if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('soal_images', $imageName, 'public');
                $soalData['gambar'] = $imagePath;
            }

            // Pembahasan image upload
            if (in_array($request->pembahasan_type, ['image', 'both']) && $request->hasFile('pembahasan_image')) {
                $img = $request->file('pembahasan_image');
                $name = time() . '_' . $img->getClientOriginalName();
                $path = $img->storeAs('pembahasan_images', $name, 'public');
                $soalData['pembahasan_image'] = $path;
            }

            $soal = Soal::create($soalData);

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

            // Validate opsi bobot based on kategori package mapping dynamically
            $kategori = KategoriSoal::find($request->kategori_id);
            $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
            $isKepribadian = $kategori && in_array($kategori->kode, $kepribadianKategoriCodes);

            foreach ($soals as $idx => $soalData) {
                if (!isset($soalData['opsi']) || !is_array($soalData['opsi'])) {
                    continue;
                }
                foreach ($soalData['opsi'] as $opsi) {
                    if ($isKepribadian) {
                        if (!isset($opsi['bobot']) || !is_numeric($opsi['bobot']) || intval($opsi['bobot']) < 1 || intval($opsi['bobot']) > 5) {
                            return back()->with('error', 'Bobot opsi harus bilangan bulat 1–5 untuk kategori kepribadian.');
                        }
                    } else {
                        if (!isset($opsi['bobot']) || !is_numeric($opsi['bobot']) || floatval($opsi['bobot']) < 0 || floatval($opsi['bobot']) > 1) {
                            return back()->with('error', 'Bobot opsi harus di antara 0–1 untuk kategori non-kepribadian.');
                        }
                    }
                }
            }

            DB::transaction(function () use ($soals) {
                foreach ($soals as $soalData) {
                    $soal = Soal::create([
                        'pertanyaan' => $soalData['pertanyaan'],
                        'tipe' => $soalData['tipe'],
                        'level' => $soalData['level'] ?? 'mudah',
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
                            } elseif (preg_match('/\[LEVEL\]\s*(mudah|sedang|sulit)/i', $text, $matches)) {
                                if ($currentSoal) {
                                    $currentSoal['level'] = strtolower(trim($matches[1]));
                                }
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
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];

        return view('admin.soal.edit', compact('soal', 'kategoris', 'tipes'));
    }

    public function update(Request $request, Soal $soal)
    {
        $rules = [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2,gambar',
            'level' => 'required|in:mudah,sedang,sulit',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'pembahasan_type' => 'nullable|in:text,image,both',
            'pembahasan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:1'
        ];

        // Add image validation if tipe is gambar and new image is uploaded
        if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
            $rules['gambar'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Dynamic validation for kepribadian categories (TKP/PSIKOTES via package mapping)
        if ($request->filled('kategori_id')) {
            $kategori = KategoriSoal::find($request->kategori_id);
            if ($kategori) {
                $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                    $rules['opsi.*.bobot'] = 'required|integer|between:1,5';
                }
            }
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $soal) {
            $soalData = [
                'pertanyaan' => $request->pertanyaan,
                'tipe' => $request->tipe,
                'level' => $request->level,
                'kategori_id' => $request->kategori_id,
                'pembahasan' => $request->pembahasan,
                'pembahasan_type' => $request->pembahasan_type ?? $soal->pembahasan_type ?? 'text',
                'jawaban_benar' => $request->jawaban_benar
            ];

            // Handle image upload
            if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
                // Delete old image if exists
                if ($soal->gambar && Storage::disk('public')->exists($soal->gambar)) {
                    Storage::disk('public')->delete($soal->gambar);
                }

                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('soal_images', $imageName, 'public');
                $soalData['gambar'] = $imagePath;
            } elseif ($request->tipe !== 'gambar' && $soal->gambar) {
                // Remove image if tipe changed from gambar to something else
                if (Storage::disk('public')->exists($soal->gambar)) {
                    Storage::disk('public')->delete($soal->gambar);
                }
                $soalData['gambar'] = null;
            }

            // Handle pembahasan image upload/update
            if (in_array($request->pembahasan_type, ['image', 'both']) && $request->hasFile('pembahasan_image')) {
                if ($soal->pembahasan_image && Storage::disk('public')->exists($soal->pembahasan_image)) {
                    Storage::disk('public')->delete($soal->pembahasan_image);
                }
                $img = $request->file('pembahasan_image');
                $name = time() . '_' . $img->getClientOriginalName();
                $path = $img->storeAs('pembahasan_images', $name, 'public');
                $soalData['pembahasan_image'] = $path;
            } elseif ($request->pembahasan_type === 'text') {
                // If switching to text-only, remove existing image
                if ($soal->pembahasan_image && Storage::disk('public')->exists($soal->pembahasan_image)) {
                    Storage::disk('public')->delete($soal->pembahasan_image);
                }
                $soalData['pembahasan_image'] = null;
            }

            $soal->update($soalData);

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
        // Delete image if exists
        if ($soal->gambar && Storage::disk('public')->exists($soal->gambar)) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $soal->delete();
        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil dihapus');
    }
}
